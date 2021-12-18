<?php

namespace App\Http\Controllers\API\Admin\CRUD;

class TagCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel("Backpack\NewsCRUD\app\Models\Tag");
        $this->crud->setRoute(config('backpack.base.route_prefix', 'admin').'/tag');
        $this->crud->setEntityNameStrings('тэг', 'Тэги');
        $this->crud->permission = 'tags';
        /*
        |--------------------------------------------------------------------------
        | COLUMNS AND FIELDS
        |--------------------------------------------------------------------------
        */

        // ------ CRUD COLUMNS
        $this->crud->addColumn([
                                'name' => 'name',
                                'label' => 'Имя',
                            ]);
        $this->crud->addColumn([
                                'name' => 'slug',
                                'label' => 'URL',
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
    }
}
