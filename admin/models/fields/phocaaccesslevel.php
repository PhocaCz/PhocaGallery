<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');


class JFormFieldPhocaAccessLevel extends ListField
{

	public $type = 'PhocaAccessLevel';


	protected function getInput()
	{
		// Initialize variables.
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="form-control"';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get the field options.
		$options = $this->getOptions();

		//return JHtml::_('access.level', $this->name, $this->value, $attr, $options, $this->id);
		return JFormFieldPhocaAccessLevel::accessLevel( $this->name, $this->value, $attr, $options, $this->id);
	}

	/*
	 * Copy of JHtml::_('access.level', $this->name, $this->value, $attr, $options, $this->id);
	 * because of prevent from loading the "Public"
	 */
	public static function accessLevel($name, $selected, $attribs = '', $params = true, $id = false)
	{
		$db		= Factory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text');
		$query->from('#__viewlevels AS a');
		$query->group('a.id, a.title');
		$query->order('a.ordering ASC');
		$query->where('a.id <> 1');//PHOCAEDIT
		$query->order('`title` ASC');

		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();

		// Check for a database error.
		/*if ($db->getErrorNum()) {
			throw new Exception($db->getErrorMsg(), 500);
			return null;
		}*/

		// If params is an array, push these options to the array
		if (is_array($params)) {
			$options = array_merge($params,$options);
		}
		// If all levels is allowed, push it into the array.
		elseif ($params) {
			array_unshift($options, HTMLHelper::_('select.option', '', Text::_('JOPTION_ACCESS_SHOW_ALL_LEVELS')));
		}

		return HTMLHelper::_('select.genericlist', $options, $name,
			array(
				'list.attr' => $attribs,
				'list.select' => $selected,
				'id' => $id
			)
		);
	}
}
