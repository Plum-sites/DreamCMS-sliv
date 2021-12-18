<?php

namespace App\Http\Controllers\API\Admin\CRUD;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Arr;

class CrudPanel extends \Backpack\CRUD\app\Library\CrudPanel\CrudPanel
{
    public function getRowViews($entry, $rowNumber = false)
    {
        $row_items = [];

        $row_items['id'] = $entry->id;

        foreach ($this->columns() as $key => $column) {
            $row_items[$key] = $this->getCellView($column, $entry, $rowNumber);
        }

        // add the buttons as the last column
        /*if ($this->buttons->where('stack', 'line')->count()) {
            $row_items[] = \View::make('crud::inc.button_stack', ['stack' => 'line'])
                ->with('crud', $this)
                ->with('entry', $entry)
                ->with('row_number', $rowNumber)
                ->render();
        }

        if ($this->details_row) {
            $details_row_button = \View::make('crud::columns.details_row_button')
                ->with('crud', $this)
                ->with('entry', $entry)
                ->with('row_number', $rowNumber)
                ->render();
            $row_items[0] = $details_row_button.$row_items[0];
        }*/

        return $row_items;
    }

    public function getEntriesAsJsonForDatatables($entries, $totalRows, $filteredRows, $startIndex = false)
    {
        $rows = [];

        foreach ($entries as $row) {
            //$rows[] = $row;
            $rows[] = $this->getRowViews($row, $startIndex === false ? false : ++$startIndex);
        }

        return [
            'draw'            => (isset($this->request['draw']) ? (int) $this->request['draw'] : 0),
            'recordsTotal'    => $totalRows,
            'recordsFiltered' => $filteredRows,
            'data'            => $rows,
        ];
    }

    public function getModelAttributeValue($model, $field)
    {
        if (isset($field['entity'])) {
            $relationArray = explode('.', $field['entity']);
            $relatedModel = array_reduce(array_splice($relationArray, 0, -1), function ($obj, $method) {
                return $obj->{$method} ? $obj->{$method} : $obj;
            }, $model);

            $relationMethod = end($relationArray);
            if ($relatedModel->{$relationMethod} && $relatedModel->{$relationMethod}() instanceof HasOneOrMany) {
                return $relatedModel->{$relationMethod}->{$field['name']};
            } else {
                return $relatedModel->{$field['name']};
            }
        }

        if (is_string($field['name'])) {
            return $model->{$field['name']};
        }

        if (is_array($field['name'])) {
            $result = [];
            foreach ($field['name'] as $key => $value) {
                $result = $model->{$value};
            }

            return $result;
        }
    }

    public function getUpdateFields($id = false)
    {
        $fields = $this->fields();
        $entry = ($id != false) ? $this->getEntry($id) : $this->getCurrentEntry();

        foreach ($fields as &$field) {
            // set the value
            if (! isset($field['value'])) {
                if (isset($field['subfields'])) {
                    $field['value'] = [];
                    foreach ($field['subfields'] as $subfield) {
                        $field['value'][] = $entry->{$subfield['name']};
                    }
                } else {
                    $field['value'] = $this->getModelAttributeValue($entry, $field);
                }
            }
        }

        foreach ($fields as &$field) {
            if (isset($field['model'])){
                $field['all_values'] = $field['model']::all();
            }
        }

        // always have a hidden input for the entry id
        if (! array_key_exists('id', $fields)) {
            $fields['id'] = [
                'name'  => $entry->getKeyName(),
                'value' => $entry->getKey(),
                'type'  => 'hidden',
            ];
        }

        return $fields;
    }

    /**
     * Update a row in the database.
     *
     * @param int   $id   The entity's id
     * @param array $data All inputs to be updated.
     *
     * @return object
     */
    public function update($id, $data)
    {
        $data = $this->decodeJsonCastedAttributes($data);
        $data = $this->compactFakeFields($data);
        $item = $this->model->findOrFail($id);

        $this->createRelations($item, $data);

        // omit the n-n relationships when updating the eloquent item
        $nn_relationships = Arr::pluck($this->getRelationFieldsWithPivot(), 'name');

        $data = Arr::except($data, $nn_relationships);

        $updated = $item->update($data);

        return $item;
    }
}