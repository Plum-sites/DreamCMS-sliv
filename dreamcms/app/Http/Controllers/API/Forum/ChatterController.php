<?php

namespace App\Http\Controllers\API\Forum;

use App\Http\Controllers\API\Admin\ExtendedPermissions;
use App\Models\Activity;
use App\Models\Forum\Category;
use App\Models\Forum\Discussion;
use App\Models\Forum\Post;
use App\Models\User;
use Auth;
use Backpack\PermissionManager\app\Models\Role;
use Carbon\Carbon;
use Gate;
use Genert\BBCode\BBCode;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class ChatterController extends Controller
{
    public function search(Request $request){
        $per_page = 10;

        $text = $request->get('text');
        $user = $request->get('user') ? User::findOrFail($request->get('user')) : false;
        $page = $request->get('page') ? intval($request->get('page')) : 0;

        $posts = [];
        $discussions = [];

        if ($text){
            $text = Str::limit(trim(strip_tags($text)), 255, '');

            $builder = Post::with('user:id,login,uuid', 'discussion')->whereIn('id', Post::search($text)->keys()->toArray());
            if ($user) $builder = $builder->where('user_id', $user->id);
            $posts = $builder->orderByDesc('id')->paginate($per_page, ['*'], 'page', $page);

            $builder = Discussion::with('user:id,login,uuid', 'category')->whereIn('id', Discussion::search($text)->keys()->toArray());
            if ($user) $builder = $builder->where('user_id', $user->id);
            $discussions = $builder->orderByDesc('id')->paginate($per_page, ['*'], 'page', $page);
        }else{
            if ($user){
                $posts = Post::with('user:id,login,uuid', 'discussion')->where('user_id', '=', $user->id)->orderByDesc('id')->paginate($per_page, ['*'], 'page', $page);
                $discussions = Discussion::with('user:id,login,uuid', 'category')->where('user_id', '=', $user->id)->orderByDesc('id')->paginate($per_page, ['*'], 'page', $page);
            }
        }

        return \Response::json([
            'success' => true,
            'data' => [
                'posts' => $posts,
                'discussions' => $discussions
            ]
        ]);
    }

    public function admins(Request $request){
        $users = collect();

        $roles = ['admin', 'forummoder', 'coder', 'curator', 'grandmoder', 'moder'];

        foreach ($roles as $role_name){
            $users = $users->merge(Role::findByName($role_name, 'web')->users()->get(['id', 'login', 'uuid', 'prefix']));
        }

        $users = $users->unique(function ($user){
            return $user->id;
        })->values();

        /* @var User $user */
        $users = $users->map(function ($user){
            $user->role = $user->getSiteRole();
            $user->posts = \DB::select('SELECT COUNT(*) as count FROM chatter_post WHERE chatter_post.user_id = ? AND chatter_post.chatter_discussion_id NOT IN (SELECT chatter_discussion.id FROM chatter_discussion WHERE chatter_discussion.chatter_category_id IN (SELECT chatter_categories.id FROM chatter_categories WHERE chatter_categories.not_count = 1))', [$user->id])[0]->count;
            $post = Post::where('user_id', '=', $user->id)->orderByDesc('id')->limit(1)->first();
            $user->last_activity = $post ? Carbon::instance($post->created_at)->getTimestamp() : 0;

            return $user;
        });

        $per_page = 25;

        return [
            'success' => true,
            'users' => new LengthAwarePaginator($users->forPage($request->get('page', 1), $per_page), $users->count(), $per_page, $request->get('page', 1))
        ];
    }

    public function index()
    {
        $categories = Category::with('childs.childs')->whereNull('parent_id')->get();

        $categories = $categories->filter(function ($rootCat){
            return ExtendedPermissions::checkModelAccess(Auth::user(), get_class($rootCat), 'view', $rootCat->id);
        });

        $categories = $categories->map(function ($rootCat, $key) {
            /*$rootCat->childs = $rootCat->childs->filter(function ($secondCat){
                return \Gate::allows('ext', [$secondCat, 'view']);
            });*/

            $rootCat->post_preview = null;

            $rootCat->childs = $rootCat->childs->map(function ($secondCat, $key){
                $childs = collect($secondCat->childs);

                /*$childs = $childs->filter(function ($child){
                    return \Gate::allows('ext', [$child, 'view']);
                });*/

                $childs->push($secondCat);

                $secondCat->discussions_count = $childs->sum('discussions_count');
                $secondCat->posts_count = $childs->sum('posts_count');

                $secondCat->post_preview = Post::with(['user:id,login,uuid', 'discussion:id,title,slug'])->find($childs->max('last_post'), ['id', 'created_at', 'user_id', 'chatter_discussion_id']);

                return $secondCat;
            });

            return $rootCat;
        });

        return [
            'categories' => $categories
        ];
    }

    public function loadCategory(Request $request, $slug){
        $category = Category::where('slug', '=', $slug)->with('childs')->first();

        if (!ExtendedPermissions::checkModelAccess(Auth::user(), get_class($category), 'view', $category->id)){
            return [
                'success' => false,
                'message' => 'Недостаточно прав!'
            ];
        }

        $rights = [
            'view' => Gate::allows('ext', [$category, 'view']),
            'pin_thread' => Gate::allows('ext', [$category, 'pin_thread']),
            'create_thread' => Gate::allows('ext', [$category, 'create_thread']),
            'close_thread' => Gate::allows('ext', [$category, 'close_thread']),
            'hide_thread' => Gate::allows('ext', [$category, 'hide_thread']),
            'delete_thread' => Gate::allows('ext', [$category, 'delete_thread']),
            'move_thread' => Gate::allows('ext', [$category, 'move_thread']),
        ];

        if (isset($category->id)) {
            $offset = 0;
            if ($page = $request->get('page')){
                if (is_numeric($page)){
                    $offset = ($page - 1) * 10;
                    if ($offset < 10) $offset = 0;
                }
            }

            foreach ($category->childs as $child){
                $child->last_post = Post::with('user:id,login,uuid')->with('discussion:id,title,slug')->find($child->last_post);
            }

            $discussions_pinned = collect();

            if(is_numeric($page) && $page == 1)
                $discussions_pinned = Discussion::with('user:id,login,uuid')->with('postsCount')->where('pinned', '=', 1)->where('chatter_category_id', '=', $category->id)->orderByDesc('updated_at')->get(['id', 'title', 'slug', 'user_id', 'updated_at', 'pinned', 'no_reply']);

            $discussions = Discussion::with('user:id,login,uuid')->with('postsCount')->where('pinned', '=', 0)->where('chatter_category_id', '=', $category->id)->orderByDesc('updated_at')->offset($offset)->limit(10)->get(['id', 'title', 'slug', 'user_id', 'updated_at', 'pinned', 'no_reply']);

            $discussions_merged = $discussions_pinned->merge($discussions);

            $discussions_merged->transform(function ($discussion) {
                $discussion->users = DB::select('SELECT chatter_post.user_id, users.id, users.login, users.uuid FROM chatter_post JOIN users ON chatter_post.user_id = users.id WHERE chatter_post.chatter_discussion_id = ? GROUP BY chatter_post.user_id, users.id, users.login, users.uuid ORDER BY chatter_post.created_at DESC', [$discussion->id]);
                return $discussion;
            });
        }

        return [
            'rights' => $rights,
            'category' => $category,
            'discussions' => [
                'data' => $discussions_merged,
                'per_page' => 10,
                'total' => Discussion::where('chatter_category_id', '=', $category->id)->where('pinned', '=', 0)->count()
            ]
        ];
    }

    public function loadLatest(){
        if (\Cache::has('forum_latest')){
            $posts = \Cache::get('forum_latest');
        }else{
            $posts = Post::with(['user:id,login,uuid', 'discussion:id,title,slug'])->orderByDesc('id')->limit(7)->get();
            \Cache::set('forum_latest', $posts, 10);
        }

        return \Response::json([
            'posts' => $posts
        ]);
    }

    public function loadLeaders(){
        if (\Cache::has('forum_leaders')){
            $leaders = \Cache::get('forum_leaders');
        }else{
            $leaders = \DB::select("SELECT COUNT(*) as posts, users.login, users.uuid FROM `chatter_post` LEFT JOIN users ON users.id = chatter_post.user_id WHERE chatter_post.user_id NOT IN (SELECT DISTINCT model_id FROM model_has_roles) GROUP BY login, uuid ORDER BY posts DESC LIMIT 7;");
            \Cache::set('forum_leaders', $leaders, 60 * 60);
        }

        return \Response::json([
            'leaders' => $leaders
        ]);
    }

    public function loadPopulars(){
        if (\Cache::has('forum_populars')){
            $leaders = \Cache::get('forum_populars');
        }else{
            $leaders = \DB::select("SELECT login, uuid, reputation FROM users WHERE id NOT IN (SELECT DISTINCT model_id FROM model_has_roles) ORDER BY reputation DESC LIMIT 7;");
            \Cache::set('forum_populars', $leaders, 60 * 60);
        }

        return \Response::json([
            'populars' => $leaders
        ]);
    }

    public function dump($value)
    {
        if (class_exists(CliDumper::class)) {
            $dumper = 'cli' === PHP_SAPI ? new CliDumper : new HtmlDumper;

            $cloner = new VarCloner();
            $cloner->setMaxItems(5000);
            $dumper->dump($cloner->cloneVar($value));
        } else {
            var_dump($value);
        }
    }

    public function sign_del(Request $request){
        $user = User::findOrFail($request->get('user'));

        if (Gate::allows('ext', ['forum_manager', 'sign.delete'])) {
            $user->sign = '';
            $user->save();

            /*Activity::user_action(Auth::user(), 'sign_del', [
                'user' => $user
            ]);*/

            return \Response::json([
                'success' => true,
                'message' => 'Подпись игрока удалена!'
            ]);
        }else{
            return \Response::json([
                'success' => true,
                'message' => 'У вас нет прав!'
            ]);
        }
    }

    public function reputation(Request $request)
    {
        User::disableAuditing();

        $user = User::findOrFail($request->get('user'));
        $reputation = $request->get('reputation');

        if ($reputation != -1 && $reputation != 1){
            return \Response::json([
                'success' => true,
                'message' => "Ошибка!"
            ]);
        }

        $info = \DB::table('user_reputation')->where([
            ['from', Auth::user()->id],
            ['to', $user->id]
        ])->get();

        if(isset($info[0])){
            if ($reputation == $info[0]->rep){
                return \Response::json([
                    'success' => false,
                    'message' => 'Вы уже ' . ($reputation > 0 ? "повышали" : "понижали") . " репупацию этого пользователя!"
                ]);
            }else{
                \DB::table('user_reputation')->where([
                    ['from', Auth::user()->id],
                    ['to', $user->id]
                ])->update([
                    'rep' => $reputation
                ]);
                $user->reputation += $reputation;
                $user->save();

                return \Response::json([
                    'success' => true,
                    'message' => 'Вы ' . ($reputation > 0 ? "повысили" : "понизили") . " репупацию этого пользователя!"
                ]);
            }
        }else{
            \DB::table('user_reputation')->insert([
                'from' => Auth::user()->id,
                'to' => $user->id,
                'rep' => $reputation
            ]);

            $user->reputation += $reputation;
            $user->save();

            return \Response::json([
                'success' => true,
                'message' => 'Вы ' . ($reputation > 0 ? "повысили" : "понизили") . " репупацию этого пользователя!"
            ]);
        }
    }

    public function ban(Request $request)
    {
        if (Gate::allows('ext', ['forum_manager', 'ban.access'])) {
            $user = User::findOrFail($request->get('user'));
            $reason = $request->get('reason');
            $days = intval($request->get('days'));
            $ingame = $request->get('ingame');

            $user->ban([
                'comment' => $reason,
                'expired_at' => Carbon::now()->addDays($days)
            ]);

            if ($ingame == 1){
                \DB::table('ban_list')->insert([
                    'UUID' => $user->uuid,
                    'Reason' => $reason,
                    'Time' => time(),
                    'Temptime' => time() + ($days * 24 * 60 * 60),
                    'Type' => 1,
                    'UUIDadmin' => Auth::user()->uuid,
                ]);
            }

            Activity::user_action($user, 'banned_forum', [
                'admin' => Auth::user()->id,
                'reason' => $reason,
                'days' => $days
            ]);

            return \Response::json([
                'success' => true,
                'message' => 'Игрок забанен на ' . $days . ' дней!'
            ]);
        }else{
            return \Response::json([
                'success' => true,
                'message' => 'У вас нет прав!'
            ]);
        }
    }

    public static function badText($body){
        $bb = new BBCode();

        $clear_text = strip_tags($bb->stripBBCodeTags(strip_tags($body)));

        $numbers = preg_replace('/[^0-9]/', '', $clear_text);

        if (Str::contains($numbers, [
            '9788113279',
            '8113279'
        ])) return true;

        return false;
    }
}
