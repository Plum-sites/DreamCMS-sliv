<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Comment
 *
 * @property int $id
 * @property int $news_id
 * @property string $user_id
 * @property string $text
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereNewsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereText($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereUserId($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
    protected $fillable = [
        'article_id', 'user_id', 'text'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User')->first();
    }

    public function article()
    {
        return $this->belongsTo('App\Models\Article')->first();
    }
}
