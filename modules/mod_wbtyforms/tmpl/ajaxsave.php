<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_footer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$jversion = new JVersion();
$above3 = version_compare($jversion->getShortVersion(), '3.0', 'ge');

if ($above3) {
    JHTML::_('jquery.framework');
}
JHTML::script('wbty_forms/ajaxsave.js', false, true);

JHtml::_ ( 'behavior.formvalidation' );
if($item) : ?>
    <div class="wbty_form">
	<?php if ($form) { ?>
		<form method="post" class="form-validate <?php if ($params->get('layout_type')) { echo $params->get('layout_type'); } ?>">
        
        	<?php if ($params->get('display_legend')) : ?>
            	<legend><?php echo $item->name; ?></legend>
            <?php endif; ?>
			<?php
			foreach ($form->getFieldset('fields') as $field) {
				$class = $field->__get('labelClass');
				echo '<div class="control-group'. ($class!='' ? ' '.$class : '') .'">'.str_replace('<label','<label class="control-label"',$field->__get('label')).'<div class="controls">'.$field->__get('input').'</div></div>';
			}
			?>
            
            <div class="control-group submit-container">
                <div class="controls">
                	<button type="submit" class="button" value="<?php echo ($item->submit_text ? $item->submit_text : 'Submit'); ?>"><?php echo ($item->submit_text ? $item->submit_text : 'Submit'); ?></button>
                </div>
            </div>
            <div class="clear"></div>
            <input type="hidden" name="id" value="<?php echo $params->get('form'); ?>" />
            <script>
            window.juri_root = '<?php echo JURI::root(true); ?>/';
            </script>
        </form>
        <div style="display:none;" class="thank-you">
        </div>
    <?php } else {
		echo $item->thank_you_message;
	} ?>
    </div>
    
    
    
<?php endif; ?>