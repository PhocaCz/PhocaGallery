<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined( '_JEXEC' ) or die();
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\CMS\Log\Log;
jimport( 'joomla.application.component.modeladmin' );
jimport( 'joomla.installer.installer' );
jimport( 'joomla.installer.helper' );
jimport( 'joomla.filesystem.folder' );
setlocale(LC_ALL, 'C.UTF-8', 'C');


class PhocaGalleryCpModelPhocaGalleryT extends AdminModel
{
	protected 	$_paths 	= array();
	protected 	$_manifest 	= null;
	protected	$option 		= 'com_phocagallery';
	protected 	$text_prefix	= 'com_phocagallery';
	public 		$typeAlias 		= 'com_phocagallery.phocagalleryt';

	function __construct(){
		parent::__construct();
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= Factory::getApplication();
		$form 	= $this->loadForm('com_phocagallery.phocagalleryt', 'phocagalleryt', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	function install($theme) {
		$app		= Factory::getApplication();
		$db 		= Factory::getDBO();
		$package 	= $this->_getPackageFromUpload();



		if (!$package) {
			$this->deleteTempFiles();
			throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_FIND_INSTALL_PACKAGE'), 500);
			return false;
		}

		if ($package['dir'] && PhocaGalleryFileFolder::exists($package['dir'])) {
			$this->setPath('source', $package['dir']);
		} else {
			$this->deleteTempFiles();
			throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_INSTALL_PATH_NOT_EXISTS'), 500);
			return false;
		}

		// We need to find the installation manifest file
		if (!$this->_findManifest()) {
			$this->deleteTempFiles();
			throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_FIND_INFO_INSTALL_PACKAGE'), 500);
			return false;
		}

		// Files - copy files in manifest
		foreach ($this->_manifest->children() as $child)
		{
			if (is_a($child, 'SimpleXMLElement') && $child->getName() == 'files') {
				if ($this->parseFiles($child) === false) {
					$this->deleteTempFiles();
					throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_FIND_INFO_INSTALL_PACKAGE'), 500);
					return false;
				}
			}
		}

		// File - copy the xml file
		$copyFile 		= array();
		$path['src']	= $this->getPath( 'manifest' ); // XML file will be copied too
		$path['dest']	= JPATH_SITE.'/media/com_phocagallery/images/'. basename($this->getPath('manifest'));
		$copyFile[] 	= $path;

		$this->copyFiles($copyFile, array());
		$this->deleteTempFiles();

		// -------------------
		// Themes
		// -------------------
		// Params -  Get new themes params
		$paramsThemes = $this->getParamsThemes();


		// -------------------
		// Component
		// -------------------
		if (isset($theme['component']) && $theme['component'] == 1 ) {

			$component			=	'com_phocagallery';
			$paramsC			= ComponentHelper::getParams($component) ;

			if (!empty($paramsThemes)) {
				foreach ($paramsThemes as $keyT => $valueT) {
					$paramsC->set($valueT['name'], $valueT['value']);
				}
			}
			$data['params'] 	= $paramsC->toArray();
			$table 				= Table::getInstance('extension');

			$idCom				= $table->find( array('element' => $component ));
			$table->load($idCom);

			if (!$table->bind($data)) {
				throw new Exception('Not a valid component', 500);
				return false;
			}

			// pre-save checks
			if (!$table->check()) {
				throw new Exception($table->getError('Check Problem'), 500);
				return false;
			}

			// save the changes
			if (!$table->store()) {
				throw new Exception($table->getError('Store Problem'), 500);
				return false;
			}
		}

		// -------------------
		// Menu Categories
		// -------------------
/*		if (isset($theme['categories']) && $theme['categories'] == 1 ){

			$link		= 'index.php?option=com_phocagallery&view=categories';
			$where 		= Array();
			$where[] 	= 'link = '. $db->Quote($link);
			$query 		= 'SELECT id, params FROM #__menu WHERE '. implode(' AND ', $where);
			$db->setQuery($query);
			$itemsCat	= $db->loadObjectList();

			if (!empty($itemsCat)) {
				foreach($itemsCat as $keyIT => $valueIT) {

					$query = 'SELECT m.params FROM #__menu AS m WHERE m.id = '.(int) $valueIT->id;
					$db->setQuery( $query );
					$paramsCJSON = $db->loadResult();
					//$paramsCJSON = $valueIT->params;

					//$paramsMc = new J Parameter;
					//$paramsMc->loadJSON($paramsCJSON);

					$paramsMc = new Registry;
					$paramsMc->loadString($paramsCJSON, 'JSON');

					foreach($paramsThemes as $keyT => $valueT) {
						$paramsMc->set($valueT['name'], $valueT['value']);
					}
					$dataMc['params'] 	= $paramsMc->toArray();


					$table = Table::getInstance( 'menu' );

					if (!$table->load((int) $valueIT->id)) {
						throw new Exception('Not a valid table', 500);
						return false;
					}

					if (!$table->bind($dataMc)) {
						throw new Exception('Not a valid table', 500);
						return false;
					}

					// pre-save checks
					if (!$table->check()) {
						throw new Exception($table->getError('Check Problem'), 500);
						return false;
					}

					// save the changes
					if (!$table->store()) {
						throw new Exception($table->getError('Store Problem'), 500);
						return false;
					}

				}
			}
		}*/

		// -------------------
		// Menu Category
		// -------------------
/*		if (isset($theme['category']) && $theme['category'] == 1 ) {

			// Select all categories to get possible menu links
			$query = 'SELECT c.id FROM #__phocagallery_categories AS c';

			$db->setQuery( $query );
			$categoriesId = $db->loadObjectList();

			// We get id from Phoca Gallery categories and try to find menu links from these categories
			if (!empty ($categoriesId)) {
				foreach($categoriesId as $keyI => $valueI) {

					$link		= 'index.php?option=com_phocagallery&view=category&id='.(int)$valueI->id;
					//$link		= 'index.php?option=com_phocagallery&view=category';
					$where 		= Array();
					$where[] 	= 'link = '. $db->Quote($link);
					$query 		= 'SELECT id, params FROM #__menu WHERE '. implode(' AND ', $where);
					$db->setQuery($query);
					$itemsCat	= $db->loadObjectList();

					if (!empty ($itemsCat)) {
						foreach($itemsCat as $keyIT2 => $valueIT2) {

							$query = 'SELECT m.params FROM #__menu AS m WHERE m.id = '.(int) $valueIT2->id;
							$db->setQuery( $query );
							$paramsCtJSON = $db->loadResult();
							//$paramsCtJSON = $valueIT2->params;

							//$paramsMct = new J Parameter;
							//$paramsMct->loadJSON($paramsCtJSON);

							$paramsMc = new Registry;
							$paramsMc->loadString($paramsCJSON, 'JSON');

							/*foreach($paramsThemes as $keyT => $valueT) {
								$paramsMct->set($valueT['name'], $valueT['value']);
							}
							$dataMct['params'] 	= $paramsMct->toArray();
*//*

							$table = Table::getInstance( 'menu' );

							if (!$table->load((int) $valueIT2->id)) {
								throw new Exception('Not a valid table', 500);
								return false;
							}

							if (!$table->bind($dataMct)) {
								throw new Exception('Not a valid table', 500);
								return false;
							}

							// pre-save checks
							if (!$table->check()) {
								throw new Exception($table->getError('Check Problem'), 500);
								return false;
							}

							// save the changes
							if (!$table->store()) {
								throw new Exception($table->getError('Store Problem'), 500);
								return false;
							}
						}
					}
				}
			}
		}*/
		return true;
	}

	function _getPackageFromUpload()
	{
		// Get the uploaded file information
		$userfile = Factory::getApplication()->input->files->get( 'Filedata', null, 'raw' );


		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_INSTALL_FILE_UPLOAD'), 500);
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_INSTALL_ZLIB'), 500);
			return false;
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_NO_FILE_SELECTED'), 500);
			return false;
		}

		// Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 ) {
			throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_UPLOAD_FILE'), 500);
			return false;
		}

		// Build the appropriate paths
		$config 	= Factory::getConfig();
		$tmp_dest 	= $config->get('tmp_path'). '/'. $userfile['name'];

		$tmp_src	= $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = File::upload($tmp_src, $tmp_dest, false, true);

		// Unpack the downloaded package file
		$package = self::unpack($tmp_dest);
		///$this->_manifest =& $manifest;

		$this->setPath('packagefile', $package['packagefile']);
		$this->setPath('extractdir', $package['extractdir']);

		return $package;
	}

	function getPath($name, $default=null) {
		return (!empty($this->_paths[$name])) ? $this->_paths[$name] : $default;
	}

	function setPath($name, $value) {
		$this->_paths[$name] = $value;
	}

	function _findManifest() {
		// Get an array of all the xml files from teh installation directory
		$xmlfiles = Folder::files($this->getPath('source'), '.xml$', 1, true);

		// If at least one xml file exists
		if (count($xmlfiles) > 0) {
			foreach ($xmlfiles as $file)
			{
				// Is it a valid joomla installation manifest file?
				$manifest = $this->_isManifest($file);
				if (!is_null($manifest)) {

					$attr = $manifest->attributes();
					if ((string)$attr['method'] != 'phocagallerytheme') {
						throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_NO_THEME_FILE'), 500);
						return false;
					}

					// Set the manifest object and path
					$this->_manifest = $manifest;
					$this->setPath('manifest', $file);

					// Set the installation source path to that of the manifest file
					$this->setPath('source', dirname($file));

					return true;
				}
			}

			// None of the xml files found were valid install files
			throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_XML_INSTALL_PHOCA'), 500);
			return false;
		} else {

			// No xml files were found in the install folder
			throw new Exception(Text::_('COM_PHOCAGALLERY_ERROR_XML_INSTALL'), 500);
			return false;
		}
	}

	function _isManifest($file) {
		$xml	= simplexml_load_file($file);
		if (!$xml) {
			unset ($xml);
			return null;
		}

		if (!is_object($xml) || ($xml->getName() != 'install' )) {

			unset ($xml);
			return null;
		}


		return $xml;
	}


	function parseFiles($element, $cid=0) {
		$copyfiles 		= array();
		$copyfolders 	= array();


		if (!is_a($element, 'SimpleXMLElement') || !count($element->children())) {
			return 0;// Either the tag does not exist or has no children therefore we return zero files processed.
		}

		$files = $element->children();// Get the array of file nodes to process

		if (count($files) == 0) {
			return 0;// No files to process
		}

		$source 	 	= $this->getPath('source');
		$destination 	= JPATH_SITE.'/media/com_phocagallery';
		//$destination2 	= JPATH_SITE.'/media/com_phocagallery';

		//foreach ($files as $file) {
		//if ($file->na me() == 'folder') {
		if(!empty($files->folder)){
			foreach ($files->folder as $fk => $fv) {
				$path['src']	= $source.'/'.$fv;
				$path['dest']	= $destination.'/'.$fv;
				$copyfolders[] = $path;
			}
		}
		//}
		//}

		if (!empty($files->filename)) {
			foreach($files->filename as $fik => $fiv) {


				$path['src']	= $source.'/'.$fiv;
				$path['dest']	= $destination.'/'.$fiv;
				$copyfiles[] = $path;
			}
		}


		return $this->copyFiles($copyfiles, $copyfolders);
	}

	function copyFiles($files, $folders) {

		$i = 0;
		$fileIncluded = $folderIncluded = 0;
		if (is_array($folders) && count($folders) > 0)
		{
			foreach ($folders as $folder)
			{
				// Get the source and destination paths
				$foldersource	= Path::clean($folder['src']);
				$folderdest		= Path::clean($folder['dest']);

				// Get info about custom css and disable all other custom css in database
				$foldersource2		= str_replace('\\', '/', $foldersource);
				$folder_array		= explode('/', $foldersource2);
				$count_array		= count($folder_array);//Count this array
				$last_array_value 	= $count_array - 1;
				$folder_name		= $folder_array[$last_array_value];
				if ($folder_name == 'css') {
					$filesF = scandir($foldersource . '/' . 'custom');

					if (!empty($filesF)) {
						foreach($filesF as $kF => $vF) {
							//$s 		= strtolower($vF);
							//$f  	= 'custom_';
							//$pos 	= strpos($s, $f);
							//if ($pos === false) {
							//} else {
								$db =Factory::getDBO();
								// disable all other custom files
								$query = ' UPDATE #__phocagallery_styles SET published = 0 WHERE type = 2';
								$db->setQuery($query);
								$db->execute();

								// disable the default style - simple
								$query = ' UPDATE #__phocagallery_styles SET published = 0 WHERE type = 1 AND filename = "theme_simple.css"';
								$db->setQuery($query);
								$db->execute();
								// enable the uploaded custom file
								$query = ' UPDATE #__phocagallery_styles SET published = 1 WHERE type = 2 AND filename = '.$db->quote($vF);
								$db->setQuery($query);
								$db->execute();
							//}
						}
					}
				}

				if (!PhocaGalleryFileFolder::exists($foldersource)) {
					throw new Exception(Text::sprintf('COM_PHOCAGALLERY_FOLDER_NOT_EXISTS', $foldersource), 500);
					return false;
				} else {
					if (!(Folder::copy($foldersource, $folderdest, '', true))) {
						throw new Exception(Text::sprintf('COM_PHOCAGALLERY_ERROR_COPY_FOLDER_TO', $foldersource, $folderdest), 500);
						return false;
					} else {
						$i++;
					}
				}
			}
			$folderIncluded = 1;
		}


		if (is_array($files) && count($files) > 0)
		{
			foreach ($files as $file)
			{
				// Get the source and destination paths
				$filesource	= Path::clean($file['src']);
				$filedest	= Path::clean($file['dest']);


				if (!file_exists($filesource)) {
					throw new Exception(Text::sprintf('COM_PHOCAGALLERY_FILE_NOT_EXISTS', $filesource), 500);
					return false;
				} else {
					if (!(File::copy($filesource, $filedest))) {
						throw new Exception(Text::sprintf('COM_PHOCAGALLERY_ERROR_COPY_FILE_TO', $filesource, $filedest), 500);
						return false;
					} else {
						$i++;
					}
				}
			}
			$fileIncluded = 1;
		}

		if ($fileIncluded == 0 && $folderIncluded ==0) {
			throw new Exception(Text::sprintf('COM_PHOCAGALLERY_ERROR_INSTALL_FILE'), 500);
			return false;
		}

		return $i;// Possible TO DO, now it returns count folders and files togeter, //return count($files);
	}

	protected function getParamsThemes() {

		$element = $this->_manifest->children()->params;



		if (!is_a($element, 'SimpleXMLElement') || !count($element->children())) {
			return null;// Either the tag does not exist or has no children therefore we return zero files processed.
		}

		$params = $element->children();

		if (count($params) == 0) {
			return null;// No params to process
		}

		// Process each parameter in the $params array.
		$paramsArray = array();
		$i=0;
		foreach ($params as $param) {
			if (!$name = $param['name']) {
				continue;
			}
			if (!$value = $param['default']) {
				continue;
			}

			$paramsArray[$i]['name'] = (string)$name;
			$paramsArray[$i]['value'] = (string)$value;
			$i++;
		}

		return $paramsArray;
	}

	function deleteTempFiles() {
		$path = $this->getPath('source');
		if (is_dir($path)) {
			$val = Folder::delete($path);
		} else if (is_file($path)) {
			$val = File::delete($path);
		}
		$packageFile = $this->getPath('packagefile');
		if (is_file($packageFile)) {
			$val = File::delete($packageFile);
		}
		$extractDir = $this->getPath('extractdir');
		if (is_dir($extractDir)) {
			$val = Folder::delete($extractDir);
		}
	}

	public static function unpack($p_filename)
	{
		// Path to the archive
		$archivename = $p_filename;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');

		// Clean the paths to use for archive extraction
		$extractdir = Path::clean(dirname($p_filename) . '/' . $tmpdir);
		$archivename = Path::clean($archivename);

		// Do the unpacking of the archive
		try
		{
			$archive = new \Joomla\Archive\Archive;
			$archive->extract($archivename, $extractdir);

		}
		catch (Exception $e)
		{
			return false;
		}

		/*
		 * Let's set the extraction directory and package file in the result array so we can
		 * cleanup everything properly later on.
		 */
		$retval['extractdir'] = $extractdir;
		$retval['packagefile'] = $archivename;

		/*
		 * Try to find the correct install directory.  In case the package is inside a
		 * subdirectory detect this and set the install directory to the correct path.
		 *
		 * List all the items in the installation directory.  If there is only one, and
		 * it is a folder, then we will set that folder to be the installation folder.
		 */
		$dirList = array_merge(Folder::files($extractdir, ''), Folder::folders($extractdir, ''));

		if (count($dirList) == 1)
		{
			if (PhocaGalleryFileFolder::exists($extractdir . '/' . $dirList[0]))
			{
				$extractdir = Path::clean($extractdir . '/' . $dirList[0]);
			}
		}

		/*
		 * We have found the install directory so lets set it and then move on
		 * to detecting the extension type.
		 */
		$retval['dir'] = $extractdir;

		/*
		 * Get the extension type and return the directory/type array on success or
		 * false on fail.
		 */
		$retval['type'] = self::detectType($extractdir);

		if ($retval['type'])
		{
			return $retval;
		}
		else
		{
			return false;
		}
	}

	public static function detectType($p_dir)
	{
		// Search the install dir for an XML file
		$files = Folder::files($p_dir, '\.xml$', 1, true);

		if (!count($files))
		{
			Log::add(Text::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'), Log::WARNING, ' ');
			return false;
		}

		foreach ($files as $file)
		{
			$xml = simplexml_load_file($file);

			if (!$xml)
			{
				continue;
			}

			if ($xml->getName() != 'install')
			{
				unset($xml);
				continue;
			}

			$type = (string) $xml->attributes()->type;

			// Free up memory
			unset($xml);
			return $type;
		}

		Log::add(Text::_('JLIB_INSTALLER_ERROR_NOTFINDJOOMLAXMLSETUPFILE'), Log::WARNING, ' ');

		// Free up memory.
		unset($xml);
		return false;
	}

}
?>
