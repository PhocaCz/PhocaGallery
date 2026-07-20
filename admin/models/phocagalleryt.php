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
use Phoca\PhocaGallery\MVC\Model\AdminModelTrait;
jimport( 'joomla.application.component.modeladmin' );
jimport( 'joomla.installer.installer' );
jimport( 'joomla.installer.helper' );
jimport( 'joomla.filesystem.folder' );
setlocale(LC_ALL, 'C.UTF-8', 'C');


class PhocaGalleryCpModelPhocaGalleryT extends AdminModel
{
	use AdminModelTrait;
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

}
?>
