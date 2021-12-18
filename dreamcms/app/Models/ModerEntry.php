<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ModerEntry extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'moder_requests';

    public $timestamps = false;

    protected $fillable = [
        'fio', 'old', 'city', 'contacts', 'about', 'status', 'time', 'user_id', 'server', 'answer'
    ];

    protected $auditEvents = [
        'updated',
        'deleted',
        'restored',
    ];

    public static function getCurrent(User $user)
    {
        $current = ModerEntry::where([
            ['user_id', '=', $user->id],
            ['status', '=', 'DENY_FULL'],
        ])->get()->first();

        if (!$current){
            $current = ModerEntry::where([
                ['user_id', '=', $user->id],
                ['time', '>', time() - 7 * 24 * 60 * 60],
            ])->get()->first();
        }
        
        return $current;
    }

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
