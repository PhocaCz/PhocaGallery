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

class JFormFieldPhocaSelectMap extends FormField
{
	public $type = 'PhocaSelectMap';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';


		if ($this->id == 'jform_latitude') {
			// One link for latitude, longitude, zoom
			$lat	= $this->form->getValue('latitude');
			$lng	= $this->form->getValue('longitude');
			$zoom	= $this->form->getValue('zoom');
			$suffix	= '';
			if ($lat != '') { $suffix .= '&amp;lat='.$lat;}
			if ($lng != '') { $suffix .= '&amp;lng='.$lng;}
			if ($zoom != '') { $suffix .= '&amp;zoom='.$zoom;}

			$link = 'index.php?option=com_phocagallery&amp;view=phocagalleryg&amp;tmpl=component&amp;field='.$this->id. $suffix;


			HTMLHelper::_('jquery.framework');

		}

		$idA	= 'pgselectmap';

		// Initialize JavaScript field attributes.
		$onchange = (string) $this->element['onchange'];

	/*	// Build the script.
		$script = array();
		$script[] = '	function phocaSelectMap_'.$this->id.'(title) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = title;';
		$script[] = '		'.$onchange;
		//$script[] = '		SqueezeBox.close();';
		$script[] = '	}';*/

		// Build the script.
		$script = array();
		$script[] = '	function phocaSelectMap_'.$this->id.'(title) {';
		$script[] = '		document.getElementById("'.$this->id.'").value = title;';
		$script[] = '		'.$onchange;
		$script[] = '		jModalClose();';
		//$script[] = '		SqueezeBox.close();';
		//$script[] = '		jQuery(\'#'.$idA.'\').modal(\'toggle\');';
		$script[] = '	}';

		// Hide Info box on start
		if ($this->id == 'jform_latitude') {
			$script[] = ' jQuery(document).ready(function() {';
			$script[] = '    jQuery(\'#'.$idA.'\').on(\'shown.bs.modal\', function () {';
			$script[] = '	    jQuery(\'#phmPopupInfo\').html(\'\');';
			$script[] = '	  })';
			$script[] = ' })';
		}

		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));



		if ($this->id == 'jform_latitude') {

			/*$html[] = '<div class="input-append">';
			$html[] = '<input type="text" id="'.$this->id.'_id" name="'.$this->name.'" value="'. $this->value.'"' . ' '.$attr.' />';
			$html[] = '<a class="modal_'.$this->id.' btn" title="'.Text::_('COM_PHOCAGALLERY_FORM_SELECT_COORDINATES').'"'
					.' href="'.($this->element['readonly'] ? '' : $link).'"'
					.' rel="{handler: \'iframe\', size: {x: 560, y: 470}}">'
					. Text::_('COM_PHOCAGALLERY_FORM_SELECT_COORDINATES').'</a>';
			$html[] = '</div>'. "\n";*/


			$html[] = '<div class="input-group input-append">';
			$html[] = '<span class="input-group input-append"><input type="text" id="' . $this->id . '" name="' . $this->name . '"'
				. ' value="' . $this->value . '"' . $attr . ' />';
			$html[] = '<a href="'.$link.'" role="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#'.$idA.'" title="' . Text::_('COM_PHOCAGALLERY_FORM_SELECT_COORDINATES') . '">'
				. '<span class="icon-list icon-white"></span> '
				. Text::_('COM_PHOCAGALLERY_FORM_SELECT_COORDINATES') . '</a></span>';
			$html[] = '</div>'. "\n";

			$html[] = HTMLHelper::_(
				'bootstrap.renderModal',
				$idA,
				array(
					'url'    => $link,
					'title'  => Text::_('COM_PHOCAGALLERY_FORM_SELECT_COORDINATES'),
					'width'  => '560px',
					'height' => '470px',
					'modalWidth' => '50',
					'bodyHeight' => '70',
					'footer' => '<div id="phmPopupInfo" class="ph-info-modal"></div><button type="button" class="btn" data-bs-dismiss="modal" aria-hidden="true">'
						. Text::_('COM_PHOCAGALLERY_CLOSE') . '</button>'
				)
			);

		} else {
			$html[] = '<input type="text" id="'.$this->id.'" name="'.$this->name.'" value="'. $this->value.'"' . ' '.$attr.' />';
		}


		return implode("\n", $html);
	}
}
