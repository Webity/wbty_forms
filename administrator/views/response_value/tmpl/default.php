<?php
/**
 * @version     0.2.0
 * @package     com_wbty_forms
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Webity <info@makethewebwork.com> - http://www.makethewebwork.com
 */

// no direct access
defined('_JEXEC') or die;
?>

<ul class="itemlist">
            
	
					<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_RESPONSE_VALUES_FIELD_ID'); ?>: <?php echo $this->item->field_id; ?></li>
					<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_RESPONSE_VALUES_VALUE'); ?>: <?php echo $this->item->value; ?></li>

</ul>

<form action="<?php echo JRoute::_('index.php?option=com_wbty_forms&response_id='.JRequest::getCmd('response_id').'&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="response_value-form" class="form-validate form-horizontal">
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="option" id="option" value="com_wbty_forms" />
    <input type="hidden" name="form_name" id="form_name" value="response_value" />
    <?php echo JHtml::_('form.token'); ?>
</form>