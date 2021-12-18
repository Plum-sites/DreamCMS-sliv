<?php

namespace App\Http\Controllers\API\Admin\CRUD;

class ServersCrudController extends CrudController {

    public function setup() {
        //DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $this->crud->setModel("App\Models\Server");
        $this->crud->setRoute("admin/servers");
        $this->crud->setEntityNameStrings('сервера', 'Сервера');
        $this->crud->permission = 'servers';

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => "Название"
            ],
            [
                'name' => 'version',
                'label' => "Версия"
            ],
            [
                'name' => 'active',
                'label' => "Активен"
            ],
            [
                'name' => 'ip',
                'label' => "IP"
            ],
            [
                'name' => 'port',
                'label' => "Порт"
            ],
            [
                'name' => 'online',
                'label' => "Онлайн"
            ],
            [
                'name' => 'pexmanager',
                'label' => "Плагин привилегий"
            ],
            [
                'name' => 'ecomanager',
                'label' => "Плагин экономики"
            ]
        ]);

        $this->crud->addField([
            'name' => 'name',
            'label' => "Имя"
        ]);

        $this->crud->addField([
            'name' => 'version',
            'label' => "Версия"
        ]);

        $this->crud->addField([
            'name' => 'icon',
            'label' => "Иконка",
            'type' => 'browse'
        ]);

        $this->crud->addField([
            'name' => 'active',
            'label' => "Активен",
            'type' => 'checkbox'
        ]);

        $this->crud->addField([
            'name' => 'donate',
            'label' => "Включен донат",
            'type' => 'checkbox'
        ]);
        
        $this->crud->addField([
            'name' => 'api_token',
            'label' => "API токен",
            'type' => 'text'
        ]);

        $this->crud->addField([
            'name' => 'ip',
            'label' => "IP"
        ]);

        $this->crud->addField([
            'name' => 'port',
            'label' => "Порт",
            'type' => 'number'
        ]);

        $this->crud->addField([
            'name' => 'rcon_port',
            'label' => "RCON порт",
            'type' => 'number'
        ]);

        $this->crud->addField([
            'name' => 'rcon_password',
            'label' => "RCON пароль"
        ]);

        $this->crud->addField([
            'name' => 'db_host',
            'label' => "Хост базы данных"
        ]);

        $this->crud->addField([
            'name' => 'db_name',
            'label' => "Имя базы данных"
        ]);

        $this->crud->addField([
            'name' => 'db_user',
            'label' => "Имя пользователя базы данных"
        ]);

        $this->crud->addField([
            'name' => 'db_pass',
            'label' => "Пароль пользователя базы данных"
        ]);

        $this->crud->addField([
            'name' => 'pexmanager',
            'label' => "Плагин привилегий",
            'type' => 'enum'
        ]);

        $this->crud->addField([
            'name' => 'ecomanager',
            'label' => "Плагин экономики",
            'type' => 'enum'
        ]);

        $this->crud->addField([
            'label' => "Магазин",
            'type' => "select",
            'name' => 'shop_id',
            'entity' => 'shop',
            'attribute' => "name",
            'model' => "App\Models\Shop"
        ]);
    }
}
