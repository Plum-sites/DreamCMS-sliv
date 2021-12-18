<?php

namespace App\Models\Forum;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Category extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'chatter_categories';
    public $timestamps = true;
    protected $fillable = ['order', 'icon', 'hidden', 'name', 'description', 'color', 'slug', 'discussions_count', 'posts_count', 'last_post', 'parent_id'];

    protected $auditExclude = [
        'discussions_count',
        'posts_count',
        'last_post'
    ];


    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'chatter_category_id');
    }

    public function parentrel()
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }

    public function childs(){
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function lastDiscussion(){
       return $this->hasMany(Discussion::class, 'chatter_category_id')->orderBy('updated_at', 'DESC');
    }

    public static function getPrivate()
    {
        return Category::where('hidden', 1)->get();
    }

    public function postsCount()
    {
        $info = \DB::select('SELECT COUNT(*) as `count` FROM chatter_post WHERE chatter_discussion_id IN (SELECT id FROM chatter_discussion WHERE chatter_category_id = ?)', [ $this->id ]);

        return $info[0]->count;
    }
}
