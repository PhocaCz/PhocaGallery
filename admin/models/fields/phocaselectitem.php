<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_BASE') or die();
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
jimport('joomla.form.formfield');


class JFormFieldPhocaSelectItem extends FormField
{
	public $type = 'PhocaSelectItem';

	protected function getInput() {
		$html 	= array();
		$url 	= 'index.php?option=com_phocagallery&view=phocagalleryitema&format=json&tmpl=component&'. Session::getFormToken().'=1';

		// Possible problem with modal
		//$attr 	= $this->element['class'] ? ' class="'.(string) $this->element['class'].' typeahead"' : ' class="typeahead"';
		$attr 	= $this->element['class'] ? ' class="'.(string) $this->element['class'].' "' : ' class=""';

		$attr  .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr  .= $this->element['required'] ? ' required aria-required="true"' : '';


		$clearId = 'phClearId'.$this->id;


		if ($this->multiple) {
			$multiple = 'true';
		} else {
			$multiple = 'false';
		}


		$onchange 	= (string) $this->element['onchange'];
		$value = '';

		$image = PhocaGalleryImage::getImageByImageId((int)$this->value);// We don't need catid, we get all categories title
		if(isset($image->id)) {
			$value .= (int)$image->id . ':'. $image->title .' ('.$image->category_title.')';
		}
		$id = (int)$this->value;



		$document = Factory::getDocument();

		HTMLHelper::_('jquery.framework', false);

		HTMLHelper::_('script', 'media/com_phocagallery/js/administrator/select2/select2.js', array('version' => 'auto'));
		HTMLHelper::_('script', 'media/com_phocagallery/js/administrator/select2/jquery.phocaselect2.js', array('version' => 'auto'));
		HTMLHelper::_('stylesheet', 'media/com_phocagallery/js/administrator/select2/select2.css', array('version' => 'auto'));

$document->addScriptOptions('phLang', array(
			'COM_PHOCAGALLERY_NO_MATCHES_FOUND' => Text::_('COM_PHOCAGALLERY_NO_MATCHES_FOUND'),
			'COM_PHOCAGALLERY_PLEASE_ENTER' => Text::_('COM_PHOCAGALLERY_PLEASE_ENTER'),
			'COM_PHOCAGALLERY_S_MORE_CHARACTER' => Text::_('COM_PHOCAGALLERY_S_MORE_CHARACTER'),
			'COM_PHOCAGALLERY_PLEASE_DELETE' => Text::_('COM_PHOCAGALLERY_PLEASE_DELETE'),
			'COM_PHOCAGALLERY_S_CHARACTER' => Text::_('COM_PHOCAGALLERY_S_CHARACTER'),
			'COM_PHOCAGALLERY_YOU_CAN_ONLY_SELECT' => Text::_('COM_PHOCAGALLERY_YOU_CAN_ONLY_SELECT'),
			'COM_PHOCAGALLERY_S_ITEM' => Text::_('COM_PHOCAGALLERY_S_ITEM'),
			'COM_PHOCAGALLERY_LOADING_MORE_RESULTS' => Text::_('COM_PHOCAGALLERY_LOADING_MORE_RESULTS'),
			'COM_PHOCAGALLERY_SEARCHING' => Text::_('COM_PHOCAGALLERY_SEARCHING')
		));
		$document->addScriptOptions('phVars', array('uriRoot' => Uri::root()));

		$s = array();
		$s[] = 'jQuery(document).ready(function() {';
		$s[] = '   phSearchItemsMultiple("#'.$this->id.'", "'.$url.'", '.(int)$id.', '.$multiple.', "[|]");';
		if (!$this->multiple) {
			$s[] = ' jQuery("#' . $clearId . '").on("click", function() {jQuery("#' . $this->id . '").select2("val", ""); });';
		}
		$s[] = '});';

		$document->addScriptDeclaration(implode("\n", $s));


		$class = '';
		if (!$this->multiple) {

			$class = 'ph-select2-clear-btn-box';
		}
		$html[] = '<div class="input-append input-group">';
		$html[] = '<input type="text" placeholder="&nbsp;" class="'.$class.'" id="'.$this->id.'" name="'.$this->name.'" value="'. $value.'"' .' '.$attr.' />';
		if (!$this->multiple) {
			$html[] = '<input type="button" class="btn btn-primary" id="' . $clearId . '" name="' . $clearId . '" value="' . Text::_('COM_PHOCAGALLERY_CLEAR') . '"' . ' />';
		}
		$html[] = '</div>'. "\n";


		return implode("\n", $html);
	}
}
?>
