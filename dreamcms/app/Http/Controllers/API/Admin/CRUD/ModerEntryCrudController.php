<?php
namespace App\Http\Controllers\API\Admin\CRUD;

use App\Models\ModerEntry;

class ModerEntryCrudController extends CrudController {

    public function setup() {
        $this->crud->setModel("App\Models\ModerEntry");
        $this->crud->setRoute("admin/moder");
        $this->crud->setEntityNameStrings('заявка', 'Заявки');
        $this->crud->permission = 'moder_entry';

        $this->crud->enableDetailsRow();
        $this->crud->allowAccess('details_row');
        $this->crud->enableExportButtons();

        $this->crud->setColumns([
            [
                'label' => "Игрок",
                'type' => "select",
                'name' => 'user_id',
                'entity' => 'user',
                'attribute' => "login",
                'model' => "App\Models\User"
            ],
            [
                'name' => 'fio',
                'label' => "ФИО"
            ],
            [
                'name' => 'old',
                'label' => "Возраст"
            ],
            [
                'name' => 'server',
                'label' => "Сервер"
            ],
            [
                'name' => 'city',
                'label' => "Город"
            ],
            [
                'name' => 'status',
                'label' => "Статус"
            ]
        ]);

        $this->crud->addField([
            'label' => "Игрок",
            'type' => "relation_text",
            'name' => 'user_id',
            'entity' => 'user',
            'attribute' => "login",
            'model' => "App\Models\User"
        ]);

        $this->crud->addField([
            'name' => 'fio',
            'label' => "ФИО",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'city',
            'label' => "Город",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'old',
            'label' => "Возраст",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'server',
            'label' => "Сервер",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'about',
            'label' => "Текст",
            'type' => 'textarea'
        ]);

        $this->crud->addField([
            'name' => 'contacts',
            'label' => "Контакты"
        ]);

        $this->crud->addField([
            'name' => 'status',
            'label' => "Статус",
            'type' => 'enum'
        ]);

        $this->crud->addField([
            'name' => 'answer',
            'label' => "Коммантарий",
            'type' => 'textarea'
        ]);

    }

    public function showDetailsRow($id){
        return ModerEntry::find($id)->about;
    }
}
