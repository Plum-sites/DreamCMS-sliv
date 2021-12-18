<?php

namespace App\Http\Controllers\API\Admin\CRUD;

class SpecialOffersCrudController extends CrudController {

    public function setup() {
        $this->crud->setModel("App\\Models\\SpecialOffer");
        $this->crud->setRoute("admin/offers");
        $this->crud->setEntityNameStrings('предложение', 'Предложения');
        $this->crud->permission = 'sp_offer';

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => "Название"
            ],
            [
                'name' => 'subject',
                'label' => "Субъект",
            ],
            [
                'name' => 'discount',
                'label' => "Скидка"
            ],
            [
                'name' => 'active',
                'label' => "Активен"
            ]
        ]);

        $this->crud->addField([
            'name'      => 'name',
            'label'     => 'Название акции',
            'type'      => 'text'
        ]);

        $this->crud->addField([
            'name'      => 'description',
            'label'     => 'Текст описания',
            'type'      => 'text'
        ]);

        $this->crud->addField([
            'name'      => 'active',
            'label'     => 'Активен',
            'type'      => 'checkbox'
        ]);

        $this->crud->addField([
            'name'      => 'discount',
            'label'     => 'Скидка в процентах',
            'type'      => 'number'
        ]);

        $this->crud->addField([
            'name'      => 'subject',
            'label'     => 'Субъект скидки',
            'type'      => 'enum',
            'hint'      => 'На что будет распространяться акция',
            'all_values' => ['GROUP']
        ]);

        $this->crud->addField([
            'name'      => 'expire',
            'label'     => 'Длительность в секундах',
            'type'      => 'number',
            'hint'      => 'Через сколько секунд после получения, специальное предложение пропадает'
        ]);

        $this->crud->addField([
            'name'      => 'conditions',
            'label'     => 'Условия получения',
            'type'      => 'text'
        ]);

        $this->crud->addField([
            'name'      => 'params',
            'label'     => 'Параметры',
            'type'      => 'table',
            'columns' => [
                'offer_key' => "Ключ",
                'offer_value' => "Значение"
            ]
        ]);

    }
}
