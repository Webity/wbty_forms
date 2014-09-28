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
            
	
					<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_RESPONSES_FORM_ID'); ?>: <?php echo $this->item->form_id; ?></li>
					<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_RESPONSES_SUBMISSION_TIME'); ?>: <?php echo $this->item->submission_time; ?></li>
					<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_RESPONSES_IP_ADDRESS'); ?>: <?php echo $this->item->ip_address; ?></li>
					<li><?php echo JText::_('COM_WBTY_FORMS_FORM_LBL_RESPONSES_SPAM'); ?>: <?php echo $this->item->spam; ?></li>

</ul>