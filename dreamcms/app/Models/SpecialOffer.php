<?php

namespace App\Models;

use App\Achievements\Achievement;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use OwenIt\Auditing\Contracts\Auditable;

class SpecialOffer extends \Eloquent implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    public $timestamps = false;

    protected $table = 'special_offer';

    protected $fillable = [
        'name', 'discount', 'subject', 'conditions', 'expire', 'params', 'active', 'extra', 'description'
    ];

    protected $casts = [
        'params' => 'array',
        'extra' => 'array'
    ];

    public static function checkConditions(User $user, $script){
        $context = self::calculateContext($user);

        $context->each(function ($value, $key) use ($context, &$script){
            $script = str_replace('{' . $key . '}', $value, $script);
        });

        return Achievement::expression($script);
    }

    /* @return Collection */
    public static function calculateContext(User $user){
        $context = collect();

        $group_buys = \DB::selectOne("SELECT COUNT(*) as `count`, MAX(time) as last_time, MIN(time) AS first_time FROM `activity` WHERE actor_id = ? AND action = 'buygroup'", [ $user->id ]);
        $shop_buys = \DB::selectOne("SELECT COUNT(*) as `count`, MAX(time) as last_time, MIN(time) AS first_time FROM `activity` WHERE actor_id = ? AND action = 'buyitem'", [ $user->id ]);
        $payments = \DB::selectOne("SELECT COUNT(*) as `count`, MAX(time) as last_time, MIN(time) AS first_time, (IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.sum'))), 0) + IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.amount'))), 0)) as total_sum FROM `activity` WHERE actor_id = ? AND action = 'unitpay_add' OR action = 'enot_add'", [ $user->id ]);

        $context->put('group.buy.count', $group_buys->count);
        $context->put('group.buy.last_time', $group_buys->last_time);
        $context->put('group.buy.first_time', $group_buys->first_time);

        $context->put('shop.buy.count', $shop_buys->count);
        $context->put('shop.buy.last_time', $shop_buys->last_time);
        $context->put('shop.buy.first_time', $shop_buys->first_time);

        $context->put('last_play', $user->last_play);

        $context->put('payments.count', $payments->count);
        $context->put('payments.last_time', $payments->last_time);
        $context->put('payments.first_time', $payments->first_time);
        $context->put('payments.total_sum', $payments->total_sum);

        return $context;
    }

    public static function processPlayer(User $user)
    {
        $user_offers = \DB::table('user_offer')->where([
            ['user_id', '=', $user->id]
        ])->get();

        $ids = $user_offers->only('id')->flatten();

        $maybe_offers = SpecialOffer::where('active', '=', 1)->whereNotIn('id', $ids)->get();

        /* @var $offer SpecialOffer */
        foreach ($maybe_offers as $offer){
            if (self::checkConditions($user, $offer->conditions)){
                self::giveOffer($user, $offer);
            }
        }
    }

    public static function giveOffer(User $user, SpecialOffer $offer){
        if (\DB::table('user_offer')->where([
            ['user_id', '=', $user->id],
            ['offer_id', '=', $offer->id],
        ])->count() <= 0){
            \DB::table('user_offer')->insert([
                'user_id' => $user->id,
                'offer_id' => $offer->id,
                'expire' => time() + $offer->expire,
                'used' => 0
            ]);
        }
    }

    public static function getOffer(User $user, $type, $extra = [])
    {
        $user_offers = \DB::table('user_offer')->where([
            ['user_id', '=', $user->id],
            ['used', '=', 0],
            ['expire', '>', time()],
        ])->get();

        $offers = collect();
        foreach ($user_offers as $user_offer) {
            $offer = SpecialOffer::find($user_offer->offer_id);
            if ($offer->subject != $type) continue;

            foreach ($offer->params as $pair) {
                if (count($pair) && $extra[$pair['offer_key']] != $pair['offer_value']) {
                    continue 2;
                }
            }
            $offers->push($offer);
        }
        $offers->sortBy('discount');

        return $offers->first();
    }

    public static function useOffer(User $user, SpecialOffer $offer)
    {
        \DB::table('user_offer')->where([
            ['user_id', $user->id],
            ['offer_id', $offer->id]
        ])->update(['used' => 1]);
    }

    public static function forUser(User $user)
    {
        return Cache::remember('specialoffers_' . $user->id, now()->addMinute(), function () use ($user) {
            self::processPlayer($user);
            $user_offers = \DB::table('user_offer')->where([
                ['user_id', '=', $user->id],
                ['used', '=', 0],
                ['expire', '>', time()],
            ])->get();

            $offers = collect();
            foreach ($user_offers as $user_offer) {
                $offer = SpecialOffer::find($user_offer->offer_id);
                if ($offer){
                    $offer->expire = $user_offer->expire;
                    $offer->discount_desc = $offer->genDescription();
                    $offers->push($offer);
                }
            }

            return $offers;
        });
    }

    public function genDescription(){
        $content = '';

        $params = collect($this->params)->mapWithKeys(function ($value, $key){
            return [ $value['offer_key'] => $value['offer_value'] ];
        });

        if ($this->subject == 'GROUP'){
            $content .= 'Скидка <b>'.$this->discount.'%</b> ';
            if (!$params->contains('group')){
                $content .= 'на ВСЕ донат группы!';
            }else{
                $dg = DonateGroup::find($params->get('group'));
                $content .= 'на группу ' . $dg->name . '!';
            }
        }

        if ($this->subject == 'SHOP'){
            $content .= 'Скидка <b>'.$this->discount.'%</b> ';
            if (!$params->contains('shop_category')){
                $content .= 'на ВЕСЬ магазин блоков!';
            }else{
                $sc = ShopCategory::find($params->get('shop_category'));
                $content .= 'на категорию ' . $sc->name . '!';
            }
        }

        return $content;
    }
}
