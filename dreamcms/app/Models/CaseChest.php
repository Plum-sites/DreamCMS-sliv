<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CaseChest extends Model
{
    use CrudTrait;

    protected $table = 'cases';
    public $timestamps = false;

    protected $fillable = [
        'id', 'name', 'price', 'image', 'items', 'description', 'css'
    ];

    protected $casts = [
        'images' => 'array',
        'items' => 'array'
    ];

    public function openCase(User $user, Server $server = null){
        $rewards = collect();

        foreach ($this->getItemsForServer($server) as $item) {
            $chance = intval($item['chance']);

            for ($i = 0; $i < $chance; $i++){
                $rewards->push($item);
            }
        }

        $reward = $rewards->shuffle(rand())->first();

        if ($reward){
            switch ($reward['type']){
                case 'item':
                    $cartItem = new CartItem();
                    $cartItem->uuid = $user->uuid;
                    $cartItem->shop = $server->shop_id;
                    $cartItem->count = intval($reward['count']);

                    if (Str::contains($reward['item'], ':') > 0){
                        $info = explode(':', $reward['item']);
                        $cartItem->type = $info[0];
                        $cartItem->damage = intval($info[1]);
                    }else{
                        $cartItem->type = $reward['item'];
                        $cartItem->damage = 0;
                    }

                    $cartItem->save();
                    break;
                case 'group':
                    $info = explode(':', $reward['item']);
                    /* @var $dg DonateGroup */
                    $dg = DonateGroup::where('pexname', $info[1])->first();
                    $dg->giveKits($user);
                    $dg->giveOrRenew($user, $server);

                    break;
            }
        }

        Activity::user_action($user, 'open_case', [
            'reward' => $reward,
            'server' => $server->id,
            'price' => $this->price
        ]);

        return $reward;
    }

    public function getItemsForServer(Server $server = null){
        $items = collect();
        collect($this->items)->each(function ($info) use (&$items, $server){
            if ($server){
                if ($info['server'] == $server->branch || $info['server'] == 'global'){
                    $items = collect($info['items']);
                    return;
                }
            }else{
                if ($info['server'] == 'global'){
                    $items = collect($info['items']);
                    return;
                }
            }
        });

        $items = $items->map(function ($item){
            $item['type'] = 'item';
            if (Str::startsWith(Str::lower($item['item']), 'group:')){
                $info = explode(':', Str::lower($item['item']));

                $item['type'] = 'group';
                $item['item'] = Str::upper($info[1]);
                $item['group'] = DonateGroup::where('pexname', $info[1])->first();
                $item['time'] = isset($info[2]) ? intval($info[2]) : (30 * 24 * 60 * 60);
            }
            return $item;
        });

        return $items;
    }
}
