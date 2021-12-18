<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Enchant extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'enchants';

    public $timestamps = false;

    protected $fillable = [
        'id', 'game_id', 'name', 'price', 'max_level'
    ];
}
