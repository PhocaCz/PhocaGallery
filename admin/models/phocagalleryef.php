<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die();
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Client\ClientHelper;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\File;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Log\Log;
jimport('joomla.application.component.modeladmin');


class PhocaGalleryCpModelPhocaGalleryEf extends AdminModel
{
	protected	$option 		= 'com_phocagallery';
	protected 	$text_prefix	= 'com_phocagallery';
	public 		$typeAlias 		= 'com_phocagallery.phocagalleryef';

	protected function canDelete($record)
	{
		//$user = JFactory::getUser();
		return parent::canDelete($record);
	}

	protected function canEditState($record)
	{
		//$user = JFactory::getUser();
		return parent::canEditState($record);
	}

	public function getTable($type = 'PhocaGalleryEf', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= Factory::getApplication();
		$form 	= $this->loadForm('com_phocagallery.phocagallerystyles', 'phocagalleryef', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocagallery.edit.phocagallerystyles.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		$date = Factory::getDate();
		$user = Factory::getUser();

		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		=ApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias =ApplicationHelper::stringURLSafe($table->title);
		}

		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocagallery_styles WHERE type = '.(int)$table->type);
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
		$condition[] = 'type = '. (int) $table->type;
		//$condition[] = 'state >= 0';
		return $condition;
	}

	public function increaseOrdering($categoryId) {

		$ordering = 1;
		$this->_db->setQuery('SELECT MAX(ordering) FROM #__phocagallery_styles WHERE type='.(int)$categoryId);
		$max = $this->_db->loadResult();
		$ordering = $max + 1;
		return $ordering;
	}

	public function &getSource($id, $filename, $type) {
		$item = new stdClass;

		$filePath = PhocaGalleryPhocaGalleryFile::existsCSS($filename, $type);
		if ($filePath) {
			//$item->id			= $id;
			//$item->type			= $type;
			//$item->filname      = $filename;
			$item->source       = file_get_contents($filePath);
		} else {
			$this->setError(Text::_('COM_PHOCAGALLERY_FILE_DOES_NOT_EXIST'));
		}
		return $item;
	}

	public function save($data) {
		jimport('joomla.filesystem.file');

		// New
		if ($data['id'] < 1) {
			$data['type'] = 2;// Custom in every case
			if ($data['title'] != '') {
				$filename =ApplicationHelper::stringURLSafe($data['title']);

				if (trim(str_replace('-','',$filename)) == '') {
					$filename = Factory::getDate()->format("Y-m-d-H-i-s");
				}
			} else {
				$filename = Factory::getDate()->format("Y-m-d-H-i-s");
			}
			$filename 			= $filename . '.css';
			$data['filename']	= $filename;
			$filePath = PhocaGalleryPhocaGalleryFile::existsCSS($filename, $data['type']);
			if ($filePath) {
				$this->setError(Text::sprintf('COM_PHOCAGALLERY_FILE_ALREADY_EXISTS', $fileName));
				return false;
			} else {
				$filePath = PhocaGalleryFile::getCSSPath($data['type']) . $filename;
			}
		} else {
			$filename = PhocaGalleryFile::getCSSFile($data['id']);
			$filePath = PhocaGalleryPhocaGalleryFile::existsCSS($filename, $data['type']);
		}

		//$dispatcher = J EventDispatcher::getInstance();
		$fileName	= $filename;


		// Include the extension plugins for the save events.
		//JPluginHelper::importPlugin('extension');

		// Set FTP credentials, if given.
		ClientHelper::setCredentialsFromRequest('ftp');
		$ftp = ClientHelper::getCredentials('ftp');

		// Try to make the template file writeable.
		if (!$ftp['enabled'] && Path::isOwner($filePath) && !Path::setPermissions($filePath, '0644')) {
			$this->setError(Text::_('COM_PHOCAGALLERY_ERROR_SOURCE_FILE_NOT_WRITABLE'));
			return false;
		}

		// Trigger the onExtensionBeforeSave event.
		/*$result = $dispatcher->trigger('onExtensionBeforeSave', array('com_phocagallery.source', &$data, false));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}*/

		$return = File::write($filePath, $data['source']);

		// Try to make the template file unwriteable.
		/*if (!$ftp['enabled'] && JPath::isOwner($filePath) && !JPath::setPermissions($filePath, '0444')) {
			$this->setError(Text::_('COM_PHOCAGALLERY_ERROR_SOURCE_FILE_NOT_UNWRITABLE'));
			return false;
		} else*/

		if (!$return) {
			$this->setError(Text::sprintf('COM_PHOCAGALLERY_ERROR_FAILED_TO_SAVE_FILENAME', $fileName));
			return false;
		}

		// Trigger the onExtensionAfterSave event.
		//$dispatcher->trigger('onExtensionAfterSave', array('com_templates.source', &$table, false));

		//return true;
		return parent::save($data);
	}

	public function delete(&$pks)
	{
		//$dispatcher = J EventDispatcher::getInstance();
		$pks = (array) $pks;
		$table = $this->getTable();

		// Include the content plugins for the on delete events.
		PluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{

			if ($table->load($pk))
			{

				if ($this->canDelete($table))
				{

					$context = $this->option . '.' . $this->name;

					// Trigger the onContentBeforeDelete event.
					$result = Factory::getApplication()->triggerEvent($this->event_before_delete, array($context, $table));
					if (in_array(false, $result, true))
					{
						$this->setError($table->getError());
						return false;
					}

					//PHOCAEDIT
					$filePath = PhocaGalleryFile::getCSSFile($pk, true);
					//END PHOCAEDIT

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}

					//PHOCAEDIT
					if (file_exists($filePath)) {
						File::delete($filePath);
					}
					//END PHOCAEDIT

					// Trigger the onContentAfterDelete event.
                    Factory::getApplication()->triggerEvent($this->event_after_delete, array($context, $table));

				}
				else
				{

					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error)
					{
						Log::add($error, Log::WARNING, ' ');
						return false;
					}
					else
					{
						Log::add(Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), Log::WARNING, ' ');
						return false;
					}
				}

			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
?>
