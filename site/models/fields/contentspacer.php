<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports an custom SQL select list
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldContentspacer extends JFormField
{
	public function __get($name)
	{
		switch ($name) {
			case 'pagebreak':
				return $this->element['pagebreak'];
			case 'class':
				return $this->element['class'];
			default:
				return parent::__get($name);
		}
	}
	
	protected function getLabel() {
		return '';
	}
	protected function getInput() {
		$class = (string) $this->element['class'];
		if ($content = (string) $this->element['content']) {
			return '<p class="'.$class.'">'.$content.'</p>';
		} else {
			return '';
		}
	}
}
