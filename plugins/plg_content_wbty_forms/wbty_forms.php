<?php
/**
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Email cloack plugin class.
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.wbty_forms
 */
class plgContentWbty_forms extends JPlugin
{
    var $script_added = false;
    
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        // Don't run this plugin when the content is being indexed
        if ($context == 'com_finder.indexer') {
            return true;
        }

        if (is_object($row)) {
            return $this->_scan($row->text);
        }
        return $this->_scan($row);
    }

    protected function _scan(&$text)
    {

        $parts = array();
        $parts[] = '(<[^\/]{0,}>{wbty_forms)';
        $parts[] = '(?!<[A-Za-z])(.*)';
        $parts[] = '(}(.*?))';
        $parts[] = '(?!<[A-Za-z])(.*?)';

        $new_text = preg_replace_callback('/'.implode($parts).'/siU',
                      array(get_class($this),'_buildForm'),
                      $text, -1, $count);
        // tends to throw "too many backlinks" error, so this skips it on error
        // currently working on a fix to help performance

        if ($new_text) {
            $text = $new_text;
        }

        
        $text = preg_replace('/(<[^\/]{0,}>{wbty_forms}(.*))(<[A-Za-z])/siU', '$3', $text);
        $text = preg_replace('/(<[^\/]{0,}>{\/wbty_forms}(.*))(<[A-Za-z])/siU', '$3', $text);

        return true;
    }

    protected function processAttributes($attr) {
        $x = new SimpleXMLElement("<element $attr />");
        return $x;
    }
    
    protected function _buildForm($matches) {

        $attribs = $this->processAttributes($matches[2]);

        require_once(JPATH_BASE . "/components/com_wbty_forms/helpers/wbty_forms.php");

        $item = Wbty_formsHelper::getItem($attribs['form']);
        $save = Wbty_formsHelper::getSave($attribs['form']);
        if ($save) {
            $form = false;
            $thank_you = true;
        } else {
            $form = Wbty_formsHelper::getForm($attribs['form']);
        }

        $params = new JRegistry();
        $a = (array)$attribs;
        foreach ($a['@attributes'] as $key => $attrib) {
            $params->set($key, $attrib);
        }

        ob_start();
        require JModuleHelper::getLayoutPath('mod_wbtyforms', 'default');
        $output = ob_get_clean();

        return $output;
    }
}
