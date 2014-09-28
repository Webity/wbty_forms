<?php
/**
 * @version     0.2.0
 * @package     com_wbty_forms
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Webity <info@makethewebwork.com> - http://www.makethewebwork.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('wbty_components.controllers.wbtycontrollerform');

/**
 * form_field controller class.
 */
class Wbty_formsControllerForm_Field extends WbtyControllerForm
{
	protected $view_list = 'form_fields';
    protected $view_form = 'form_field';
    protected $com_name = 'wbty_forms';

    function __construct() {
        parent::__construct();
		
		$this->_model = $this->getModel();
    }
	
	function back() {
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);
	}
	
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&form_id=' . JRequest::getVar('form_id');

		return $append;
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&form_id=' . JRequest::getVar('form_id');

		return $append;
	}

	
	function ajax_save() {
		$this->model = $this->getModel();
		$jinput = JFactory::getApplication()->input;
		$jform = $jinput->get('jform', array(), 'ARRAY');
		$data = $jform[$this->view_form];

		$return = array(json_encode($data));
		if (JSession::checkToken() && $id = $this->model->save($data, array())) {
			require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/ajax.php');
			$helper_name = $this->com_name . 'HelperAjax';
			$helper = new $helper_name();

			ob_start();
			$item = $this->model->getItem($id);
			$link = array();
			$link['form'] = $this->model->getForm(array(), true, 'jform', $id);
			$link['field_type'] = $item['form_field']->field_type;
			$link['type_options'] = $this->model->getFieldOptions($id, $item['form_field']->field_type);


			$return['id'] = $id;
			$return['data'] = $helper->link_html($this->view_form, $id, $link);

			$dump = ob_get_clean();
			$return['data'] .= $dump;

			$return['token'] = JSession::getFormToken();
		} else {
			$return['error'] = "error";
			$return['token'] = JSession::getFormToken();
		}
		echo json_encode($return);
		exit();
	}

	function extraFields() {
	
		$type = JRequest::getVar('field');
		$field_id = JRequest::getVar('id');
		
		if (!$type) {
			exit();
		} 
		
		$db =& JFactory::getDBO();
		$model = $this->getModel();
		$form = $model->getFieldForm($type);
		
		if (!$form) {
			echo "invalid-field-type";
			exit();
		}
		
		if ($type == 'J_SQL') { // If the chosen field is an SQL type
			$session = JFactory::getSession();
			$com_id = $session->get('fscombuilder.component_id');
			
			// Prepare the table HTML
			$query = "SELECT id, table_display_name AS name FROM #__fscombuilder_tables WHERE component_id=$com_id";
			$db->setQuery($query);
			$tables = $db->loadAssocList();
			
			$table_html = '<select id="jfields_query" name="jfields[query]">';
			foreach ($tables as $table) {
				$table_html .= '<option value="'.$table['id'].'">'.$table['name'].'</option>';
			}
			$table_html .= '</select>';
			
			
			$query = "SELECT value FROM #__wbty_forms_field_options WHERE field_id=".$db->quote($field_id)." AND NAME = 'query'";
			$db->setQuery($query);
			$table_id = $db->loadResult();
			
			JRequest::setVar('ajax_table_id', $table_id);
			$key_value_html = $this->getKeyValue(true);
		}
		
		if ($field_id) {
			$query = "SELECT * FROM #__wbty_forms_field_options WHERE field_id=".$db->quote($field_id)." AND NAME != 'sql_value'";
			$db->setQuery($query);
			
			$field_vals = $db->loadAssocList();
			if ($field_vals) {
				foreach ($field_vals as $v) {
					if ($v['name'] == 'query') {
						$table_html = str_replace('<option value="'.$v['value'].'"', '<option value="'.$v['value'].'" selected="selected"', $table_html);
					}
					elseif ($v['name'] == 'key_field') {
						$key_value_html['key'] = str_replace('<option value="'.$v['value'].'"', '<option value="'.$v['value'].'" selected="selected"', $key_value_html['key']);
					}
					elseif ($v['name'] == 'value_field') {
						$key_value_html['value'] = str_replace('<option value="'.$v['value'].'"', '<option value="'.$v['value'].'" selected="selected"', $key_value_html['value']);
					}
					$form->setValue($v['name'], NULL, $v['value']);
				}
			}
		}
		
		echo "<ul>";
		foreach ($form->getFieldset('fields') as $field) {
			echo "<li>".$field->__get('label');
			
			if ($type == 'J_SQL' && $field->__get('name') == 'jfields[query]') {
				echo $table_html."</li>";
			} 
			//elseif ($type == 'J_SQL' && $field->__get('name') == 'jfields[key_field]') {
			//	echo $key_value_html['key']."</li>";
			//} 
			elseif ($type == 'J_SQL' && $field->__get('name') == 'jfields[value_field]') {
				echo $key_value_html['value']."</li>";
			} 
			else {
				echo $field->__get('input')."</li>";
			}
		}					
		
		echo "</ul>";
		
		exit();
	}
	
	function getKeyValue($return = false) {
		$db = JFactory::getDBO();
		$table_id = JRequest::getVar('ajax_table_id');
		
		if(empty($table_id)) {
			return false;
		}
		
		$query = "SELECT field.id as id, (SELECT options.value FROM #__fscombuilder_field_options as options WHERE options.field_id=field.id AND options.name='label') as name FROM #__fscombuilder_fields as field WHERE field.table_id = $table_id";
		$db->setQuery($query);
		$fields = $db->loadAssocList();
		
		//$key_value_html['key'] = '<select id="jfields_key_field" name="jfields[key_field]">';
		$key_value_html['value'] = '<select id="jfields_value_field" name="jfields[value_field]">';
		foreach ($fields as $field) {
			//$key_value_html['key'] .= '<option value="'.$field['id'].'">'.$field['name'].'</option>';
			$key_value_html['value'] .= '<option value="'.$field['id'].'">'.$field['name'].'</option>';
		}
		//$key_value_html['key'] .= '</select>';
		$key_value_html['value'] .= '</select>';
		
		if ($return === true) {
			return $key_value_html;
		} else {
			//echo '<li><label>Key Field</label>'.$key_value_html['key'].'</li>';
			echo '<li><label>Value Field</label>"'.$key_value_html['value'].'</li>';
			exit();
		}
	}
	
}