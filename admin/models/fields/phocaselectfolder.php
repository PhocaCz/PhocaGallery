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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
jimport('joomla.form.formfield');

class JFormFieldPhocaSelectFolder extends FormField
{
	public $type = 'PhocaSelectFolder';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$link = 'index.php?option=com_phocagallery&amp;view=phocagalleryf&amp;tmpl=component&amp;field='.$this->id;

		$attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$onchange = (string) $this->element['onchange'];
		$required 	= ($v = $this->element['required']) ? ' required="required"' : '';


		// Build the script.
	/*	$script = array();
		$script[] = '	function phocaSelectFolder_'.$this->id.'(title) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = title;';
		$script[] = '		'.$onchange;
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';*/


		HTMLHelper::_('jquery.framework');
		$idA		= 'pgselectfolder';

		// Build the script.
		$script = array();
		$script[] = '	function phocaSelectFolder_'.$this->id.'(title) {';
		$script[] = '		document.getElementById("'.$this->id.'").value = title;';
		$script[] = '		'.$onchange;
		$script[] = '		jQuery(\'#'.$idA.'\').modal(\'hide\');';
		$script[] = '	}';


		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

/*
		$html[] = '<div class="fltlft">';
		$html[] = '<input type="text" id="'.$this->id.'_id" name="'.$this->name.'" value="'. $this->value.'"' .' '.$attr.' />';
		$html[] = '</div>';

		// Create the user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '		<a class="modal_'.$this->id.'" title="'.Text::_('COM_PHOCAGALLERY_FORM_SELECT_FOLDER').'"' .
							' href="'.($this->element['readonly'] ? '' : $link).'"' .
							' rel="{handler: \'iframe\', size: {x: 650, y: 375}}">';
		$html[] = '			'.Text::_('COM_PHOCAGALLERY_FORM_SELECT_FOLDER').'</a>';
		$html[] = '  </div>';
		$html[] = '</div>';*/

	/*	$html[] = '<div class="input-append">';
		$html[] = '<input type="text" id="'.$this->id.'_id" name="'.$this->name.'" value="'. $this->value.'"' .' '.$attr.' />';
		$html[] = '<a class="modal_'.$this->id.' btn" title="'.Text::_('COM_PHOCAGALLERY_FORM_SELECT_FOLDER').'"'
				.' href="'.($this->element['readonly'] ? '' : $link).'"'
				.' rel="{handler: \'iframe\', size: {x: 650, y: 400}}">'
				. Text::_('COM_PHOCAGALLERY_FORM_SELECT_FOLDER').'</a>';
		$html[] = '</div>'. "\n";*/


		$html[] = '<div class="input-group input-append">';

		$html[] = '<span class="input-group input-append"><input type="text" ' . $required . ' id="' . $this->id . '" name="' . $this->name . '"'
			. ' value="' . $this->value . '"' . $attr . ' />';
		$html[] = '<a href="'.$link.'" role="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#'.$idA.'" title="' . Text::_('COM_PHOCAGALLERY_FORM_SELECT_FOLDER') . '">'
			. '<span class="icon-list icon-white"></span> '
			. Text::_('COM_PHOCAGALLERY_FORM_SELECT_FOLDER') . '</a></span>';

		$html[] = '</div>'. "\n";

		$html[] = HTMLHelper::_(
			'bootstrap.renderModal',
			$idA,
			array(
				'url'    => $link,
				'title'  => Text::_('COM_PHOCAGALLERY_FORM_SELECT_FOLDER'),
				'width'  => '700px',
				'height' => '400px',
				'modalWidth' => '80',
				'bodyHeight' => '70',
				'footer' => '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">'
					. Text::_('COM_PHOCAGALLERY_CLOSE') . '</button>'
			)
		);


		return implode("\n", $html);
	}
}
