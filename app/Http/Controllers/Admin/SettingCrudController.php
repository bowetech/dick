<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CrudController;

use Illuminate\Http\Request;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SettingRequest as StoreRequest;
use App\Http\Requests\SettingRequest as UpdateRequest;

class SettingCrudController extends CrudController {

	public $crud = array(
						"model" => "App\Models\Setting",
						"entity_name" => "setting",
						"entity_name_plural" => "settings",
						"route" => "admin/setting",

						"view_table_permission" => true,
						"add_permission" => false,
						"edit_permission" => true,
						"delete_permission" => false,

						"reorder" => false,
						"reorder_label" => "name",

						// *****
						// COLUMNS
						// *****
						"columns" => [
											[
												'name' => 'name',
												'label' => "Name"
											],
											[
												'name' => 'value',
												'label' => "Value"
											],
											[
												'name' => 'description',
												'label' => "Description"
											],
									],


						// *****
						// FIELDS
						// *****
						"fields" => [
												[
													'name' => 'name',
													'label' => 'Name',
													'type' => 'text',
													'disabled' => 'disabled'
												],
												[
													'name' => 'value',
													'label' => 'Value',
													'type' => 'text'
												],
											],
						);

	/**
	 * Show the form for editing the specified setting.
	 * This overwrites the default CrudController behaviour:
	 * - instead of showing the same field type for all settings, show the field type from the "field" db column
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		// if edit_permission is false, abort
		if (isset($this->crud['edit_permission']) && !$this->crud['edit_permission']) {
			abort(403, 'Not allowed.');
		}

		// get the info for that entry
		$model = $this->crud['model'];
		$this->data['entry'] = $model::find($id);
		// set the default field type (defined in SettingCrudController)
		if (isset($this->data['crud']['update_fields']))
		{
			$this->crud['fields'] = $this->data['crud']['update_fields'];
		}
		// replace the VALUE field with the one defined as JSON in the database
		if ($this->data['entry']->field) {
			$value_field = (array)json_decode($this->data['entry']->field);
			foreach ($this->crud['fields'] as $key => $field) {
				if ($field['name'] == 'value') {
					$this->crud['fields'][$key] = $value_field;
				}
			}
		}
		$this->_prepare_fields($this->data['entry']); // prepare the fields you need to show and prepopulate the values

		$this->data['crud'] = $this->crud;
		return view('crud/edit', $this->data);
	}

	public function store(StoreRequest $request)
	{
		return parent::store_crud();
	}

	public function update(UpdateRequest $request)
	{
		return parent::update_crud();
	}
}
