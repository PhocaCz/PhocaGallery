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
phocagalleryimport( 'phocagallery.render.renderinfo' );
phocagalleryimport( 'phocagallery.utils.utils' );

class PhocaGalleryCpViewPhocaGalleryIn extends JViewLegacy
{
	protected $t;
	protected $r;
	protected $foutput;

	public function display($tpl = null) {


		$params 	= JComponentHelper::getParams('com_phocagallery');

		//$this->sidebar = JHtmlSidebar::render();
		$this->t	= PhocaGalleryUtils::setVars('in');
		$this->r	= new PhocaGalleryRenderAdminview();

		$this->t['component_head'] 	= $this->t['l'].'_PHOCA_Gallery';
		$this->t['component_links']	= PhocaGalleryRenderAdmin::getLinks(1);


		$this->t['version'] 					= PhocaGalleryRenderInfo::getPhocaVersion();
		$this->t['enablethumbcreation']		= $params->get('enable_thumb_creation', 1 );
		$this->t['paginationthumbnailcreation']= $params->get('pagination_thumbnail_creation', 0 );
		$this->t['cleanthumbnails']			= $params->get('clean_thumbnails', 0 );
		$this->t['enablethumbcreationstatus'] 	= PhocaGalleryRenderAdmin::renderThumbnailCreationStatus((int)$this->t['enablethumbcreation'], 1);

		//Main Function support

	//	echo '<table border="1" cellpadding="5" cellspacing="5" style="border:1px solid #ccc;border-collapse:collapse">';

		$function = array('getImageSize','imageCreateFromJPEG', 'imageCreateFromPNG', 'imageCreateFromGIF', 'imageCreateFromWEBP', 'imageRotate', 'imageCreateTruecolor', 'imageCopyResampled', 'imageFill', 'imageColorTransparent', 'imageColorAllocate', 'exif_read_data');
		$this->foutput = '';
		foreach ($function as $key => $value) {

			if (function_exists($value)) {
				$bgStyle 	= 'class="alert alert-success"';
				//$icon		= 'true';
				$icon		= 'success';
				$iconText	= JText::_('COM_PHOCAGALLERY_ENABLED');
			} else {
				$bgStyle = 'class="alert alert-error"';
				//$icon		= 'false';
				$icon		= 'minus-circle';
				$iconText	= JText::_('COM_PHOCAGALLERY_DISABLED');
			}

			$this->foutput .= '<tr '.$bgStyle.'><td>'.JText::_('COM_PHOCAGALLERY_FUNCTION') .' '. $value.'</td>';
			//$this->foutput .=  '<td align="center">'.Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ).'</td>';
			//$this->foutput .=  '<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-'.$icon.'.png', JText::_($iconText)).'</td></tr>';

			$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-success" title="'. JText::_('COM_PHOCAGALLERY_ENABLED') .'"></i></td>';
			$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  JText::_($iconText) .'"></i></td></tr>';

		}

		// PICASA
		$this->foutput .= '<tr><td align="left"><b>'. JText::_('COM_PHOCAGALLERY_PICASA_SUPPORT').'</b></td></tr>';

		if(!PhocaGalleryUtils::iniGetBool('allow_url_fopen')){
			$bgStyle 	= 'class="alert alert-error"';
			$icon		= 'minus-circle';
			$iconText	= JText::_('COM_PHOCAGALLERY_DISABLED');
		} else {
			$bgStyle 	= 'class="alert alert-success"';
			$icon		= 'success';
			$iconText	= JText::_('COM_PHOCAGALLERY_ENABLED');
		}

		$this->foutput .= '<tr '.$bgStyle.'><td>'.JText::_('COM_PHOCAGALLERY_PHP_SETTINGS_PARAM') .' allow_url_fopen ('.JText::_('COM_PHOCAGALLERY_ENABLED_IF_CURL_DISABLED') .')</td>';
		//$this->foutput .=  '<td align="center">'.Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ).'</td>';
		//$this->foutput .=  '<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-'.$icon.'.png', JText::_($iconText)).'</td></tr>';
		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-success" title="'. JText::_('COM_PHOCAGALLERY_ENABLED') .'"></i></td>';
		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  JText::_($iconText) .'"></i></td></tr>';


		if(function_exists("curl_init")){
			$bgStyle 	= 'class="alert alert-success"';
			$icon		= 'success';
			$iconText	= JText::_('COM_PHOCAGALLERY_ENABLED');
		} else {
			$bgStyle = 'class="alert alert-error"';
			$icon		= 'minus-circle';
			$iconText	= JText::_('COM_PHOCAGALLERY_DISABLED');
		}

		if(function_exists("json_decode")){
			$bgStylej 	= 'class="alert alert-success"';
			$iconj		= 'success';
			$iconTextj	= JText::_('COM_PHOCAGALLERY_ENABLED');
		} else {
			$bgStylej = 'class="alert alert-error"';
			$iconj		= 'minus-circle';
			$iconTextj	= JText::_('COM_PHOCAGALLERY_DISABLED');
		}

		$this->foutput .= '<tr '.$bgStyle.'><td>'.JText::_('COM_PHOCAGALLERY_FUNCTION') .' cURL ('.JText::_('COM_PHOCAGALLERY_ENABLED_IF_FOPEN_DISABLED') .')</td>';
		//$this->foutput .=  '<td align="center">'.Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ).'</td>';
		//$this->foutput .=  '<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-'.$icon.'.png', JText::_($iconText)).'</td></tr>';

		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-success" title="'. JText::_('COM_PHOCAGALLERY_ENABLED') .'"></i></td>';
		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  JText::_($iconText) .'"></i></td></tr>';

		$this->foutput .= '<tr '.$bgStylej.'><td>'.JText::_('COM_PHOCAGALLERY_FUNCTION') .' json_decode</td>';
		//$this->foutput .=  '<td align="center">'.Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ).'</td>';
		//$this->foutput .=  '<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-'.$iconj.'.png', JText::_($iconTextj)).'</td></tr>';

		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-success" title="'. JText::_('COM_PHOCAGALLERY_ENABLED') .'"></i></td>';
		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  JText::_($iconText) .'"></i></td></tr>';




		$this->addToolbar();
		parent::display($tpl);
	}


	protected function addToolBar(){
		require_once JPATH_COMPONENT.'/helpers/phocagallerycp.php';
		$canDo = PhocaGalleryCpHelper::getActions(NULL);
        JToolbarHelper ::title(JText::_('COM_PHOCAGALLERY_PG_INFO'), 'info');

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = JToolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocagallery" class="btn btn-small"><i class="icon-home-2" title="'.JText::_('COM_PHOCAGALLERY_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCAGALLERY_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.admin')) {
			JToolbarHelper ::preferences('com_phocagallery');
		}
	    JToolbarHelper ::help( 'screen.phocagallery', true );
    }
}
?>
