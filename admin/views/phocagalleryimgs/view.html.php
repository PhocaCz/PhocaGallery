<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die();

use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\File;
jimport( 'joomla.application.component.view' );
phocagalleryimport('phocagallery.library.library');
phocagalleryimport('phocagallery.render.renderdetailwindow');


class PhocaGalleryCpViewPhocaGalleryImgs extends HtmlView
{

	protected $items;
	protected $items_thumbnail;
	protected $pagination;
	protected $state;
	protected $button;
	protected $t;
	protected $r;
	public $filterForm;
	public $activeFilters;
	//public $_context 	= 'com_phocagallery.phocagalleryimg';

	function display($tpl = null) {


		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->r = new PhocaGalleryRenderAdminViews();
		$this->t			= PhocaGalleryUtils::setVars('img');

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->catid][] = $item->id;
		}


		$this->processImages();



		$document	= Factory::getDocument();



		$params 	= ComponentHelper::getParams('com_phocagallery');


		$this->t['enablethumbcreation']			= $params->get('enable_thumb_creation', 1 );
		$this->t['enablethumbcreationstatus'] 	= PhocaGalleryRenderAdmin::renderThumbnailCreationStatus((int)$this->t['enablethumbcreation']);



		/*$app	= JFactory::getApplication();
		$uri		= \Joomla\CMS\Uri\Uri::getInstance();

		$db		    = JFactory::getDBO();*/

		$this->t['notapproved'] 	=  $this->get( 'NotApprovedImage' );

		// Button
		/*
		$this->button = new CMSObject();
		$this->button->set('modal', true);
		$this->button->set('methodname', 'modal-button');
		//$this->button->set('link', $link);
		$this->button->set('text', Text::_('COM_PHOCAGALLERY_DISPLAY_IMAGE_DETAIL'));
		//$this->button->set('name', 'image');
		$this->button->set('modalname', 'modal_phocagalleryimgs');
		$this->button->set('options', "{handler: 'image', size: {x: 200, y: 150}}");*/


		$library 			= PhocaGalleryLibrary::getLibrary();
		$libraries			= array();
		$btn 				= new PhocaGalleryRenderDetailWindow();
		$btn->popupWidth 	= '640';
		$btn->popupHeight 	= '480';
		$btn->backend		= 1;

		$btn->setButtons(14, $libraries, $library);
		$this->button = $btn->getB1();


		$this->addToolbar();
		parent::display($tpl);
	}



	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagalleryimgs.php';



		$state	= $this->get('State');
		$canDo	= PhocaGalleryImgsHelper::getActions($state->get('filter.image_id'));
		$user  = Factory::getUser();
		$bar = Toolbar::getInstance('toolbar');
		ToolbarHelper::title( Text::_('COM_PHOCAGALLERY_IMAGES'), 'image.png' );
		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew( 'phocagalleryimg.add','JToolbar_NEW');
			ToolbarHelper::custom( 'phocagallerym.edit', 'multiple.png', '', 'COM_PHOCAGALLERY_MULTIPLE_ADD' , false);
		}
		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList('phocagalleryimg.edit','JToolbar_EDIT');
		}

		if ($canDo->get('core.create')) {

			/*
			$bar->appendButton( 'Custom', '<a href="#" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert(\''.JText::_('COM_PHOCAGALLERY_WARNING_RECREATE_MAKE_SELECTION').'\');}else{if(confirm(\''.JText::_('COM_PHOCAGALLERY_WARNING_RECREATE_THUMBNAILS').'\')){Joomla.submitbutton(\'phocagalleryimg.recreate\');}}" class="toolbar"><span class="icon-32-recreate" title="'.JText::_('COM_PHOCAGALLERY_RECREATE_THUMBS').'" type="Custom"></span>'.JText::_('COM_PHOCAGALLERY_RECREATE').'</a>');*/

			$dhtml = '<joomla-toolbar-button id="toolbar-recreate-thumbnails" list-selection>';
			$dhtml .= '<button class="btn btn-small" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert(\''.Text::_('COM_PHOCAGALLERY_WARNING_RECREATE_MAKE_SELECTION').'\');}else{if(confirm(\''.Text::_('COM_PHOCAGALLERY_WARNING_RECREATE_THUMBNAILS').'\')){Joomla.submitbutton(\'phocagalleryimg.recreate\');}}" ><i class="icon-recreate" title="'.Text::_('COM_PHOCAGALLERY_RECREATE_THUMBS').'"></i> '.Text::_('COM_PHOCAGALLERY_RECREATE_THUMBS').'</button>';
			$dhtml .= '</joomla-toolbar-button>';
			$bar->appendButton('Custom', $dhtml);

		}


		if ($canDo->get('core.edit.state')) {

			ToolbarHelper::divider();
			ToolbarHelper::custom('phocagalleryimgs.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			ToolbarHelper::custom('phocagalleryimgs.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);
			ToolbarHelper::custom( 'phocagalleryimgs.approve', 'approve.png', '',  'COM_PHOCAGALLERY_APPROVE' , true);
			ToolbarHelper::custom( 'phocagalleryimgs.disapprove', 'disapprove.png', '',  'COM_PHOCAGALLERY_NOT_APPROVE' , true);
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList( Text::_( 'COM_PHOCAGALLERY_WARNING_DELETE_ITEMS' ), 'phocagalleryimgs.delete', 'COM_PHOCAGALLERY_DELETE');
		}

		// Add a batch button
		if ($user->authorise('core.edit'))
		{

			//HTMLHelper::_('bootstrap.renderModal', 'collapseModal');
			$title = Text::_('JToolbar_BATCH');
			$dhtml = '<joomla-toolbar-button id="toolbar-batch" list-selection>';
			$dhtml .= "<button data-bs-toggle=\"modal\" data-bs-target=\"#collapseModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$dhtml .= '</joomla-toolbar-button>';

			$bar->appendButton('Custom', $dhtml, 'batch');




		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}


	protected function processImages() {

		if (!empty($this->items)) {

			$params							= ComponentHelper::getParams( 'com_phocagallery' );
			$pagination_thumbnail_creation 	= $params->get( 'pagination_thumbnail_creation', 0 );
			$clean_thumbnails 				= $params->get( 'clean_thumbnails', 0 );


			//Server doesn't have CPU power
			//we do thumbnail for all images - there is no pagination...
			//or we do thumbanil for only listed images
			if (empty($this->items_thumbnail)) {
				if ($pagination_thumbnail_creation == 1) {
					$this->items_thumbnail 	= $this->items;
				} else {
					$this->items_thumbnail	= $this->get('ItemsThumbnail');

				}
			}

			// - - - - - - - - - - - - - - - - - - - -
			// Check if the file stored in database is on the server. If not please refer to user
			// Get filename from every object there is stored in database
			// file - abc.img, file_no - folder/abc.img
			// Get folder variables from Helper
			$path 				= PhocaGalleryPath::getPath();
			$origPath 			= $path->image_abs;
			$origPathServer 	= str_replace('\\', '/', $path->image_abs);

			//-----------------------------------------
			//Do all thumbnails no limit no pagination
			if (!empty($this->items_thumbnail)) {
				foreach ($this->items_thumbnail as $key => $value) {
					$fileOriginalThumb = PhocaGalleryFile::getFileOriginal($value->filename);
					//Let the user know that the file doesn't exists and delete all thumbnails
					if (File::exists($fileOriginalThumb)) {
						$refreshUrlThumb = 'index.php?option=com_phocagallery&view=phocagalleryimgs';
						$fileThumb = PhocaGalleryFileThumbnail::getOrCreateThumbnail( $value->filename, $refreshUrlThumb, 1, 1, 1);
					}
				}
			}

			$this->items_thumbnail = null; // delete data to reduce memory

			//Only the the site with limitation or pagination...
			if (!empty($this->items)) {
				foreach ($this->items as $key => $value) {
					$fileOriginal = PhocaGalleryFile::getFileOriginal($value->filename);
					//Let the user know that the file doesn't exists and delete all thumbnails

					if (!File::exists($fileOriginal)) {
						$this->items[$key]->filename = Text::_( 'COM_PHOCAGALLERY_IMG_FILE_NOT_EXISTS' );
						$this->items[$key]->fileoriginalexist = 0;
					} else {
						//Create thumbnails small, medium, large
						$refresh_url 	= 'index.php?option=com_phocagallery&view=phocagalleryimgs';
						$fileThumb 		= PhocaGalleryFileThumbnail::getOrCreateThumbnail($value->filename, $refresh_url, 1, 1, 1);

						$this->items[$key]->linkthumbnailpath 	= $fileThumb['thumb_name_s_no_rel'];
						$this->items[$key]->fileoriginalexist = 1;
					}
				}
			}

			//Clean Thumbs Folder if there are thumbnail files but not original file
			if ($clean_thumbnails == 1) {
				PhocaGalleryFileFolder::cleanThumbsFolder();
			}
		}
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
			'a.title' 		=> Text::_('COM_PHOCAGALLERY_TITLE'),
			'a.filename'	=> Text::_('COM_PHOCAGALLERY_FILENAME'),
			'a.published' 	=> Text::_('COM_PHOCAGALLERY_PUBLISHED'),
			'a.approved' 	=> Text::_('COM_PHOCAGALLERY_APPROVED'),
			'category_id' 	=> Text::_('COM_PHOCAGALLERY_CATEGORY'),
			'category_owner_id'=> Text::_('COM_PHOCAGALLERY_OWNER'),
			'uploadusername'=> Text::_('COM_PHOCAGALLERY_UPLOADED_BY'),
			'ratingavg' 		=> Text::_('COM_PHOCAGALLERY_RATING'),
			'a.hits' 		=> Text::_('COM_PHOCAGALLERY_HITS'),
			'language' 		=> Text::_('JGRID_HEADING_LANGUAGE'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}
}
?>
