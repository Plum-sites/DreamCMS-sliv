<?php

namespace App\Http\Controllers\API\Admin\CRUD;

use App\Achievements\Achievement;
use App\Events\BuyGroupEvent;
use App\Events\BuyKitEvent;
use App\Events\ExchangeCoinsEvent;
use App\Events\FriendshipEvent;
use App\Events\PaymentEvent;
use App\Events\PostLikeEvent;
use App\Events\SendCoinsToServerEvent;

class AchievementCrudController extends CrudController {

    public function setup() {
        $this->crud->setModel(Achievement::class);
        $this->crud->setRoute("admin/achievement");
        $this->crud->setEntityNameStrings('ачивку', 'ачивки');
        $this->crud->permission = 'achievements';

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => "Название"
            ],
            [
                'name' => 'description',
                'label' => "Описание"
            ],
            [
                'name' => 'secret',
                'label' => "Секретная"
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
            'name' => 'min_progress',
            'label' => "Минимальный прогресс",
            'type' => 'number'
        ]);

        $this->crud->addField([
            'name' => 'max_progress',
            'label' => "Максимальный прогресс",
            'type' => 'number'
        ]);

        $this->crud->addField([
            'name' => 'secret',
            'label' => "Секретная",
            'type' => 'checkbox'
        ]);

        $this->crud->addField([
            'name' => 'progress_formula',
            'label' => "Формула расчета прогресса",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'events',
            'label' => "Эвенты для обновления",
            'type' => 'checklist',
            'attribute' => '',
            'all_values' => [
                BuyGroupEvent::class,
                BuyKitEvent::class,
                ExchangeCoinsEvent::class,
                FriendshipEvent::class,
                PaymentEvent::class,
                PostLikeEvent::class,
                SendCoinsToServerEvent::class,
            ]
        ]);

        /*$this->crud->addField([
            'name' => 'conditions',
            'label' => "Условия",
            'type' => 'table',
            'columns' => [
                'context'  => 'Ключ',
                'url'  => 'Оператор',
                'value'  => 'Значение',
            ]
        ]);*/
    }
}
