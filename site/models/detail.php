<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;
jimport('joomla.application.component.model');
phocagalleryimport('phocagallery.access.access');
phocagalleryimport('phocagallery.ordering.ordering');

class PhocaGalleryModelDetail extends BaseDatabaseModel
{

	public $_category 			= null;

	function __construct() {
		parent::__construct();
		$app				= Factory::getApplication();
		$id = $app->input->get('id', 0, 'int');

		$this->setState('filter.language',$app->getLanguageFilter());
		$this->setId((int)$id);
	}

	function setId($id) {
		$this->_id			= $id;
		$this->_data		= null;
	}

	function &getData() {
		if (!$this->_loadData()) {
			$this->_initData();
		}
		return $this->_data;
	}

	function _loadData() {

		if (empty($this->_data)) {
			$app				= Factory::getApplication();
			$params				= $app->getParams();
			//$image_ordering		= $params->get( 'image_ordering', 1 );
			//$imageOrdering 		= PhocaGalleryOrdering::getOrderingString($image_ordering);



			$whereLang = '';
			if ($this->getState('filter.language')) {
				$whereLang =  ' AND a.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			}


			$selectUser	= ' ua.id AS userid, ua.username AS username, ua.name AS usernameno,';
			$leftUser 	= ' LEFT JOIN #__users AS ua ON ua.id = a.userid';


			$query = 'SELECT a.*, c.accessuserid as cataccessuserid, c.access as cataccess, c.owner_id as owner_id, '
					. $selectUser
					.' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug,'
					.' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug'
					.' FROM #__phocagallery AS a'
					.' LEFT JOIN #__phocagallery_categories AS c ON c.id = a.catid'
					. $leftUser
					.' WHERE a.id = '.(int) $this->_id
					. $whereLang
					.' AND a.published > 0'
					.' AND a.approved > 0';
					//.' ORDER BY a.'.$imageOrdering;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();

			return (boolean) $this->_data;
		}
		return true;
	}

	function _initData() {
		if (empty($this->_data)) {
			$this->_data = '';
			return (boolean) $this->_data;
		}
		return true;
	}

	function hit($id) {
		$table = Table::getInstance('phocagallery', 'Table');
		$table->hit($id);
		return true;
	}

	function rate($data) {
		$row = $this->getTable('phocagalleryimgvotes', 'Table');

		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}

		$row->date 		= gmdate('Y-m-d H:i:s');

		$row->published = 1;

		if (!$row->id) {
			$where = 'imgid = ' . (int) $row->imgid ;
			$row->ordering = $row->getNextOrder( $where );
		}

		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}

		if (!$row->store()) {
			$this->setError($row->getError());
			return false;
		}

		// Update the Vote Statistics
		phocagalleryimport('phocagallery.rate.rateimage');
		if (!PhocaGalleryRateImage::updateVoteStatistics( $data['imgid'])) {
			return false;
		}

		return true;
	}

	public function getThumbnails($id, $catid, $order) {

		$paramsC 				= ComponentHelper::getParams('com_phocagallery') ;
		$multibox_thubms_count	= $paramsC->get( 'multibox_thubms_count', 4 );

		// 1) Display only next thumbnails of current image - use the $order variable - to know next order values
		$thumbnails1 = array();
		$this->_db->setQuery($this->getThumbnailsQuery($id, $catid, $order), 0, (int)$multibox_thubms_count);
		$thumbnails1 = $this->_db->loadObjectList();
		$cT = count($thumbnails1);

		// 2) if there are no more next thumbnails, fill them with thumbnails from beginning
		$thumbnails2 = array();
		if ((int)$cT < (int)$multibox_thubms_count) {
			$newCount = (int)$multibox_thubms_count - (int)$cT;
			$this->_db->setQuery($this->getThumbnailsQuery($id, $catid, 0, 1, $thumbnails1), 0, (int)$newCount);
			$thumbnails2 = $this->_db->loadObjectList();
		}
		$thumbnails = array_merge((array)$thumbnails1, (array)$thumbnails2);

		return $thumbnails;
	}

	protected function getThumbnailsQuery($id, $catid, $order, $completion = 0, $currentThumbs = array()) {
		$paramsC 				= ComponentHelper::getParams('com_phocagallery') ;
		$image_ordering			= $paramsC->get( 'image_ordering', 1 );

		$wheres				= array();
		if ($this->getState('filter.language')) {
			$wheres[]	= ' a.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}
		$imageOrdering 		= PhocaGalleryOrdering::getOrderingString($image_ordering, 1);
		$published  		= ' AND a.published = 1';
		$published  		.= ' AND a.approved = 1';

		$wheres[]	= ' a.catid = '.(int) $catid;


		// Known issue - thumbnails with only larger order taken
		if ($completion == 1) {
			// with completion displaying, it can happen, that the same thumbnail as active thumbnail can be selected
			// because thumbnails are selected from beginning
			$wheres[]	= ' a.id <> '.(int) $id;//do not complete thumbnails with image which is displayed as active
			if (!empty($currentThumbs)) {
				foreach ($currentThumbs as $k => $v) {
					$wheres[] = ' a.id <> '.(int) $v->id;
				}
			}
		} else {
			// with standard displaying, it cannot happen, that the active image will be displayed,
			// as only images with larger order will be displayed
			$wheres[]	= ' a.ordering > '.(int) $order;
		}


		$query = 'SELECT a.id, a.extid, a.exts, a.filename, a.title, a.description, a.metadesc, cc.alias AS catalias, cc.accessuserid AS cataccessuserid, cc.access AS cataccess,'
			. ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(\':\', cc.id, cc.alias) ELSE cc.id END as catslug,'
			. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug'
			.' FROM #__phocagallery AS a'
			.' LEFT JOIN #__phocagallery_categories AS cc ON cc.id = a.catid'
			. ' WHERE ' . implode( ' AND ', $wheres )
			. $published
			. $imageOrdering['output'];

		return $query;

	}

	function comment($data) {

		$row = $this->getTable('phocagallerycommentimgs', 'Table');


		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}

		$row->date 		= gmdate('Y-m-d H:i:s');
		$row->published = 1;

		if (!$row->id) {
			$where = 'imgid = ' . (int) $row->imgid ;
			$row->ordering = $row->getNextOrder( $where );
		}

		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}

		if (!$row->store()) {
			$this->setError($row->getError());
			return false;
		}

		return true;
	}

	function getItemNext($ordering, $catid) {
		if (empty($this->_itemnext)) {
			$query				= $this->_getItemQueryOrdering( $ordering, $catid, 2 );
			$this->_itemnext	= $this->_getList( $query, 0 , 1 );

			if (empty($this->_itemnext)) {
				return null;
			}

		}
		return $this->_itemnext;
	}
	function getItemPrev($ordering, $catid) {
		if (empty($this->_itemprev)) {
			$query				= $this->_getItemQueryOrdering( $ordering, $catid, 1 );
			$this->_itemprev	= $this->_getList( $query, 0 , 1 );

			if (empty($this->_itemprev)) {
				return null;
			}

		}
		return $this->_itemprev;
	}

	private function _getItemQueryOrdering($ordering, $catid, $direction) {

		$wheres[]	= " c.catid= ".(int) $catid;
		//$wheres[]	= " c.catid= cc.id";
		$wheres[] = " c.published = 1";
		$wheres[] = " cc.published = 1";

		if ($direction == 1) {
			$wheres[] = " c.ordering < " . (int) $ordering;
			$order = 'DESC';
		} else {
			$wheres[] = " c.ordering > " . (int) $ordering;
			$order = 'ASC';
		}

		if ($this->getState('filter.language')) {
			$wheres[] =  ' c.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			$wheres[] =  ' cc.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}

		$query = ' SELECT c.id, c.title, c.alias, c.catid,'
		        .' cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias'
				.' FROM #__phocagallery AS c'
				.' LEFT JOIN #__phocagallery_categories AS cc ON cc.id = c.catid'
				.' WHERE ' . implode( ' AND ', $wheres )
				.' ORDER BY c.ordering '.$order;
		return $query;

	}

	function getCategory($imageId) {
		if (empty($this->_category)) {
			$query			= $this->_getCategoryQuery( $imageId );
			$this->_category= $this->_getList( $query, 0, 1 );
		}
		return $this->_category;
	}

	function _getCategoryQuery( $imageId ) {

		$wheres		= array();
		$app		= Factory::getApplication();
		$params 	= $app->getParams();
		$user 		= Factory::getUser();
		$userLevels	= implode (',', $user->getAuthorisedViewLevels());



		$wheres[]	= " c.id= ".(int)$imageId;
		$wheres[] = " cc.access IN (".$userLevels.")";
		$wheres[] = " cc.published = 1";

		if ($this->getState('filter.language')) {
			$wheres[] =  ' c.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			$wheres[] =  ' cc.language IN ('.$this->_db->Quote(JFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}


		$query = " SELECT cc.id, cc.title, cc.alias, cc.description, cc.access as cataccess, cc.accessuserid as cataccessuserid, cc.parent_id as parent_id"
				. " FROM #__phocagallery_categories AS cc"
				. " LEFT JOIN #__phocagallery AS c ON c.catid = cc.id"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY cc.ordering";


		return $query;
	}
}
?>
