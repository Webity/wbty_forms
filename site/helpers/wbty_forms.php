<?php
/**
 * @version     0.2.0
 * @package     com_wbty_forms
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Webity <info@makethewebwork.com> - http://www.makethewebwork.com
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Wbty_forms helper.
 */
class Wbty_formsHelper
{
	static $model;
	static $modernizer;
	
	public static function getForm($form_id) {
		if (!self::$model) {
			self::getModel();
		}
		
		self::$model->setState('form.id', $form_id);
		
		return self::$model->getForm();
	}
	
	public static function getItem($form_id) {
		if (!self::$model) {
			self::getModel();
		}
		
		self::$model->setState('form.id', $form_id);
		
		return self::$model->getItem();
	}
	
	public static function getSave($form_id) {
		if (!self::$model) {
			self::getModel();
		}
		
		self::$model->setState('form.id', $form_id);
		
		return self::$model->getSave();
	}
	
	public static function getModel() {
		require_once(JPATH_BASE.'/components/com_wbty_forms/models/form.php');
		require_once(JPATH_BASE.'/administrator/components/com_wbty_forms/tables/forms.php');
		self::$model = new Wbty_formsModelForm();
		self::$model->getState();
		return true;
	}
	
	public static function addModernizer() {
		if (!self::$modernizer) {
			self::$modernizer = true;

			$document = JFactory::getDocument();
			$document->addScript(JURI::root() . 'media/wbty_forms/modernizr.inputs.js');
			
			ob_start();
			?>
				jQuery(document).ready(function(){
	
					if(!Modernizr.input.placeholder){
					
						jQuery('[placeholder]').focus(function() {
						  var input = jQuery(this);
						  if (input.val() == input.attr('placeholder')) {
							input.val('');
							input.removeClass('placeholder');
						  }
						}).blur(function() {
						  var input = jQuery(this);
						  if (input.val() == '' || input.val() == input.attr('placeholder')) {
							input.addClass('placeholder');
							input.val(input.attr('placeholder'));
						  }
						}).blur();
						jQuery('[placeholder]').parents('form').submit(function() {
						  jQuery(this).find('[placeholder]').each(function() {
							var input = jQuery(this);
							if (input.val() == input.attr('placeholder')) {
							  input.val('');
							}
						  })
						});
					
					}
				});
	
			<?php 
			$script = ob_get_contents();
			ob_end_clean();
			$document->addScriptDeclaration($script);
		}
	}

}
