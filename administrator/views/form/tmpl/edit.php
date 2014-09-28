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
    document.formvalidator = document.formvalidator;
    jQuery('#form-form .toolbar-list a').each(function() {
        $(this).attr('data-onclick', $(this).attr('onclick')).attr('onclick','');
    });
    jQuery('#form-form .toolbar-list a').click(function() { 
        Joomla.submitbutton = document.formsubmitbutton;
        
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
    
    if (task == 'form.cancel' || document.formvalidator.isValid(document.id('form-form'))) {
        Joomla.submitform(task, document.getElementById('form-form'));
    }
    else {
        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
    }
}
document.formsubmitbutton = Joomla.submitbutton;

jQuery(document).ready(function($) { // when document has loaded
    var Relations = new Object;
    
    $('.relationalTable').each( function() {
        Relations[$(this).attr('id')] = new Object;
    });
    
    if ($('#jform_id').val()) {
        $('input.set2id').val( $('#jform_id').val() );
    } else {
        $('input.set2id').val( '|set2id|' );
    }
    
    $.each(Relations, function (index, $value) {
        $.ajax('index.php?option=com_wbty_forms&task=form.link&id=<?php echo $this->item->id; ?>', {
            data: {link: index}, 
            success: function(data) {
                Relations[index].html = data;
            }
        });
        
        $.ajax('index.php?option=com_wbty_forms&task=form.link_load&id=<?php echo $this->item->id; ?>', {
            data: {link: index}, 
            type: 'POST', 
            success: function (data) {
                $('#'+index).html(data);
                Relations[index].size = $('.'+index+'remove').size() + 1;
            }
        });
        
        $('a#'+index+'add').on( 'click', function(event) { // when you click the add link
            regex = new RegExp('{'+index+'}', "g");
            $('a#'+index+'add:last').before(Relations[index].html.replace(regex, Relations[index].size));
            event.preventDefault();
            Relations[index].size++;
        });
        
        
        $('a.'+index+'remove').on('click', function(event) { // similar to the previous, when you click remove link
            $(this).closest('li').remove();
            event.preventDefault();
        });
    });
});
<?php
// end javascript output -- /script
$script=ob_get_contents();
ob_end_clean();
$document->addScriptDeclaration($script);

$script = "
    jQuery(document).ready(function($) { 
        // Declare this object outside of the change function to maintain values for the life of the script
        var jvalues = new Object();
    
        $( document ).on('change', '#jform_form_field_field_type', 
            function() {
                var option = ($( this ).val());
                var id = $(this).closest('form').find('#jform_id').val();
                
                // store all current values
                $(this).closest('fieldset').find('div').find('input').each(function(index, e) {
                    name = $(e).attr('name');
                    if ($(e).attr('type') != 'hidden') {
                        if ($(e).val()) {
                            jvalues[name] = $(e).val();
                        } else {
                            delete jvalues[name];
                        }
                    }
                });

                if (option) {
                    var fieldset = null;
                    $(hidden_forms).each(function() {
                        if ($(this).attr('name') == option.toLowerCase()) {
                            fieldset = $(this);
                        }
                    });
                    
                    var jqthis = $(this);
                    $(this).closest('fieldset').find('div.field-options').html('').append(fieldset);

                    if ($(this).closest('.edit-form').find('.id').val() > 0) {
                        values = $.parseJSON(\$this.closest('fieldset').find('.record_values').val());
                        $.each(values, function(i, value) {
                            console.log($('.edit-form [name=\"'+i+'\"]'));
                            $('.edit-form [name=\"'+i+'\"]').val(value);
                        });
                    }

                    /*$.ajax({  
                      type: \"POST\",  
                      url: \"".JRoute::_('index.php?option=com_wbty_forms&task=form_field.extraFields&tmpl=component', false)."\",  
                      data: {'field' : option, 'id' : id},  
                      success: function(applyData) {
                            //alert (applyData);
                            jqthis.closest('fieldset').find('div').html(applyData);
                            // override any currently stored database values with values entered in this session
                            for (key in jvalues) {
                                $('input[name=\''+key+'\']').val(jvalues[key]);
                            }
                        }
                    })*/
                } else {
                    $(this).closest('fieldset').find('div.field-options').html('');  
                }
            });

        if ($( '#jform_form_field_field_type' ).val()) {
            $( '#jform_form_field_field_type' ).trigger('change');
        }

        $(document).on('inline-edit-trigger-after', function() {
            $('.edit-form .edit-buttons').before('<div class=\"field-options\"></div>');
            $( '#jform_form_field_field_type' ).trigger('change');
        });
        
        /* General Field listeners */
        
        $(document).on('change', '.field-label', function() {
            $('.field-name').val($(this).val().toLowerCase().replace(/ /g,'_'));
        });
        
        
        $(document).on('change', '.field-name', function() {
            var value = $(this).val();                         
            if (value === 'id' || value === 'ordering' || value === 'state' || value === 'checked_out' || value === 'checked_out_time' || value === 'created_by' || value === 'created_time' || value === 'modified_by' || value === 'modified_time') {
                alert('The table name \"'+ value +'\" is used by the core system. Please choose a different name.');
                $(this).val((value + '_1'));
            }
        });
        
        
        /* Start Field Specific listeners */
        
        $( document ).on('change', '#jfields_query', 
            function() {
                var option = $(this).val();
                
                if (option) {
                    var jqthis = $(this);
                    /*$('#jfields_key_field').closest('li').remove();*/
                    $('#jfields_value_field').closest('li').remove();
                    $.ajax({  
                      type: \"POST\",  
                      url: \"".JRoute::_('index.php?option=com_wbty_forms&task=field.getKeyValue&tmpl=component', false)."\",  
                      data: {'ajax_table_id' : option},  
                      success: function(applyData) {
                            jqthis.closest('li').after(applyData);
                            // override any currently stored database values with values entered in this session
                            for (key in jvalues) {
                                $('input[name=\''+key+'\']').val(jvalues[key]);
                            }
                        }
                    })
                } else {
                    $(this).html('');  
                }
            });
        
        $('#field_table').on('change', function() {
            window.val = $('#jform_email_field').val();
            
            $('#jform_email_field option').remove();
            $('#jform_email_field').append($('<option />'));
            
            $('#field_table tr').each(function() {
                $('#jform_email_field').append($('<option value=\"' + $(this).find('input').val() + '\">' + $(this).find('td').first().text() + '</option>'));
            });
            
            $('#jform_email_field').val(window.val);
        });
    });
    ";

$document->addScriptDeclaration($script);
?>

<?php echo JHTML::_('wbty_formsHelper.buildEditForm', $this->form); ?>

<form action="<?php echo JRoute::_('index.php?option=com_wbty_forms&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="form-form" class="form-validate form-horizontal">
    <div class="row-fluid">
        <div class="span7">
            <fieldset class="adminform parentform" data-controller="form" data-task="form.ajax_save">
                <legend><?php echo JText::_('COM_WBTY_FORMS_LEGEND_FORM'); ?></legend>
                <div class="items">
                    <?php 
                        foreach($this->form->getFieldset('form') as $field):
                            JHtml::_('wbty.renderField', $field);
                        endforeach; 
                    ?>

                    <div class="control-group"> 
                        <div class="controls">
                            <span class="btn btn-success save-primary"><i class="icon-ok"></i> Save Form Info</span>
                        </div>
                    </div>
                </div>

            </fieldset>
            
        </div>
            
        <?php // fieldset for each linked table  ?>
        <div class="span5 subtables">
		<?php
		// Add hidden form fields so as to run neccesary scripts for any modals, ect.
		require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/ajax.php');
		$helper = new wbty_formsHelperAjax;
		?>
		<fieldset class="adminform">
        	<legend>Form Fields</legend>
        	<div id="form_field" >
				<?php
				JRequest::setVar('link', 'form_field');
				echo $helper->link_load('form_id');
				?>
            </div>
		</fieldset></div>
    </div>
	
    
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="option" id="option" value="com_wbty_forms" />
    <input type="hidden" name="form_name" id="form_name" value="form" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>