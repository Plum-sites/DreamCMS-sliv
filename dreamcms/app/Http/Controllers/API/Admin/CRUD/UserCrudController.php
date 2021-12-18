<?php

namespace App\Http\Controllers\API\Admin\CRUD;

use App\Models\User;

class UserCrudController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel('App\Models\User');
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        $this->crud->setRoute(config('backpack.base.route_prefix').'/user');
        $this->crud->permission = 'users';

        $this->crud->setColumns([
            [
                'name'  => 'login',
                'label' => 'Логин',
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'uuid',
                'label' => 'UUID',
                'type'  => 'text',
            ],
            [
                'name'  => 'reg_time',
                'label' => 'Регистрация',
                'type'  => 'timestamp',
            ],
            [
                'name'  => 'money',
                'label' => 'Монет',
                'type'  => 'number',
            ],
            [
                'name'  => 'realmoney',
                'label' => 'Рублей',
                'type'  => 'number',
            ],
            [
                'name'  => 'reg_ip',
                'label' => 'IP регистрации',
                'type'  => 'text',
            ],
            [
                'name'  => 'vk_id',
                'label' => 'VK ID',
                'type'  => 'number',
            ]
        ]);

        $this->crud->addColumn([ // n-n relationship (with pivot table)
           'label'     => trans('backpack::permissionmanager.roles'), // Table column heading
           'type'      => 'select_multiple',
           'name'      => 'roles', // the method that defines the relationship in your Model
           'entity'    => 'roles', // the method that defines the relationship in your Model
           'attribute' => 'name', // foreign key attribute that is shown to user
           'model'     => "Backpack\PermissionManager\app\Models\Roles", // foreign key model
        ]);

//        $this->crud->addColumn([ // n-n relationship (with pivot table)
//           'label'     => trans('backpack::permissionmanager.extra_permissions'), // Table column heading
//           'type'      => 'select_multiple',
//           'name'      => 'permissions', // the method that defines the relationship in your Model
//           'entity'    => 'permissions', // the method that defines the relationship in your Model
//           'attribute' => 'name', // foreign key attribute that is shown to user
//           'model'     => "Backpack\PermissionManager\app\Models\Permission", // foreign key model
//        ]);

        $this->crud->addFields([
            [
                'name'  => 'login',
                'label' => 'Логин',
                'type'  => 'text',
            ],
            [
                'name'  => 'prefix',
                'label' => 'Кастомный префикс',
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'money',
                'label' => 'Монет',
                'type'  => 'number',
            ],
            [
                'name'  => 'realmoney',
                'label' => 'Рублей',
                'type'  => 'number',
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ],
            [
                'name'  => 'vk_id',
                'label' => 'VK ID',
                'type'  => 'text',
            ],
            [
                'name'  => 'vk_token',
                'label' => 'VK Access token',
                'type'  => 'text',
            ],
            [
                'label'            => 'Роли на сайте',
                'name'             => 'roles', // the method that defines the relationship in your Model
                'entity'           => 'roles', // the method that defines the relationship in your Model
                'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                'attribute'        => 'name', // foreign key attribute that is shown to user
                'model'            => "Backpack\PermissionManager\app\Models\Role", // foreign key model
                'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                'number_columns'   => 3, //can be 1,2,3,4,6
                'type'      => 'select_multiple',
            ],
            [
                'label'          => 'Дополнительные права на сайте',
                'name'           => 'permissions', // the method that defines the relationship in your Model
                'entity'         => 'permissions', // the method that defines the relationship in your Model
                'entity_primary' => 'roles', // the method that defines the relationship in your Model
                'attribute'      => 'name', // foreign key attribute that is shown to user
                'model'          => "Backpack\PermissionManager\app\Models\Permission", // foreign key model
                'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                'number_columns' => 3, //can be 1,2,3,4,6
                'type'      => 'select_multiple',
            ],
        ]);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @param StoreRequest $request - type injection used for validation using Requests
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $request = $this->crud->validateRequest();

        if (!$request->user()->hasRole('admin')){
            if ($roles = $request->get('roles')){
                if (count($roles)){
                    abort(403, 'Вы не можете создавать аккаунт с правами!');
                }
            }
        }

        $this->crud->hasAccessOrFail('create');
    
        
        if ($request->input('prefix')){
            $item = $this->crud->create(\Request::except(['redirect_after_save']));
    
            $item->prefix = \Purifier::clean($request->input('prefix'), 'user_prefix');
            $item->save();
        }

        // insert item in the db
        if ($request->input('password')) {
            $item = $this->crud->create(\Request::except(['redirect_after_save']));

            // now bcrypt the password
            $item->password = bcrypt($request->input('password'));
            $item->save();
        } else {
            $item = $this->crud->create(\Request::except(['redirect_after_save', 'password']));
        }

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        return parent::store($request);
    }

    public function update()
    {
        $request = $this->crud->validateRequest();

        if (!\Auth::user()->hasRole('admin')){
            $user = User::findOrFail($request->get('id'));
            if ($user->hasRole('admin')){
                abort(403, 'Вы не можете редактировать аккаунт администратора!');
            }
    
            if ($roles = $request->get('roles')){
                if (count($roles)){
                    abort(403, 'Вы не можете создавать аккаунт с правами!');
                }
            }
        }
        //encrypt password and set it to request
        $this->crud->hasAccessOrFail('update');

        $dataToUpdate = \Request::except(['redirect_after_save', 'password']);
    
        if ($request->input('prefix')){
            $dataToUpdate['prefix']= \Purifier::clean($request->input('prefix'), 'user_prefix');
        }
        
        //encrypt password
        if ($request->input('password')) {
            $dataToUpdate['password'] = bcrypt($request->input('password'));
        }

        // update the row in the db
        $this->crud->update(\Request::get($this->crud->model->getKeyName()), $dataToUpdate);

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        return parent::update($request);
    }


    public function edit($id)
    {
        if (!\Auth::user()->hasRole('admin')){
            $user = User::findOrFail($id);
            if ($user->hasRole('admin')){
                abort(403, 'Вы не можете редактировать аккаунт администратора!');
            }
        }
        return parent::edit($id);
    }

    public function show($id)
    {
        if (!\Auth::user()->hasRole('admin')){
            $user = User::findOrFail($id);
            if ($user->hasRole('admin')){
                abort(403, 'Вы не можете редактировать аккаунт администратора!');
            }
        }
        return parent::show($id);
    }

    public function destroy($id)
    {
        if (!\Auth::user()->hasRole('admin')){
            $user = User::findOrFail($id);
            if ($user->hasRole('admin')){
                abort(403, 'Вы не можете удалять аккаунт администратора!');
            }
        }
        return parent::destroy($id);
    }
}
