<?php

namespace App\Http\Controllers\API\Admin\CRUD;

class KitsCrudController extends CrudController {

    public function setup() {
        $this->crud->setModel("App\Models\Kit");
        $this->crud->setRoute("admin/kits");
        $this->crud->setEntityNameStrings('кит', 'киты');
        $this->crud->permission = 'kits';

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => "Название"
            ],
            [
                'name' => 'server_name',
                'label' => "ID на сервере"
            ],
            [
                'name' => 'price',
                'label' => "Цена"
            ]
        ]);


        $this->crud->addField([
            'name' => 'name',
            'label' => "Название",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'description',
            'label' => "Описание",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'image',
            'label' => "Картинка",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'server_name',
            'label' => "ID на сервере",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'price',
            'label' => "Цена",
            'type' => 'number'
        ]);

        $this->crud->addField([
            'name' => 'css',
            'label' => "CSS класс",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'images',
            'label' => "Картинки",
            'type' => 'table',
            'columns' => [
                'server'  => 'Ветка',
                'url'  => 'Картинка'
            ]
        ]);

        $this->crud->addField([
            'name' => 'items',
            'label' => "Предметы",
            'type' => 'inherit_table',
            'columns' => [
                [
                    'name' => 'server',
                    'label' => "Ветка",
                    'type' => 'text'
                ],
                [
                    'name' => "items",
                    'label' => "Предметы",
                    'type' => 'inherit_table',
                    'columns' => [
                        [
                            'name' => 'item',
                            'label' => "Предмет",
                            'type' => 'text'
                        ],
                        [
                            'name' => 'count',
                            'label' => "Кол-во",
                            'type' => 'number'
                        ]
                    ]
                ]
            ]
        ]);
    }
}
