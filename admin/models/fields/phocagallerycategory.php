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
defined('_JEXEC') or die();
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
if (! class_exists('PhocaGalleryLoader')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocagallery/libraries/loader.php');
}
phocagalleryimport('phocagallery.render.renderadmin');
phocagalleryimport('phocagallery.html.categoryhtml');

class JFormFieldPhocaGalleryCategory extends FormField
{
	protected $type 		= 'PhocaGalleryCategory';

	protected function getInput() {



		$db = Factory::getDBO();

       //build the list of categories
		$query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
		. ' FROM #__phocagallery_categories AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$phocagallerys = $db->loadObjectList();


		// TO DO - check for other views than category edit
		$view 	= Factory::getApplication()->input->get( 'view' );
		$catId	= -1;
		if ($view == 'phocagalleryc') {
			$id 	= $this->form->getValue('id'); // id of current category
			if ((int)$id > 0) {
				$catId = $id;
			}
		}

		// Initialize JavaScript field attributes.
		$attr = '';
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= ' class="form-select"';
		($this->element->attributes()['multiple'] == true) ? $attr .= ' multiple="multiple"' : '';

		$document					= Factory::getDocument();
		$document->addCustomTag('<script type="text/javascript">
function changeCatid() {
	var catid = document.getElementById(\'jform_catid\').value;
	var href = document.getElementById(\'pgselectytb\').href;
    href = href.substring(0, href.lastIndexOf("&"));
    href += \'&catid=\' + catid;
    document.getElementById(\'pgselectytb\').href = href;
}
</script>');

		$tree = array();
		$text = '';
		$tree = PhocaGalleryCategoryhtml::CategoryTreeOption($phocagallerys, $tree, 0, $text, $catId);
		array_unshift($tree, HTMLHelper::_('select.option', '', '- '.Text::_('COM_PHOCAGALLERY_SELECT_CATEGORY').' -', 'value', 'text'));
		return HTMLHelper::_('select.genericlist',  $tree,  $this->name, trim($attr), 'value', 'text', $this->value, $this->id );
	}
}
?>
