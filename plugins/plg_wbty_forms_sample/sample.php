<?php
/**
 * @copyright   Copyright (C) 2013 Webity. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

class plgWbty_formsSample extends JPlugin {    
    public function onWbtyFormsSave($form_id, &$data)
    {
        // add form items here
        return true;
    }
}
