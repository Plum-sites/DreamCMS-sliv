<?php

namespace App\Achievements;

use App\Models\Forum\Post;
use App\Models\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;

class Achievement extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'achievements';

    public $timestamps = false;

    protected $fillable = ['name', 'description', 'min_progress', 'max_progress', 'secret', 'conditions', 'progress_formula', 'image', 'events'];

    protected $casts = [
        'conditions' => 'array',
        'events' => 'array'
    ];

    public function getProgressFor(User $user){
        return self::calculateProgress($user, $this->progress_formula);
    }

    public static function calculateProgress(User $user, $script){
        $context = self::calculateContext($user);

        $context->each(function ($value, $key) use ($context, &$script){
            $script = str_replace('{' . $key . '}', $value, $script);
        });

        return self::expression($script);
    }

    /* @return Collection */
    public static function calculateContext(User $user){
        $context = collect();

        $group_buys = \DB::select("SELECT COUNT(*) as `count`, JSON_UNQUOTE(JSON_EXTRACT(params, '$.group')) as `group` FROM `activity` WHERE actor_id = ? AND action = 'buygroup' GROUP BY `group`", [ $user->id ]);

        foreach ($group_buys as $group_buy){
            $context->put('group.' . strtolower($group_buy->group) . '.buys', $group_buy->count);
        }

        $context->put('forum.post.count', \DB::select('SELECT COUNT(id) as count FROM chatter_post WHERE chatter_post.user_id = ? AND chatter_post.chatter_discussion_id NOT IN (SELECT chatter_discussion.id FROM chatter_discussion WHERE chatter_discussion.chatter_category_id IN (SELECT chatter_categories.id FROM chatter_categories WHERE chatter_categories.not_count = 1))', [ $user->id ])[0]->count);
        $context->put('forum.discussions', \DB::select('SELECT COUNT(id) as count FROM chatter_discussion WHERE chatter_discussion.user_id = ? AND chatter_discussion.chatter_category_id NOT IN (SELECT chatter_categories.id FROM chatter_categories WHERE chatter_categories.not_count = 1)', [ $user->id ])[0]->count);

        $post = Post::where('user_id','=', $user->id)->orderByDesc('id')->first();
        if ($post){
            $context->put('forum.last_post.days', $post->created_at->diffInDays());
        }

        $context->put('user.friends.count', $user->friends()->count());
        $context->put('user.last_play', $user->last_play);

        if ($user->last_play){
            $context->put('user.last_play.days', Carbon::createFromTimestamp($user->last_play)->diffInDays());
        }

        $context->put('user.reg.days', Carbon::createFromTimestamp($user->reg_time)->diffInDays());
        $context->put('user.reputation', $user->reputation);
        $context->put('user.realmoney', $user->realmoney);

        return $context;
    }

    public static function expression($expression) {
        static $function_map = array(
            'floor'     => 'floor',
            'ceil'      => 'ceil',
            'round'     => 'round',
            'sin'       => 'sin',
            'cos'       => 'cos',
            'tan'       => 'tan',
            'asin'      => 'asin',
            'acos'      => 'acos',
            'atan'      => 'atan',
            'abs'       => 'abs',
            'log'       => 'log',
            'pi'        => 'pi',
            'exp'       => 'exp',
            'min'       => 'min',
            'max'       => 'max',
            'rand'      => 'rand',
            'fmod'      => 'fmod',
            'sqrt'      => 'sqrt',
            'deg2rad'   => 'deg2rad',
            'rad2deg'   => 'rad2deg',
            'time'      => 'time',
        );

        // Remove any whitespace
        $expression = strtolower(preg_replace('~\s+~', '', $expression));

        // Empty expression
        if ($expression === '') {
            trigger_error('Empty expression', E_USER_ERROR);
            return null;
        }

        // Illegal function
        $expression = preg_replace_callback('~\b[a-z]\w*\b~', function($match) use($function_map) {
            $function = $match[0];
            if (!isset($function_map[$function])) {
                trigger_error("Illegal function '{$match[0]}'", E_USER_ERROR);
                return null;
            }
            return $function_map[$function];
        }, $expression);

        // Invalid function calls
        if (preg_match('~[a-z]\w*(?![\(\w])~', $expression, $match) > 0) {
            trigger_error("Invalid function call '{$match[0]}'", E_USER_ERROR);
            return null;
        }

        // Legal characters
        if (preg_match('~[^-+/%*&|<>!=.()0-9a-z,]~', $expression, $match) > 0) {
            trigger_error("Illegal character '{$match[0]}'", E_USER_ERROR);
            return null;
        }

        return eval("return({$expression});");
    }
}
