<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\controllers;

use Travelpayouts;
use Travelpayouts\components\Controller;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\modules\searchForms\models\SearchFormModel;

/**
 * Class Controller
 */
class SearchFormsController extends Controller
{
    /**
     * @Inject
     * @var SearchFormModel
     */
    protected $model;

    public function actionIndex()
    {
        $records = [];
		foreach ($this->model->findAll() as $record) {
			$records[] = $record->toArray();
		}
		$this->response(true, $records);
	}

	public function actionRawData()
	{
		$this->response(true, $this->model->getOptionValue());
	}

	/**
	 * @param $id
	 * @return void
	 */
	public function actionView($id)
	{
		$model = $this->model->findByPk($id);
		$model ? $this->response(true, $model->toArray(), ['id' => $model->id]) : $this->response(false, []);
	}

	public function actionViewBySlug($slug)
	{
		$model = $this->model->findByColumnValue('slug', $slug);
		$model ? $this->response(true, $model->toArray(), ['id' => $model->id]) : $this->response(false);
	}

	public function actionCreate()
	{
		$attributes = $this->getInputParam('query');
		$model = new SearchFormModel();
		$model->attributes = $attributes;
		if ($model->save()) {
			$this->response(true, $model->toArray(), ['id' => $model->id]);
		} else {
			$this->response(false, ['errors'=> $model->getErrors()], ['id' => $model->id]);
		}
	}

	public function actionUpdate($id)
	{
		$attributes = $this->getInputParam('query');
		$model = $this->model->findByPk($id);
		if ($model) {
			$model->attributes = $attributes;
			if ($model->update()) {
				$this->response(true, $model->toArray(), ['id' => $model->id]);
			} else {
				$this->response(false, ['errors'=> $model->getErrors()], ['id' => $model->id]);
			}
		}
		$this->response(false);
	}

	public function actionDelete($id)
	{
		try {
			$model = $this->model->findByPk($id);
			if ($model && $model->delete()) {
				$this->response(true, [], [$model->id]);
			}
		} catch (\Exception $e) {
			$this->response(false);
		}
	}

	public function actionDeleteById()
	{
		$attributes = $this->getInputParam('query');
		if (is_array($attributes)) {
			try {
				$this->model->deleteAllByPk(
					$attributes
				);
				$this->response(true, array_map('intval', $attributes));

			} catch (\Exception $e) {
				$this->response(false);

			}
		}
		$this->response(false);
	}

	public function actionGetTranslations()
	{
		$this->response(true, [
			'item_add_title' => Travelpayouts::__('Add a new search form'),
			'item_edit_title' => Travelpayouts::__('Edit the search form "{searchForm_name}"'),
			'item_title' => Travelpayouts::__('Title'),
			'item_from_city' => Travelpayouts::__('Default departure city'),
			'item_to_city' => Travelpayouts::__('Default arrival city'),
			'item_city_hotel' => Travelpayouts::__('Default city or hotel'),
			'item_widget_code' => Travelpayouts::__('Generated widget code'),
			'item_apply_params' => Travelpayouts::__('Apply settings (origin, destination, etc.) from generated widget code '),

			'button_add' => Travelpayouts::__('Add'),
			'button_edit' => Travelpayouts::__('Edit'),
			'button_cancel' => Travelpayouts::__('Cancel'),
			'button_save' => Travelpayouts::__('Save'),
			'button_ok' => Travelpayouts::__('Ok'),
			'button_delete' => Travelpayouts::__('Delete'),
			'button_add_new_item' => Travelpayouts::__('Add a new search form'),

			'grid_empty' => Travelpayouts::__("You don't have any form added yet"),
			'grid_column_title' => Travelpayouts::__('Title'),
			'grid_column_date' => Travelpayouts::__('Date'),
			'grid_column_shortcode' => Travelpayouts::__('Shortcode'),
			'grid_column_actions' => Travelpayouts::__('Actions'),
			'grid_button_remove_items' => Travelpayouts::_x('Remove {num, plural, one {# item} other {# items}}', '{num, plural, one {# VALUE_NAME} other {# VALUE_NAMES}}'),
			'grid_button_remove_items_description' => Travelpayouts::_x('{num, plural, one {# item} other {# items}} will be removed. This operation cannot be undone.', '{num, plural, one {# VALUE_NAME} other {# VALUE_NAMES}}'),

			'item_create_success' => Travelpayouts::__('Search form has been created'),
			'item_remove_success' => Travelpayouts::__('item has been removed'),
			'item_remove_title' => Travelpayouts::__('Do you want to delete this item?'),
			'item_remove_description' => Travelpayouts::__('This item will be deleted permanently. This operation cannot be undone.'),
			'item_update_success' => Travelpayouts::__('Search form has been updated'),
			'item_update_error' => Travelpayouts::__('An error has occurred while updating the search form'),
			'items_remove_success' => Travelpayouts::__('selected items have been successfully removed'),
			'item_create_error' => Travelpayouts::__('An error has occurred while creating the search form'),
			'item_remove_error' => Travelpayouts::__('An error has occurred while removing the search form'),
			'items_remove_error' => Travelpayouts::__('An error has occurred while removing search forms'),

			'request_processing' => Travelpayouts::__('Sending data. Please wait'),
			'request_delete_success' => Travelpayouts::_x('{num, plural, one {# search form} other {# search forms}} was successfully removed.', '{num, plural, one {# VALUE_NAME} other {# VALUE_NAMES}}'),

			'introduce_description' => Travelpayouts::__('You need to create a new search form at Travelpayouts.com and get the widget code to add the form to the plugin'),
			'introduce_form_code_exists' => Travelpayouts::__('I have a generated widget code'),
			'introduce_form_code_not_exists' => Travelpayouts::__("I don't have a code"),
			'introduce_form_create_button' => Travelpayouts::__('Create an {source} form'),
			'introduce_form_tutorial_link' => Travelpayouts::__('Here is a {tutorial_link} in help'),
			'introduce_form_tutorial_link_title' => Travelpayouts::__('tutorial'),
			'introduce_form_ok' => Travelpayouts::__('Ok, got it'),

			'validation_string_length_short' => Travelpayouts::__('{attribute} should contain at least {min} characters.'),
			'validation_invalid_value' => Travelpayouts::__('{attribute} has invalid value'),
		], ['locale' => LanguageHelper::fallbackLocaleDashboard(),]);
	}
}
