<?php

namespace App\Http\Controllers\API\Admin\CRUD;

class CategoryCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel("Backpack\NewsCRUD\app\Models\Category");
        $this->crud->setRoute(config('backpack.base.route_prefix', 'admin').'/category');
        $this->crud->setEntityNameStrings('категория', 'Категории');
        $this->crud->permission = 'categories';

        /*
        |--------------------------------------------------------------------------
        | COLUMNS AND FIELDS
        |--------------------------------------------------------------------------
        */

        $this->crud->allowAccess('reorder');
        $this->crud->enableReorder('name', 2);

        // ------ CRUD COLUMNS
        $this->crud->addColumn([
                                'name' => 'name',
                                'label' => 'Имя',
                            ]);
        $this->crud->addColumn([
                                'name' => 'slug',
                                'label' => 'URL',
                            ]);
        $this->crud->addColumn([
                                'label' => 'Родительская категория',
                                'type' => 'select',
                                'name' => 'parent_id',
                                'entity' => 'parent',
                                'attribute' => 'name',
                                'model' => "Backpack\NewsCRUD\app\Models\Category",
                            ]);

        // ------ CRUD FIELDS
        $this->crud->addField([
                                'name' => 'name',
                                'label' => 'Имя',
                            ]);
        $this->crud->addField([
                                'name' => 'slug',
                                'label' => 'URL',
                                'type' => 'text',
                                'hint' => 'Будет сгенерирована автоматически если оставить пустой',
                                // 'disabled' => 'disabled'
                            ]);
        $this->crud->addField([
                                'label' => 'Родитель',
                                'type' => 'select',
                                'name' => 'parent_id',
                                'entity' => 'parent',
                                'attribute' => 'name',
                                'model' => "Backpack\NewsCRUD\app\Models\Category",
                            ]);
    }
}
