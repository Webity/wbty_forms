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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$document = &JFactory::getDocument();
JHTML::script("wbty_components/linked_tables.js", false, true);
JHTML::script("wbty_forms/edit.js", false, true);


ob_start();
// start javascript output -- script
?>
window.addEvent('domready', function(){
    // save validator, getting overwritten by AJAX call
    document.response_valuevalidator = document.formvalidator;
    jQuery('#response_value-form .toolbar-list a').each(function() {
    	$(this).attr('data-onclick', $(this).attr('onclick')).attr('onclick','');
    });
    jQuery('#response_value-form .toolbar-list a').click(function() { 
    	Joomla.submitbutton = document.response_valuesubmitbutton;
        
        // clean up hidden subtables
        jQuery('.subtables:hidden').remove();
        
        eval($(this).attr('data-onclick'));
    });
});

window.juri_root = '<?php echo JURI::root(); ?>';
window.juri_base = '<?php echo JURI::base(); ?>';

Joomla.submitbutton = function(task)
{
    if (jQuery('#sbox-window').attr('aria-hidden')==true) {
    	Joomla.submitform = defaultsubmitform;
    }
    
    if (task == 'response_value.cancel' || document.response_valuevalidator.isValid(document.id('response_value-form'))) {
        Joomla.submitform(task, document.getElementById('response_value-form'));
    }
    else {
        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
    }
}
document.response_valuesubmitbutton = Joomla.submitbutton;
<?php
// end javascript output -- /script
$script=ob_get_contents();
ob_end_clean();
$document->addScriptDeclaration($script);
?>

<?php echo JHTML::_('wbty_formsHelper.buildEditForm', $this->form); ?>

<form action="<?php echo JRoute::_('index.php?option=com_wbty_forms&response_id='.JRequest::getCmd('response_id').'&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="response_value-form" class="formview form-validate form-horizontal">
    <?php echo $this->addToolbar(); ?>
    <div class="clr"></div>
    <div class="row-fluid">
    	<div class="span6">
    		<fieldset class="adminform parentform" data-controller="response_value" data-task="response_value.ajax_save">
    			<legend><?php echo JText::_('COM_WBTY_FORMS_LEGEND_RESPONSE_VALUE'); ?></legend>
                <div class="items">
                    <?php 
                        foreach($this->form->getFieldset('response_value') as $field):
                            JHtml::_('wbty.renderField', $field);
                        endforeach; 
                    ?>

                    <div class="control-group"> 
                        <div class="controls">
                            <span class="btn btn-success save-primary"><i class="icon-ok"></i> Save Response_Value Info</span>
                        </div>
                    </div>
                </div>

    		</fieldset>
            
    	</div>
            
    	<?php // fieldset for each linked table  ?>
        <div class="span6 subtables">
		<?php
		// Add hidden form fields so as to run neccesary scripts for any modals, ect.
		require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/ajax.php');
		$helper = new wbty_formsHelperAjax;
		?></div>
    </div>

    
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="option" id="option" value="com_wbty_forms" />
    <input type="hidden" name="form_name" id="form_name" value="response_value" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>