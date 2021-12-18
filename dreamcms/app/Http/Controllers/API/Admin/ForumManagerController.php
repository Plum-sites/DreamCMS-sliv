<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forum\Discussion;
use App\Models\Forum\Post;
use App\Models\User;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForumManagerController extends Controller{

    public function loadUser(Request $request){
        if (Gate::denies('ext', ['admin', 'forum_manager.access'])){
            abort(403, 'Недостаточно прав!');
        }

        /* @var User $user */
        $user = User::where('id', '=', $request->get('id'))->get(['id', 'uuid', 'login', 'email', 'realmoney', 'money', 'last_play', 'reputation', 'sign'])->first();

        if (!$user){
            abort(404, 'Пользователь не найден!');
        }

        $user->head_url = '/head/' . $user->login . '/50';

        return \Response::json([
            'user' => $user,
            'bans' => [
                'forum' => $user->bans()->get()
            ]
        ]);
    }

    public function findUser(Request $request){
        if (Gate::denies('ext', ['admin', 'forum_manager.access'])){
            abort(403, 'Недостаточно прав!');
        }

        $q = $request->get('q');

        $data = User::where('login', 'LIKE', '%' . $q . '%')->limit(100)->get(['id', 'uuid', 'login', 'email', 'realmoney', 'money', 'last_play', 'reputation']);

        $user = User::where('login', '=', $q)->get(['id', 'uuid', 'login', 'email', 'realmoney', 'money', 'last_play', 'reputation'])->first();

        if($user) $data->prepend($user);

        $data = $data->map(function ($item, $key) {
            $item->head_url = '/head/' . $item->login . '/50';
            return $item;
        });

        return \Response::json([
            'data' => $data
        ]);
    }

    public function unbanForum(Request $request){
        if (Gate::denies('ext', ['admin', 'forum_manager.access'])){
            abort(403, 'Недостаточно прав!');
        }

        if (Gate::denies('ext', ['forum_manager', 'ban.access'])){
            abort(403, 'Недостаточно прав!');
        }

        /* @var User $user */
        $user = User::findOrFail($request->get('user'));
        $user->unban();

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно сняли ВСЕ баны с форума!'
        ]);
    }

    public function banForum(Request $request){
        if (Gate::denies('ext', ['admin', 'forum_manager.access'])){
            abort(403, 'Недостаточно прав!');
        }

        if (Gate::denies('ext', ['forum_manager', 'ban.access'])){
            abort(403, 'Недостаточно прав!');
        }

        /* @var User $user */
        $user = User::findOrFail($request->get('user'));

        $reason = $request->get('reason');
        $time = $request->get('time');

        $user->ban([
            'comment' => $reason,
            'expired_at' => Carbon::createFromTimestamp($time)
        ]);

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно выдали бан на форуме!'
        ]);
    }

    public function findDiscussion(Request $request){
        if (Gate::denies('ext', ['admin', 'forum_manager.access'])){
            abort(403, 'Недостаточно прав!');
        }

        /* @var Discussion $discussion */
        $discussion = Discussion::where('slug', '=', $request->get('slug'))->get()->first();

        if (!$discussion){
            abort(404, "Тема не найдена!");
        }
        
        return \Response::json([
            'discussion' => $discussion
        ]);
    }

    public function discussionLogs(Request $request){
        if (Gate::denies('ext', ['forum_manager', 'logs.access'])){
            abort(403, 'Недостаточно прав!');
        }

        /* @var Discussion $discussion */
        $discussion = Discussion::findOrFail($request->get('id'));

        $start = $request->get('last');

        $audits = collect();

        $dis_audits = DB::table('audits')
            ->where('id', '<', $start > 0 ? $start : PHP_INT_MAX)
            ->where('auditable_id', '=', $discussion->id)
            ->where('auditable_type', '=', 'App\\Models\\Forum\\Discussion')
        ->get();

        $audits = $audits->union($dis_audits);

        /* @var Post $post */

        foreach ($discussion->posts()->get() as $post){
            $audits = $audits->union($post->audits()->get());
        }

        $del_audits = DB::table('audits')
            ->where('auditable_type', '=', 'App\\Models\\Forum\\Post')
            ->where(function($q) use ($discussion) {
                $q->where('old_values', 'LIKE', '"chatter_discussion_id":' . $discussion->id)->orWhere('new_values', 'LIKE', '"chatter_discussion_id":' . $discussion->id);
            })
        ->get();

        $audits = $audits->union($del_audits);

        return \Response::json(
            $audits->sortByDesc('id')->values()->toArray()
        );
    }

    public function userLogs(Request $request){
        if (Gate::denies('ext', ['forum_manager', 'logs.access'])){
            abort(403, 'Недостаточно прав!');
        }

        /* @var User $user */
        $user = User::findOrFail($request->get('user'));

        $start = $request->get('last');

        return \Response::json(
            DB::table('audits')
                ->where('id', '<', $start > 0 ? $start : PHP_INT_MAX)
                ->where('user_id', '=', $user->id)
                ->where('auditable_type', 'LIKE', '%Forum%')
                ->orderByDesc('id')
                ->limit(25)
            ->get()
        );
    }

    public function removeSign(Request $request){
        if (Gate::denies('ext', ['forum_manager', 'sign.delete'])){
            abort(403, 'Недостаточно прав!');
        }

        /* @var User $user */
        $user = User::findOrFail($request->get('user'));

        $user->sign = null;
        $user->save();

        return \Response::json([
            'success' => true,
            'message' => 'Подпись успешно удалена!'
        ]);
    }
}