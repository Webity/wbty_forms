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


JHtml::_ ( 'behavior.formvalidation' );

if($this->item) : ?>

    <div class="wbty_form">
	<?php if ($this->form) { ?>
		<form method="post" class="form-validate <?php if ($this->params->get('layout_type')) { echo $this->params->get('layout_type'); } ?>">
        
        	<?php if ($this->params->get('display_legend')) : ?>
        		<legend><?php echo $this->item->name; ?></legend>
            <?php endif; ?>
			<?php
			foreach ($this->form->getFieldset('fields') as $field) {
                $class = $field->__get('labelClass');
				echo '<div class="control-group'. ($class!='' ? ' '.$class : '') .'">'.str_replace('<label', '<label class="control-label"', $field->__get('label')).'<div class="controls">'.$field->__get('input').'</div></div>';
			}
			?>
            <?php if (0&&count($this->item->fields)) : ?>
            	<?php foreach($this->item->fields as $field) : ?>
                <div class="control-group">
                	<?php if ($field->label) : ?>
                    <label class="control-label" for="input_<?php echo $field->label; ?>"><?php echo $field->label; ?></label>
                    <?php endif; ?>
                    <div class="controls">
                      <?php echo $field->field_type; ?>
                    </div>
                </div>
            	<?php endforeach; ?>
        	<?php endif; ?>
            
            <div class="control-group">
                <div class="controls">
                  <input type="submit" class="button btn btn-primary" value="<?php echo ($item->submit_text ? $item->submit_text : 'Submit'); ?>" />
                </div>
            </div>
        </form>
    <?php } else {
		echo $this->item->thank_you_message;
	} ?>
    </div>
    
<?php endif; ?>