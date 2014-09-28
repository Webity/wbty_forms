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

$item = $this->item['response'];

$document = &JFactory::getDocument();
ob_start();
// start javascript output -- script
?>

Joomla.submitbutton = function(task)
{
    if (jQuery('#sbox-window').attr('aria-hidden')==true) {
        Joomla.submitform = defaultsubmitform;
    }
    
    if (task == 'response.cancel' || document.responsevalidator.isValid(document.id('response-form'))) {
        Joomla.submitform(task, document.getElementById('response-form'));
    }
    else {
        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
    }
}
document.responsesubmitbutton = Joomla.submitbutton;
<?php
// end javascript output -- /script
$script=ob_get_contents();
ob_end_clean();
$document->addScriptDeclaration($script);
?>

<h2><?php echo $item->form->name; ?></h2>

<ul class="itemlist">
	<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_RESPONSES_SUBMISSION_TIME'); ?>: <?php echo strftime('%B %d, %Y %H:%M', $item->submission_time); ?></li>
	<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_RESPONSES_IP_ADDRESS'); ?>: <?php echo $item->ip_address; ?></li>
	<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_RESPONSES_SPAM'); ?>: <?php echo $item->spam; ?></li>
</ul>

<?php foreach ($item->fields as $field) : ?>
	<p><span class="label"><?php echo $field->label; ?></span>: <?php echo $field->value; ?></p>
<?php endforeach; ?>

<form action="<?php echo JRoute::_('index.php?option=com_wbty_forms&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="response-form" class="form-validate form-horizontal">
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="option" id="option" value="com_wbty_forms" />
    <input type="hidden" name="form_name" id="form_name" value="response" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>