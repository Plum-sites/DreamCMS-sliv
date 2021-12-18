<?php

namespace App\Console\Commands;

use App\Models\Shop;
use Illuminate\Console\Command;

class ShopExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:export {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export shop with id as yml';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = intval($this->argument('id'));

        $shop = Shop::findOrFail($id);

        $i = 0;
        $end = PHP_EOL;

        foreach ($shop->categories()->get() as $category){
            foreach ($category->items()->get() as $item){
                $yml = '';

                $price = $item->price;

                if ($shop->discount > 0 && $this->in_datarange($shop->discount_start, $shop->discount_end)){
                    $price = $item->price * ((100 - $shop->discount)/100);
                }

                if ($item->category()->first()->discount > 0 && $this->in_datarange($item->category()->first()->discount_start, $item->category()->first()->discount_end)){
                    $price = $item->price * ((100 - $item->category()->first()->discount)/100);
                }

                if ($item->discount > 0 && $this->in_datarange($item->discount_start, $item->discount_end)){
                    $price = $item->price * ((100 - $item->discount)/100);
                }


                $yml .= 'i' . $i . ':' . $end;
                $yml .= ' real: true' . $end;
                $yml .= ' price: ' . number_format(round($price, 2), 2, '.', '') . $end;
                $yml .= ' personal: false' . $end;
                $yml .= ' purchases: 0' . $end;

                if ($item->damage)
                    $yml .= ' item: \'{type:"' . $item->type .'", amount:'.$item->count.', damage:' . $item->damage . 's}\'' . $end;
                else
                    $yml .= ' item: \'{type:"' . $item->type .'", amount:'.$item->count.'}\'' . $end;

                echo $yml;

                $i++;
            }
        }

        return true;
    }

    function in_datarange($start, $end, $time = false){
        if ($time == false) $time = time();

        if(strtotime($start) < $time && strtotime($end) > $time) return true;
        return false;
    }
}
