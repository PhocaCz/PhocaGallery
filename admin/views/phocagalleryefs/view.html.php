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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );

class PhocaGalleryCpViewPhocaGalleryEfs extends HtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $r;
	protected $t;
	public $filterForm;
	public $activeFilters;


	function display($tpl = null) {

		$model				= $this->getModel();
		$model->checkItems();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');


		$this->r = new PhocaGalleryRenderAdminViews();
		$this->t			= PhocaGalleryUtils::setVars('ef');

		foreach ($this->items as &$item) {
			$this->ordering[$item->type][] = $item->id;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);

	}

	function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagalleryefs.php';

		$state	= $this->get('State');
		$canDo	= PhocaGalleryEfsHelper::getActions($state->get('filter.category_id'));

		ToolbarHelper::title( Text::_( 'COM_PHOCAGALLERY_STYLES' ), 'eye' );

		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew( 'phocagalleryef.add','JToolbar_NEW');
		}

		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList('phocagalleryef.edit','JToolbar_EDIT');
		}
		if ($canDo->get('core.edit.state')) {

			ToolbarHelper::divider();
			ToolbarHelper::custom('phocagalleryefs.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			ToolbarHelper::custom('phocagalleryefs.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList(  Text::_( 'COM_PHOCAGALLERY_WARNING_DELETE_ITEMS' ), 'phocagalleryefs.delete', 'COM_PHOCAGALLERY_DELETE');
		}
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('COM_PHOCAGALLERY_ORDERING'),
			'a.title'	 	=> Text::_('COM_PHOCAGALLERY_TITLE'),
			'a.filename'	=> Text::_('COM_PHOCAGALLERY_FILENAME'),
			'a.published'	=> Text::_('COM_PHOCAGALLERY_PUBLISHED'),
			'a.type'	 	=> Text::_('COM_PHOCAGALLERY_TYPE'),
			'language' 		=> Text::_('JGRID_HEADING_LANGUAGE'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}
}
?>
