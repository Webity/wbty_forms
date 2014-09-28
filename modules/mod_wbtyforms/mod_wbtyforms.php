<?php defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE . "/components/com_wbty_forms/helpers/wbty_forms.php");

JHTML::stylesheet('wbty_forms/wbty_forms.css', false, true);
JHTML::stylesheet('wbty_forms/site.css', false, true);

$item = Wbty_formsHelper::getItem($params->get('form'));
$save = Wbty_formsHelper::getSave($params->get('form'));
if ($save) {
	$form = false;
	$thank_you = true;
} else {
	$form = Wbty_formsHelper::getForm($params->get('form'));
}

require JModuleHelper::getLayoutPath('mod_wbtyforms', $params->get('layout', 'default'));
?>