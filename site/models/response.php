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
class Wbty_formsModelresponse extends WbtyModelAdmin
{
	protected $text_prefix = 'com_wbty_forms';
	protected $com_name = 'wbty_forms';
	protected $list_name = 'responses';
	
	public function getTable($type = 'responses', $prefix = 'Wbty_formsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true, $control='jform', $key=0)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields');
		// Get the form.
		$form = $this->loadForm('com_wbty_forms.response.'.$control.'.'.$key, 'response', array('control' => $control, 'load_data' => $loadData, 'key'=>$key));

		if (empty($form)) {
			return false;
		}

		return $form;
	}
	
	public function getItems($parent_id, $parent_key) {
		$query = $this->_db->getQuery(true);
		
		$query->select('id, state');
		$query->from($this->getTable()->getTableName());
		$query->where($parent_key . '=' . (int)$parent_id);
		$query->order('state DESC');
		
		$data = $this->_db->setQuery($query)->loadObjectList();

		if (count($data)) {
			$this->getState();
			foreach ($data as $key=>$d) {
				$this->data = null;
				$this->setState($this->getName() . '.id', $d->id);

				$return[$d->id] = $this->getForm(array(), true, 'jform', $d->id);
			}
		}
		
		return $return;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		if (isset($this->data) && $this->data) {
			return $this->data;
		}
		
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_wbty_forms.edit.response.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function getItem($pk = null)
	{
		if ($item['response'] = parent::getItem($pk)) {

			//Do any procesing on fields here if needed
			
			
		}

		return $item;
	}
	
	protected function prepareTable(&$table)
	{
		$user =& JFactory::getUser();

		

		parent::prepareTable($table);
	}
	
	public function save($data) {
		if (!parent::save($data)) {
			return false;
		}
		
		// manage link
		
		//$response_value = JRequest::getVar('response_value', array(), 'post', 'ARRAY');
		//$this->save_sub('response_value', $response_value, 'response_id');
		
		return $this->table_id;
	}

}