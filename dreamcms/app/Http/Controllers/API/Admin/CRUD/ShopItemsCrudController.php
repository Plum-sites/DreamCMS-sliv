<?php

namespace App\Http\Controllers\API\Admin\CRUD;

class ShopItemsCrudController extends CrudController {

    public function setup() {
        $this->crud->setModel("App\\Models\\ShopItem");
        $this->crud->setRoute("admin/shop_items");
        $this->crud->setEntityNameStrings('предмет', 'Предметы');
        $this->crud->permission = 'shop_items';

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => "Название"
            ],
            [
                'name' => 'type',
                'label' => "ID"
            ],
            [
                'name' => 'damage',
                'label' => "SubID"
            ],
            [
                'label'     => 'Категория',
                'type'      => 'select',
                'name'      => 'category_id',
                'entity'    => 'category',
                'attribute' => 'name',
                'model'     => "App\\Models\\ShopCategory"
            ],
            [
                'name' => 'count',
                'label' => "Кол-во"
            ],
            [
                'name' => 'price',
                'label' => "Цена"
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
            'name' => 'type',
            'label' => "Type предмета (/modfix iteminfo)"
        ]);

        $this->crud->addField([
            'name' => 'damage',
            'label' => "SubID \ Damage",
            'type' => 'number',
            'attributes' => [
                "step" => "1",
                "min" => "0"
            ]
        ]);


        $this->crud->addField([
            'name' => 'count',
            'label' => "Кол-во (1\\2\\4\\8\\16\\32\\64)",
            'type' => 'number',
            'attributes' => [
                "step" => "1",
                "min" => "0",
                "max" => "64"
            ]
        ]);

        $this->crud->addField([
            'name' => 'price',
            'label' => "Цена за кол-во",
            'type' => 'number',
            'attributes' => [
                "step" => "1",
                "min" => "0"
            ]
        ]);

        $this->crud->addField([
            'name' => 'enchantable',
            'label' => "Можно зачаровать",
            'type' => 'checkbox'
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
            'name' => 'enchants',
            'label' => "Чары в JSON формате",
            'type' => 'textarea'
        ]);

        $this->crud->addField([
            'name' => 'nbt',
            'label' => "NBT в JSON формате",
            'type' => 'textarea'
        ]);

        $this->crud->addField([
            'label'     => 'Категория',
            'type'      => 'select',
            'name'      => 'category_id',
            'entity'    => 'category',
            'attribute' => 'name',
            'model'     => "App\\Models\\ShopCategory"
        ]);
    }
}
