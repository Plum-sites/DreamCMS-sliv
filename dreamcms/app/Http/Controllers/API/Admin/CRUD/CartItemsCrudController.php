<?php
namespace App\Http\Controllers\API\Admin\CRUD;

class CartItemsCrudController extends CrudController {

    public function setup() {
        $this->crud->setModel("App\\Models\\CartItem");
        $this->crud->setRoute("admin/cart_items");
        $this->crud->setEntityNameStrings('предмет', 'Предметы');
        $this->crud->permission = 'cart';

        $this->crud->setColumns([
            [
                'name' => 'uuid',
                'label' => "Владелец"
            ],
            [
                'name' => 'shop',
                'label' => "Магазин",
                'type' => 'select',
                'entity' => 'shops',
                'attribute' => 'name',
                'model' => "App\\Models\\Shop"
            ],
            [
                'name' => 'type',
                'label' => "TypeName"
            ],
            [
                'name' => 'damage',
                'label' => "Damage"
            ],
            [
                'name' => 'count',
                'label' => "Кол-во"
            ]
        ]);

        $this->crud->addField([
            'name' => 'uuid',
            'label'     => 'Владелец',
            'type'      => 'text'
        ]);

        $this->crud->addField([
            'name' => 'shop',
            'label'     => 'Магазин',
            'type'      => 'select2',
            'entity'    => 'shops',
            'attribute' => 'name',
            'model'     => "App\\Models\\Shop"
        ]);

        $this->crud->addField([
            'name' => 'type',
            'label' => "TypeName",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'damage',
            'label' => "Damage",
            'type' => 'number',
            'attributes' => [
                "step" => "1",
                "max" => "64",
                "min" => "0"
            ]
        ]);

        $this->crud->addField([
            'name' => 'count',
            'label' => "Кол-во",
            'type' => 'number',
            'attributes' => [
                "step" => "1",
                "max" => "64",
                "min" => "0"
            ]
        ]);

        $this->crud->addField([
            'name' => 'enchants',
            'label' => "Чары в JSON",
            'type' => 'textarea'
        ]);

        $this->crud->addField([
            'name' => 'nbt',
            'label' => "NBT в JSON",
            'type' => 'textarea'
        ]);
    }
}
