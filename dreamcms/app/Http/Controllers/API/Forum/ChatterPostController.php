<?php

namespace App\Http\Controllers\API\Forum;

use App\Events\PostLikeEvent;
use App\Http\Controllers\Controller;
use App\Models\Forum\Category;
use App\Models\Forum\Discussion;
use App\Models\Forum\Models;
use App\Models\Forum\Post;
use App\Models\User;
use App\Notifications\NewReply;
use Auth;
use Carbon\Carbon;
use Genert\BBCode\BBCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Purifier;
use Validator;
use Wkhooy\ObsceneCensorRus;

class ChatterPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*$total = 10;
        $offset = 0;
        if ($request->total) {
            $total = $request->total;
        }
        if ($request->offset) {
            $offset = $request->offset;
        }
        $posts = Models::post()->with('user')->orderBy('created_at', 'DESC')->take($total)->offset($offset)->get();*/

        // This is another unused route
        // we return an empty array to not expose user data to the public
        return response()->json([]);
    }

    public function getLikes(Request $request, $id){
        $post = Post::findOrFail($id);

        $likes = \DB::select('SELECT post_likes.to, post_likes.from, post_likes.rep, users.id, users.login, users.uuid FROM post_likes JOIN users ON post_likes.from = users.id  WHERE post_likes.to = ?', [ $post->id ]);

        return \Response::json([
            'success' => true,
            'users' => $likes
        ]);
    }

    public function tryToGiveRep(User $user, $reputation){
        User::disableAuditing();

        $user->reputation += $reputation;
        $user->save();
    }

    public function like(Request $request, $id){
        Post::disableAuditing();

        if (!Auth::user()->last_play){
            return \Response::json([
                'success' => false,
                'message' => 'Вы не можете оценивать посты если вы не заходили в игру!'
            ]);
        }

        $post = Post::findOrFail($id);

        if ($post->user_id == Auth::user()->id){
            return \Response::json([
                'success' => false,
                'message' => 'Вы не можете оценивать свои посты!'
            ]);
        }

        if (!Auth::user()->last_play){
            return \Response::json([
                'success' => false,
                'message' => 'Игроки которые ни разу не входили в игру, не могут оценивать посты!'
            ]);
        }

        $reputation = intval($request->get('like'));
        if ($reputation !== -1 && $reputation !== 1){
            return \Response::json([
                'success' => false,
                'message' => 'Неверные данные!'
            ]);
        }

        $limit = \DB::selectOne("SELECT COUNT(post_likes.id) as `count` FROM post_likes JOIN chatter_post ON chatter_post.id = post_likes.`to` WHERE post_likes.`from` = ? AND chatter_post.user_id = ? AND post_likes.time > ?", [Auth::user()->id, $post->user_id, (time() - (60 * 60))])->count;

        if ($limit >= 5){
            return \Response::json([
                'success' => false,
                'message' => 'Нельзя ставить больше 5 лайков/дизлайков одному человеку за час!'
            ]);
        }

        $info = \DB::table('post_likes')->where([
            ['from', Auth::user()->id],
            ['to', $post->id]
        ])->first();

        if ($info){
            if ($reputation == $info->rep){
                return \Response::json([
                    'success' => false,
                    'message' => 'Вы уже ' . ($reputation > 0 ? "лайкали" : "дизлайкали") . " этот пост!"
                ]);
            }else{
                \DB::table('post_likes')->where([
                    ['from', Auth::user()->id],
                    ['to', $post->id]
                ])->update([
                    'rep' => $reputation,
                    'time' => time()
                ]);
                $post->likes += $reputation * 2;
                $post->save();

                $user = User::find($post->user_id);
                $this->tryToGiveRep($user, $reputation * 2);

                event(new PostLikeEvent($post, Auth::user(), $user, $reputation * 2));

                return \Response::json([
                    'success' => true,
                    'message' => 'Вы поставили ' . ($reputation > 0 ? "лайк" : "дизлайк") . " этому посту"
                ]);
            }
        }

        \DB::table('post_likes')->insert([
            'from' => Auth::user()->id,
            'to' => $post->id,
            'rep' => $reputation,
            'time' => time()
        ]);

        $post->likes += $reputation;
        $post->save();

        $user = User::find($post->user_id);
        $this->tryToGiveRep($user, $reputation);

        event(new PostLikeEvent($post, Auth::user(), $user, $reputation));

        return \Response::json([
            'success' => true,
            'message' => 'Вы поставили ' . ($reputation > 0 ? "лайк" : "дизлайк") . " этому посту"
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->request->set('body', Purifier::clean($request->body));

        $stripped_tags_body = ['body' => strip_tags($request->body)];
        $validator = Validator::make($stripped_tags_body, [
            'body' => 'required|min:10',
        ]);
        
        if ($validator->fails()) {
            return \Response::json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        if ($ban = $user->bans()->first()){
            return \Response::json([
                'success' => false,
                'message' => 'Вы забанены на форуме до ' . $ban->expired_at . ' по причине ' . $ban->comment
            ]);
        }

        if (config('chatter.security.limit_time_between_posts')) {
            if ($this->notEnoughTimeBetweenPosts()) {
                $minute_copy = (config('chatter.security.time_between_posts') == 1) ? ' минуту' : ' минуты';

                return \Response::json([
                    'success' => false,
                    'message' => 'Что бы предотвратить спам, подождите '.config('chatter.security.time_between_posts').$minute_copy.' перед публикацией следующего поста.'
                ]);
            }
        }

        if (!$user->isModer()){
            if (ChatterController::badText($request->body)){
                return \Response::json([
                    'success' => false,
                    'message' => 'В тексте содержатся запрещенные слова!'
                ]);
            }

            if (\Censure::is_bad(strip_tags($request->body))){
                return \Response::json([
                    'success' => false,
                    'message' => 'Запрещено использовать нецензурную лексику и оскорбления!'
                ]);
            }

            if ($user->isStrictChecking() && !ObsceneCensorRus::isAllowed(strip_tags($request->body))){
                return \Response::json([
                    'success' => false,
                    'message' => 'Запрещено использовать нецензурную лексику!'
                ]);
            }
        }

        if(\DB::table('ban_list')->where([
            ['UUID', '=', $user->uuid],
            ['Reason', '=', '10.1'],
            ['Temptime', '<', time() + (7 * 24 * 60 * 60)]
        ])->count()){
            return \Response::json([
                'success' => false,
                'message' => 'У вас временная блокировка на форуме!'
            ]);
        }

        $request->request->add(['user_id' => $user->id]);


        $new_post = Post::create($request->all());

        $discussion = Discussion::find($request->chatter_discussion_id);
        $discussion->touch();

        Category::disableAuditing();
        $category = Models::category()->find($discussion->chatter_category_id);
        if (!isset($category->slug)) {
            $category = Models::category()->first();
        }

        if ($new_post->id) {
            $category->posts_count += 1;
            $category->last_post = $new_post->id;
            $category->save();

            if ($discussion->user->id != $user->id){
                $discussion->user->notify(new NewReply($discussion, $new_post));
            }

            return \Response::json([
                'success' => true,
                'message' => 'Ваш ответ успешно опубликован!'
            ]);
        } else {
            return \Response::json([
                'success' => false,
                'message' => 'Простите, что-то пошло не так. Попробуйте позже.'
            ]);
        }
    }

    private function notEnoughTimeBetweenPosts()
    {
        $user = Auth::user();

        if($user->hasPermissionTo('forum.moder.access') || $user->isModer()) return false;

        $past = Carbon::now()->subMinutes(config('chatter.security.time_between_posts'));

        $last_post = Models::post()->where('user_id', '=', $user->id)->where('created_at', '>=', $past)->first();

        if (isset($last_post)) {
            return true;
        }

        return false;
    }

    private function sendEmailNotifications($discussion)
    {
        $users = $discussion->users->except(Auth::user()->id);
        foreach ($users as $user) {
            //Mail::to($user)->queue(new ChatterDiscussionUpdated($discussion));
        }
    }

    public function update(Request $request, $id)
    {
        $stripped_tags_body = ['body' => strip_tags($request->body)];
        $validator = Validator::make($stripped_tags_body, [
            'body' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $bb = new BBCode();

        if (!Auth::user()->isModer() && !ObsceneCensorRus::isAllowed($bb->stripBBCodeTags($request->body))){
            return \Response::json([
                'success' => false,
                'message' => 'Запрещено использовать мат!'
            ]);
        }

        if (ChatterController::badText($request->body)){
            return \Response::json([
                'success' => false,
                'message' => 'В тексте содержатся запрещенные слова!'
            ]);
        }

        $post = Models::post()->find($id);
        $discussion = Models::discussion()->find($post->chatter_discussion_id);

        if (!Auth::guest() && ((Auth::user()->id == $post->user_id) || \Gate::allows('ext', ['forum', 'post.edit']))) {
            if ($discussion->no_reply){
                return \Response::json([
                    'success' => false,
                    'message' => 'Тема закрыта, вы не можете изменять сообщения'
                ]);
            }

            $post->body = Purifier::clean($request->body);
            $post->save();

            return \Response::json([
                'success' => true,
                'message' => 'Вы успешно отредактировали сообщение!'
            ]);
        } else {
            return \Response::json([
                'success' => false,
                'message' => 'Недостаточно прав!'
            ]);
        }
    }

    public function destroy($id, Request $request)
    {
        $post = Models::post()->with('discussion')->findOrFail($id);

        if ($request->user()->id !== (int) $post->user_id && \Gate::denies('ext', ['forum', 'post.delete'])) {
            return \Response::json([
                'success' => false,
                'message' => 'Вы не можете удалить это сообщение!'
            ]);
        }

        Category::disableAuditing();
        $category = $post->discussion->category()->first();

        if ($post->discussion->posts()->oldest()->first()->id === $post->id) {
            return \Response::json([
                'success' => false,
                'message' => 'Вы не можете удалять первое сообщение в теме!'
            ]);

//            $category->discussions_count -= 1;
//            $category->posts_count -= $post->discussion->posts()->count();
//
//            $category->save();
//
//            $post->discussion->posts()->delete();
//            $post->discussion()->delete();
//
//            return \Response::json([
//                'success' => true,
//                'message' => 'Вы успешно удалили тему!'
//            ]);
        }

        $post->delete();
        $category->posts_count -= 1;
        $category->save();

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили сообщение из темы!'
        ]);
    }

    public function saveTemplate(Request $request)
    {
        $name = $request->name;
        $body = Purifier::clean($request->body);

        \DB::table('forum_templates')->insert([
            'user' => Auth::user()->id,
            'name' => $name,
            'body' => $body
        ]);

        return \Response::json([
            'success' => true,
            'message' => 'Шаблон создан! Обновите страницу!'
        ]);
    }

    public function deleteTemplate(Request $request)
    {
        \DB::table('forum_templates')->where([
            'user' => Auth::user()->id,
            'id' => $request->get('id')
        ])->delete();

        return \Response::json([
            'success' => true,
            'message' => 'Шаблон удален!'
        ]);
    }
}
