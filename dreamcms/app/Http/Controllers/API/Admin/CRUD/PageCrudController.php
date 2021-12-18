<?php

namespace App\Http\Controllers\API\Admin\CRUD;

use App\PageTemplates;

class PageCrudController extends CrudController
{
    use PageTemplates;

    public function setup($template_name = false)
    {
        $modelClass = config('backpack.pagemanager.page_model_class', 'Backpack\PageManager\app\Models\Page');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel($modelClass);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/page');
        $this->crud->setEntityNameStrings(trans('backpack::pagemanager.page'), trans('backpack::pagemanager.pages'));

        $this->crud->addField([
            'name' => 'template',
            'label' => trans('backpack::pagemanager.template'),
            'type' => 'select',
            'options' => ['services'],
            'all_values' => ['services'],
            'value' => 'services',
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'metas_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>'.trans('backpack::pagemanager.metas').'</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'meta_title',
            'label' => trans('backpack::pagemanager.meta_title'),
            'fake' => true,
            'store_in' => 'extras',
        ]);
        $this->crud->addField([
            'name' => 'meta_description',
            'label' => trans('backpack::pagemanager.meta_description'),
            'fake' => true,
            'store_in' => 'extras',
        ]);
        $this->crud->addField([
            'name' => 'meta_keywords',
            'type' => 'textarea',
            'label' => trans('backpack::pagemanager.meta_keywords'),
            'fake' => true,
            'store_in' => 'extras',
        ]);
        $this->crud->addField([   // CustomHTML
            'name' => 'content_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>'.trans('backpack::pagemanager.content').'</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'content',
            'label' => trans('backpack::pagemanager.content'),
            'type' => 'textarea',
            'placeholder' => trans('backpack::pagemanager.content_placeholder'),
        ]);

        $this->crud->addField([
            'name' => 'name',
            'label' => trans('backpack::pagemanager.page_name'),
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
            // 'disabled' => 'disabled'
        ]);
        $this->crud->addField([
            'name' => 'title',
            'label' => trans('backpack::pagemanager.page_title'),
            'type' => 'text',
            // 'disabled' => 'disabled'
        ]);
        $this->crud->addField([
            'name' => 'slug',
            'label' => trans('backpack::pagemanager.page_slug'),
            'type' => 'text',
            'hint' => trans('backpack::pagemanager.page_slug_hint'),
            // 'disabled' => 'disabled'
        ]);

        /*
        |--------------------------------------------------------------------------
        | COLUMNS
        |--------------------------------------------------------------------------
        */

        $this->crud->addColumn([
                                'name' => 'name',
                                'label' => trans('backpack::pagemanager.name'),
                                ]);
        $this->crud->addColumn([
                                'name' => 'template',
                                'label' => trans('backpack::pagemanager.template'),
                                'type' => 'model_function',
                                'function_name' => 'getTemplateName',
                                ]);
        $this->crud->addColumn([
                                'name' => 'slug',
                                'label' => trans('backpack::pagemanager.slug'),
                                ]);

        /*
        |--------------------------------------------------------------------------
        | FIELDS
        |--------------------------------------------------------------------------
        */

        // In PageManager,
        // - default fields, that all templates are using, are set using $this->addDefaultPageFields();
        // - template-specific fields are set per-template, in the PageTemplates trait;

        /*
        |--------------------------------------------------------------------------
        | BUTTONS
        |--------------------------------------------------------------------------
        */
        $this->crud->addButtonFromModelFunction('line', 'open', 'getOpenButton', 'beginning');
    }

    // Overwrites the CrudController store() method to add template usage.
    public function store()
    {
        $this->initCrud();
        $request = $this->crud->validateRequest();

        return parent::store();
    }

    // Overwrites the CrudController edit() method to add template usage.
    public function edit($id, $template = false)
    {
        $this->initCrud();
        $template = request('template');

        // if the template in the GET parameter is missing, figure it out from the db
        if ($template == false) {
            $model = $this->crud->model;
            $this->data['entry'] = $model::findOrFail($id);
            $template = $this->data['entry']->template;
        }

        return parent::edit($id);
    }

    // Overwrites the CrudController update() method to add template usage.
    public function update()
    {
        $this->initCrud();
        $request = $this->crud->validateRequest();

        \Cache::delete('page_' . $request->input('slug'));

        return parent::update();
    }
}
