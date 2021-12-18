<?php

namespace App\Http\Controllers\API\Forum;

use App\Http\Controllers\API\Admin\ExtendedPermissions;
use App\Models\Forum\Category;
use App\Models\Forum\Discussion;
use App\Models\Forum\Models;
use App\Models\Forum\Post;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as Controller;
use Purifier;
use Validator;
use Wkhooy\ObsceneCensorRus;

class ChatterDiscussionController extends Controller
{
    public function loadDiscussion(Request $request, $slug = null)
    {
        $discussion = Discussion::with('user:id,login,uuid')->where('slug', '=', $slug)->first();
        if (is_null($discussion)) {
            return [
                'success' => false,
                'message' => 'Эта тема не найдена или была удалена!'
            ];
        }

        $discussion_category = Category::find($discussion->chatter_category_id);

        if (($discussion_category->hidden || $discussion->only_admins) && (!Auth::check() || !Auth::user()->hasPermissionTo('forum.discussion.access'))){
            return [
                'success' => false,
                'message' => 'Эта тема скрыта для просмотра!'
            ];
        }

        if (!ExtendedPermissions::checkModelAccess(Auth::user(), get_class($discussion_category), 'view', $discussion_category->id)){
            return [
                'success' => false,
                'message' => 'Недостаточно прав [' . $discussion_category->id . ']!'
            ];
        }

        $offset = 0;
        if ($page = $request->get('page'))
        {
            if (is_numeric($page)){
                $offset = ($page - 1) * 10;
                if ($offset < 0) $offset = 0;
            }
        }

        $posts = Post::with('user:id,login,uuid,reputation,prefix,sign')->where('chatter_discussion_id', '=', $discussion->id)->orderBy('created_at', 'ASC')->offset($offset)->limit(10)->get();
        $posts->transform(function ($post){
            $post->user->siterole = $post->user->getSiteRole();
            if ($post->user->isBanned()){
                $post->user->siterole = '<span style="text-decoration: line-through;">Заблокирован</span>';
            }
            if (\Cache::has('user_post_count_' . $post->user->id)){
                $post->user->posts = \Cache::get('user_post_count_' . $post->user->id);
            }else{
                $post->user->posts = \DB::select('SELECT COUNT(*) as count FROM chatter_post WHERE chatter_post.user_id = ? AND chatter_post.chatter_discussion_id NOT IN (SELECT chatter_discussion.id FROM chatter_discussion WHERE chatter_discussion.chatter_category_id IN (SELECT chatter_categories.id FROM chatter_categories WHERE chatter_categories.not_count = 1))', [$post->user->id])[0]->count;
                \Cache::set('user_post_count_' . $post->user->id, $post->user->posts, 60);
            }
            return $post;
        });


        $templates = collect();
        if (Auth::check() && Auth::user()->hasPermissionTo('forum.template.access')){
            $templates = \DB::table('forum_templates')->where('user', '=', Auth::user()->id)->get();

            $templates = $templates->map(function ($template){
                return [
                    'id' => $template->id,
                    'title' => $template->name,
                    'content' => $template->body,
                    'description' => ''
                ];
            });
        }

        return \Response::json([
           'discussion' => $discussion,
           'category' => $discussion_category,
           'posts' => [
               'total' => Post::where('chatter_discussion_id', '=', $discussion->id)->count(),
               'data' => $posts
           ],
           'templates' => $templates,
        ]);
    }

    public function pin(Request $request)
    {
        $discussions = $request->get('discussion');
        if (!is_array($discussions)){
            $discussions = [Discussion::findOrFail($discussions)];
        }else{
            foreach ($discussions as $key=>$value){
                $discussions[$key] = Discussion::findOrFail($value);
            }
        }

        foreach ($discussions as $discussion) {
            if (\Gate::allows('ext', [$discussion->category()->first(), 'pin_thread'])){
                $discussion->pinned = 1;
                $discussion->save();
            }else{
                return \Response::json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав!'
                ]);
            }
        }

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно закрепили темы!'
        ]);
    }

    public function unpin(Request $request)
    {
        $discussions = $request->get('discussion');
        if (!is_array($discussions)){
            $discussions = [Discussion::findOrFail($discussions)];
        }else{
            foreach ($discussions as $key=>$value){
                $discussions[$key] = Discussion::findOrFail($value);
            }
        }

        foreach ($discussions as $discussion) {
            if (\Gate::allows('ext', [$discussion->category()->first(), 'pin_thread'])){
                $discussion->pinned = 0;
                $discussion->save();
            }else{
                return \Response::json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав!'
                ]);
            }
        }

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно открепили темы!'
        ]);
    }

    public function lock(Request $request)
    {
        $discussions = $request->get('discussion');
        if (!is_array($discussions)){
            $discussions = [Discussion::findOrFail($discussions)];
        }else{
            foreach ($discussions as $key=>$value){
                $discussions[$key] = Discussion::findOrFail($value);
            }
        }

        foreach ($discussions as $discussion) {
            if (\Gate::allows('ext', [$discussion->category()->first(), 'close_thread'])){
                $discussion->no_reply = 1;
                $discussion->save();
            }else{
                return \Response::json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав!'
                ]);
            }
        }

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно закрыли темы!'
        ]);
    }

    public function unlock(Request $request)
    {
        $discussions = $request->get('discussion');
        
        if (!is_array($discussions)){
            $discussions = [Discussion::findOrFail($discussions)];
        }else{
            foreach ($discussions as $key=>$value){
                $discussions[$key] = Discussion::findOrFail($value);
            }
        }
    
        foreach ($discussions as $discussion) {
            if (\Gate::allows('ext', [$discussion->category()->first(), 'close_thread'])){
                $discussion->no_reply = 0;
                $discussion->save();
            }else{
                return \Response::json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав!'
                ]);
            }
        }
    
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно открыли темы!'
        ]);
    }

    public function move(Request $request)
    {
        $category = Category::findOrFail($request->get('category'));
    
        if (!\Gate::allows('ext', [$category, 'move_thread'])){
            return \Response::json([
                'success' => false,
                'message' => 'У вас недостаточно прав!'
            ]);
        }

        $discussions = $request->get('discussion');
        if (!is_array($discussions)){
            $discussions = [Discussion::findOrFail($discussions)];
        }else{
            foreach ($discussions as $key => $value){
                $discussion = Discussion::findOrFail($value);
                if (\Gate::allows('ext', [$discussion->category()->first(), 'move_thread'])){
                    $discussions[$key] = $discussion;
                }
            }
        }

        foreach ($discussions as $disc){
            $oldcat = $disc->category()->first();
            $oldcat->discussions_count -= 1;
            $oldcat->posts_count -= $disc->posts()->count();
            $oldcat->save();

            $disc->chatter_category_id = $category->id;
            $disc->save();
        }

        $category->discussions_count += count($discussions);

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно перенесли темы!'
        ]);
    }

    public function hide(Request $request)
    {
        $discussions = $request->get('discussion');
        if (!is_array($discussions)){
            $discussions = [Discussion::findOrFail($discussions)];
        }else{
            foreach ($discussions as $key=>$value){
                $discussions[$key] = Discussion::findOrFail($value);
            }
        }

        foreach ($discussions as $disc){
            if (\Gate::allows('ext', [$disc->category()->first(), 'hide_thread'])){
                $disc->only_admins = 1;
                $disc->save();
            }else{
                return \Response::json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав!'
                ]);
            }
        }

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно скрыли темы!'
        ]);
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        
        $discussions = $request->get('discussion');
        if (!is_array($discussions)){
            $discussions = [Discussion::findOrFail($discussions)];
        }else{
            foreach ($discussions as $key=>$value){
                $discussions[$key] = Discussion::findOrFail($value);
            }
        }

        Category::disableAuditing();
        foreach ($discussions as $disc){
            if (\Gate::allows('ext', [$disc->category()->first(), 'delete_thread'])){
                $oldcat = $disc->category()->first();
                $oldcat->discussions_count -= 1;
                $oldcat->posts_count -= $disc->posts()->count();
                $oldcat->save();

                $disc->delete();
            }else{
                return \Response::json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав!'
                ]);
            }
        }

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили темы!'
        ]);
    }

    public function store(Request $request)
    {
        $request->request->set('body', Purifier::clean($request->body));

        $request->request->add(['body_content' => strip_tags($request->body)]);

        $validator = Validator::make($request->all(), [
            'title'               => 'required|min:5|max:50',
            'body_content'        => 'required|min:10',
            'category_slug'         => 'required',
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = Auth::user();

        if ($ban = $user->bans()->first()){
            return \Response::json([
                'success' => false,
                'message' => 'Вы забанены на форуме до ' . $ban->expired_at . ' по причине ' . $ban->comment
            ]);
        }
    
        $category = Category::where('slug', '=', $request->category_slug)->first();
    
        if (!\Gate::allows('ext', [$category, 'create_thread'])){
            return \Response::json([
                'success' => false,
                'message' => 'Недостаточно прав для создания темы в этом разделе!'
            ]);
        }

        if (config('chatter.security.limit_time_between_posts')) {
            if ($this->notEnoughTimeBetweenDiscussion()) {
                $minute_copy = (config('chatter.security.time_between_posts') == 1) ? ' минуту' : ' минут';
                return \Response::json([
                    'success' => false,
                    'message' => 'Подождите '. $minute_copy . ' перед созданием новой темы!'
                ]);
            }
        }

        if (!$user->isModer()){
            if (ChatterController::badText($request->body_content)){
                return \Response::json([
                    'success' => false,
                    'message' => 'В тексте содержатся запрещенные слова!'
                ]);
            }

            if (\Censure::is_bad(strip_tags($request->body_content)) || \Censure::is_bad(strip_tags($request->title))){
                return \Response::json([
                    'success' => false,
                    'message' => 'Запрещено использовать нецензурную лексику или оскорбления!'
                ]);
            }
        }

        if ($user->isStrictChecking() && (!ObsceneCensorRus::isAllowed(strip_tags($request->body_content)) || !ObsceneCensorRus::isAllowed($request->title))){
            return \Response::json([
                'success' => false,
                'message' => 'Запрещено использовать мат!'
            ]);
        }

        $slug = \Str::slug($request->title, '-');

        $discussion_exists = Models::discussion()->where('slug', '=', $slug)->first();
        $incrementer = 1;
        $new_slug = $slug;
        while (isset($discussion_exists->id)) {
            $new_slug = $slug.'-'.$incrementer;
            $discussion_exists = Models::discussion()->where('slug', '=', $new_slug)->first();
            $incrementer += 1;
        }

        if ($slug != $new_slug) {
            $slug = $new_slug;
        }

        $new_discussion = [
            'title'               => strip_tags($request->title),
            'chatter_category_id' => $category->id,
            'user_id'             => $user->id,
            'slug'                => $slug,
            'only_admins'         => 0,
            'pinned'              => \Gate::allows('ext', [$category, 'pin_thread']) ? ($request->pinned ? $request->pinned : 0) : 0,
            'no_reply'            => \Gate::allows('ext', [$category, 'close_thread']) ? ($request->no_reply ? $request->no_reply : 0) : 0,
        ];

        if (!isset($category->slug)) {
            $category = Models::category()->first();
        }

        $category->discussions_count += 1;

        $discussion = Models::discussion()->create($new_discussion);

        $new_post = [
            'chatter_discussion_id' => $discussion->id,
            'user_id'               => $user->id,
            'body'                  => $request->body,
         ];


        $post = Post::create($new_post);
        $post->save();

        if ($post->id) {
            $category->posts_count += 1;
            $category->last_post = $post->id;
            $category->save();

            return \Response::json([
                'success' => true,
                'slug' => $slug
            ]);
        } else {
            $category->save();

            return \Response::json([
                'success' => false,
                'message' => 'Произошла ошибка при создании темы! Попробуйте позже!'
            ]);
        }
    }

    private function notEnoughTimeBetweenDiscussion()
    {
        $user = Auth::user();

        if($user->isModer()) return false;

        $past = Carbon::now()->subMinutes(config('chatter.security.time_between_posts'));

        $last_discussion = Models::discussion()->where('user_id', '=', $user->id)->where('created_at', '>=', $past)->first();

        if (isset($last_discussion)) {
            return true;
        }

        return false;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
