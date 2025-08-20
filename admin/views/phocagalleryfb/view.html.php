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
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
jimport('joomla.application.component.view');
phocagalleryimport( 'phocagallery.facebook.fb' );
phocagalleryimport( 'phocagallery.facebook.fbsystem' );


class PhocaGalleryCpViewPhocaGalleryFb extends HtmlView
{
	protected $item;
	protected $form;
	protected $state;
	protected $t;
	protected $r;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{


		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		$this->t	= PhocaGalleryUtils::setVars('fb');
		$this->r	= new PhocaGalleryRenderAdminview();


		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagalleryfbs.php';
		Factory::getApplication()->getInput()->set('hidemainmenu', true);
		$bar 		= Toolbar::getInstance('toolbar');
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaGalleryFbsHelper::getActions( $this->item->id);
		$paramsC 	= ComponentHelper::getParams('com_phocagallery');

		$text = $isNew ? Text::_( 'COM_PHOCAGALLERY_NEW' ) : Text::_('COM_PHOCAGALLERY_EDIT');
		ToolbarHelper::title(   Text::_( 'COM_PHOCAGALLERY_FB_USER' ).': <small><small>[ ' . $text.' ]</small></small>' , 'user');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocagalleryfb.apply', 'JToolbar_APPLY');
			ToolbarHelper::save('phocagalleryfb.save', 'JToolbar_SAVE');
		}

		ToolbarHelper::cancel('phocagalleryfb.cancel', 'JToolbar_CLOSE');
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}

}

?>
