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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
jimport( 'joomla.application.component.view' );

class PhocaGalleryCpViewPhocaGalleryImg extends HtmlView
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $r;


	public function display($tpl = null) {

		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');

		$this->t	= PhocaGalleryUtils::setVars('img');
		$this->r	= new PhocaGalleryRenderAdminview();

		$params = ComponentHelper::getParams('com_phocagallery');

		$this->t['enablethumbcreation']			= $params->get('enable_thumb_creation', 1 );
		$this->t['enablethumbcreationstatus'] 	= PhocaGalleryRenderAdmin::renderThumbnailCreationStatus((int)$this->t['enablethumbcreation']);

		if ($this->item->extlink1 != '') {
			$extLink = PhocaGalleryRenderAdmin::renderExternalLink($this->item->extlink1);
			$this->form->setValue('extlink1link', '', $extLink[0]);
			$this->form->setValue('extlink1title', '', $extLink[1]);
			$this->form->setValue('extlink1target', '', $extLink[2]);
			$this->form->setValue('extlink1icon', '', $extLink[3]);
		}
		if ($this->item->extlink2 != '') {
			$extLink = PhocaGalleryRenderAdmin::renderExternalLink($this->item->extlink2);
			$this->form->setValue('extlink2link', '', $extLink[0]);
			$this->form->setValue('extlink2title', '', $extLink[1]);
			$this->form->setValue('extlink2target', '', $extLink[2]);
			$this->form->setValue('extlink2icon', '', $extLink[3]);
		}

		$this->addToolbar();
		parent::display($tpl);
	}


	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagalleryimgs.php';
		Factory::getApplication()->getInput()->set('hidemainmenu', true);
		$bar 		= Toolbar::getInstance('toolbar');
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaGalleryImgsHelper::getActions($this->state->get('filter.image_id'), $this->item->id);
		$paramsC 	= ComponentHelper::getParams('com_phocagallery');


		$text = $isNew ? Text::_( 'COM_PHOCAGALLERY_NEW' ) : Text::_('COM_PHOCAGALLERY_EDIT');
		ToolbarHelper::title(   Text::_( 'COM_PHOCAGALLERY_IMAGE' ).': <small><small>[ ' . $text.' ]</small></small>' , 'image');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocagalleryimg.apply', 'JToolbar_APPLY');
			ToolbarHelper::save('phocagalleryimg.save', 'JToolbar_SAVE');
			ToolbarHelper::addNew('phocagalleryimg.save2new', 'JToolbar_SAVE_AND_NEW');

		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolbarHelper::custom('phocagalleryc.save2copy', 'copy.png', 'copy_f2.png', 'JToolbar_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			ToolbarHelper::cancel('phocagalleryimg.cancel', 'JToolbar_CANCEL');
		}
		else {
			ToolbarHelper::cancel('phocagalleryimg.cancel', 'JToolbar_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}
}
?>
