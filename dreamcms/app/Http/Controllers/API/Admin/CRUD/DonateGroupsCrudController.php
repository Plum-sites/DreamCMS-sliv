<?php

namespace App\Http\Controllers\API\Admin\CRUD;

class DonateGroupsCrudController extends CrudController {

    public function setup() {
        $this->crud->setModel("App\Models\DonateGroup");
        $this->crud->setRoute("admin/groups");
        $this->crud->setEntityNameStrings('группа', 'Группы');
        $this->crud->permission = 'donategroups';

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => "Название"
            ],
            [
                'name' => 'pexname',
                'label' => "PEX группа"
            ],
            [
                'name' => 'price',
                'label' => "Цена"
            ],
            [
                'name' => 'active',
                'label' => "Активна"
            ]
        ]);

        $this->crud->addField([
            'name' => 'name',
            'label' => "Имя"
        ]);

        $this->crud->addField([
            'name' => 'pexname',
            'label' => "PEX группа"
        ]);

        $this->crud->addField([
            'name' => 'price',
            'label' => "Цена",
            'type' => 'number',
            'attributes' => [
                "step" => "1",
                "min" => "0",
                "max" => "10000"
            ]
        ]);

        $this->crud->addField([
            'name' => 'active',
            'label' => "Активна",
            'type' => 'checkbox'
        ]);

        $this->crud->addField([
            'name' => 'css',
            'label' => "CSS-класс"
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
            'name' => 'benefits',
            'label' => "Привилегии для ЛК",
            'type' => 'table',
            'columns' => [
                'desc'  => 'Описание',
                'value'  => 'Значение'
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
            'name' => 'time',
            'label' => "Длительность в секундах",
            'type' => 'number',
            'attributes' => [
                "step" => "1",
                "min" => "3600"
            ]
        ]);
    }
}
