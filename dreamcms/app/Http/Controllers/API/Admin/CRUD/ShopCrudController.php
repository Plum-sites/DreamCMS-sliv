<?php
namespace App\Http\Controllers\API\Admin\CRUD;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopCrudController extends CrudController {

    public function setup() {
        $this->crud->setModel("App\Models\Shop");
        $this->crud->setRoute("admin/shops");
        $this->crud->setEntityNameStrings('магазин', 'Магазины');
        $this->crud->permission = 'shop';

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => "Название"
            ],
            [
                'name' => 'icon',
                'label' => "Иконка"
            ],
            [
                'name' => 'discount',
                'label' => "Скидка"
            ],
            [
                'name' => 'active',
                'label' => "Активен"
            ]
        ]);

        $this->crud->addField([
            'name' => 'name',
            'label' => "Имя"
        ]);

        $this->crud->addField([
            'name' => 'icon',
            'label' => "Ссылка на иконку",
            'type' => 'browse'
        ]);

        $this->crud->addField([
            'name' => 'discount',
            'label' => "Скидка в процентах",
            'type' => 'number',
            'attributes' => [
                "step" => "1",
                "min" => "0",
                "max" => "100"
            ]
        ]);

        $this->crud->addField([
            'name' => 'discount_time',
            'label' => "Время действия скидок",
            'type' => 'date_range',
            'start_name' => 'discount_start',
            'end_name' => 'discount_end',
            'start_default' => date('Y-m-d H:i'),
            'end_default' => date('Y-m-d H:i'),
            'date_range_options' => [
                'timePicker' => true,
                'locale' => ['format' => 'DD/MM/YYYY HH:mm']
            ]
        ]);


        $this->crud->addField([
            'name' => 'active',
            'label' => "Активен",
            'type' => 'checkbox'
        ]);

        $this->crud->addField([
            'label'     => 'Категории',
            'type'      => 'checklist',
            'name'      => 'categories',
            'entity'    => 'categories',
            'attribute' => 'name',
            'model'     => "App\Models\ShopCategory",
            'pivot'     => true,
        ]);

        $this->crud->addButtonFromModelFunction('line', 'export_shop', 'exportYAML', 'beginning');
    }

    public function export(Request $request, $id){
        $shop = Shop::findOrFail($id);
        $items = $shop->items();

        $yml = '';
        $i = 0;
        $end = '</br>';
        foreach ($items as $item){

            if ($shop->discount > 0 && in_datarange($shop->discount_start, $shop->discount_end)){
                $priceonce = $item->price * ((100 - $shop->discount)/100);
            }

            if ($item->category()->first()->discount > 0 && in_datarange($item->category()->first()->discount_start, $item->category()->first()->discount_end)){
                $priceonce = $item->price * ((100 - $item->category()->first()->discount)/100);
            }

            if ($item->discount > 0 && in_datarange($item->discount_start, $item->discount_end)){
                $priceonce = $item->price * ((100 - $item->discount)/100);
            }

            $yml .= 'i' . $i . ':' . $end;
            $yml .= '&nbsp real: true' . $end;
            $yml .= '&nbsp price: ' . sprintf("%.1f", round($priceonce, 2)) . $end;
            $yml .= '&nbsp personal: false' . $end;
            $yml .= '&nbsp purchases: 0' . $end;

            if ($item->damage)
                $yml .= '&nbsp item: \'{type:"' . $item->type .'", amount:'.$item->count.', damage:' . $item->damage . 's}\'' . $end;
            else
                $yml .= '&nbsp item: \'{type:"' . $item->type .'", amount:'.$item->count.'}\'' . $end;

            $i++;
        }
        echo $yml;
    }
}
