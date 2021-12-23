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
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

jimport( 'joomla.application.component.view' );
phocagalleryimport('phocagallery.library.library');
phocagalleryimport('phocagallery.render.renderdetailwindow');

jimport( 'joomla.filesystem.file' );
class PhocaGalleryCpViewPhocaGalleryUsers extends HtmlView
{

	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	protected $r;
	public $filterForm;
	public $activeFilters;


	function display($tpl = null) {

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->r = new PhocaGalleryRenderAdminViews();
		$this->t			= PhocaGalleryUtils::setVars('user');

		foreach ($this->items as &$item) {
			$this->ordering[0][] = $item->id;
		}

		$path 							= PhocaGalleryPath::getPath();
		$this->t['avatarpathabs']	= $path->avatar_abs . '/thumbs/phoca_thumb_s_';
		$this->t['avatarpathrel']	= $path->avatar_rel . 'thumbs/phoca_thumb_s_';
		$this->t['avtrpathrel']		= $path->avatar_rel;


		$document	= Factory::getDocument();


		// Button
		/*
		$this->button = new CMSObject();
		$this->button->set('modal', true);
		$this->button->set('methodname', 'modal-button');
		//$this->button->set('link', $link);
		$this->button->set('text', Text::_('COM_PHOCAGALLERY_DISPLAY_IMAGE_DETAIL'));
		//$this->button->set('name', 'image');
		$this->button->set('modalname', 'modal_phocagalleryusers');
		$this->button->set('options', "{handler: 'image', size: {x: 200, y: 150}}");*/

		$library 			= PhocaGalleryLibrary::getLibrary();
		$libraries			= array();
		$btn 				= new PhocaGalleryRenderDetailWindow();
		$btn->popupWidth 	= '640';
		$btn->popupHeight 	= '480';
		$btn->backend		= 1;

		$btn->setButtons(14, $libraries, $library);
		$this->button = $btn->getB1();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);


	}

	function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagalleryusers.php';
		$state	= $this->get('State');
		$canDo	= PhocaGalleryUsersHelper::getActions($state->get('filter.category_id'));

		ToolbarHelper::title( Text::_( 'COM_PHOCAGALLERY_USERS' ), 'users' );

		if ($canDo->get('core.edit.state')) {

			ToolbarHelper::custom('phocagalleryusers.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			ToolbarHelper::custom('phocagalleryusers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);
			ToolbarHelper::custom( 'phocagalleryusers.approve', 'approve.png', '', 'COM_PHOCAGALLERY_APPROVE' , true);
			ToolbarHelper::custom( 'phocagalleryusers.disapprove', 'disapprove.png', '', 'COM_PHOCAGALLERY_NOT_APPROVE' , true);
			ToolbarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			$bar = Toolbar::getInstance('toolbar');
		/*$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(confirm(\''.addslashes(JText::_('COM_PHOCAGALLERY_WARNING_AUTHORIZE_ALL')).'\')){Joomla.submitbutton(\'phocagalleryusers.approveall\');}" class="toolbar"><span class="icon-32-authorizeall" title="'.JText::_('COM_PHOCAGALLERY_APPROVE_ALL').'" type="Custom"></span>'.JText::_('COM_PHOCAGALLERY_APPROVE_ALL').'</a>');*/

			$dhtml = '<button class="btn btn-small" onclick="javascript:if(confirm(\''.addslashes(Text::_('COM_PHOCAGALLERY_WARNING_AUTHORIZE_ALL')).'\')){Joomla.submitbutton(\'phocagalleryusers.approveall\');}" ><i class="icon-authorizeall" title="'.Text::_('COM_PHOCAGALLERY_APPROVE_ALL').'"></i> '.Text::_('COM_PHOCAGALLERY_APPROVE_ALL').'</button>';
			$bar->appendButton('Custom', $dhtml);


			ToolbarHelper::divider();
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList(  'COM_PHOCAGALLERY_WARNING_DELETE_ITEMS_AVATAR', 'phocagalleryusers.delete', 'COM_PHOCAGALLERY_DELETE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
			'ua.username' 	=> Text::_('COM_PHOCAGALLERY_USER'),
			'a.published' 	=> Text::_('COM_PHOCAGALLERY_PUBLISHED'),
			'a.approved' 	=> Text::_('COM_PHOCAGALLERY_APPROVED'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}
}
?>
