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

jimport('legacy.view.legacy');

// check for Joomla 2.5
if (!class_exists('JViewLegacy')) {
	jimport('joomla.application.component.view');
	class JViewLegacy extends JView {}
}

/**
 * View class for a list of Wbty_forms.
 */
class Wbty_formsViewForm_Fields extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/wbty_forms.php';

		$state	= $this->get('State');
		$canDo	= Wbty_formsHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_WBTY_FORMS_TITLE_FORM_FIELDS'), 'form_fields.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/form_field';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('form_field.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('form_field.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state') && isset($this->items[0]->state)) {

            if ($this->state->get('filter.state') == -2) { 
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('form_fields.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
            } else {
			    JToolBarHelper::divider();
			    JToolBarHelper::trash('form_fields.trash','JTOOLBAR_TRASH');
		    }
        }
	}
}
