<?php

namespace App\Http\Controllers\API\Admin\CRUD;

use App\Models\Article;

class ArticleCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel(Article::class);
        $this->crud->setRoute(config('backpack.base.route_prefix', 'admin').'/article');
        $this->crud->setEntityNameStrings('статья', 'Статьи');
        $this->crud->permission = 'article';

        /*
        |--------------------------------------------------------------------------
        | COLUMNS AND FIELDS
        |--------------------------------------------------------------------------
        */

        // ------ CRUD COLUMNS
        $this->crud->addColumn([
                                'name' => 'date',
                                'label' => 'Дата',
                                'type' => 'date',
                            ]);
        $this->crud->addColumn([
                                'name' => 'status',
                                'label' => 'Статус',
                            ]);
        $this->crud->addColumn([
                                'name' => 'title',
                                'label' => 'Заголовок',
                            ]);
        $this->crud->addColumn([
                                'name' => 'featured',
                                'label' => 'Запланирована',
                                'type' => 'check',
                            ]);
        $this->crud->addColumn([
                                'label' => 'Категория',
                                'type' => 'select',
                                'name' => 'category_id',
                                'entity' => 'category',
                                'attribute' => 'name',
                                'model' => "Backpack\NewsCRUD\app\Models\Category",
                            ]);

        // ------ CRUD FIELDS
        $this->crud->addField([    // TEXT
                                'name' => 'title',
                                'label' => 'Заголовок',
                                'type' => 'text',
                                'placeholder' => 'Your title here',
                            ]);
        $this->crud->addField([
                                'name' => 'slug',
                                'label' => 'URL',
                                'type' => 'text',
                                'hint' => 'Будет сгенерирована автоматически если оставить пустой',
                                // 'disabled' => 'disabled'
                            ]);

        $this->crud->addField([    // TEXT
                                'name' => 'date',
                                'label' => 'Дата',
                                'type' => 'date',
                                'value' => date('Y-m-d'),
                            ], 'create');
        $this->crud->addField([    // TEXT
                                'name' => 'date',
                                'label' => 'Дата',
                                'type' => 'date',
                            ], 'update');

        $this->crud->addField([    // WYSIWYG
                                'name' => 'short_content',
                                'label' => 'Краткое содержание',
                                'type' => 'ckeditor',
                                'placeholder' => 'Your textarea text here',
                            ]);
        $this->crud->addField([    // WYSIWYG
                                'name' => 'full_content',
                                'label' => 'Полное содержание',
                                'type' => 'ckeditor',
                                'placeholder' => 'Your textarea text here',
                            ]);

        $this->crud->addField([    // Image
                                'name' => 'image',
                                'label' => 'Картинка',
                                'type' => 'text',
                            ]);
        $this->crud->addField([    // SELECT
                                'label' => 'Категория',
                                'type' => 'select2',
                                'name' => 'category_id',
                                'entity' => 'category',
                                'attribute' => 'name',
                                'model' => "Backpack\NewsCRUD\app\Models\Category",
                            ]);
        $this->crud->addField([       // Select2Multiple = n-n relationship (with pivot table)
                                'label' => 'Теги',
                                'type' => 'select2_multiple',
                                'name' => 'tags', // the method that defines the relationship in your Model
                                'entity' => 'tags', // the method that defines the relationship in your Model
                                'attribute' => 'name', // foreign key attribute that is shown to user
                                'model' => "Backpack\NewsCRUD\app\Models\Tag", // foreign key model
                                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                            ]);
        $this->crud->addField([    // ENUM
                                'name' => 'status',
                                'label' => 'Статус',
                                'type' => 'enum',
                            ]);
        $this->crud->addField([    // CHECKBOX
                                'name' => 'featured',
                                'label' => 'Запланирована',
                                'type' => 'checkbox',
                            ]);
    }
}
