<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Enchant;
use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\ShopItem;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ShopController extends Controller
{
    const ENCHANT_PRICE = [
        0 => 20,
        1 => 10,
        2 => 10,
        3 => 10,
        4 => 10,
        5 => 10,
        6 => 10,
        7 => 15,
        16 => 20,
        17 => 10,
        18 => 10,
        19 => 10,
        20 => 10,
        21 => 15,
        32 => 25,
        33 => 20,
        34 => 30,
        35 => 20,
        48 => 10,
        49 => 10,
        50 => 10,
        51 => 15
    ];

    public function index(){
        $shops = Shop::getActive();

        $shops->transform(function ($shop){
            $shop->items_count = $shop->categories->sum('items_count');

            if ($shop->discount <= 0 || !in_datarange($shop->discount_start, $shop->discount_end)){
                $shop->discount = 0;
            }

            return $shop;
        });

        return [
            'success' => true,
            'shops' => $shops
        ];
    }

    public function load(Request $request){
        /* @var $shop Shop */
        if ($request->has('shop_id')){
            $shop = Shop::find($request->get('shop_id'));
        }else{
            $shop = Shop::where('name', '=', $request->get('shop'))->with('categories')->first();
        }

        if (!$shop){
            return [
                'success' => false,
                'message' => 'Такого магазина не существует!'
            ];
        }

        $category = ShopCategory::find($request->get('category'));
        $search = $request->get('search');
        $sort = intval($request->get('sort', 1));

        $shop->categories->transform(function ($cat, $key) use ($shop){
            if ($cat->discount <= 0 || !in_datarange($cat->discount_start, $cat->discount_end)){
                $cat->discount = 0;
            }

            return $cat;
        });

        /* @var $items Collection */
        $items = $shop->findItems($category, $search, $sort);
        $items->transform(function ($item, $key) use ($shop){
            if ($shop->discount > 0 && in_datarange($shop->discount_start, $shop->discount_end)){
                $item->discount = $shop->discount;
            }

            $category = $item->category;

            if ($category && $category->discount > 0 && in_datarange($category->discount_start, $category->discount_end)){
                $item->discount = $category->discount;
            }

            if ($item->discount > 0 && in_datarange($item->discount_start, $item->discount_end)){
                $item->orig_price = $item->price;
                $item->price = $item->price * ((100 - $item->discount)/100);
            }

            $item->icon = '/items/' . $item->type . ($item->damage ? '@' . $item->damage : '') . '.png';

            return $item;
        });

        if ($sort === 4){
            $items = $items->values()->sortByDesc(function ($item){
                return $item->discount ? $item->discount : 0;
            })->values();
        }

        $page = $request->get('page', 1);
        $per_page = 30;

        $items = new LengthAwarePaginator($items->forPage($page, $per_page)->values(), $items->count(), $per_page, $page);

        return [
            'success' => true,
            'shop' => $shop,
            'items' => $items,
            'enchants' => Enchant::all()
        ];
    }

    public function buy(Request $request){
        $wallet = $request->get('wallet', 0);

        //Shop and item
        $shop = Shop::findOrFail($request->get('shop_id'));
        $item = ShopItem::findOrFail($request->get('item_id'));

        //Fill CartItem info
        $citem = $item->getCartItem();
        $citem->count = intval($request->get('count'));
        $citem->shop = $shop->id;
        $citem->uuid = Auth::user()->uuid;

        //Enchants
        $ench_price = 0;
        if ($item->enchantable){
            $enchants = $request->get('enchants');

            $cart_enchants = array();

            if ($enchants){
                foreach ($enchants as $id => $level){
                    if ($level > 0){
                        $enchant = Enchant::find($id);

                        if ($level <= $enchant->max_level){
                            $ench_price += $citem->count * ($enchant->price * $level);

                            $game_id = ((string) $enchant->game_id) ? ((string) $enchant->game_id) : "zero";
                            $cart_enchants[] = [
                                $game_id => intval($level)
                            ];
                        }else{
                            return Response::json(array(
                                'success' => false,
                                'message' => 'Максимальный уровень зачарования "'. $enchant->name . '" это ' . $enchant->max_level . '!'
                            ));
                        }
                    }
                }
            }

            $citem->enchants = str_replace('zero', '0', json_encode($cart_enchants));
        }

        //Price and count check
        $priceonce = $item->price;

        //Count discount
        if ($shop->discount > 0 && in_datarange($shop->discount_start, $shop->discount_end)){
            $priceonce = $item->price * ((100 - $shop->discount) / 100);
        }
        if ($item->category()->first()->discount > 0 && in_datarange($item->category()->first()->discount_start, $item->category()->first()->discount_end)){
            $priceonce = $item->price * ((100 - $item->category()->first()->discount) / 100);
        }
        if ($item->discount > 0 && in_datarange($item->discount_start, $item->discount_end)){
            $priceonce = $item->price * ((100 - $item->discount) / 100);
        }

        $price = round((($ench_price + ($priceonce / $item->count)) * $citem->count), 2);
        if($citem->count > 64 || $citem->count < 1){
            return Response::json(array(
                'success' => false,
                'message' => 'Количество должно быть от 1 до 64!'
            ));
        }

        if ($wallet != 1 && $wallet != 0){
            return Response::json(array(
                'success' => false,
                'message' => 'Выберите кошелек с которого платить!'
            ));
        }

        if ($price > 0){
            if (($wallet == 0 && Auth::user()->withdrawRealmoney($price)) || ($wallet == 1 && Auth::user()->withdrawMoney($price * config('settings.exchange_course')))){
                $citem->save();

                Activity::user_action(Auth::user(), 'buyitem', [
                    'shop' => $shop->id,
                    'item' => $item->id,
                    'count' => $citem->count,
                    'enchants' => $citem->enchants,
                    'wallet' => $wallet,
                    'price' => $price
                ]);

                return Response::json(array(
                    'success' => true,
                    'message' => 'Ваша покупка в корзине! (команда /cart)'
                ));
            }
        }

        return Response::json(array(
            'success' => false,
            'message' => 'Недостаточно средств!'
        ));
    }
}
