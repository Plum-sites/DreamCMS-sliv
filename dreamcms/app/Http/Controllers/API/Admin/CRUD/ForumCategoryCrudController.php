<?php

namespace App\Http\Controllers\API\Admin\CRUD;

class ForumCategoryCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel("App\Models\Forum\Category");
        $this->crud->setRoute(config('backpack.base.route_prefix', 'admin').'/crud/forum_category');
        $this->crud->setEntityNameStrings('категория', 'Категории');

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => 'Название',
            ],
            [
                'label' => 'Родительская категория',
                'type' => 'select',
                'name' => 'parent_id',
                'entity' => 'parentrel',
                'attribute' => 'name',
                'model' => "App\Models\Forum\Category",
                'pivot' => false
            ],
            [
                'name' => 'order',
                'label' => 'Сортировка',
            ]
        ]);

        //CRUD FIELDS

        $this->crud->addField([
            'name' => 'name',
            'label' => 'Название',
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Ссылка (латиница)',
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'description',
            'label' => 'Описание',
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'icon',
            'label' => 'Иконка',
            'type' => 'text'
        ]);

        $this->crud->addField([
            'label' => 'Родительская категория',
            'type' => 'select',
            'name' => 'parent_id',
            'entity' => 'parentrel',
            'attribute' => 'name',
            'model' => "App\Models\Forum\Category",
            'pivot' => false
        ]);

        $this->crud->addField([
            'name' => 'order',
            'label' => 'Сортировка',
            'type' => 'number'
        ]);

        $this->crud->addField([
            'name' => 'not_count',
            'label' => 'Не учитывать сообщения',
            'type' => 'checkbox'
        ]);
    }
}
