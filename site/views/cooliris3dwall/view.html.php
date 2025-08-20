<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
jimport( 'joomla.application.component.view' );
class PhocaGalleryViewCooliris3DWall extends HtmlView
{
	public $t;
	protected $params;

	function display($tpl = null) {

		$app				= Factory::getApplication();
		$document			= Factory::getDocument();
		$uri 				= \Joomla\CMS\Uri\Uri::getInstance();
		$menus				= $app->getMenu();
		$menu				= $menus->getActive();
		$this->params		= $app->getParams();
		$this->t['path']	= PhocaGalleryPath::getPath();
		$model				= $this->getModel();

		// PARAMS
		$this->t['displaycatnametitle'] 			= $this->params->get( 'display_cat_name_title', 1 );
		$display_cat_name_breadcrumbs 				= $this->params->get( 'display_cat_name_breadcrumbs', 1 );
		$this->t['showpageheading'] 				= $this->params->get( 'show_page_heading', 1 );
		$this->t['cooliris3d_wall_width']		= $this->params->get( 'cooliris3d_wall_width', 600 );
		$this->t['cooliris3d_wall_height']		= $this->params->get( 'cooliris3d_wall_height', 370 );
		$this->t['gallerymetakey'] 				= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 				= $this->params->get( 'gallery_metadesc', '' );
		$this->t['enablecustomcss']				= $this->params->get( 'enable_custom_css', 0);
		$this->t['customcss']					= $this->params->get( 'custom_css', '');

		$idCategory									= $app->getInput()->get('id', 0, 'int');

		// CSS
		HTMLHelper::stylesheet('media/com_phocagallery/css/phocagallery.css' );
		if ($this->t['enablecustomcss'] == 1) {
			HTMLHelper::stylesheet('media/com_phocagallery/css/phocagallerycustom.css' );
			PhocaGalleryRenderFront::displayCustomCSS($this->t['customcss']);
		}

		if ((int)$idCategory > 0) {
			$category	= $model->getCategory($idCategory);
			$this->_prepareDocument($category);
			// Define image tag attributes
			/*if (!empty ($category->image)) {
				$attribs['align'] = '"'.$category->image_position.'"';
				$attribs['hspace'] = '"6"';
				$this->t['image'] = HTMLHelper::_('image', 'images/stories/'.$category->image, '', $attribs);
			}*/

			$this->_addBreadCrumbs($category, isset($menu->query['id']) ? $menu->query['id'] : 0, $display_cat_name_breadcrumbs);

			// ASIGN
			$this->t['display_category']		= 1;
			$this->tmpl =		$this->t;
			$this->category =	$category;
			//$this->params = 	$this->params;
		} else {
			$this->t['display_category']		= 0;
			$this->tmpl =		$this->t;
		}
			parent::display($tpl);
	}

	protected function _prepareDocument($category) {

		$app		= Factory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		//$this->params		= $app->getParams();
		$title 		= null;

		$this->t['gallerymetakey'] 		= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 		= $this->params->get( 'gallery_metadesc', '' );
		$this->t['displaycatnametitle'] 			= $this->params->get( 'display_cat_name_title', 1 );

		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->get('sitename'));
		} else if ($app->get('sitename_pagetitles', 0) == 1) {
			$title = Text::sprintf('JPAGETITLE', htmlspecialchars_decode($app->get('sitename')), $title);

			if ($this->t['display_cat_name_title'] == 1 && isset($this->category->title) && $this->category->title != '') {
				$title = $title .' - ' .  $this->category->title;
			}

		} else if ($app->get('sitename_pagetitles', 0) == 2) {

			if ($this->t['display_cat_name_title'] == 1 && isset($this->category->title) && $this->category->title != '') {
				$title = $title .' - ' .  $this->category->title;
			}

			$title = Text::sprintf('JPAGETITLE', $title, htmlspecialchars_decode($app->get('sitename')));
		}

		$this->document->setTitle($title);

		if ($category->metadesc != '') {
			$this->document->setDescription($category->metadesc);
		} else if ($this->t['gallerymetadesc'] != '') {
			$this->document->setDescription($this->t['gallerymetadesc']);
		} else if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		}

		if ($category->metakey != '') {
			$this->document->setMetadata('keywords', $category->metakey);
		} else if ($this->t['gallerymetakey'] != '') {
			$this->document->setMetadata('keywords', $this->t['gallerymetakey']);
		} else if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->get('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}

		/*if ($app->get('MetaAuthor') == '1') {
			$this->document->setMetaData('author', $this->item->author);
		}

		/*$mdata = $this->item->metadata->toArray();
		foreach ($mdata as $k => $v) {
			if ($v) {
				$this->document->setMetadata($k, $v);
			}
		}*/

	}

	/**
	 * Method to add Breadcrubms in Phoca Gallery
	 * @param array $category Object array of Category
	 * @param int $rootId Id of Root Category
	 * @param int $displayStyle Displaying of Breadcrubm - Nothing, Category Name, Menu link with Name
	 * @return string Breadcrumbs
	 */
	function _addBreadCrumbs($category, $rootId, $displayStyle) {
	    $app	= Factory::getApplication();

	    $pathway 		= $app->getPathway();
		$pathWayItems 	= $pathway->getPathWay();
		$lastItemIndex 	= count($pathWayItems) - 1;
		switch ($displayStyle)  {
			case 0:	// 0 - only menu link
				// do nothing
				break;
			case 1:	// 1 - menu link with category name
				// replace the last item in the breadcrumb (menu link title) with the current value plus the category title
				$pathway->setItemName($lastItemIndex, $pathWayItems[$lastItemIndex]->name . ' - ' . $category->title);
				break;
			case 2:	// 2 - only category name
				// replace the last item in the breadcrumb (menu link title) with the category title
				$pathway->setItemName($lastItemIndex, $category->title);
				break;
		}
	}
}
?>
