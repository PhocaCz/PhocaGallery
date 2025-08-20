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

class JFormFieldPhocaSelectFbUser extends FormField
{
	protected $type 		= 'PhocaSelectFbUser';

	protected function getInput() {

		$db = Factory::getDBO();

       //build the list of categories
		$query = 'SELECT a.id AS value, '
		. ' CASE WHEN CHAR_LENGTH(a.name) THEN a.name ELSE a.appid END as text'
		. ' FROM #__phocagallery_fb_users AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$items = $db->loadObjectList();

		// TO DO - check for other views than category edit
		/*$view 	= JFactory::getApplication()->getInput()->get( 'view' );
		$catId	= -1;
		if ($view == 'phocagalleryc') {
			$id 	= $this->form->getValue('id'); // id of current category
			if ((int)$id > 0) {
				$catId = $id;
			}
		}*/


		$fieldId = $this->element['fieldid'] ? $this->element['fieldid'] : '';

		$link = 'index.php?option=com_phocagallery&amp;view=phocagalleryfba&amp;tmpl=component&amp;field=jform_'.$fieldId.'&amp;uid=';
		$js = 'document.getElementById(\'pglinktoalbum\').href = \''.$link.'\' + this.value';



		array_unshift($items, HTMLHelper::_('select.option', '', '- '.Text::_('COM_PHOCAGALLERY_SELECT_FB_USER').' -', 'value', 'text'));

		return HTMLHelper::_('select.genericlist',  $items,  $this->name, 'class="form-control" onchange="'.$js.'"', 'value', 'text', $this->value, $this->id );
	}
}
?>
