<?php
namespace App\Http\Controllers\API\Admin\CRUD;

class ShopCategoryCrudController extends CrudController {

    public function setup() {
        $this->crud->setModel("App\Models\ShopCategory");
        $this->crud->setRoute("admin/shop_categories");
        $this->crud->setEntityNameStrings('категория', 'Категории');
        $this->crud->permission = 'shop_category';

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
    }
}
