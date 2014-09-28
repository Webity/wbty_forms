<?php
/**
 * @version     0.2.0
 * @package     com_wbty_forms
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Webity <info@makethewebwork.com> - http://www.makethewebwork.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('wbty_components.models.wbtymodeladmin');

/**
 * Wbty_forms model.
 */
class Wbty_formsModelform_field extends WbtyModelAdmin
{
	protected $text_prefix = 'com_wbty_forms';
	protected $com_name = 'wbty_forms';
	protected $list_name = 'form_fields';

	public function getTable($type = 'form_fields', $prefix = 'Wbty_formsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true, $control='jform', $key=0)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();
		
		// Get the form.
		$form = $this->loadForm('com_wbty_forms.form_field.'.$control.'.'.$key, 'form_field', array('control' => $control, 'load_data' => $loadData, 'key'=>$key));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
	
	public function getItems($parent_id, $parent_key) {
		$query = $this->_db->getQuery(true);
		
		$query->select('id, state, field_type');
		$query->from($this->getTable()->getTableName());
		$query->where($parent_key . '=' . (int)$parent_id);
		$query->where($parent_key . '!= 0');
		$query->where('base_id=0');
		$query->order('state DESC, ordering ASC');
		
		$data = $this->_db->setQuery($query)->loadObjectList();
		if (count($data)) {
			$this->getState();
			$key=0;
			foreach ($data as $key=>$d) {
				$this->data = null;
				$this->setState($this->getName() . '.id', $d->id);
				$return[$d->id]['form'] = $this->getForm(array(), true, 'jform', $d->id);
				$return[$d->id]['type_options'] = $this->getFieldOptions($d->id, $d->field_type);
				$return[$d->id]['field_type'] = $d->field_type;
			}
		}
		
		return $return;
	}

	public function getFieldOptions($id, $fieldtype) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('*')
			->from('#__wbty_forms_field_options')
			->where('field_id=' . (int)$id);

		return $db->setQuery($query)->loadObjectList('name');
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		if ($this->data) {
			return $this->data;
		}
		
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_wbty_forms.edit.form_field.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item['form_field'] = parent::getItem($pk)) {

			//Do any procesing on fields here if needed
			
				$db =& JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->from('#__wbty_forms_form_fields as a');
				
				$query->select('field_types.name as field_types_name');
				$query->join('LEFT', '#__wbty_forms_field_types as field_types ON a.field_type=field_types.id');
				$query->select('validation_types.name as validation_types_name');
				$query->join('LEFT', '#__wbty_forms_validation_types as validation_types ON a.validation=validation_types.id');
				$query->where('a.id='.(int)$item->id);
				$items = $db->setQuery($query)->loadObject();
				if($items) {
					foreach($items as $key=>$value) {
						if ($value && $key) {
							$item->$key = $value;
						}
					}
				}
			
		}

		return $item;
	}

	protected function prepareTable(&$table)
	{
		$user =& JFactory::getUser();
		
		if (JRequest::getVar('form_id')) {
			$table->form_id=JRequest::getVar('form_id');
		}

		parent::prepareTable($table);
	}
	
	function save($data) {
		if (!parent::save($data)) {
			return false;
		}

		// manage link
		$jform = JFactory::getApplication()->input->post->get('jform', array(), 'ARRAY');

		$field_type = $jform['form_field']['field_type'];
		if ($field_type) {
			$field_options = $jform[strtolower($field_type)];
			if ($field_options) {
				$query = "DELETE FROM #__wbty_forms_field_options WHERE field_id=".$this->_db->quote($this->table_id);
				$this->_db->setQuery($query);
				$this->_db->query();
				
				foreach($field_options as $name=>$value) {
					if ($value=='') {
						continue;
					}
					$query = "INSERT INTO #__wbty_forms_field_options SET name=".$this->_db->quote($name).", value=".$this->_db->quote($value).", field_id=".$this->_db->quote($this->table_id);
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}

		return $this->table_id;
	}
}