<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Shop extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'shops';

    public $timestamps = false;

    protected $fillable = [
        'name', 'icon', 'discount', 'discount_start', 'discount_end', 'active'
    ];

    public function categories()
    {
        return $this->belongsToMany('App\Models\ShopCategory', 'shop_category_shop', 'shop_id', 'shop_category_id');
    }

    /* @var $need_category ShopCategory */
    public function findItems($need_category = false, $search = false, $sort = 1)
    {
        if ($need_category){
            $builder = $need_category->items();
            if ($search){
                $builder = $builder->where('name', 'LIKE', '%' . $search. '%');
            }

            switch ($sort){
                case 1: $builder = $builder->orderBy('id'); break;
                case 2: $builder = $builder->orderByDesc('price'); break;
                case 3: $builder = $builder->orderBy('price'); break;
            }

            return $builder->with('category')->get();
        }else{
            $items = collect();

            foreach ($this->categories as $category){
                if ($search){
                    $items = $items->merge($category->items()->with('category')->where('name', 'LIKE', '%' . $search. '%')->get());
                }else{
                    $items = $items->merge($category->items()->with('category')->get());
                }
            }

            switch ($sort){
                case 1: $items = $items->sortBy('id')->values(); break;
                case 2: $items = $items->sortByDesc(function ($item) { return $item->price; })->values(); break;
                case 3: $items = $items->sortBy(function ($item) { return $item->price; })->values(); break;
            }

            return $items;
        }
    }

    public static function getActive(){
        return Shop::where('active', 1)->with(['categories' => function($query){
            $query->withCount('items');
        }])->get();
    }

    public function exportYAML($crud = false)
    {
        return '<a class="btn btn-xs btn-default" target="_blank" href="/admin/shop/export/'.$this->id.'" data-toggle="tooltip"><i class="fa fa-arrow-circle-down"></i>Экспорт в кабинет</a>';
    }
}
