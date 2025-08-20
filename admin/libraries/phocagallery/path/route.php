<?php
/**
 * @package   Phoca Gallery
 * @author    Jan Pavelka - https://www.phoca.cz
 * @copyright Copyright (C) Jan Pavelka https://www.phoca.cz
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 and later
 * @cms       Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;

jimport('joomla.application.component.helper');

class PhocaGalleryRoute
{
	public static function getCategoriesRoute() {

		// TEST SOLUTION
	/*	$app 		= Factory::getApplication();
		$menu 		= $app->getMenu();
		$active 	= $menu->getActive();


		$activeId 	= 0;
		if (isset($active->id)){
			$activeId    = $active->id;
		}

		$itemId 	= 0;
		/* There cannot be $item->id yet
		// 1) get standard item id if exists
		if (isset($item->id)) {
			$itemId = (int)$item->id;
		}*//*

		$option			= $app->getInput()->get( 'option', '', 'string' );
		$view			= $app->getInput()->get( 'view', '', 'string' );
		if ($option == 'com_phocagallery' && $view == 'category') {
			if ((int)$activeId > 0) {
				// 2) if there are two menu links, try to select the one active
				$itemId = $activeId;
			}
		}*/

		$needles = array(
			'categories' => ''
		);

		$link = 'index.php?option=com_phocagallery&view=categories';

		if($item = self::_findItem($needles, 1)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}

			if (isset($item->id)) {
				$link .= '&Itemid='.(int)$item->id;;
			}

			// TEST SOLUTION
			/*if ((int)$itemId > 0) {
				$link .= '&Itemid='.(int)$itemId;
			} else if (isset($item->id) && ((int)$item->id > 0)) {
				$link .= '&Itemid='.$item->id;
			}*/

			// $item->id should be a "categories view" and it should have preference to category view
			// so first we check item->id then itemId

			// 1) there can be two categories view, when yes, first set itemId then item->id
			// 2) but when there is one category view, and one categories view - first select item->id (categories view)
			// 3) then select itemid even we don't know if categories or category view

			/*if ((int)$itemId > 0 && isset($active->query['view']) && $active->query['view'] == 'categories') {
				$link .= '&Itemid='.(int)$itemId;
			} else if (isset($item->id) && ((int)$item->id > 0)) {
				$link .= '&Itemid='.$item->id;
			} else if ((int)$itemId > 0) {
				$link .= '&Itemid='.(int)$itemId;
			}*/
		};


		return $link;
	}

	public static function getCategoryRoute($catid, $catidAlias = '') {

		// TEST SOLUTION
		/*$app 		= Factory::getApplication();
		$menu 		= $app->getMenu();
		$active 	= $menu->getActive();
		$option		= $app->getInput()->get( 'option', '', 'string' );


		$activeId 	= 0;
		$notCheckId	= 1;
		if (isset($active->id)){
			$activeId    = $active->id;
		}
		if ((int)$activeId > 0 && $option == 'com_phocagallery') {

			$needles 	= array(
				'category' => (int)$catid,
				'categories' => (int)$activeId
			);
			$notCheckId = 0;// when categories view, do not check id
			// we need to check the ID - there can be more menu links (to categories, to category)
		} else {
			$needles = array(
				'category' => (int)$catid,
				'categories' => ''
			);
			$notCheckId = 0;
		}

		if ($catidAlias != '') {
			$catid = $catid . ':' . $catidAlias;
		}

		//Create the link
		$link = 'index.php?option=com_phocagallery&view=category&id='. $catid;

		if($item = PhocaGalleryRoute::_findItem($needles, $notCheckId)) {

			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			if (isset($item->id) && ((int)$item->id > 0)) {
				$link .= '&Itemid='.$item->id;
			}
		};*/

		$needles = array(
			'category' => (int)$catid,
			'categories' => ''
		);

		if ($catidAlias != '') {
			$catid = $catid . ':' . $catidAlias;
		}

		//Create the link
		$link = 'index.php?option=com_phocagallery&view=category&id='. $catid;

		if($item = self::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			if(isset($item->id)) {
				$link .= '&Itemid='.$item->id;
			}
		};

		return $link;
	}

	public static function getFeedRoute($view = 'categories', $catid = 0, $catidAlias = '') {

		if ($view == 'categories') {
			$needles = array(
				'categories' => ''
			);
			$link = 'index.php?option=com_phocagallery&view=categories&format=feed';

		} else if ($view == 'category') {
			if ($catid > 0) {
				$needles = array(
					'category' => (int) $catid,
					'categories' => ''
				);
				if ($catidAlias != '') {
					$catid = (int)$catid . ':' . $catidAlias;
				}

				$link = 'index.php?option=com_phocagallery&view=category&format=feed&id='.$catid;

			} else {
				$needles = array(
				'categories' => ''
				);
				$link = 'index.php?option=com_phocagallery&view=categories&format=feed';
			}
		} else {
			$needles = array(
				'categories' => ''
			);
			$link = 'index.php?option=com_phocagallery&view=feed&format=feed';
		}


		if($item = PhocaGalleryRoute::_findItem($needles, 1)) {

			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			if (isset($item->id) && ((int)$item->id > 0)) {
				$link .= '&Itemid='.$item->id;
			}
		};

		return $link;
	}







	public static function getCategoryRouteByTag($tagId) {
		$needles = array(
			'category' => '',
			//'section'  => (int) $sectionid,
			'categories' => ''
		);

		$db = Factory::getDBO();

		$query = 'SELECT a.id, a.title, a.link_ext, a.link_cat'
		.' FROM #__phocagallery_tags AS a'
		.' WHERE a.id = '.(int)$tagId
		.' ORDER BY a.id';

		$db->setQuery($query, 0, 1);
		$tag = $db->loadObject();



		//Create the link
		if (isset($tag->id)) {
			$link = 'index.php?option=com_phocagallery&view=category&id=0:category&tagid='.(int)$tag->id;
		} else {
			$link = 'index.php?option=com_phocagallery&view=category&id=0:category&tagid=0';
		}

		if($item = self::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}

			if (isset($item->id) && ((int)$item->id > 0)) {
				$link .= '&Itemid='.$item->id;
			}
		};

		return $link;
	}



	public static function getImageRoute($id, $catid = 0, $idAlias = '', $catidAlias = '', $type = 'detail', $suffix = '')
	{
		// TEST SOLUTION
		/*$app 		= Factory::getApplication();
		$menu 		= $app->getMenu();
		$active 	= $menu->getActive();
		$option		= $app->getInput()->get( 'option', '', 'string' );

		$activeId 	= 0;
		$notCheckId	= 0;
		if (isset($active->id)){
			$activeId    = $active->id;
		}

		if ((int)$activeId > 0 && $option == 'com_phocagallery') {

			$needles = array(
				'detail'  => (int) $id,
				'category' => (int) $catid,
				'categories' => (int)$activeId
			);
			$notCheckId	= 1;
		} else {
			$needles = array(
				'detail'  => (int) $id,
				'category' => (int) $catid,
				'categories' => ''
			);
			$notCheckId	= 0;
		}*/

		$needles = array(
			'detail'  => (int) $id,
			'category' => (int) $catid,
			'categories' => ''
		);


		if ($idAlias != '') {
			$id = $id . ':' . $idAlias;
		}
		if ($catidAlias != '') {
			$catid = $catid . ':' . $catidAlias;
		}

		//Create the link

		switch ($type)
		{
			case 'detail':
				$link = 'index.php?option=com_phocagallery&view=detail&catid='. $catid .'&id='. $id;
				break;

			default:
				$link = '';
			break;
		}

		if ($item = self::_findItem($needles)) {
			if (isset($item->id) && ((int)$item->id > 0)) {
				$link .= '&Itemid='.$item->id;
			}
		}

		if ($suffix != '') {
			$link .= '&'.$suffix;
		}

		return $link;
	}

	protected static function _findItem($needles, $notCheckId = 0, $component = 'com_phocagallery') {


		$app		= Factory::getApplication();
		//$menus		= $app->getMenu('site', array()); // Problems in indexer
		$menus    = AbstractMenu::getInstance('site');
		$items		= $menus->getItems('component', $component);
		//$menu 		= $menus;//$app->getMenu();
		$active 	= $menus->getActive();
		$option		= $app->getInput()->get( 'option', '', 'string' );

		// Don't check ID for specific views. e.g. categories view does not have ID
		$notCheckIdArray =  array('categories');

		if(!$items) {
			$itemId =  $app->getInput()->get('Itemid', 0, 'int');
			if ($itemId > 0) {
				$item = new stdClass();
				$item->id = $itemId;
				return $item;
			}
			return null;
		}

		$match = null;
		// FIRST - test active menu link
		foreach($needles as $needle => $id) {
			if (isset($active->query['option']) && $active->query['option'] == $component
				&& isset($active->query['view']) && $active->query['view'] == $needle
				&& (in_array($needle, $notCheckIdArray) || (isset($active->query['id']) && $active->query['id'] == $id ))
			) {
				$match = $active;
			}
		}

		if(isset($match)) {
			return $match;
		}

		// SECOND - if not find in active, try to run other items
		//          ordered by function which calls this function - e.g. file, category, categories
		//          as last the categories view should be checked, it has no ID so we skip the checking
		//          of ID for categories view with OR: in_array($needle, $notCheckIdArray) ||
		foreach($needles as $needle => $id) {

			foreach($items as $item) {

				if (isset($item->query['option']) && $item->query['option'] == $component
					&& isset($item->query['view']) && $item->query['view'] == $needle
					&& (in_array($needle, $notCheckIdArray) || (isset($item->query['id']) && $item->query['id'] == $id ))
				) {
					$match = $item;
				}
			}

			if(isset($match)) {
				break;
			}
		}

		return $match;
	}
}
?>
