<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use CrudTrait;

    protected $table = 'cart_items';
    public $timestamps = false;

    protected $fillable = [
        'uuid', 'shop', 'type', 'damage', 'count', 'enchants', 'nbt',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'uuid', 'uuid')->first();
    }

    public function shops(){
        return $this->belongsTo('App\Models\Shop', 'shop', 'id');
    }
}
