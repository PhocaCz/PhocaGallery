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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Phoca\PhocaGallery\MVC\Model\AdminModelTrait;
jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
phocagalleryimport('phocagallery.file.filefolderlist');

class PhocaGalleryCpModelPhocaGalleryI extends BaseDatabaseModel
{
	use AdminModelTrait;
	protected $option 			= 'com_phocagallery';
	protected $text_prefix		= 'com_phocagallery';
	//public 		$typeAlias 		= 'com_phocagallery.phocagalleryi';

	function getFolderState($property = null) {
		static $set;

		if (!$set) {
			$folder = Factory::getApplication()->getInput()->get( 'folder', '', '', 'path' );
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}
		return parent::getState($property);
	}

	function getImages() {
		$tab 			= Factory::getApplication()->getInput()->get( 'tab', '', '', 'string' );
		$muFailed		= Factory::getApplication()->getInput()->get( 'mufailed', '0', '', 'int' );
		$muUploaded		= Factory::getApplication()->getInput()->get( 'muuploaded', '0', '', 'int' );

		$refreshUrl = 'index.php?option=com_phocagallery&view=phocagalleryi&tab='.$tab.'&mufailed='.$muFailed.'&muuploaded='.$muUploaded.'&tmpl=component';
		$list = PhocaGalleryFileFolderList::getList(0,1,0,$refreshUrl);
		return $list['Images'];
	}

	function getFolders() {
		$tab = Factory::getApplication()->getInput()->get( 'tab', 0, '', 'int' );
		$refreshUrl = 'index.php?option=com_phocagallery&view=phocagalleryi&tab='.$tab.'&tmpl=component';
		$list = PhocaGalleryFileFolderList::getList(0,0,0,$refreshUrl);
		return $list['folders'];
	}
}
?>
