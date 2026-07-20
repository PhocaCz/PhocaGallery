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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Client\ClientHelper;
jimport('joomla.application.component.controllerform');
jimport('joomla.client.helper');

class PhocaGalleryCpControllerPhocaGalleryT extends FormController
{
	protected	$option 		= 'com_phocagallery';

	function __construct() {
		parent::__construct();
		$this->registerTask( 'themeinstall'  , 	'themeinstall' );
		$this->registerTask( 'bgimagesmall'  , 	'bgimagesmall' );
		$this->registerTask( 'bgimagemedium'  , 'bgimagemedium' );
		$this->registerTask( 'displayeditcss'  , 'displayeditcss' );
	}

	function displayeditcss() {
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
		$fileid = $this->input->get('fileid');
		$fileid = urldecode(base64_decode($fileid));
		$model		= $this->getModel();
		echo $model->getFileContent($fileid);
		Factory::getApplication()->close();
	}


	function cancel($key = NULL) {
		$this->setRedirect( 'index.php?option=com_phocagallery' );
	}
}
?>
