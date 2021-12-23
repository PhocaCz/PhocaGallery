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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );

class PhocaGalleryCpViewPhocaGalleryTag extends HtmlView
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

		$this->t	= PhocaGalleryUtils::setVars('tag');
		$this->r	= new PhocaGalleryRenderAdminview();


		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagallerytags.php';
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaGalleryTagsHelper::getActions($this->state->get('filter.tag_id'), $this->item->id);
		//$paramsC 	= JComponentHelper::getParams('COM_PHOCADOWNLOAD');



		$text = $isNew ? Text::_( 'COM_PHOCAGALLERY_NEW' ) : Text::_('COM_PHOCAGALLERY_EDIT');
		ToolbarHelper::title(   Text::_( 'COM_PHOCAGALLERY_TAG' ).': <small><small>[ ' . $text.' ]</small></small>' , 'tags.png');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocagallerytag.apply', 'JToolbar_APPLY');
			ToolbarHelper::save('phocagallerytag.save', 'JToolbar_SAVE');
			ToolbarHelper::addNew('phocagallerytag.save2new', 'JToolbar_SAVE_AND_NEW');
		}

		if (empty($this->item->id))  {
			ToolbarHelper::cancel('phocagallerytag.cancel', 'JToolbar_CANCEL');
		}
		else {
			ToolbarHelper::cancel('phocagallerytag.cancel', 'JToolbar_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}
}
?>
