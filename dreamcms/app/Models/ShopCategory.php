<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ShopCategory extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    public $timestamps = false;

    protected $table = 'shop_categories';

    protected $fillable = [
        'name', 'icon', 'discount', 'discount_start', 'discount_end'
    ];

    public function shops()
    {
        return $this->belongsToMany('App\Models\ShopCategory', 'shop_category_shop', 'shop_category_id', 'shop_id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\ShopItem', 'category_id');
    }
}
