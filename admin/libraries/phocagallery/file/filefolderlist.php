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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\CMS\Object\CMSObject;
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
phocagalleryimport('phocagallery.image.image');
phocagalleryimport('phocagallery.path.path');
phocagalleryimport('phocagallery.file.filefolder');
setlocale(LC_ALL, 'C.UTF-8', 'C');

class PhocaGalleryFileFolderList
{
	public static function getList($small = 0, $medium = 0, $large = 0, $refreshUrl = '') {
		static $list;

		$params				= ComponentHelper::getParams( 'com_phocagallery' );
		$clean_thumbnails 	= $params->get( 'clean_thumbnails', 0 );

		// Only process the list once per request
		if (is_array($list)) {
			return $list;
		}

		// Get current path from request
		$current = Factory::getApplication()->input->get('folder', '', 'path');

		// If undefined, set to empty
		if ($current == 'undefined') {
			$current = '';
		}

		//Get folder variables from Helper
		$path = PhocaGalleryPath::getPath();

		// Initialize variables
		if (strlen($current) > 0) {
			$origPath = Path::clean($path->image_abs.$current);
		} else {
			$origPath = $path->image_abs;
		}
		$origPathServer = str_replace('\\', '/', $path->image_abs);

		$images 	= array ();
		$folders 	= array ();

		// Get the list of files and folders from the given folder
		$fileList 		= Folder::files($origPath);
		$folderList 	= Folder::folders($origPath, '', false, false, array(0 => 'thumbs'));

		if(is_array($fileList) && !empty($fileList)) {
			natcasesort($fileList);
		}

		$field			= Factory::getApplication()->input->get('field');;
		$refreshUrl 	= $refreshUrl . '&folder='.$current.'&field='.$field;


		// Iterate over the files if they exist
		//file - abc.img, file_no - folder/abc.img
		if ($fileList !== false) {
			foreach ($fileList as $file) {

				$ext = strtolower(File::getExt($file));
				// Don't display thumbnails from defined files (don't save them into a database)...
				$dontCreateThumb	= PhocaGalleryFileThumbnail::dontCreateThumb($file);
				if ($dontCreateThumb == 1) {
					$ext = '';// WE USE $ext FOR NOT CREATE A THUMBNAIL CLAUSE
				}
				if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif' || $ext == 'jpeg' || $ext == 'webp' || $ext == 'avif') {

					if (PhocaGalleryFile::exists($origPath. '/'. $file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html') {

						//Create thumbnails small, medium, large
						$fileNo			= $current . "/" . $file;
						$fileThumb 		= PhocaGalleryFileThumbnail::getOrCreateThumbnail($fileNo, $refreshUrl, $small, $medium, $large);

						$tmp 						= new CMSObject();
						$tmp->name 					= $fileThumb['name'];
						$tmp->nameno 				= $fileThumb['name_no'];
						$tmp->linkthumbnailpath		= $fileThumb['thumb_name_m_no_rel'];
						$tmp->linkthumbnailpathabs	= $fileThumb['thumb_name_m_no_abs'];
						$images[] 					= $tmp;
					}
				}
			}
		}

		//Clean Thumbs Folder if there are thumbnail files but not original file
		if ($clean_thumbnails == 1) {
			PhocaGalleryFileFolder::cleanThumbsFolder();
		}
		// - - - - - - - - - - - -

		// Iterate over the folders if they exist

		if ($folderList !== false) {
			foreach ($folderList as $folder) {
				$tmp 							= new CMSObject();
				$tmp->name 						= basename($folder);
				$tmp->path_with_name 			= str_replace('\\', '/', Path::clean($origPath . '/'. $folder));
				$tmp->path_without_name_relative= $path->image_abs . str_replace($origPathServer, '', $tmp->path_with_name);
				$tmp->path_with_name_relative_no= str_replace($origPathServer, '', $tmp->path_with_name);


				$folders[] = $tmp;
			}
		}

		$list = array('folders' => $folders, 'Images' => $images);
		return $list;
	}
}
?>
