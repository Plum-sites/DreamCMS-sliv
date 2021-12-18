<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ShopItem extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'shop_items';

    protected $fillable = [
        'category_id', 'name', 'shop_id', 'icon', 'type', 'damage', 'count', 'price', 'enchantable', 'enchants', 'nbt', 'discount', 'discount_start', 'discount_end'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\ShopCategory', 'category_id', 'id');
    }

    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    public function getCartItem(){
        $item = new CartItem;

        $item->type = $this->type;
        $item->damage = $this->damage;
        $item->enchants = $this->enchants;
        $item->nbt = $this->nbt;

        return $item;
    }
}
