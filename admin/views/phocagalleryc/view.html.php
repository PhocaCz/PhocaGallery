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
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
jimport( 'joomla.application.component.view' );
phocagalleryimport( 'phocagallery.access.access' );
phocagalleryimport( 'phocagallery.rate.ratecategory' );
phocagalleryimport( 'phocagallery.facebook.api' );

class PhocaGalleryCpViewPhocaGalleryC extends HtmlView
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

		$this->t	= PhocaGalleryUtils::setVars('c');
		$this->r	= new PhocaGalleryRenderAdminview();


		//$this->item->accessuserid = PhocaGalleryUtils::toArray($this->item->accessuserid);
		//$this->item->accessuserid = explode(',', $this->item->accessuserid);



		$mainframe	= Factory::getApplication();
		$db			= Factory::getDBO();
		$uri 		= \Joomla\CMS\Uri\Uri::getInstance();
		$user 		= Factory::getUser();
		$model		= $this->getModel();
		$editor 	= \Joomla\CMS\Editor\Editor::getInstance();
		$paramsC 	= ComponentHelper::getParams('com_phocagallery');

		$this->t['enablepicasaloading'] = $paramsC->get( 'enable_picasa_loading', 1 );

		//JHtml::_('behavior.calendar');


		//Data from model
		//$this->item	=& $this->get('Data');

		//Image button
	/*	$link = 'index.php?option=com_phocagallery&amp;view=phocagalleryf&amp;tmpl=component';

		$button = new CMSObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', Text::_('COM_PHOCAGALLERY_FOLDER'));
		$button->set('name', 'image');
		$button->set('modalname', 'modal-button');
		$button->set('options', "{handler: 'iframe', size: {x: 620, y: 400}}");*/

		$lists 	= array();
		$isNew	= ((int)$this->item->id == 0);

		// Edit or Create?
		if (!$isNew) {
			$model->checkout( $user->get('id') );
		} else {
			// initialise new record
			$this->item->approved 		= 1;
			$this->item->published 		= 1;
			$this->item->order 			= 0;
			$this->item->access			= 0;
		}

		$this->addToolbar();

		parent::display($tpl);
	}


	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagallerycs.php';
		Factory::getApplication()->getInput()->set('hidemainmenu', true);
		$bar 		= Toolbar::getInstance('toolbar');
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaGalleryCsHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		$paramsC 	= ComponentHelper::getParams('com_phocagallery');



		$text = $isNew ? Text::_( 'COM_PHOCAGALLERY_NEW' ) : Text::_('COM_PHOCAGALLERY_EDIT');
		ToolbarHelper::title(   Text::_( 'COM_PHOCAGALLERY_CATEGORY' ).': <small><small>[ ' . $text.' ]</small></small>' , 'folder');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocagalleryc.apply', 'JToolbar_APPLY');
			ToolbarHelper::save('phocagalleryc.save', 'JToolbar_SAVE');
			ToolbarHelper::addNew('phocagalleryc.save2new', 'JToolbar_SAVE_AND_NEW');
			/*$this->t['enablepicasaloading'] = $paramsC->get( 'enable_picasa_loading', 1 );
			///$this->t['enablefacebookloading'] = $paramsC->get( 'enable_facebook_loading', 1 );
			if($this->t['enablepicasaloading'] == 1){
				ToolbarHelper::custom('phocagalleryc.loadextimgp', 'loadextp.png', '', 'COM_PHOCAGALLERY_P_IMPORT' , false);
			}*/

			ToolbarHelper::custom('phocagalleryc.loadextimgi', 'loadexti.png', '', 'COM_PHOCAGALLERY_I_IMPORT' , false);
///			if($this->t['enablefacebookloading'] == 1){
				///JToolbarHelper::custom('phocagalleryc.loadextimgf', 'loadextf.png', '', 'COM_PHOCAGALLERY_FB_IMPORT' , false);
				///JToolbarHelper::custom('phocagalleryc.uploadextimgf', 'uploadextf.png', '', 'COM_PHOCAGALLERY_FB_EXPORT' , false);
///			}
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolbarHelper::custom('phocagalleryc.save2copy', 'copy.png', 'copy_f2.png', 'JToolbar_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			ToolbarHelper::cancel('phocagalleryc.cancel', 'JToolbar_CANCEL');
		}
		else {
			ToolbarHelper::cancel('phocagalleryc.cancel', 'JToolbar_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}
}
?>
