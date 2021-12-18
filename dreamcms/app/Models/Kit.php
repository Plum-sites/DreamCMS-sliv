<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Kit extends \Eloquent implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'kits';

    public $timestamps = false;

    protected $fillable = [
        'name', 'image', 'server_name', 'price', 'images', 'items', 'description', 'css'
    ];

    protected $casts = [
        'images' => 'array',
        'items' => 'array'
    ];

    public function getItemsForServer(Server $server = null){
        $items = collect();

        collect($this->items)->each(function ($info) use (&$items, $server){
            if ($server){
                if ($info['server'] == $server->branch){
                    $items = collect($info['items']);
                    return;
                }
            }
        });

        return $items;
    }
}
