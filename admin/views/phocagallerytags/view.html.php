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

class phocagalleryCpViewPhocaGalleryTags extends HtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $r;
	protected $t;
	public $filterForm;
	public $activeFilters;

	function display($tpl = null) {

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->r = new PhocaGalleryRenderAdminViews();
		$this->t			= PhocaGalleryUtils::setVars('tag');

		foreach ($this->items as &$item) {
			$this->ordering[0][] = $item->id;
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

		require_once JPATH_COMPONENT.'/helpers/phocagallerytags.php';

		$state	= $this->get('State');
		$canDo	= PhocaGalleryTagsHelper::getActions($state->get('filter.tag_id'));

		ToolbarHelper::title( Text::_( 'COM_PHOCAGALLERY_TAGS' ), 'tags.png' );

		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew('phocagallerytag.add','JToolbar_NEW');
		}

		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList('phocagallerytag.edit','JToolbar_EDIT');
		}
		if ($canDo->get('core.edit.state')) {

			ToolbarHelper::divider();
			ToolbarHelper::custom('phocagallerytags.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			ToolbarHelper::custom('phocagallerytags.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList( 'COM_PHOCAGALLERY_WARNING_DELETE_ITEMS', 'phocagallerytags.delete', 'COM_PHOCAGALLERY_DELETE');
		}
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
			'a.title' 		=> Text::_('COM_PHOCAGALLERY_TITLE'),
			'a.published' 	=> Text::_('COM_PHOCAGALLERY_PUBLISHED'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}
}
?>
