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
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Log\Log;
jimport('joomla.application.component.modeladmin');
phocagalleryimport('phocagallery.tag.tag');

class PhocaGalleryCpModelPhocaGalleryImg extends AdminModel
{
	protected	$option 		= 'com_phocagallery';
	protected 	$text_prefix	= 'com_phocagallery';
	public 		$typeAlias 		= 'com_phocagallery.phocagalleryimg';

	protected function canDelete($record)
	{
		$user = Factory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.delete', 'com_phocagallery.phocagalleryimg.'.(int) $record->catid);
		} else {
			return parent::canDelete($record);
		}
	}

	protected function canEditState($record)
	{
		$user = Factory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_phocagallery.phocagalleryimg.'.(int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}

	public function getTable($type = 'PhocaGallery', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= Factory::getApplication();
		$form 	= $this->loadForm('com_phocagallery.phocagalleryimg', 'phocagalleryimg', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{


		// Check the session for previously entered form data.
		$app = Factory::getApplication('administrator');
		$data = $app->getUserState('com_phocagallery.edit.phocagallery.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		// Try to preselect category when we add new image
		// Take the value from filter select box in image list
		// Or take it from GET - if someone wants to add new file and wants to have preselected category
		if (empty($data) || (!empty($data) && (int)$data->id < 1)) {
			$filter = (array) $app->getUserState('com_phocagallery.phocagalleryimgs.filter.category_id');
			if (isset($filter[0]) && (int)$filter[0] > 0) {

				$data->set('catid', (int)$filter[0]);
			} else {
				// UNDER TEST
				$catid = $app->input->get('catid');
				if ((int)$catid > 0) {
					$data->set('catid', (int)$catid);
				}
			}
		}

		return $data;
	}

		public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.
			if (isset($item->metadata)) {
				$registry = new Registry;
				$registry->loadString($item->metadata);
				$item->metadata = $registry->toArray();
			}
		}

		return $item;
	}

	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		$date = Factory::getDate();
		$user = Factory::getUser();

		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		=ApplicationHelper::stringURLSafe($table->alias);
		$table->hits 		= PhocaGalleryUtils::getIntFromString($table->hits);
		$table->zoom 		= PhocaGalleryUtils::getIntFromString($table->zoom);
		$table->pcproductid = PhocaGalleryUtils::getIntFromString($table->pcproductid);
		$table->vmproductid = PhocaGalleryUtils::getIntFromString($table->vmproductid);

		if (empty($table->alias)) {
			$table->alias =ApplicationHelper::stringURLSafe($table->title);
		}

		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocagallery WHERE catid = '.(int)$table->catid);
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
			//$table->modified	= $date->toSql();
			//$table->modified_by	= $user->get('id');
		}
	}



	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'catid = '. (int) $table->catid;
		//$condition[] = 'state >= 0';
		return $condition;
	}

	function approve(&$pks, $value = 1)
	{
		// Initialise variables.
		//$dispatcher	= JDispatcher::getInstance();
		$app		= Factory::getApplication();
		$user		= Factory::getUser();
		$table		= $this->getTable('phocagallery');
		$pks		= (array) $pks;

		// Include the content plugins for the change of state event.
		PluginHelper::importPlugin('content');

		// Access checks.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)) {
				if (!$this->canEditState($table)) {
					// Prune items that you can't change.
					unset($pks[$i]);
					throw new Exception(Text::_('JLIB_APPLICATION_ERROR_EDIT_STATE_NOT_PERMITTED'), 403);
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->approve($pks, $value, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		$context = $this->option.'.'.$this->name;

		// Trigger the onContentChangeState event.
		//$result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));
		$result = $app->triggerEvent($this->event_change_state, array($context, $pks, $value));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		PluginHelper::importPlugin($this->events_map['change_state']);
		$result = $app->triggerEvent($this->event_change_state, array($context, $pks, $value));
		if (\in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	function save($data) {

		$app						= Factory::getApplication();
		$params						= ComponentHelper::getParams( 'com_phocagallery' );
		$clean_thumbnails 			= $params->get( 'clean_thumbnails', 0 );
		$fileOriginalNotExist		= 0;


		if ((int)$data['extid'] > 0) {
			$data['imgorigsize'] 	= 0;
			if ($data['title'] == '') {
				$data['title'] = 'External Image';
			}
		} else {
			//If this file doesn't exists don't save it
			if (!PhocaGalleryFile::existsFileOriginal($data['filename'])) {
				//$this->setError('Original File does not exist');
				//return false;
				$fileOriginalNotExist = 1;
				$errorMsg = Text::_('COM_PHOCAGALLERY_ORIGINAL_IMAGE_NOT_EXIST');
			}

			$data['imgorigsize'] 	= PhocaGalleryFile::getFileSize($data['filename'], 0);
			$data['format'] 		= PhocaGalleryFile::getFileFormat($data['filename']);

			//If there is no title and no alias, use filename as title and alias
			if ($data['title'] == '') {
				$data['title'] = PhocaGalleryFile::getTitleFromFile($data['filename']);
			}
		}

		if ($data['extlink1link'] != '') {
			$extlink1			= str_replace('http://','', $data['extlink1link']);
			$data['extlink1'] 	= $extlink1 . '|'.$data['extlink1title'].'|'.$data['extlink1target'].'|'.$data['extlink1icon'];
		} else {
			$data['extlink1'] 	= $data['extlink1link'] . '|'.$data['extlink1title'].'|'.$data['extlink1target'].'|'.$data['extlink1icon'];
		}

		if ($data['extlink2link'] != '') {
			$extlink2			= str_replace('http://','', $data['extlink2link']);
			$data['extlink2'] 	= $extlink2 . '|'.$data['extlink2title'].'|'.$data['extlink2target'].'|'.$data['extlink2icon'];
		} else {
			$data['extlink2'] 	= $data['extlink2link'] . '|'.$data['extlink2title'].'|'.$data['extlink2target'].'|'.$data['extlink2icon'];
		}

		// Geo
		if($data['longitude'] == '' || $data['latitude'] == '') {
			phocagalleryimport('phocagallery.geo.geo');
			$coords = PhocaGalleryGeo::getGeoCoords($data['filename']);

			if ($data['longitude'] == '' ){
				$data['longitude'] = $coords['longitude'];
			}

			if ($data['latitude'] == '' ){
				$data['latitude'] = $coords['latitude'];
			}

			if ($data['latitude'] != '' && $data['longitude'] != '' && $data['zoom'] == ''){
				$data['zoom'] = PhocaGallerySettings::getAdvancedSettings('geozoom');
			}
		}



		if ($data['alias'] == '') {
			$data['alias'] = $data['title'];
		}

		//clean alias name (no bad characters)
		//$data['alias'] = PhocaGalleryText::getAliasName($data['alias']);


		// if new item, order last in appropriate group
		//if (!$row->id) {
		//	$where = 'catid = ' . (int) $row->catid ;
		//	$row->ordering = $row->getNextOrder( $where );
		//}

		// = = = = = = = = = =


		// Initialise variables;
		//$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
		$isNew		= true;

		// Include the content plugins for the on save events.
		PluginHelper::importPlugin('content');

		// Load the row if saving an existing record.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		if(intval($table->date) == 0) {
			$table->date = Factory::getDate()->toSql();
		}

		// Prepare the row for saving
		$this->prepareTable($table);

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Trigger the onContentBeforeSave event.
		/*$result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name, $table, $isNew));
		$result = $app->triggerEvent($this->event_before_save, array($this->option.'.'.$this->name, $table, $isNew));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}*/

		PluginHelper::importPlugin($this->events_map['save']);
		$result = $app->triggerEvent($this->event_before_save, array($this->option.'.'.$this->name, $table, $isNew, $data));
		if (\in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		// Store to ref table
		if (!isset($data['tags'])) {
			$data['tags'] = array();
		}
		if ((int)$table->id > 0) {
			PhocaGalleryTag::storeTags($data['tags'], (int)$table->id);
		}

		// Clean the cache.
		$cache = Factory::getCache($this->option);
		$cache->clean();

		// Trigger the onContentAfterSave event.
		//$dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, $table, $isNew));
		PluginHelper::importPlugin($this->events_map['save']);
		$result = $app->triggerEvent($this->event_after_save, array($this->option.'.'.$this->name, $table, $isNew, $data));
		if (\in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		$pkName = $table->getKeyName();
		if (isset($table->$pkName)) {
			$this->setState($this->getName().'.id', $table->$pkName);
		}
		$this->setState($this->getName().'.new', $isNew);

		// = = = = = =


		$task = Factory::getApplication()->input->get('task');
		if (isset($table->$pkName)) {
			$id = $table->$pkName;
		}

		if ((int)$data['extid'] > 0 || $fileOriginalNotExist == 1) {

		} else {
			// - - - - - - - - - - - - - - - - - -
			//Create thumbnail small, medium, large
			//file - abc.img, file_no - folder/abc.img
			//Get folder variables from Helper
			//Create thumbnails small, medium, large
			$refresh_url = 'index.php?option=com_phocagallery&task=phocagalleryimg.thumbs';
			$task = Factory::getApplication()->input->get('task');
			if (isset($table->$pkName) && $task == 'apply') {
				$id = $table->$pkName;
				$refresh_url = 'index.php?option=com_phocagallery&task=phocagalleryimg.edit&id='.(int)$id;
			}
			if ($task == 'save2new') {
				// Don't create automatically thumbnails in case, we are going to add new image
			} else {
				$file_thumb = PhocaGalleryFileThumbnail::getOrCreateThumbnail($data['filename'], $refresh_url, 1, 1, 1);
			}
			//Clean Thumbs Folder if there are thumbnail files but not original file
			if ($clean_thumbnails == 1) {
				phocagalleryimport('phocagallery.file.filefolder');
				PhocaGalleryFileFolder::cleanThumbsFolder();
			}
			// - - - - - - - - - - - - - - - - - - - - -
		}

		return true;
	}



	function delete(&$cid = array()) {
		$params				= ComponentHelper::getParams( 'com_phocagallery' );
		$clean_thumbnails 	= $params->get( 'clean_thumbnails', 0 );
		$result 			= false;

		$table		= $this->getTable();
		if (count( $cid )) {
			\Joomla\Utilities\ArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			// - - - - - - - - - - - - -
			// Get all filenames we want to delete from database, we delete all thumbnails from server of this file
			$queryd = 'SELECT filename as filename FROM #__phocagallery WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery($queryd);
			$fileObject = $this->_db->loadObjectList();
			// - - - - - - - - - - - - -

			//Delete it from DB
			/*$query = 'DELETE FROM #__phocagallery'
				. ' WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			$this->_db->execute();
			/*if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}*/
			$app		= Factory::getApplication();
			PluginHelper::importPlugin($this->events_map['delete']);
			foreach ($cid as $i => $pk) {
				if ($table->load($pk)) {
					if ($this->canDelete($table)) {
						if (!$table->delete($pk)) {
							throw new Exception($table->getError(), 500);
							return false;
						}
						$app->triggerEvent($this->event_after_delete, array($this->option.'.'.$this->name, $table));
					}
				}
			}

			// - - - - - - - - - - - - - -
			// Delete thumbnails - medium and large, small from server
			// All id we want to delete - gel all filenames
			foreach ($fileObject as $key => $value) {
				//The file can be stored in other category - don't delete it from server because other category use it
				$querys = "SELECT id as id FROM #__phocagallery WHERE filename='".$value->filename."' ";
				$this->_db->setQuery($queryd);
				$sameFileObject = $this->_db->loadObject();
				// same file in other category doesn't exist - we can delete it
				if (!$sameFileObject) {
					PhocaGalleryFileThumbnail::deleteFileThumbnail($value->filename, 1, 1, 1);
				}
			}
			// Clean Thumbs Folder if there are thumbnail files but not original file
			if ($clean_thumbnails == 1) {
				phocagalleryimport('phocagallery.file.filefolder');
				PhocaGalleryFileFolder::cleanThumbsFolder();
			}
			// - - - - - - - - - - - - - -
		}
		return true;
	}

	function recreate($cid = array(), &$message = '') {

		if (count( $cid )) {
			\Joomla\Utilities\ArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'SELECT a.filename, a.extid'.
					' FROM #__phocagallery AS a' .
					' WHERE a.id IN ( '.$cids.' )';
			$this->_db->setQuery($query);
			$files = $this->_db->loadObjectList();
			if (isset($files) && count($files)) {
				foreach($files as $key => $value) {



					if (isset($value->extid) && ((int)$value->extid > 0)) {
						// Picasa cannot be recreated
						$message = Text::_('COM_PHOCAGALLERY_ERROR_EXT_IMG_NOT_RECREATE');
						return false;
					} else if (isset($value->filename) && $value->filename != '') {

						$original	= PhocaGalleryFile::existsFileOriginal($value->filename);
						if (!$original) {
							// Original does not exist - cannot generate new thumbnail
							$message = Text::_('COM_PHOCAGALLERY_FILEORIGINAL_NOT_EXISTS');
							return false;
						}
						// Delete old thumbnails
						$deleteThubms = PhocaGalleryFileThumbnail::deleteFileThumbnail($value->filename, 1, 1, 1);
					} else {
						$message = Text::_('COM_PHOCAGALLERY_FILENAME_NOT_EXISTS');
						return false;
					}
					if (!$deleteThubms) {
						$message = Text::_('COM_PHOCAGALLERY_ERROR_DELETE_THUMBNAIL');
						return false;
					}
				}
			} else {
				$message = Text::_('COM_PHOCAGALLERY_ERROR_LOADING_DATA_DB');
				return false;
			}

		} else {
			$message = Text::_('COM_PHOCAGALLERY_ERROR_ITEM_NOT_SELECTED');
			return false;
		}
		return true;
	}
	/*
	function deletethumbs($id) {

		if ($id > 0) {
			$query = 'SELECT a.filename as filename'.
					' FROM #__phocagallery AS a' .
					' WHERE a.id = '.(int) $id;
			$this->_db->setQuery($query);
			$file = $this->_db->loadObject();
			if (isset($file->filename) && $file->filename != '') {

				$deleteThubms = PhocaGalleryFileThumbnail::deleteFileThumbnail($file->filename, 1, 1, 1);

				if ($deleteThubms) {
					return true;
				} else {
					return false;
				}
			} return false;
		} return false;
	}*/

	public function disableThumbs() {

		$component			=	'com_phocagallery';
		$paramsC			= ComponentHelper::getParams($component) ;
		$paramsC->set('enable_thumb_creation', 0);
		$data['params'] 	= $paramsC->toArray();
		$table 				= Table::getInstance('extension');
		$idCom				= $table->find( array('element' => $component ));
		$table->load($idCom);

		if (!$table->bind($data)) {
			throw new Exception($db->getErrorMsg());
			return false;
		}

		// pre-save checks
		if (!$table->check()) {
			throw new Exception($table->getError());
			return false;
		}

		// save the changes
		if (!$table->store()) {
			throw new Exception($table->getError());
			return false;
		}
		return true;
	}

	function rotate($id, $angle, &$errorMsg) {
		phocagalleryimport('phocagallery.image.imagerotate');

		if ($id > 0 && $angle !='') {
			$query = 'SELECT a.filename as filename'.
					' FROM #__phocagallery AS a' .
					' WHERE a.id = '.(int) $id;
			$this->_db->setQuery($query);
			$file = $this->_db->loadObject();

			if (isset($file->filename) && $file->filename != '') {

				$thumbNameL	= PhocaGalleryFileThumbnail::getThumbnailName ($file->filename, 'large');
				$thumbNameM	= PhocaGalleryFileThumbnail::getThumbnailName ($file->filename, 'medium');
				$thumbNameS	= PhocaGalleryFileThumbnail::getThumbnailName ($file->filename, 'small');

				$errorMsg = $errorMsgS = $errorMsgM = $errorMsgL ='';
				PhocaGalleryImageRotate::rotateImage($thumbNameL, 'large', $angle, $errorMsgL);
				if ($errorMsgL != '') {
					$errorMsg = $errorMsgL;
					return false;
				}
				PhocaGalleryImageRotate::rotateImage($thumbNameM, 'medium', $angle, $errorMsgM);
				if ($errorMsgM != '') {
					$errorMsg = $errorMsgM;
					return false;
				}
				PhocaGalleryImageRotate::rotateImage($thumbNameS, 'small', $angle, $errorMsgS);
				if ($errorMsgS != '') {
					$errorMsg = $errorMsgS;
					return false;
				}

				if ($errorMsgL == '' && $errorMsgM == '' && $errorMsgS == '' ) {
					return true;
				} else {
					$errorMsg = ' ('.$errorMsg.')';
					return false;
				}
			}
			$errorMsg = Text::_('COM_PHOCAGALLERY_FILENAME_NOT_EXISTS');
			return false;
		}
		$errorMsg = Text::_('COM_PHOCAGALLERY_ERROR_ITEM_NOT_SELECTED');
		return false;
	}

	function deletethumbs($id) {

		if ($id > 0) {
			$query = 'SELECT a.filename as filename'.
					' FROM #__phocagallery AS a' .
					' WHERE a.id = '.(int) $id;
			$this->_db->setQuery($query);
			$file = $this->_db->loadObject();
			if (isset($file->filename) && $file->filename != '') {

				$deleteThubms = PhocaGalleryFileThumbnail::deleteFileThumbnail($file->filename, 1, 1, 1);

				if ($deleteThubms) {
					return true;
				} else {
					return false;
				}
			} return false;
		} return false;
	}

	protected function batchCopy($value, $pks, $contexts)
	{
		$categoryId	= (int) $value;

		$table	= $this->getTable();
		$db		= $this->getDbo();

		// Check that the category exists
		if ($categoryId) {
			$categoryTable = Table::getInstance('PhocaGalleryC', 'Table');

			if (!$categoryTable->load($categoryId)) {
				if ($error = $categoryTable->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
					return false;
				}
			}
		}

		if (empty($categoryId)) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
			return false;
		}

		// Check that the user has create permission for the component
		$extension	= Factory::getApplication()->input->getCmd('option');
		$user		= Factory::getUser();
		if (!$user->authorise('core.create', $extension)) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
			return false;
		}

		//NEW
		//$i		= 0;
		//ENDNEW

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					// Not fatal error
					$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $table->alias, $table->title);
			$table->title   = $data['0'];
			$table->alias   = $data['1'];

			// Reset the ID because we are making a copy
			$table->id		= 0;

			// New category ID
			$table->catid	= $categoryId;

			// Ordering
			$table->ordering = $this->increaseOrdering($categoryId);

			$table->hits = 0;

			// Check the row.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			//NEW
			// Get the new item ID
			$newId = $table->get('id');

			// Add the new ID to the array
			$newIds[$pk]	= $newId;

			if ($newId > 0) {
				$tags = PhocaGalleryTag::getTags($pk, 1);
				PhocaGalleryTag::storeTags($tags, $newId);
			}
			//$i++;
			//ENDNEW
		}

		// Clean the cache
		$this->cleanCache();

		//NEW
		return $newIds;
		//END NEW
	}

	/**
	 * Batch move articles to a new category
	 *
	 * @param   integer  $value  The new category ID.
	 * @param   array    $pks    An array of row IDs.
	 *
	 * @return  booelan  True if successful, false otherwise and internal error is set.
	 *
	 * @since	11.1
	 */
	protected function batchMove($value, $pks, $contexts)
	{
		$categoryId	= (int) $value;

		$table	= $this->getTable();
		//$db		= $this->getDbo();

		// Check that the category exists
		if ($categoryId) {
			$categoryTable = Table::getInstance('PhocaGalleryC', 'Table');
			if (!$categoryTable->load($categoryId)) {
				if ($error = $categoryTable->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
					return false;
				}
			}
		}

		if (empty($categoryId)) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
			return false;
		}

		// Check that user has create and edit permission for the component
		$extension	= Factory::getApplication()->input->getCmd('option');
		$user		= Factory::getUser();
		if (!$user->authorise('core.create', $extension)) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
			return false;
		}

		if (!$user->authorise('core.edit', $extension)) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// Parent exists so we let's proceed
		foreach ($pks as $pk)
		{
			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				}
				else {
					// Not fatal error
					$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Set the new category ID
			$table->catid = $categoryId;

			// Check the row.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}


	public function increaseOrdering($categoryId) {

		$ordering = 1;
		$this->_db->setQuery('SELECT MAX(ordering) FROM #__phocagallery WHERE catid='.(int)$categoryId);
		$max = $this->_db->loadResult();
		$ordering = $max + 1;
		return $ordering;
	}
	/*
	public function publish(&$pks, $value = 1)
	{
		$dispatcher = J EventDispatcher::getInstance();
		$user = Factory::getUser();
		$table = $this->getTable();
		$pks = (array) $pks;

		//PHOCAEDIT
		// Include the plugins for the change of state event.
		//JPluginHelper::importPlugin($this->events_map['change_state']);

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					Log::add(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), Log::WARNING, ' ');

					return false;
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id')))
		{
			$this->setError($table->getError());

			return false;
		}

		$context = $this->option . '.' . $this->name;

		// Trigger the change state event.
		$result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}*/
}
?>
