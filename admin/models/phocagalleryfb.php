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
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Registry\Registry;
jimport('joomla.application.component.modeladmin');

class PhocaGalleryCpModelPhocaGalleryFb extends AdminModel
{
	protected $option 		= 'com_phocagallery';
	protected $text_prefix 	= 'com_phocagallery';
	public 		$typeAlias 		= 'com_phocagallery.phocagalleryfb';

	protected function canDelete($record)
	{
		$user = Factory::getUser();

		if ($record->id) {
			return $user->authorise('core.delete', 'com_phocagallery.phocagalleryfb.'.(int) $record->id);
		} else {
			return parent::canDelete($record);
		}
	}

	protected function canEditState($record)
	{
		$user = Factory::getUser();

		if ($record->id) {
			return $user->authorise('core.edit.state', 'com_phocagallery.phocagalleryfb.'.(int) $record->id);
		} else {
			return parent::canEditState($record);
		}
	}


	public function getTable($type = 'PhocaGalleryFbUsers', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_phocagallery.phocagalleryfb', 'phocagalleryfb', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Determine correct permissions to check.
		if ($this->getState('phocagalleryfb.id')) {
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute( 'id', 'action', 'core.edit');
		} else {
			// New record. Can only create in selected categories.
			$form->setFieldAttribute( 'id', 'action', 'core.create');
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocagallery.edit.phocagalleryfb.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
/*
	protected function getReorderConditions($table = null){
		$condition = array();
		//$condition[] = 'catid = '.(int) $table->catid;
		return $condition;
	}*/

	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		$date = Factory::getDate();
		$user = Factory::getUser();

		/*$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		=ApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias =ApplicationHelper::stringURLSafe($table->title);
		}*/

		if (empty($table->id)) {
			// Set the values
			//$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__phocagallery_fb_users');
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

	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.

			$registry = new Registry;
			$registry->loadString($item->comments);
			$item->comments = $registry->toArray();
		}

		return $item;

	}

}
?>
