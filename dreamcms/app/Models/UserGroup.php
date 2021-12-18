<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserGroup extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'usergroups';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'server_id', 'group_id', 'time', 'expire'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\DonateGroup', 'group_id', 'id');
    }

    public function server()
    {
        return $this->belongsTo('App\Models\Server', 'server_id', 'id');
    }

    public function getDonateGroup(){
        return DonateGroup::find($this->group_id);
    }

    /* @return Server */
    public function getServer(){
        return Server::find($this->server_id);
    }

    public function getUser(){
        return User::find($this->user_id);
    }

    public function isActive(){
        return $this->time <= time() && $this->expire >= time();
    }

    public function isExpire(){
        return $this->expire < time();
    }

    public function isFutured(){
        return $this->time > time();
    }
}
