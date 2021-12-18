<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;

class Post extends \Eloquent implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use Searchable;

    protected $table = 'chatter_post';
    public $timestamps = true;
    protected $fillable = ['chatter_discussion_id', 'user_id', 'body', 'markdown'];

    protected $auditInclude = [
        'body',
        'chatter_discussion_id'
    ];

    public function discussion()
    {
        return $this->belongsTo(Models::className(Discussion::class), 'chatter_discussion_id');
    }

    public function user()
    {
        return $this->belongsTo(config('chatter.user.namespace'));
    }
}
