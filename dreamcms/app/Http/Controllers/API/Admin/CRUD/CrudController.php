<?php

namespace App\Http\Controllers\API\Admin\CRUD;

use Backpack\CRUD\app\Http\Controllers\CrudController as OrigCrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Gate;
use Illuminate\Support\Facades\Request;
use DB;

class CrudController extends OrigCrudController {
    /**
     * @var CrudPanel
     */
    public $crud;

    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->initCrud();

            return $next($request);
        });
    }

    public function initCrud(){
        if ($this->crud) {
            return;
        }

        $request = Request::instance();

        $this->crud = app()->make(CrudPanel::class);
        $this->crud->setRequest($request);
        $this->request = $request;

        $this->setupDefaults();
        $this->setup();
        $this->setupConfigurationForCurrentOperation();

        if (Gate::denies('crud', [$this->crud->getModel(), 'view'])){
            $this->crud->denyAccess(['list']);
        }
        if (Gate::denies('crud', [$this->crud->getModel(), 'create'])){
            $this->crud->denyAccess(['create']);
        }
        if (Gate::denies('crud', [$this->crud->getModel(), 'edit'])){
            $this->crud->denyAccess(['update']);
        }
        if (Gate::denies('crud', [$this->crud->getModel(), 'delete'])){
            $this->crud->denyAccess(['delete']);
        }
    }

    /* LIST */
    
    public function index()
    {
        $this->initCrud();

        $this->crud->hasAccessOrFail('list');
        $this->crud->setOperation('list');

        $fields = [];
        foreach ($this->crud->getCreateFields() as $field){
            if (isset($field['model'])){
                $field['all_values'] = $field['model']::all();
            }
            if ($field['type'] == 'enum'){
                $field['all_values'] = $this->getEnumValues($field['name']);
            }

            $fields[] = $field;
        }

        $ufields = [];
        foreach ($this->crud->getCreateFields() as $field){
            if (isset($field['model'])){
                $field['all_values'] = $field['model']::all();
            }
            if ($field['type'] == 'enum'){
                $field['all_values'] = $this->getEnumValues($field['name']);
            }

            $ufields[] = $field;
        }

        return \Response::json([
            'crud' =>  $this->crud,
            'title' => $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural),
            'columns' => $this->crud->columns(),
            'create_fields' => $fields,
            'edit_fields' => $ufields,
        ]);
    }

    public function getEnumValues($column){
        $enumStr = DB::select(DB::raw('SHOW COLUMNS FROM ' . $this->crud->model->getTable() . ' WHERE Field = "'.$column.'"'))[0]->Type;
        preg_match_all("/'([^']+)'/", $enumStr, $matches);
        return isset($matches[1]) ? $matches[1] : [];
    }
    
    public function search()
    {
        $this->initCrud();

        $this->crud->hasAccessOrFail('list');
        $this->crud->setOperation('list');

        $totalRows = $this->crud->model->count();
        $filteredRows = $this->crud->count();
        $startIndex = $this->request->input('start') ?: 0;
        // if a search term was present
        if ($this->request->input('search') && $this->request->input('search')['value']) {
            // filter the results accordingly
            $this->crud->applySearchTerm($this->request->input('search')['value']);
            // recalculate the number of filtered rows
            $filteredRows = $this->crud->count();
        }
        // start the results according to the datatables pagination
        if ($this->request->input('start')) {
            $this->crud->skip((int) $this->request->input('start'));
        }
        // limit the number of results according to the datatables pagination
        if ($this->request->input('length')) {
            $this->crud->take((int) $this->request->input('length'));
        }
        // overwrite any order set in the setup() method with the datatables order
        if ($this->request->input('order')) {
            $column_number = $this->request->input('order')[0]['column'];
            $column_direction = $this->request->input('order')[0]['dir'];
            $column = $this->crud->findColumnById($column_number);
            if ($column['tableColumn']) {
                // clear any past orderBy rules
                $this->crud->query->getQuery()->orders = null;
                // apply the current orderBy rules
                $this->crud->query->orderBy($column['name'], $column_direction);
            }

            // check for custom order logic in the column definition
            if (isset($column['orderLogic'])) {
                $this->crud->customOrderBy($column, $column_direction);
            }
        }
        $entries = $this->crud->getEntries();

        return $this->crud->getEntriesAsJsonForDatatables($entries, $totalRows, $filteredRows, $startIndex);
    }

    public function show($id)
    {
        //$this->crud->hasAccessOrFail('show');
        $this->crud->setOperation('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // set columns from db
        $this->crud->setFromDb();

        // cycle through columns
        foreach ($this->crud->columns as $key => $column) {
            // remove any autoset relationship columns
            if (array_key_exists('model', $column) && array_key_exists('autoset', $column) && $column['autoset']) {
                $this->crud->removeColumn($column['name']);
            }

            // remove the row_number column, since it doesn't make sense in this context
            if ($column['type'] == 'row_number') {
                $this->crud->removeColumn($column['name']);
            }

            // remove columns that have visibleInShow set as false
            if (isset($column['visibleInShow']) && $column['visibleInShow'] == false) {
                $this->crud->removeColumn($column['name']);
            }
        }

        // remove preview button from stack:line
        $this->crud->removeButton('show');
        $this->crud->removeButton('delete');

        // remove bulk actions colums
        $this->crud->removeColumns(['blank_first_column', 'bulk_actions']);

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        //return view($this->crud->getShowView(), $this->data);

        return \Response::json([
            'title' => $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural),
            'crud' =>  $this->crud,
            'fields' => $this->crud->getCurrentFields(),
            'entry' => $this->crud->getEntry($id)
        ]);
    }

    public function showDetailsRow($id)
    {
        $this->crud->hasAccessOrFail('details_row');
        $this->crud->setOperation('list');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getDetailsRowView(), $this->data);
    }

    /* CREATE */

    public function create()
    {
        $this->crud->hasAccessOrFail('create');
        $this->crud->setOperation('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getCreateFields();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        //return view($this->crud->getCreateView(), $this->data);

        return \Response::json([
            'title' =>  $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name,
            'crud' =>  $this->crud,
            'fields' =>  $this->crud->getCreateFields()
        ]);
    }


    /* DELETE */

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        $this->crud->setOperation('delete');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        return \Response::json([
            'success' => $this->crud->delete($id)
        ]);
    }
    
    public function bulkDelete()
    {
        $this->crud->hasAccessOrFail('delete');
        $this->crud->setOperation('delete');

        $entries = $this->request->input('entries');
        $deletedEntries = [];

        foreach ($entries as $key => $id) {
            if ($entry = $this->crud->model->find($id)) {
                $deletedEntries[] = $entry->delete();
            }
        }

        return \Response::json([
            'deleted' => $deletedEntries
        ]);
    }

    /* EDIT */

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        $this->crud->setOperation('update');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        return \Response::json([
            'title' =>  $this->crud->getTitle() ?? trans('backpack::crud.edit').' '.$this->crud->entity_name,
            'entry' => $this->crud->getEntry($id),
            'id' =>  $id,
            'crud' =>  $this->crud,
            'fields' =>  $this->crud->getUpdateFields($id)
        ]);
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        $request = $this->crud->validateRequest();

        $item = $this->crud->update($request->get($this->crud->model->getKeyName()), $this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
