<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\PluginHelper;
jimport( 'joomla.application.component.view');
phocagalleryimport( 'phocagallery.image.image');
phocagalleryimport( 'phocagallery.image.imagefront');
phocagalleryimport( 'phocagallery.file.filethumbnail');
phocagalleryimport( 'phocagallery.rate.rateimage');
phocagalleryimport( 'phocagallery.picasa.picasa');
phocagalleryimport( 'phocagallery.facebook.fbsystem');
phocagalleryimport( 'phocagallery.youtube.youtube');
phocagalleryimport( 'phocagallery.user.user');
phocagalleryimport('phocagallery.comment.comment');
phocagalleryimport('phocagallery.comment.commentimage');

class PhocaGalleryViewDetail extends HtmlView
{

	public $t;
	protected $params;
	protected $itemnext;
	protected $itemprev;

	function display($tpl = null) {


		$app		= Factory::getApplication();
		$uri        = Uri::getInstance();
        $id			= $app->input->get('id', 0, 'int');




		$document				= Factory::getDocument();
		$this->params			= $app->getParams();
		$user					= Factory::getUser();
		$var['slideshow']		= $app->input->get('phocaslideshow', 0, 'int');
		$var['download'] 		= $app->input->get('phocadownload', 0, 'int');
		$this->t['action']	    = $uri->toString();
		$path					= PhocaGalleryPath::getPath();
		$this->itemId			= $app->input->get('Itemid', 0, 'int');

		$this->t['tmpl']			= $app->input->get('tmpl', '', 'string');

		$neededAccessLevels		= PhocaGalleryAccess::getNeededAccessLevels();
		$access					= PhocaGalleryAccess::isAccess($user->getAuthorisedViewLevels(), $neededAccessLevels);

		PhocaGalleryRenderFront::renderAllCSS();
		PhocaGalleryRenderFront::renderMainJs();


		// Information from the plugin - window is displayed after plugin action
		$get				= array();
		$get['detail']		= $app->input->get( 'detail', '',  'string');
		$get['buttons']		= $app->input->get( 'buttons', '',  'string' );
		$get['ratingimg']	= $app->input->get( 'ratingimg', '', 'string' );

		$this->t['tmpl']		= $app->input->get( 'tmpl', '',  'string');

		$this->t['picasa_correct_width_l']		= (int)$this->params->get( 'large_image_width', 640 );
		$this->t['picasa_correct_height_l']		= (int)$this->params->get( 'large_image_height', 480 );
		$this->t['enablecustomcss']				= $this->params->get( 'enable_custom_css', 0);
		$this->t['customcss']					= $this->params->get( 'custom_css', '');
		$this->t['enable_multibox']				= $this->params->get( 'enable_multibox', 0);
		$this->t['multibox_height']				= (int)$this->params->get( 'multibox_height', 560 );
		$this->t['multibox_width']				= (int)$this->params->get( 'multibox_width', 980 );
		$this->t['multibox_map_height']			= (int)$this->params->get( 'multibox_map_height', 300 );
		$this->t['multibox_map_width']			= (int)$this->params->get( 'multibox_map_width', 280 );
		$this->t['multibox_height_overflow']		= (int)$this->t['multibox_height'] - 10;//padding
		$this->t['multibox_comments_width']		= $this->params->get( 'multibox_comments_width', 300 );
		$this->t['multibox_comments_height']		= $this->params->get( 'multibox_comments_height', 600 );
		$this->t['multibox_thubms_box_width']	= $this->params->get( 'multibox_thubms_box_width', 300 );
		$this->t['multibox_thubms_count']		= $this->params->get( 'multibox_thubms_count', 4 );
		$this->t['large_image_width']			= $this->params->get( 'large_image_width', 640 );
		$this->t['large_image_height']			= $this->params->get( 'large_image_height', 640 );
		$this->t['multibox_fixed_cols']			= $this->params->get( 'multibox_fixed_cols', 1 );
		$this->t['display_multibox']				= $this->params->get( 'display_multibox', array(1,2));
		$this->t['display_title_description']	= $this->params->get( 'display_title_description', 0);
		$this->t['responsive']					= $this->params->get( 'responsive', 0 );
		$this->t['bootstrap_icons']				= $this->params->get( 'bootstrap_icons', 0 );

		$this->t['display_comment_img']				= $this->params->get( 'display_comment_img', 0 );


		// CSS
		PhocaGalleryRenderFront::renderAllCSS(1);

		// Plugin information
		$this->t['detailwindow']	= $this->params->get( 'detail_window', 0 );
		if (isset($get['detail']) && $get['detail'] != '') {
			$this->t['detailwindow'] 		= $get['detail'];
		}

		// Plugin information
		$this->t['detailbuttons']	= $this->params->get( 'detail_buttons', 1 );
		if (isset($get['buttons']) && $get['buttons'] != '') {
			$this->t['detailbuttons'] = $get['buttons'];
		}

		// Close and Reload links (for different window types)
		$close = PhocaGalleryRenderFront::renderCloseReloadDetail($this->t['detailwindow']);
		$this->t['detailwindowclose']	= $close['detailwindowclose'];
		$this->t['detailwindowreload']	= $close['detailwindowreload'];


		$this->t['displaydescriptiondetail']		= $this->params->get( 'display_description_detail', 0 );

		$this->t['display_rating_img']				= $this->params->get( 'display_rating_img', 0 );
		$this->t['display_icon_download'] 			= $this->params->get( 'display_icon_download', 0 );
		$this->t['externalcommentsystem'] 			= $this->params->get( 'external_comment_system', 0 );
		$this->t['largewidth'] 					= $this->params->get( 'large_image_width', 640 );
		$this->t['largeheight'] 					= $this->params->get( 'large_image_height', 480 );
		$this->t['boxlargewidth'] 					= $this->params->get( 'front_modal_box_width', 680 );
		$this->t['boxlargeheight'] 				= $this->params->get( 'front_modal_box_height', 560 );
		$this->t['slideshow_delay'] 				= $this->params->get( 'slideshow_delay', 3000 );
		$this->t['slideshow_pause'] 				= $this->params->get( 'slideshow_pause', 2500 );
		$this->t['slideshowrandom'] 				= $this->params->get( 'slideshow_random', 0 );
		$this->t['slideshow_description'] 			= $this->params->get( 'slideshow_description', 'peekaboo' );
		$this->t['gallerymetakey'] 				= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 				= $this->params->get( 'gallery_metadesc', '' );
		$this->t['altvalue']		 				= $this->params->get( 'alt_value', 1 );
		$this->t['enablecustomcss']				= $this->params->get( 'enable_custom_css', 0);
		$this->t['customcss']					= $this->params->get( 'custom_css', '');
		$this->t['display_tags_links'] 			= $this->params->get( 'display_tags_links', 0 );
		$this->t['ytb_display'] 					= $this->params->get( 'ytb_display', 0 );

		/*$paramsFb = PhocaGalleryFbSystem::getCommentsParams($this->params->get( 'fb_comment_user_id', ''));// Facebook
		$this->t['fb_comment_app_id']		= isset($paramsFb['fb_comment_app_id']) ? $paramsFb['fb_comment_app_id'] : '';
		$this->t['fb_comment_width']			= isset($paramsFb['fb_comment_width']) ? $paramsFb['fb_comment_width'] : 550;
		$this->t['fb_comment_lang'] 			= isset($paramsFb['fb_comment_lang']) ? $paramsFb['fb_comment_lang'] : 'en_US';
		$this->t['fb_comment_count'] 		= isset($paramsFb['fb_comment_count']) ? $paramsFb['fb_comment_count'] : '';*/

        $this->t['max_upload_char']			= $this->params->get( 'max_upload_char', 1000 );
		$this->t['max_comment_char']			= $this->params->get( 'max_comment_char', 1000 );
		$this->t['max_create_cat_char']			= $this->params->get( 'max_create_cat_char', 1000 );

		$oH = '';
		if ($this->t['enable_multibox'] == 1) {
			$this->t['fb_comment_width'] = $this->t['multibox_comments_width'];
			$oH = 'overflow:hidden;';
		}


		// CSS
		/*JHtml::stylesheet('media/com_phocagallery/css/phocagallery.css' );
		if ($this->t['enablecustomcss'] == 1) {
			HTMLHelper::stylesheet('media/com_phocagallery/css/phocagallerycustom.css' );
			if ($this->t['customcss'] != ''){
				$document->addCustomTag( "\n <style type=\"text/css\"> \n"
				.$this->escape(strip_tags($this->t['customcss']))
				."\n </style> \n");

			}
		}*/

		//Multibox displaying
		/*$this->t['mb_title'] 		= PhocaGalleryUtils::isEnabledMultiboxFeature(1);
		$this->t['mb_desc'] 			= PhocaGalleryUtils::isEnabledMultiboxFeature(2);
		$this->t['mb_uploaded_by'] 	= PhocaGalleryUtils::isEnabledMultiboxFeature(3);
		$this->t['mb_rating'] 		= PhocaGalleryUtils::isEnabledMultiboxFeature(4);
		$this->t['mb_maps'] 			= PhocaGalleryUtils::isEnabledMultiboxFeature(5);
		$this->t['mb_tags'] 			= PhocaGalleryUtils::isEnabledMultiboxFeature(6);
		$this->t['mb_comments'] 		= PhocaGalleryUtils::isEnabledMultiboxFeature(7);
		$this->t['mb_thumbs'] 		= PhocaGalleryUtils::isEnabledMultiboxFeature(8);


		// No bar in Detail View
		if ($this->t['detailwindow'] == 7) {

		} else {

			$oS = " html, body, .contentpane, #all, #main {".$oH."padding:0px !important;margin:0px !important; width: 100% !important; max-width: 100% !important;} \n"
				// gantry-fix-begin
				."body {min-width:100%} \n"
				.".rt-container {width:100%} \n";
				// gantry-fix-end
			if ($this->t['responsive'] == 1) {
				$oS .= "html, body {height:100%;} \n"
				. ".pg-detail-view {
					position: relative;
					top: 50%;
					transform: perspective(1px) translateY(-50%);
				} \n";

			}


				$document->addCustomTag( "<style type=\"text/css\"> \n" . $oS . " </style> \n");
		}
*/
		// Download from the detail view which is not in the popupbox
		if ($var['download'] == 2 ){
			$this->t['display_icon_download'] = 2;
		}

		// Plugin Information
		if (isset($get['ratingimg']) && $get['ratingimg'] != '') {
			$this->t['display_rating_img'] = $get['ratingimg'];
		}



		// Model
		$model	= $this->getModel();
		$item	= $model->getData();

		//Multibox Thumbnails
		/*$this->t['mb_thumbs_data'] = '';
		if ($this->t['mb_thumbs'] == 1) {
			// if we get item variable, we have rights to load the thumbnails, this is why we checking it
			if (isset($item->id) && isset($item->catid) && (int)$item->id > 0 && (int)$item->catid > 0) {
				$this->t['mb_thumbs_data'] = $model->getThumbnails((int)$item->id, (int)$item->catid, (int)$item->ordering);
			}
		}*/

		// User Avatar
		$this->t['useravatarimg'] 		= '';
		$this->t['useravatarmiddle'] 	= '';
		$userAvatar = false;
		if (isset($item->userid)) {
			$userAvatar						= PhocaGalleryUser::getUserAvatar($item->userid);
		}
		if ($userAvatar) {
			$pathAvatarAbs	= $path->avatar_abs  .'thumbs/phoca_thumb_s_'. $userAvatar->avatar;
			$pathAvatarRel	= $path->avatar_rel . 'thumbs/phoca_thumb_s_'. $userAvatar->avatar;
			if (File::exists($pathAvatarAbs)){
				$sIH	= $this->params->get( 'small_image_height', 96 );
				$sIHR	= @getImageSize($pathAvatarAbs);
				if (isset($sIHR[1])) {
					$sIH = $sIHR[1];
				}
				if ((int)$sIH > 0) {
					$this->t['useravatarmiddle'] = ((int)$sIH / 2) - 10;
				}
				$this->t['useravatarimg']	= '<img src="'.Uri::base(true) . '/' . $pathAvatarRel.'?imagesid='.md5(uniqid(time())).'" alt="" />';
			}
		}



		// Access check - don't display the image if you have no access to this image (if user add own url)
		// USER RIGHT - ACCESS - - - - - - - - - -
		$rightDisplay	= 0;
		if (!empty($item)) {
			$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $item->cataccessuserid, $item->cataccess, $user->getAuthorisedViewLevels(), $user->get('id', 0), 0);
		}

		if ((int)$rightDisplay == 0) {

			echo $close['html'];
			//Some problem with cache - Joomla! return this message if there is no reason for do it.
			//$this->t['pl']		= 'index.php?option=com_users&view=login&return='.base64_encode($uri->toString());
			//$app->redirect(JRoute::_($this->t['pl'], false));
			exit;

		}

		// - - - - - - - - - - - - - - - - - - - -

		phocagalleryimport('phocagallery.image.image');
		phocagalleryimport('phocagallery.render.renderdetailbutton'); // Javascript Slideshow buttons
		$detailButton 			= new PhocaGalleryRenderDetailButton();
		if ($this->t['enable_multibox'] == 1) {
			$detailButton->setType('multibox');
		}
		$item->reloadbutton		= $detailButton->getReload($item->catslug, $item->slug);
		$item->closebutton		= $detailButton->getClose($item->catslug, $item->slug);
		$item->closetext		= $detailButton->getCloseText($item->catslug, $item->slug);
		$item->nextbutton		= $detailButton->getNext((int)$item->catid, (int)$item->id, (int)$item->ordering);
		$item->nextbuttonhref	= $detailButton->getNext((int)$item->catid, (int)$item->id, (int)$item->ordering, 1);
		$item->prevbutton		= $detailButton->getPrevious((int)$item->catid, (int)$item->id, (int)$item->ordering);
		$slideshowData			= $detailButton->getJsSlideshow((int)$item->catid, (int)$item->id, (int)$var['slideshow'], $item->catslug, $item->slug);
		$item->slideshowbutton	= $slideshowData['icons'];
		$item->slideshowfiles	= $slideshowData['files'];
		$item->slideshow		= $var['slideshow'];
		$item->download			= $var['download'];

		// ALT VALUE
		$altValue	= PhocaGalleryRenderFront::getAltValue($this->t['altvalue'], $item->title, $item->description, $item->metadesc);
		$item->altvalue			= $altValue;

		// Get file thumbnail or No Image
		$item->filenameno		= $item->filename;
		$item->filename			= PhocaGalleryFile::getTitleFromFile($item->filename, 1);
		$item->filesize			= PhocaGalleryFile::getFileSize($item->filenameno);
		$realImageSize	= '';
		$extImage = PhocaGalleryImage::isExtImage($item->extid);
		if ($extImage) {
			$item->extl			=	$item->extl;
			$item->exto			=	$item->exto;
			$realImageSize 		= PhocaGalleryImage::getRealImageSize($item->extl, '', 1);
			$item->imagesize 	= PhocaGalleryImage::getImageSize($item->exto, 1, 1);
			if ($item->extw != '') {
				$extw 		= explode(',',$item->extw);
				$item->extw	= $extw[0];
			}
			if ($item->exth != '') {
				$exth 		= explode(',',$item->exth);
				$item->exth	= $exth[0];
			}
			$correctImageRes 		= PhocaGalleryPicasa::correctSizeWithRate($item->extw, $item->exth, $this->t['picasa_correct_width_l'], $this->t['picasa_correct_height_l']);
			$item->linkimage		= HTMLHelper::_( 'image', $item->extl, $item->altvalue, array('width' => $correctImageRes['width'], 'height' => $correctImageRes['height'], 'class' => 'pg-detail-image img img-responsive'));
			$item->realimagewidth 	= $correctImageRes['width'];
			$item->realimageheight	= $correctImageRes['height'];


		} else {
			$item->linkthumbnailpath	= PhocaGalleryImageFront::displayCategoryImageOrNoImage($item->filenameno, 'large');
			$item->linkimage			= HTMLHelper::_( 'image', $item->linkthumbnailpath, $item->altvalue, array( 'class' => 'pg-detail-image img img-responsive'));
			$realImageSize 				= PhocaGalleryImage::getRealImageSize ($item->filenameno);
			$item->imagesize			= PhocaGalleryImage::getImageSize($item->filenameno, 1);
			if (isset($realImageSize['w']) && isset($realImageSize['h'])) {
				$item->realimagewidth		= $realImageSize['w'];
				$item->realimageheight		= $realImageSize['h'];
			} else {
				$item->realimagewidth	 	= $this->t['largewidth'];
				$item->realimageheight		= $this->t['largeheight'];
			}
		}

		// Add Statistics
		$model->hit($app->input->get( 'id', '', 'int' ));

		// R A T I N G
		// Only registered (VOTES + COMMENTS)
		$this->t['not_registered_img'] 	= true;
		$this->t['usernameimg']		= '';
		if ($access > 0) {
			$this->t['not_registered_img'] 	= false;
			$this->t['usernameimg']		= $user->name;
		}

		// VOTES Statistics Img
		//if ((int)$this->t['display_rating_img'] == 1 || $this->t['mb_rating']) {
        if ((int)$this->t['display_rating_img'] == 1) {

			$this->t['votescountimg']		= 0;
			$this->t['votesaverageimg'] 	= 0;
			$this->t['voteswidthimg']		= 0;
			$votesStatistics	= PhocaGalleryRateImage::getVotesStatistics((int)$item->id);
			if (!empty($votesStatistics->count)) {
				$this->t['votescountimg'] = $votesStatistics->count;
			}
			if (!empty($votesStatistics->average)) {
				$this->t['votesaverageimg'] = $votesStatistics->average;
				if ($this->t['votesaverageimg'] > 0) {
					$this->t['votesaverageimg'] 	= round(((float)$this->t['votesaverageimg'] / 0.5)) * 0.5;
					$this->t['voteswidthimg']		= 22 * $this->t['votesaverageimg'];
				} else {
					$this->t['votesaverageimg'] = (int)0;// not float displaying
				}
			}
			if ((int)$this->t['votescountimg'] > 1) {
				$this->t['votestextimg'] = 'COM_PHOCAGALLERY_VOTES';
			} else {
				$this->t['votestextimg'] = 'COM_PHOCAGALLERY_VOTE';
			}

			// Already rated?
			$this->t['alreay_ratedimg']	= PhocaGalleryRateImage::checkUserVote( (int)$item->id, (int)$user->id );
		}

		// Tags
		$this->t['displaying_tags_output'] = '';
		//if ($this->t['display_tags_links'] == 1 || $this->t['display_tags_links'] == 3 || $this->t['mb_tags'])  {
        if ($this->t['display_tags_links'] == 1 || $this->t['display_tags_links'] == 3)  {

			if ($this->t['detailwindow'] == 7) {
				$this->t['displaying_tags_output'] = PhocaGalleryTag::displayTags($item->id);
			} else {
				$this->t['displaying_tags_output'] = PhocaGalleryTag::displayTags($item->id, 1);
			}
		}


        // Only registered (VOTES + COMMENTS)
		$this->t['not_registered'] 	= true;
		$this->t['name']		= '';
		if ($access) {
			$this->t['not_registered'] 	= false;
			$this->t['name']		= $user->name;
		}


        $this->t['already_commented'] = PhocaGalleryCommentImage::checkUserComment( (int)$item->id, (int)$user->id );
		$this->t['commentitem']					= PhocaGalleryCommentImage::displayComment( (int)$item->id);



		$this->itemnext[0]			= false;
		$this->itemprev[0]			= false;
		//if ($this->t['enable_image_navigation'] == 1) {
			if (isset($item->ordering) && isset($item->catid) && isset($item->id) && $item->catid > 0 && $item->id > 0) {
				$this->itemnext			= $model->getItemNext($item->ordering, $item->catid);
				$this->itemprev			= $model->getItemPrev($item->ordering, $item->catid);
			}
		//}

		// ASIGN

		$this->item = $item;
		$this->_prepareDocument($item);



		if ($this->t['enable_multibox'] == 1) {

			if ($item->download > 0) {

				if ($this->t['display_icon_download'] == 2) {
					$backLink = 'index.php?option=com_phocagallery&view=category&id='. $item->catslug.'&Itemid='. $this->itemId;
					phocagalleryimport('phocagallery.file.filedownload');
					if (isset($item->exto) && $item->exto != '') {

						PhocaGalleryFileDownload::download($item, $backLink, 1);
					} else {
						PhocaGalleryFileDownload::download($item, $backLink);
					}
					exit;
				} else {
					parent::display('multibox');
					//parent::display('download');
				}
			} else {


				if (isset($item->videocode) && $item->videocode != '' && $item->videocode != '0') {
					$item->videocode = PhocaGalleryYoutube::displayVideo($item->videocode);
				}
				parent::display('multibox');
			}
		} else if (isset($item->videocode) && $item->videocode != ''  && $item->videocode != '0') {
			$item->videocode = PhocaGalleryYoutube::displayVideo($item->videocode);

			if ($this->t['detailwindow'] != 7 && $this->t['ytb_display'] == 1) {
				$document->addCustomTag( "<style type=\"text/css\"> \n"
					." html, body, .contentpane, div#all, div#main, div#system-message-container {padding: 0px !important;margin: 0px !important;} \n"
					." div#sbox-window {background-color:#fff;padding: 0px;margin: 0px;} \n"
					." </style> \n");
			}

			parent::display('video');
		} else {
			//parent::display('slideshowjs');
			/*if ($item->slideshow == 1) {
				parent::display('slideshow');
			} else*/
            if ($item->download > 0) {

				if ($this->t['display_icon_download'] == 2) {
					$backLink = 'index.php?option=com_phocagallery&view=category&id='. $item->catslug.'&Itemid='. $this->itemId;
					phocagalleryimport('phocagallery.file.filedownload');
					if (isset($item->exto) && $item->exto != '') {

						PhocaGalleryFileDownload::download($item, $backLink, 1);
					} else {
						PhocaGalleryFileDownload::download($item, $backLink);
					}
					exit;
				} else {
					parent::display('download');
				}
			} else {
				parent::display($tpl);
			}
		}
	}

	protected function _prepareDocument($item) {

		$app		= Factory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		//$this->params		= $app->getParams();
		$title 		= null;

		$this->t['gallerymetakey'] 		= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 		= $this->params->get( 'gallery_metadesc', '' );

		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->get('sitename'));
		} else if ($app->get('sitename_pagetitles', 0) == 1) {
			$title = Text::sprintf('JPAGETITLE', htmlspecialchars_decode($app->get('sitename')), $title);

			if (isset($item->title) && $item->title != '') {
				$title = $title .' - ' .  $item->title;
			}

		} else if ($app->get('sitename_pagetitles', 0) == 2) {

			if (isset($item->title) && $item->title != '') {
				$title = $title .' - ' .  $item->title;
			}

			$title = Text::sprintf('JPAGETITLE', $title, htmlspecialchars_decode($app->get('sitename')));
		}
		$this->document->setTitle($title);

		if ($item->metadesc != '') {
			$this->document->setDescription($item->metadesc);
		} else if ($this->t['gallerymetadesc'] != '') {
			$this->document->setDescription($this->t['gallerymetadesc']);
		} else if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		}

		if ($item->metakey != '') {
			$this->document->setMetadata('keywords', $item->metakey);
		} else if ($this->t['gallerymetakey'] != '') {
			$this->document->setMetadata('keywords', $this->t['gallerymetakey']);
		} else if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->get('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}

		/*if ($app->get('MetaAuthor') == '1') {
			$this->document->setMetaData('author', $this->item->author);
		}

		/*$mdata = $this->item->metadata->toArray();
		foreach ($mdata as $k => $v) {
			if ($v) {
				$this->document->setMetadata($k, $v);
			}
		}*/

		// Breadcrumbs TO DO (Add the whole tree)
		/*if (isset($this->category[0]->parentid)) {
			if ($this->category[0]->parentid == 1) {
			} else if ($this->category[0]->parentid > 0) {
				$pathway->addItem($this->category[0]->parenttitle, Route::_(PhocaDocumentationHelperRoute::getCategoryRoute($this->category[0]->parentid, $this->category[0]->parentalias)));
			}
		}

		if (!empty($this->category[0]->title)) {
			$pathway->addItem($this->category[0]->title);
		}*/


		// Features added by Bernard Gilly - alphaplug.com
		// load external plugins
		/*$user       = Factory::getUser();
		$imgid      = $item->id;
		$catid		= $item->catid;
		$db	   		= Factory::getDBO();
		$query 		= "SELECT owner_id FROM #__phocagallery_categories WHERE `id`='$catid'";
		$db->setQuery( $query );
		$ownerid 	= $db->loadResult();
		$dispatcher = JDispatcher::getInstance();
		PluginHelper::importPlugin('phocagallery');
		$results 	= Factory::getApplication()->triggerEvent('onViewImage', array($imgid, $catid, $ownerid, $user->id ) );*/

		$user       = Factory::getUser();
		//$dispatcher = J Dispatcher::getInstance();
		PluginHelper::importPlugin('phocagallery');
		$results 	= Factory::getApplication()->triggerEvent('onViewImage', array((int)$item->id, (int)$item->catid, (int)$item->owner_id, (int)$user->id ) );


	}
}
