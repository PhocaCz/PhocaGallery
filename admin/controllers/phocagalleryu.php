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
defined('_JEXEC') or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
jimport('joomla.client.helper');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class PhocaGalleryCpControllerPhocaGalleryu extends PhocaGalleryCpController
{
	function __construct() {
		parent::__construct();
	}

	function createfolder() {
		$app	= Factory::getApplication();
		// Check for request forgeries
		Session::checkToken() or jexit( 'Invalid Token' );

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		ClientHelper::setCredentialsFromRequest('ftp');

		$paramsC = ComponentHelper::getParams('com_phocagallery');
		$folder_permissions = $paramsC->get( 'folder_permissions', 0755 );
		//$folder_permissions = octdec((int)$folder_permissions);

		$path			= PhocaGalleryPath::getPath();
		//$folderNew		= J Request::getCmd( 'foldername', '');
		//$folderCheck	= JFactory::getApplication()->input->get( 'foldername', null, '', 'string', J REQUEST_ALLOWRAW);
		$folderNew      = $app->input->getstring('foldername', '');
		//$folderCheck    = $app->input->getstring('foldername', null, '', 'string', J REQUEST_ALLOWRAW);
		$folderCheck    = $app->input->getstring('foldername', null, '', 'string');
		$parent			= Factory::getApplication()->input->get( 'folderbase', '', '', 'path' );
		$tab			= Factory::getApplication()->input->get( 'tab', '', '', 'string' );
		$field			= Factory::getApplication()->input->get( 'field');
		$viewBack		= Factory::getApplication()->input->get( 'viewback', '', '', '' );

		$link = '';
		switch ($viewBack) {
			case 'phocagalleryi':
				$link = 'index.php?option=com_phocagallery&view=phocagalleryi&tmpl=component&folder='.$parent.'&tab='.(string)$tab.'&field='.$field;
			break;

			case 'phocagallerym':
				$link = 'index.php?option=com_phocagallery&view=phocagallerym&layout=edit&hidemainmenu=1&tab='.(string)$tab.'&folder='.$parent;
			break;

			case 'phocagalleryf':
				$link = 'index.php?option=com_phocagallery&view=phocagalleryf&tmpl=component&folder='.$parent.'&field='.$field;
			break;

			default:
				$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_ERROR_CONTROLLER'));
				$app->redirect('index.php?option=com_phocagallery');
			break;

		}

		//JFactory::getApplication()->input->set('folder', $parent);
		Factory::getApplication()->input->set('folder', $parent);

		if (($folderCheck !== null) && ($folderNew !== $folderCheck)) {
			$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_WARNING_DIRNAME'));
			$app->redirect($link);
		}

		if (strlen($folderNew) > 0) {
			$folder = Path::clean($path->image_abs. '/'. $parent. '/'. $folderNew);
			if (!Folder::exists($folder) && !File::exists($folder)) {
				//JFolder::create($path, $folder_permissions );
				switch((int)$folder_permissions) {
					case 777:
						Folder::create($folder, 0777 );
					break;
					case 705:
						Folder::create($folder, 0705 );
					break;
					case 666:
						Folder::create($folder, 0666 );
					break;
					case 644:
						Folder::create($folder, 0644 );
					break;
					case 755:
					Default:
						Folder::create($folder, 0755 );
					break;
				}
				if (isset($folder)) {
					$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
					File::write($folder. '/'. "index.html", $data);
				}

				$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_SUCCESS_FOLDER_CREATING'));
				$app->redirect($link);
			} else {
				$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_ERROR_FOLDER_CREATING_EXISTS'));
				$app->redirect($link);
			}
			//JFactory::getApplication()->input->set('folder', ($parent) ? $parent.'/'.$folder : $folder);
		}
		$app->redirect($link);
	}

	function multipleupload() {
		$result = PhocaGalleryFileUpload::realMultipleUpload();
		return true;
	}

	function upload() {
		$result = PhocaGalleryFileUpload::realSingleUpload();
		return true;
	}


	function javaupload() {
		$result = PhocaGalleryFileUpload::realJavaUpload();
		return true;
	}

}
