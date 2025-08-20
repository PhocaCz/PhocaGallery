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

	function themeinstall() {

		Session::checkToken() or die( 'Invalid Token' );
		//$post	= JFactory::getApplication()->getInput()->get('post');

		$post = array();
		$post['theme_component']	= Factory::getApplication()->getInput()->get('theme_component', array(), 'raw');
		$post['theme_categories']	= Factory::getApplication()->getInput()->get('theme_categories', array(), 'raw');
		$post['theme_category']		= Factory::getApplication()->getInput()->get('theme_category', array(), 'raw');
		$theme = array();

		if (isset($post['theme_component'])) {
			//$theme['component'] = 1;
		}
		if (isset($post['theme_categories'])) {
			// TO DO - change to 1 in case the parameters component will be added to Joomla! CMS back
			$theme['categories'] = 0;
		}
		if (isset($post['theme_category'])) {
			// TO DO - change to 1 in case the parameters component will be added to Joomla! CMS back
			$theme['category'] 	= 0;
		}
		$theme['component'] = 1;

		if (!empty($theme)) {

			$ftp = ClientHelper::setCredentialsFromRequest('ftp');

			$model	= $this->getModel( 'phocagalleryt' );

			if ($model->install($theme)) {
				$cache = Factory::getCache('mod_menu');
				$cache->clean();
				$msg = Text::_('COM_PHOCAGALLERY_SUCCESS_THEME_INSTALLED');
			}
		} else {
			$msg = Text::_('COM_PHOCAGALLERY_ERROR_THEME_APPLICATION_AREA');
		}

		$this->setRedirect( 'index.php?option=com_phocagallery&view=phocagalleryt', $msg );
	}

	function cancel($key = NULL) {
		$this->setRedirect( 'index.php?option=com_phocagallery' );
	}

	function bgimagesmall() {
		Session::checkToken() or die( 'Invalid Token' );

		//$post				= JFactory::getApplication()->getInput()->get('post');
		$post = array();
		$post['siw'] = Factory::getApplication()->getInput()->files->get( 'siw');
		$post['sih'] = Factory::getApplication()->getInput()->files->get( 'sih');
		$post['ssbgc'] = Factory::getApplication()->getInput()->files->get( 'ssbgc');
		$post['sibgc'] = Factory::getApplication()->getInput()->files->get( 'sibgc');
		$post['sibrdc'] = Factory::getApplication()->getInput()->files->get( 'sibrdc');
		$post['siec'] = Factory::getApplication()->getInput()->files->get( 'siec');
		$post['sie'] = Factory::getApplication()->getInput()->files->get( 'sie');

		$data['image']	= 'shadow3';
		$data['iw']		= $post['siw'];
		$data['ih']		= $post['sih'];
		$data['sbgc']	= $post['ssbgc'];
		$data['ibgc']	= $post['sibgc'];
		$data['ibrdc']	= $post['sibrdc'];
		$data['iec']	= $post['siec'];
		$data['ie']		= $post['sie'];

		phocagalleryimport('phocagallery.image.imagebgimage');
		$errorMsg = '';
		$bgImage = PhocaGalleryImageBgImage::createBgImage($data, $errorMsg);

		if ($bgImage) {
			$msg = Text::_('COM_PHOCAGALLERY_SUCCESS_BG_IMAGE');
		} else {
			$msg = Text::_('COM_PHOCAGALLERY_ERROR_BG_IMAGE');
			if($errorMsg != '') {
				$msg .= '<br />' . $errorMsg;
			}
		}

		$linkSuffix = '&siw='.$post['siw'].'&sih='.$post['sih'].'&ssbgc='.str_replace('#','',$post['ssbgc']).'&sibgc='.str_replace('#','',$post['sibgc']).'&sibrdc='.str_replace('#','',$post['sibrdc']).'&sie='.$post['sie'].'&siec='.str_replace('#','',$post['siec']);

		$this->setRedirect( 'index.php?option=com_phocagallery&view=phocagalleryt'.$linkSuffix , $msg );
	}

	function bgimagemedium() {
		Session::checkToken() or die( 'Invalid Token' );
		//$post				= JFactory::getApplication()->getInput()->get('post');
		$post = array();
		$post['miw'] = Factory::getApplication()->getInput()->files->get( 'miw');
		$post['mih'] = Factory::getApplication()->getInput()->files->get( 'mih');
		$post['msbgc'] = Factory::getApplication()->getInput()->files->get( 'msbgc');
		$post['mibgc'] = Factory::getApplication()->getInput()->files->get( 'mibgc');
		$post['mibrdc'] = Factory::getApplication()->getInput()->files->get( 'mibrdc');
		$post['miec'] = Factory::getApplication()->getInput()->files->get( 'miec');
		$post['mie'] = Factory::getApplication()->getInput()->files->get( 'mie');

		$data['image']	= 'shadow1';
		$data['iw']		= $post['miw'];
		$data['ih']		= $post['mih'];
		$data['sbgc']	= $post['msbgc'];
		$data['ibgc']	= $post['mibgc'];
		$data['ibrdc']	= $post['mibrdc'];
		$data['iec']	= $post['miec'];
		$data['ie']		= $post['mie'];

		phocagalleryimport('phocagallery.image.imagebgimage');
		$errorMsg = '';
		$bgImage = PhocaGalleryImageBgImage::createBgImage($data, $errorMsg);

		if ($bgImage) {
			$msg = Text::_('COM_PHOCAGALLERY_SUCCESS_BG_IMAGE');
		} else {
			$msg = Text::_('COM_PHOCAGALLERY_ERROR_BG_IMAGE');
			if($errorMsg != '') {
				$msg .= '<br />' . $errorMsg;
			}
		}

		$linkSuffix = '&miw='.$post['miw'].'&mih='.$post['mih'].'&msbgc='.str_replace('#','',$post['msbgc']).'&mibgc='.str_replace('#','',$post['mibgc']).'&mibrdc='.str_replace('#','',$post['mibrdc']).'&mie='.$post['mie'].'&miec='.str_replace('#','',$post['miec']);

		$this->setRedirect( 'index.php?option=com_phocagallery&view=phocagalleryt'.$linkSuffix , $msg );
	}
}
?>
