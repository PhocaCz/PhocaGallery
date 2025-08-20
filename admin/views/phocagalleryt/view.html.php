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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\Filesystem\Folder;
jimport( 'joomla.application.component.view' );
jimport( 'joomla.client.helper' );
phocagalleryimport( 'phocagallery.image.image' );
class PhocaGalleryCpViewPhocaGalleryT extends HtmlView
{

	protected $require_ftp;
	protected $theme_name;
	protected $files;
	protected $r;
	protected $t;

	public function display($tpl = null) {

		$document	= Factory::getDocument();



		$this->t	= PhocaGalleryUtils::setVars('t');
		$this->r	= new PhocaGalleryRenderAdminview();

		//JHtml::stylesheet( 'media/com_phocagallery/js/jcp/picker.css' );
		//$document->addScript(JUri::base(true).'/media/com_phocagallery/js/jcp/picker.js');

		$this->require_ftp	= ClientHelper::setCredentialsFromRequest('ftp');
		$this->files	= $this->get('Files');
		$this->form		= $this->get('Form');

		if($this->themeName()) {
			$this->theme_name = $this->themeName();
		}
		// Background Image
		/*
		$params = ComponentHelper::getParams('com_phocagallery');


		// Small
		$this->t['siw']		= $params->get('small_image_width', 128 );
		$this->t['sih']		= $params->get('small_image_height', 96 );

		//After creating an image (post with data);
		$this->t['ssbgc']	= Factory::getApplication()->getInput()->get( 'ssbgc', '', '', 'string' );
		$this->t['sibgc']	= Factory::getApplication()->getInput()->get( 'sibgc', '', '', 'string' );
		$this->t['sibrdc']	= Factory::getApplication()->getInput()->get( 'sibrdc', '', '', 'string' );
		$this->t['sie']		= Factory::getApplication()->getInput()->get( 'sie', '', '', 'int' );
		$this->t['siec']		= Factory::getApplication()->getInput()->get( 'siec', '', '', 'string' );
		$siw					= Factory::getApplication()->getInput()->get( 'siw', '', '', 'int' );
		$sih					= Factory::getApplication()->getInput()->get( 'sih', '', '', 'int' );

		$this->t['ssbgc']	= PhocaGalleryUtils::filterInput($this->t['ssbgc']);
		$this->t['sibgc']	= PhocaGalleryUtils::filterInput($this->t['sibgc']);
		$this->t['sibrdc']	= PhocaGalleryUtils::filterInput($this->t['sibrdc']);
		$this->t['siec']		= PhocaGalleryUtils::filterInput($this->t['siec']);

		if($this->t['ssbgc'] 	!= '') 	{$this->t['ssbgc'] = '#'.$this->t['ssbgc'];}
		if($this->t['sibgc'] 	!= '') 	{$this->t['sibgc'] = '#'.$this->t['sibgc'];}
		if($this->t['sibrdc'] 	!= '') 	{$this->t['sibrdc'] = '#'.$this->t['sibrdc'];}
		if($this->t['siec'] 		!= '') 	{$this->t['siec'] = '#'.$this->t['siec'];}
		if ((int)$siw > 0) 			{$this->t['siw'] = (int)$siw;}
		if ((int)$sih > 0) 			{$this->t['sih'] = (int)$sih;}

		// Medium
		$this->t['miw']		= $params->get('medium_image_width', 256 );
		$this->t['mih']		= $params->get('medium_image_height', 192 );

		//After creating an image (post with data);
		$this->t['msbgc']	= Factory::getApplication()->getInput()->get( 'msbgc', '', '', 'string' );
		$this->t['mibgc']	= Factory::getApplication()->getInput()->get( 'mibgc', '', '', 'string' );
		$this->t['mibrdc']	= Factory::getApplication()->getInput()->get( 'mibrdc', '', '', 'string' );
		$this->t['mie']		= Factory::getApplication()->getInput()->get( 'mie', '', '', 'int' );
		$this->t['miec']		= Factory::getApplication()->getInput()->get( 'miec', '', '', 'string' );
		$miw					= Factory::getApplication()->getInput()->get( 'miw', '', '', 'int' );
		$mih					= Factory::getApplication()->getInput()->get( 'mih', '', '', 'int' );

		$this->t['msbgc']	= PhocaGalleryUtils::filterInput($this->t['msbgc']);
		$this->t['mibgc']	= PhocaGalleryUtils::filterInput($this->t['mibgc']);
		$this->t['mibrdc']	= PhocaGalleryUtils::filterInput($this->t['mibrdc']);
		$this->t['miec']		= PhocaGalleryUtils::filterInput($this->t['miec']);

		if($this->t['msbgc']		!= '') 	{$this->t['msbgc'] = '#'.$this->t['msbgc'];}
		if($this->t['mibgc'] 	!= '') 	{$this->t['mibgc'] = '#'.$this->t['mibgc'];}
		if($this->t['mibrdc']	!= '') 	{$this->t['mibrdc'] = '#'.$this->t['mibrdc'];}
		if($this->t['miec'] 		!= '') 	{$this->t['miec'] = '#'.$this->t['miec'];}
		if ((int)$miw > 0) 			{$this->t['miw'] = (int)$miw;}
		if ((int)$mih > 0) 			{$this->t['mih'] = (int)$mih;}*/

		$this->addToolbar();
		parent::display($tpl);

	}


	protected function addToolbar() {

		ToolbarHelper::title(   Text::_( 'COM_PHOCAGALLERY_THEMES' ), 'grid-view-2');
		ToolbarHelper::cancel('phocagalleryt.cancel', 'JToolbar_CLOSE');
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}

	function themeName() {
		// Get an array of all the xml files from teh installation directory
		$path		= PhocaGalleryPath::getPath();
		$xmlFiles 	= Folder::files($path->image_abs_front, '.xml$', 1, true);

		// If at least one xml file exists
		if (count($xmlFiles) > 0) {
			foreach ($xmlFiles as $file)
			{
				// Is it a valid joomla installation manifest file?
				$manifest = $this->_isManifest($file);

				if(!is_null($manifest)) {
					foreach ($manifest->children() as $key => $value){
						if ((string)$value->getName() == 'name') {
							return (string)$value;
						}
					}
				}
				return false;
			}
			return false;
		} else {
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
}
?>
