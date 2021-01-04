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
defined( '_JEXEC' ) or die();
jimport( 'joomla.client.helper' );
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pane' );
phocagalleryimport( 'phocagallery.file.fileuploadmultiple' );
phocagalleryimport( 'phocagallery.file.fileuploadsingle' );
phocagalleryimport( 'phocagallery.file.fileuploadjava' );

class PhocaGalleryCpViewPhocagalleryI extends JViewLegacy
{
	protected $field;
	protected $fce;
	protected $folderstate;
	protected $images;
	protected $folders;
	protected $t;
	protected $r;
	protected $session;
	protected $currentFolder;

	public function display($tpl = null) {

		$this->field	= JFactory::getApplication()->input->get('field');
		$this->fce 		= 'phocaSelectFileName_'.$this->field;


		$this->t = PhocaGalleryUtils::setVars('i');
		$this->r = new PhocaGalleryRenderAdminView();
		$this->folderstate	= $this->get('FolderState');
		$this->images		= $this->get('Images');
		$this->folders		= $this->get('Folders');
		$this->session		= JFactory::getSession();

		$params 									= JComponentHelper::getParams('com_phocagallery');
		$this->t['enablethumbcreation']			= $params->get('enable_thumb_creation', 1 );
		$this->t['enablethumbcreationstatus'] 	= PhocaGalleryRenderAdmin::renderThumbnailCreationStatus((int)$this->t['enablethumbcreation']);
		$this->t['multipleuploadchunk']			= $params->get( 'multiple_upload_chunk', 0 );
		$this->t['large_image_width']	= $params->get( 'large_image_width', 640 );
		$this->t['large_image_height']	= $params->get( 'large_image_height', 480 );
		$this->t['javaboxwidth'] 		= $params->get( 'java_box_width', 480 );
		$this->t['javaboxheight'] 		= $params->get( 'java_box_height', 480 );
		$this->t['uploadmaxsize'] 		= $params->get( 'upload_maxsize', 3145728 );
		$this->t['uploadmaxsizeread'] 	= PhocaGalleryFile::getFileSizeReadable($this->t['uploadmaxsize']);
		$this->t['uploadmaxreswidth'] 	= $params->get( 'upload_maxres_width', 3072 );
		$this->t['uploadmaxresheight'] 	= $params->get( 'upload_maxres_height', 2304 );
		$this->t['enablejava'] 			= $params->get( 'enable_java', -1 );
		$this->t['enablemultiple'] 		= $params->get( 'enable_multiple', 0 );
		$this->t['multipleuploadmethod'] = $params->get( 'multiple_upload_method', 4 );
		$this->t['multipleresizewidth'] 	= $params->get( 'multiple_resize_width', -1 );
		$this->t['multipleresizeheight'] = $params->get( 'multiple_resize_height', -1 );

		if((int)$this->t['enablemultiple']  >= 0) {
			PhocaGalleryFileUploadMultiple::renderMultipleUploadLibraries();
		}
		$this->r = new PhocaGalleryRenderAdminView();


		$this->currentFolder = '';
		if (isset($this->folderstate->folder) && $this->folderstate->folder != '') {
			$this->currentFolder = $this->folderstate->folder;
		}

		// - - - - - - - - - -
		//TABS
		// - - - - - - - - - -
		$this->t['tab'] 			= JFactory::getApplication()->input->get('tab', '', '', 'string');
		$this->t['displaytabs']	= 0;

		// UPLOAD
		$this->t['currenttab']['upload'] = $this->t['displaytabs'];
		$this->t['displaytabs']++;

		// MULTIPLE UPLOAD
		if((int)$this->t['enablemultiple']  >= 0) {
			$this->t['currenttab']['multipleupload'] = $this->t['displaytabs'];
			$this->t['displaytabs']++;
		}

		// MULTIPLE UPLOAD
		if($this->t['enablejava']  >= 0) {
			$this->t['currenttab']['javaupload'] = $this->t['displaytabs'];
			$this->t['displaytabs']++;
		}

		// - - - - - - - - - - -
		// Upload
		// - - - - - - - - - - -
		$sU							= new PhocaGalleryFileUploadSingle();
		$sU->returnUrl				= 'index.php?option=com_phocagallery&view=phocagalleryi&tab=upload&tmpl=component&field='.$this->field.'&folder='. $this->currentFolder;
		$sU->tab					= 'upload';
		$this->t['su_output']	= $sU->getSingleUploadHTML();
		$this->t['su_url']		= JURI::base().'index.php?option=com_phocagallery&task=phocagalleryu.upload&amp;'
								  .$this->session->getName().'='.$this->session->getId().'&amp;'
								  . JSession::getFormToken().'=1&amp;viewback=phocagalleryi&amp;field='.$this->field.'&amp;'
								  .'folder='. $this->currentFolder.'&amp;tab=upload';


		// - - - - - - - - - - -
		// Multiple Upload
		// - - - - - - - - - - -
		// Get infos from multiple upload
		$muFailed						= JFactory::getApplication()->input->get( 'mufailed', '0', '', 'int' );
		$muUploaded						= JFactory::getApplication()->input->get( 'muuploaded', '0', '', 'int' );
		$this->t['mu_response_msg']	= $muUploadedMsg 	= '';

		if ($muUploaded > 0) {
			$muUploadedMsg = JText::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded;
		}
		if ($muFailed > 0) {
			$muFailedMsg = JText::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed;
		}
		if ($muFailed > 0 && $muUploaded > 0) {
			$this->t['mu_response_msg'] = '<div class="alert alert-info">'
			.'<button type="button" class="close" data-dismiss="alert">&times;</button>'
			.JText::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded .'<br />'
			.JText::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed.'</div>';
		} else if ($muFailed > 0 && $muUploaded == 0) {
			$this->t['mu_response_msg'] = '<div class="alert alert-error">'
			.'<button type="button" class="close" data-dismiss="alert">&times;</button>'
			.JText::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed.'</div>';
		} else if ($muFailed == 0 && $muUploaded > 0){
			$this->t['mu_response_msg'] = '<div class="alert alert-success">'
			.'<button type="button" class="close" data-dismiss="alert">&times;</button>'
			.JText::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded.'</div>';
		} else {
			$this->t['mu_response_msg'] = '';
		}

		if((int)$this->t['enablemultiple']  >= 0) {


			$mU						= new PhocaGalleryFileUploadMultiple();
			$mU->frontEnd			= 0;
			$mU->method				= $this->t['multipleuploadmethod'];
			$mU->url				= JURI::base().'index.php?option=com_phocagallery&task=phocagalleryu.multipleupload&amp;'
									 .$this->session->getName().'='.$this->session->getId().'&'
									 . JSession::getFormToken().'=1&tab=multipleupload&field='.$this->field.'&folder='. $this->currentFolder;
			$mU->reload				= JURI::base().'index.php?option=com_phocagallery&view=phocagalleryi&tmpl=component&'
									.$this->session->getName().'='.$this->session->getId().'&'
									. JSession::getFormToken().'=1&tab=multipleupload&'
									.'field='.$this->field.'&folder='. $this->currentFolder;
			$mU->maxFileSize		= PhocaGalleryFileUploadMultiple::getMultipleUploadSizeFormat($this->t['uploadmaxsize']);
			$mU->chunkSize			= '1mb';
			$mU->imageHeight		= $this->t['multipleresizeheight'];
			$mU->imageWidth			= $this->t['multipleresizewidth'];
			$mU->imageQuality		= 100;
			$mU->renderMultipleUploadJS(0, $this->t['multipleuploadchunk']);
			$this->t['mu_output']= $mU->getMultipleUploadHTML();
		}

		// - - - - - - - - - - -
		// Java Upload
		// - - - - - - - - - - -
		if((int)$this->t['enablejava']  >= 0) {
			$jU							= new PhocaGalleryFileUploadJava();
			$jU->width					= $this->t['javaboxwidth'];
			$jU->height					= $this->t['javaboxheight'];
			$jU->resizewidth			= $this->t['multipleresizewidth'];
			$jU->resizeheight			= $this->t['multipleresizeheight'];
			$jU->uploadmaxsize			= $this->t['uploadmaxsize'];
			$jU->returnUrl				= JURI::base().'index.php?option=com_phocagallery&view=phocagalleryi&tmpl=component&tab=javaupload&'
										.'field='.$this->field.'&folder='. $this->currentFolder;
			$jU->url					= JURI::base().'index.php?option=com_phocagallery&task=phocagalleryu.javaupload&amp;'
									 .$this->session->getName().'='.$this->session->getId().'&'
									 . JSession::getFormToken().'=1&amp;viewback=phocagalleryi&amp;tab=javaupload'
									 .'&field='.$this->field.'&folder='. $this->currentFolder;
			$jU->source 				= JURI::root(true).'/media/com_phocagallery/js/jupload/wjhk.jupload.jar';
			$this->t['ju_output']	= $jU->getJavaUploadHTML();

		}
		$this->t['ftp'] 			= !JClientHelper::hasCredentials('ftp');

		parent::display($tpl);
		echo Joomla\CMS\HTML\HTMLHelper::_('behavior.keepalive');
	}

	function setFolder($index = 0) {
		if (isset($this->folders[$index])) {
			$this->_tmp_folder = &$this->folders[$index];
		} else {
			$this->_tmp_folder = new JObject;
		}
	}

	function setImage($index = 0) {
		if (isset($this->images[$index])) {
			$this->_tmp_img = &$this->images[$index];
		} else {
			$this->_tmp_img = new JObject;
		}
	}
}
?>
