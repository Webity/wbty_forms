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
class Wbty_formsModelform extends WbtyModelAdmin
{
	protected $text_prefix = 'com_wbty_forms';
	protected $com_name = 'wbty_forms';
	protected $list_name = 'forms';
	
	public function getTable($type = 'forms', $prefix = 'Wbty_formsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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

	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('form.id');
		
		$app = JFactory::getApplication();
        $params = $app->getParams();
		if (!$pk) {
			$pk = (int) $params->get('form');
		}
		
		$this->pk = $pk;
		
		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

                        $db = $this->getDbo();
                        $query = $db->getQuery(true);

                        $query->select($this->getState(
                                'item.select', 'a.*'
                                )
                        );
                        $query->from('#__wbty_forms_forms AS a');
                        
                        $query->where('a.id = '. (int) $pk);

                        // Filter by published state.
                        $published = $this->getState('filter.published');
                        $archived = $this->getState('filter.archived');

                        if (is_numeric($published)) {
                                $query->where('(a.state = ' . (int) $published . ' OR a.state =' . (int) $archived . ')');
                        }

                        $db->setQuery($query);

                        $data = $db->loadObject();

                        if ($error = $db->getErrorMsg()) {
                                JError::raiseError(404, $error);
                                return false;
                        }
						
                        $query = $db->getQuery(true);
						$query->select($this->getState(
                                'item.select', 'a.*'
                                )
                        );
						
						$query->from('#__wbty_forms_form_fields AS a');
						$query->where('a.base_id=0');
						$query->where('a.form_id = '. (int) $pk);
						
						$query->where('(a.state = 1)');
						$query->order('ordering ASC');
						$db->setQuery($query);

                        $data->fields = $db->loadObjectList();
						
						foreach ($data->fields as $k=>$f) {
							$query = $db->getQuery(true);
							$query->select('a.*');
							$query->from('#__wbty_forms_field_options AS a');
							$query->where('a.field_id = '. (int) $f->id);
							$query->where('(a.state = 1)');
							$db->setQuery($query);
							$data->fields[$k]->options = $db->loadAssocList('name');
						}

                        $this->_item[$pk] = $data;
			
		}

		return $this->_item[$pk];
	}
	
	public function setModelState() {
		$this->populateState();
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Check to make sure we have an item to build into an XML object
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('form.id');
		
		$app = JFactory::getApplication();
        $params = $app->getParams();
		if (!$pk) {
			$pk = (int) $params->get('form');
		}
		
		if (!$this->_item[$pk]) {
			$this->getItem();
		}
		
		if (!$this->_item[$pk]) {
			return false;
		}
		
		JForm::addFieldPath(dirname(__FILE__) . '/fields');
		JForm::addFieldPath(JPATH_BASE . '/libraries/cms/form/field');
		
		// We are ready to start building
		$xml = '<?xml version="1.0" encoding="utf-8"?>
<form>
	<fieldset name="fields">';
		$fields = $this->_item[$pk]->fields;
		foreach ($fields as $field) {
			if ($field->field_type=='J_CAPTCHA') {
				$this->captcha = true;
			}
			$options = '';
			$xml .= '
				<field type="'.strtolower(str_replace('J_','', $field->field_type)).'" ';
			foreach ($field->options as $key=>$value) {
				if ($key=='sqlname') {
					//ignore
				} elseif ( ($key=='value' || $key=='values') && $field->field_type != 'J_CHECKBOX') {
					$opts = explode('|', $value['value']);
					foreach ($opts as $o) {
						$options .= '<option value="'.htmlentities($o).'">'.htmlentities($o).'</option>';
					}
				} elseif($key=='class') {
					$xml .= $key . '="' . $value['value'] . '" labelclass="' . $value['value'] . '" ';
				} else {
					$xml .= $key . '="' . $value['value'] . '" ';
				}
			}
			if ($options) {
				$xml .= '>'.$options.'</field>';
			} else {
				$xml .= '/>';
			}
		}
		if ($app->getParams('com_wbty_forms')->get('use_honeypot', 1)) {
			$xml .= '		<field type="wbtytext" class="wbty_email" name="wbty_email" labelclass="wbty_email" label="Leave this field blank" />';
			JFactory::getDocument()->addScriptDeclaration("jQuery(document).ready(function(\$) {\$('.wbty_email').attr('tabindex', '-1');});");
		}
		$xml .= '	</fieldset>

</form>';

		// xml should be ready, convert to jform object
		$form = JForm::getInstance('wbty_forms'.$pk, $xml, array('control'=>'wbty_forms'.$pk));
		$form->bind($this->loadFormData());
		
		// add modernizer support for forms
		require_once(JPATH_BASE . '/components/com_wbty_forms/helpers/wbty_forms.php');
		Wbty_formsHelper::addModernizer();
		
		return $this->form[$pk] = $form;
	}
	
	protected function loadFormData() {
		$pk = (!empty($this->pk)) ? $this->pk : (int) $this->getState('form.id');
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_wbty_forms.wbty_forms'.$pk, array());
		return $data;
	}
	
	public function getSave() {
		$input = JFactory::getApplication()->input;
		$pk = (!empty($this->pk)) ? $this->pk : (int) $this->getState('form.id');
		$app =& JFactory::getApplication();
		
		$post = $input->get('wbty_forms'.$pk, array(), 'ARRAY');
		
		if (!$post) {
			return false;
		}
		
		$form = $this->getForm();
		/*if (!$form->validate($post)) {
			$errors = $form->getErrors();
			foreach ($errors as $error) {
				$app->enqueueMessage('An error has occurred'.$error, 'error');
			}
			$app->setUserState('com_wbty_forms.wbty_forms'.$pk, $post);
			return false;
		}*/

		if ($this->getCaptcha()) {
			//verify captcha
			if (!$this->verifyCaptcha($input->get('recaptcha_response_field'))) {
				$app->enqueueMessage('Security verification failed', 'error');
				$app->setUserState('com_wbty_forms.wbty_forms'.$pk, $post);
				return false;
			}
		}
		
		$spam = false;

		if (isset($post['wbty_email']) && $post['wbty_email'] !== '') {
			$spam = true;
		}

		if (!$spam) {
			$spamcheck = file_get_contents('http://www.stopforumspam.com/api?&ip='.$_SERVER['REMOTE_ADDR'].'&f=json' . ($post['email'] ? '&email='.$this->_db->escape($post['email']) : ''));
			if ($spamcheck) {
				$spamcheck = json_decode($spamcheck);
				if ($spamcheck->email->confidence > 80 || $spamcheck->ip->confidence > 80) {
					$spam = true;
				} 
			}
		}
		
		//store record
		$d = new stdClass();
		$d->form_id = $pk;
		$d->submission_time = time();
		$d->ip_address = $_SERVER['REMOTE_ADDR'];
		$d->spam = (int)$spam;
		$this->_db->insertObject('#__wbty_forms_responses', $d);

		$response_id = $this->_db->insertid();
		
		foreach($post as $key=>$item) {
			if (is_array($item)) $item = implode(', ', $item);
			$query = "SELECT fo.field_id FROM #__wbty_forms_field_options as fo LEFT JOIN #__wbty_forms_form_fields as f ON f.id = fo.field_id WHERE f.form_id=$pk AND fo.name='name' AND fo.value='".$this->_db->escape($key)."'";
			$field_id = $this->_db->setQuery($query)->loadResult();
			
			$d = new stdClass();
			$d->response_id = $response_id;
			$d->field_id = $field_id;
			$d->value = $item;
			$this->_db->insertObject('#__wbty_forms_response_values', $d);
		}

		if (!$spam) {
			$this->sendEmail($pk, $response_id);
		}
		$this->sendThankYouEmail($pk, $response_id);
		$app->setUserState('com_wbty_forms.wbty_forms'.$pk, null);

		// leave as true for no redirect. Otherwise replace in a plugin with a 'index.php?...' link to force a redirect.
		$redirect_url = true;
		
		// hook for after save actions
		JPluginHelper::importPlugin( 'wbty_forms' );
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger( 'onWbtyFormsSave', array( $pk, &$post, &$redirect_url ) );

		return $redirect_url;
	}
	
	protected function sendEmail($pk, $response_id) {
		
		if (!$emails = $this->_item[$pk]->email_recipients) {
			return false;
		}
		
		$config =& JFactory::getConfig();
		
		if (!$this->responses[$response_id]) {
			$query = "SELECT *, form.name as form_name, rv.value as response_value, rv.field_id as field_id, fo2.value as value_key FROM #__wbty_forms_response_values as rv LEFT JOIN #__wbty_forms_field_options as fo ON fo.field_id=rv.field_id LEFT JOIN #__wbty_forms_form_fields as f ON f.id=rv.field_id LEFT JOIN #__wbty_forms_forms as form ON form.id=f.form_id LEFT JOIN #__wbty_forms_field_options as fo2 ON fo2.field_id=rv.field_id WHERE rv.response_id=$response_id AND fo.name='label' AND fo2.name='name'";
			$this->responses[$response_id] = $this->_db->setQuery($query)->loadAssocList();
		}
		
		$msg = "<h3>" . $this->responses[$response_id][0]['form_name'] . " Submission from ". $config->get( 'config.sitename') . "<h3><p>";
		
		// Put our notification email subject line in a more managable variable
		$subject = $this->_item[$pk]->email_subject ? $this->_item[$pk]->email_subject : '';
		
		if ($this->responses[$response_id]) {
			foreach ($this->responses[$response_id] as $r) {
				
				// Replace any shortcodes in the notification email subject line
				$subject = str_replace('{'. $r['value_key'] .'}', $r['response_value'], $subject);
				
				$msg .= $r['value'] . ": <b>". $r['response_value'].'</b><br>';
			}
		}
		$msg .= '</p>';
		
		$mailer =& JFactory::getMailer();
		
		$sender = array( 
			$config->get( 'config.mailfrom' ),
			$config->get( 'config.fromname' ) );
		 
		$mailer->setSender($sender);
		
		
		$emails = str_replace(';',',', $emails);
		$recipients = explode(',', $emails);
		foreach ($recipients as $k=>$r) {
			$recipients[$k] = trim($r);
		}
		$mailer->addRecipient($recipients);
		
		// If we don't have a custom subject line, use the default.
		if (empty($subject))
			$subject = $this->responses[$response_id][0]['form_name'] . " Submission from ". $config->get('sitename');
			
		$mailer->setSubject($subject);
		
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setBody($msg);
		
		$send =& $mailer->Send();
		
		if ( $send !== true ) {
			$app =& JFactory::getApplication();
			$app->enqueueMessage('Error sending email: ' . $send->get('message'));
			return false;
		} else {
			return true;
		}
		
	}
	
	protected function sendThankYouEmail($pk, $response_id) {
		
		$email_field = $this->_item[$pk]->email_field;
		$thank_you_subject = $this->_item[$pk]->thank_you_subject;
		$thank_you_email = $this->_item[$pk]->thank_you_email;
		
		if (!$email_field || !$thank_you_subject || !$thank_you_email) {
			return false;
		}
		
		$config =& JFactory::getConfig();
		
		$query = "SELECT *, form.name as form_name, fo.value as field_name, rv.value as response_value, rv.field_id as field_id FROM #__wbty_forms_response_values as rv LEFT JOIN #__wbty_forms_field_options as fo ON fo.field_id=rv.field_id LEFT JOIN #__wbty_forms_form_fields as f ON f.id=rv.field_id LEFT JOIN #__wbty_forms_forms as form ON form.id=f.form_id WHERE rv.response_id=$response_id AND fo.name='name'";
		$responses = $this->_db->setQuery($query)->loadAssocList();
		
		if ($responses) {
			foreach ($responses as $r) {
				$thank_you_email = str_replace('{'.$r['field_name'].'}', $r['response_value'], $thank_you_email);
				$thank_you_subject = str_replace('{'.$r['field_name'].'}', $r['response_value'], $thank_you_subject);
				if ($email_field == $r['field_id']) {
					$recipients[] = $r['response_value'];
				}
			}
		}
		
		$mailer =& JFactory::getMailer();
		
		$sender = array( 
			$config->get( 'config.mailfrom' ),
			$config->get( 'config.fromname' ) );
		 
		$mailer->setSender($sender);
		
		foreach ($recipients as $k=>$r) {
			$recipients[$k] = trim($r);
		}
		$mailer->addRecipient($recipients);
		$mailer->setSubject($thank_you_subject);
		
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setBody($thank_you_email);
		
		$send =& $mailer->Send();
		
		if ( $send !== true ) {
			$app =& JFactory::getApplication();
			$app->enqueueMessage('Error sending email: ' . $send->get('message'));
			return false;
		} else {
			return true;
		}
		
	}
	
	public function verifyCaptcha($response) {
		JPluginHelper::importPlugin('captcha');
		$dispatcher = JDispatcher::getInstance();
		$res = $dispatcher->trigger('onCheckAnswer',$response);
		return ($res[0] ? true : false);
	}
	
	public function getCaptcha() {
		return $this->captcha ? true : false;
	}


}