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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );
phocagalleryimport( 'phocagallery.render.renderinfo' );
phocagalleryimport( 'phocagallery.utils.utils' );

class PhocaGalleryCpViewPhocaGalleryIn extends HtmlView
{
	protected $t;
	protected $r;
	protected $foutput;

	public function display($tpl = null) {


		$params 	= ComponentHelper::getParams('com_phocagallery');

		//$this->sidebar = Sidebar::render();
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

		$function = array('getImageSize','imageCreateFromJPEG', 'imageCreateFromPNG', 'imageCreateFromGIF', 'imageCreateFromWEBP', 'imageCreateFromAVIF', 'imageRotate', 'imageCreateTruecolor', 'imageCopyResampled', 'imageFill', 'imageColorTransparent', 'imageColorAllocate', 'exif_read_data');
		$this->foutput = '';
		foreach ($function as $key => $value) {

			if (function_exists($value)) {
				$bgStyle 	= 'class="alert alert-success"';
				//$icon		= 'true';
				$icon		= 'success';
				$iconText	= Text::_('COM_PHOCAGALLERY_ENABLED');
			} else {
				$bgStyle = 'class="alert alert-error alert-danger"';
				//$icon		= 'false';
				$icon		= 'minus-circle';
				$iconText	= Text::_('COM_PHOCAGALLERY_DISABLED');
			}

			$this->foutput .= '<tr '.$bgStyle.'><td>'.Text::_('COM_PHOCAGALLERY_FUNCTION') .' '. $value.'</td>';
			//$this->foutput .=  '<td align="center">'.JHtml::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ).'</td>';
			//$this->foutput .=  '<td align="center">'. JHtml::_('image','media/com_phocagallery/images/administrator/icon-16-'.$icon.'.png', JText::_($iconText)).'</td></tr>';

			$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-success" title="'. Text::_('COM_PHOCAGALLERY_ENABLED') .'"></i></td>';
			$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  Text::_($iconText) .'"></i></td></tr>';

		}

		// PICASA
		$this->foutput .= '<tr><td align="left"><b>'. Text::_('COM_PHOCAGALLERY_EXTERNAL_IMAGES_SUPPORT').'</b></td></tr>';

		if(!PhocaGalleryUtils::iniGetBool('allow_url_fopen')){
			$bgStyle 	= 'class="alert alert-error alert-danger"';
			$icon		= 'minus-circle';
			$iconText	= Text::_('COM_PHOCAGALLERY_DISABLED');
		} else {
			$bgStyle 	= 'class="alert alert-success"';
			$icon		= 'success';
			$iconText	= Text::_('COM_PHOCAGALLERY_ENABLED');
		}

		$this->foutput .= '<tr '.$bgStyle.'><td>'.Text::_('COM_PHOCAGALLERY_PHP_SETTINGS_PARAM') .' allow_url_fopen ('.Text::_('COM_PHOCAGALLERY_ENABLED_IF_CURL_DISABLED') .')</td>';
		//$this->foutput .=  '<td align="center">'.JHtml::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ).'</td>';
		//$this->foutput .=  '<td align="center">'. JHtml::_('image','media/com_phocagallery/images/administrator/icon-16-'.$icon.'.png', JText::_($iconText)).'</td></tr>';
		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-success" title="'. Text::_('COM_PHOCAGALLERY_ENABLED') .'"></i></td>';
		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  Text::_($iconText) .'"></i></td></tr>';


		if(function_exists("curl_init")){
			$bgStyle 	= 'class="alert alert-success"';
			$icon		= 'success';
			$iconText	= Text::_('COM_PHOCAGALLERY_ENABLED');
		} else {
			$bgStyle = 'class="alert alert-error alert-danger"';
			$icon		= 'minus-circle';
			$iconText	= Text::_('COM_PHOCAGALLERY_DISABLED');
		}

		if(function_exists("json_decode")){
			$bgStylej 	= 'class="alert alert-success"';
			$iconj		= 'success';
			$iconTextj	= Text::_('COM_PHOCAGALLERY_ENABLED');
		} else {
			$bgStylej = 'class="alert alert-error alert-danger"';
			$iconj		= 'minus-circle';
			$iconTextj	= Text::_('COM_PHOCAGALLERY_DISABLED');
		}

		$this->foutput .= '<tr '.$bgStyle.'><td>'.Text::_('COM_PHOCAGALLERY_FUNCTION') .' cURL ('.Text::_('COM_PHOCAGALLERY_ENABLED_IF_FOPEN_DISABLED') .')</td>';
		//$this->foutput .=  '<td align="center">'.JHtml::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ).'</td>';
		//$this->foutput .=  '<td align="center">'. JHtml::_('image','media/com_phocagallery/images/administrator/icon-16-'.$icon.'.png', JText::_($iconText)).'</td></tr>';

		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-success" title="'. Text::_('COM_PHOCAGALLERY_ENABLED') .'"></i></td>';
		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  Text::_($iconText) .'"></i></td></tr>';

		$this->foutput .= '<tr '.$bgStylej.'><td>'.Text::_('COM_PHOCAGALLERY_FUNCTION') .' json_decode</td>';
		//$this->foutput .=  '<td align="center">'.JHtml::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ).'</td>';
		//$this->foutput .=  '<td align="center">'. JHtml::_('image','media/com_phocagallery/images/administrator/icon-16-'.$iconj.'.png', JText::_($iconTextj)).'</td></tr>';

		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-success" title="'. Text::_('COM_PHOCAGALLERY_ENABLED') .'"></i></td>';
		$this->foutput .=  '<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  Text::_($iconText) .'"></i></td></tr>';




		$this->addToolbar();
		parent::display($tpl);
	}


	protected function addToolBar(){
		require_once JPATH_COMPONENT.'/helpers/phocagallerycp.php';
		$canDo = PhocaGalleryCpHelper::getActions(NULL);
        ToolbarHelper::title(Text::_('COM_PHOCAGALLERY_PG_INFO'), 'info');

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocagallery" class="btn btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAGALLERY_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAGALLERY_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_phocagallery');
		}
	    ToolbarHelper::help( 'screen.phocagallery', true );
    }
}
?>
