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
jimport( 'joomla.application.component.view' );
jimport( 'joomla.client.helper' );
phocagalleryimport( 'phocagallery.image.image' );
class PhocaGalleryCpViewPhocaGalleryT extends JViewLegacy
{

	protected $require_ftp;
	protected $theme_name;
	protected $files;
	protected $r;
	protected $t;

	public function display($tpl = null) {

		$document	= JFactory::getDocument();



		$this->t	= PhocaGalleryUtils::setVars('t');
		$this->r	= new PhocaGalleryRenderAdminview();

		//JHTML::stylesheet( 'media/com_phocagallery/js/jcp/picker.css' );
		//$document->addScript(JURI::base(true).'/media/com_phocagallery/js/jcp/picker.js');

		$this->require_ftp	= JClientHelper::setCredentialsFromRequest('ftp');
		$this->files	= $this->get('Files');
		$this->form		= $this->get('Form');

		if($this->themeName()) {
			$this->theme_name = $this->themeName();
		}
		// Background Image
		/*
		$params = JComponentHelper::getParams('com_phocagallery');


		// Small
		$this->t['siw']		= $params->get('small_image_width', 50 );
		$this->t['sih']		= $params->get('small_image_height', 50 );

		//After creating an image (post with data);
		$this->t['ssbgc']	= JFactory::getApplication()->input->get( 'ssbgc', '', '', 'string' );
		$this->t['sibgc']	= JFactory::getApplication()->input->get( 'sibgc', '', '', 'string' );
		$this->t['sibrdc']	= JFactory::getApplication()->input->get( 'sibrdc', '', '', 'string' );
		$this->t['sie']		= JFactory::getApplication()->input->get( 'sie', '', '', 'int' );
		$this->t['siec']		= JFactory::getApplication()->input->get( 'siec', '', '', 'string' );
		$siw					= JFactory::getApplication()->input->get( 'siw', '', '', 'int' );
		$sih					= JFactory::getApplication()->input->get( 'sih', '', '', 'int' );

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
		$this->t['miw']		= $params->get('medium_image_width', 100 );
		$this->t['mih']		= $params->get('medium_image_height', 100 );

		//After creating an image (post with data);
		$this->t['msbgc']	= JFactory::getApplication()->input->get( 'msbgc', '', '', 'string' );
		$this->t['mibgc']	= JFactory::getApplication()->input->get( 'mibgc', '', '', 'string' );
		$this->t['mibrdc']	= JFactory::getApplication()->input->get( 'mibrdc', '', '', 'string' );
		$this->t['mie']		= JFactory::getApplication()->input->get( 'mie', '', '', 'int' );
		$this->t['miec']		= JFactory::getApplication()->input->get( 'miec', '', '', 'string' );
		$miw					= JFactory::getApplication()->input->get( 'miw', '', '', 'int' );
		$mih					= JFactory::getApplication()->input->get( 'mih', '', '', 'int' );

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

		JToolbarHelper ::title(   JText::_( 'COM_PHOCAGALLERY_THEMES' ), 'grid-view-2');
		JToolbarHelper ::cancel('phocagalleryt.cancel', 'JToolbar_CLOSE');
		JToolbarHelper ::divider();
		JToolbarHelper ::help( 'screen.phocagallery', true );
	}

	function themeName() {
		// Get an array of all the xml files from teh installation directory
		$path		= PhocaGalleryPath::getPath();
		$xmlFiles 	= JFolder::files($path->image_abs_front, '.xml$', 1, true);

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
