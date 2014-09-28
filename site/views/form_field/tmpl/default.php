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
            
	
					<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_FORM_FIELDS_LABEL'); ?>: <?php echo $this->item->label; ?></li>
					<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_FORM_FIELDS_FIELD_TYPE'); ?>: <?php echo $this->item->field_types_name; ?></li>
					<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_FORM_FIELDS_VALIDATION'); ?>: <?php echo $this->item->validation_types_name; ?></li>

</ul>