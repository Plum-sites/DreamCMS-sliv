<?php

namespace App\Achievements;

use Illuminate\Database\Eloquent\Model;

class AchievementProgress extends Model
{
    protected $fillable = ['achievement_id', 'user_id', 'progress'];

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function achievement(){
        return $this->hasOne(Achievement::class, 'id', 'achievement_id');
    }
}
