<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;

class Discussion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use Searchable;

    protected $table = 'chatter_discussion';
    public $timestamps = true;
    protected $fillable = ['title', 'chatter_category_id', 'only_admins', 'pinned', 'no_reply', 'user_id', 'slug', 'color'];

    protected $auditInclude = [
        'title',
        'user_id',
        'chatter_category_id',
        'only_admins',
        'pinned',
        'no_reply'
    ];

    public function user()
    {
        return $this->belongsTo(config('chatter.user.namespace'));
    }

    public function category()
    {
        return $this->belongsTo(Models::className(Category::class), 'chatter_category_id');
    }

    public function posts()
    {
        return $this->hasMany(Models::className(Post::class), 'chatter_discussion_id');
    }

    public function post()
    {
        return $this->hasMany(Models::className(Post::class), 'chatter_discussion_id')->orderBy('created_at', 'ASC');
    }

    public function postsCount()
    {
        return $this->posts()
        ->selectRaw('chatter_discussion_id, count(*)-1 as total')
        ->groupBy('chatter_discussion_id');
    }

    public function users()
    {
        return $this->posts()->with('user:id,login,uuid');
    }
}
