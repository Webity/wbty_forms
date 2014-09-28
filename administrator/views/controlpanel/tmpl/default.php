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

?>

<div class="cpanel">
	<h2>Main Tasks</h2>
    <div class="icon-wrapper">
        
				<div class="btn cpanel-btn">
					<a href="index.php?option=com_wbty_forms&view=forms"><img src="<?php echo JURI::root(); ?>media/wbty_forms/img/forms.png" alt=""><span>Forms</span></a>
				</div>
				<div class="btn cpanel-btn">
					<a href="index.php?option=com_wbty_forms&view=responses"><img src="<?php echo JURI::root(); ?>media/wbty_forms/img/responses.png" alt=""><span>Responses</span></a>
				</div>
        <div class="clr"></div>
    </div>
    <h2 style="clear:left;">Configuration / Settings</h2>
    <div class="icon-wrapper">
        
        <div class="clr"></div>
    </div>
</div>