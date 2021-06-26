<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die();
jimport( 'joomla.html.pane' );
jimport( 'joomla.client.helper' );
jimport( 'joomla.application.component.view' );
phocagalleryimport('phocagallery.file.fileupload');
phocagalleryimport( 'phocagallery.file.fileuploadmultiple' );
phocagalleryimport( 'phocagallery.file.fileuploadsingle' );
phocagalleryimport( 'phocagallery.file.fileuploadjava' );
phocagalleryimport('phocagallery.rate.ratecategory');
phocagalleryimport('phocagallery.rate.rateimage');
phocagalleryimport('phocagallery.comment.comment');
phocagalleryimport('phocagallery.comment.commentcategory');
phocagalleryimport('phocagallery.comment.commentimage');
phocagalleryimport('phocagallery.picasa.picasa');
phocagalleryimport( 'phocagallery.facebook.fbsystem');

class PhocaGalleryViewCategory extends JViewLegacy
{
	public 		$t;
	protected 	$params;

	function display($tpl = null) {

		$app						= JFactory::getApplication();
		// Don't load all the framework if nonsense
		$id 						= $app->input->get('id', 0, 'int');
		$this->tagId				= $app->input->get( 'tagid', 0, 'int' );


		if ($id < 1 && $this->tagId < 1) {

			throw new Exception(JText::_( "COM_PHOCAGALLERY_CATEGORY_IS_UNPUBLISHED" ) , 404);

			exit;
		}

		$document					= JFactory::getDocument();
		$uri 						= \Joomla\CMS\Uri\Uri::getInstance();
		$menus						= $app->getMenu();
		$menu						= $menus->getActive();
		$this->params				= $app->getParams();
		$this->t['user'] 		= JFactory::getUser();
		$this->t['action']		= $uri->toString();
		$this->t['path'] 		= PhocaGalleryPath::getPath();
		$limitStart					= $app->input->get( 'limitstart', 0, 'int');

		$this->t['tab'] 			= $app->input->get('tab', 0, 'int');
		$this->t['pl']			= 'index.php?option=com_users&view=login&return='.base64_encode($uri->toString());
		$this->t['icon_path']	= 'media/com_phocagallery/images/';
		$this->t['plcat']		= 'index.php?option=com_phocagallery&view=category';
		$this->itemId				= $app->input->get('Itemid', 0, 'int');
		$neededAccessLevels			= PhocaGalleryAccess::getNeededAccessLevels();
		$access						= PhocaGalleryAccess::isAccess($this->t['user']->getAuthorisedViewLevels(), $neededAccessLevels);

		// CSS
		PhocaGalleryRenderFront::renderAllCSS();

		// LIBRARY
		$library 							= PhocaGalleryLibrary::getLibrary();
		$libraries['pg-group-shadowbox']	= $library->getLibrary('pg-group-shadowbox');
		$libraries['pg-group-highslide']	= $library->getLibrary('pg-group-highslide');
		$libraries['pg-group-jak']			= $library->getLibrary('pg-group-jak');
		$libraries['pg-group-photoswipe']	= $library->getLibrary('pg-group-photoswipe');


		// PARAMS
		$this->t['image_categories_size_cv'] = $this->params->get( 'image_categories_size_cv', 1 );
		$this->t['display_cat_name_title'] 	= $this->params->get( 'display_cat_name_title', 1 );
		$this->t['display_categories_cv'] 	= $this->params->get( 'display_categories_cv', 0 );
		$this->t['switch_image']				= $this->params->get( 'switch_image', 0 );
		$this->t['switch_height'] 			= $this->params->get( 'switch_height', 480 );
		$this->t['switch_width'] 			= $this->params->get( 'switch_width', 640);
		$this->t['switch_fixed_size'] 		= $this->params->get( 'switch_fixed_size', 0);
		$this->t['show_page_heading'] 		= $this->params->get( 'show_page_heading', 1 );
		$this->t['phocagallery_width']		= $this->params->get( 'phocagallery_width', '');
		$this->t['phocagallery_center']		= $this->params->get( 'phocagallery_center', 0);
		$this->t['imagewidth']				= $this->params->get( 'medium_image_width', 100 );
		$this->t['imageheight'] 				= $this->params->get( 'medium_image_height', 100 );
		$this->t['picasa_correct_width_m']	= (int)$this->params->get( 'medium_image_width', 100 );
		$this->t['picasa_correct_height_m']	= (int)$this->params->get( 'medium_image_height', 100 );
		$this->t['picasa_correct_width_s']	= (int)$this->params->get( 'small_image_width', 50 );
		$this->t['picasa_correct_height_s']	= (int)$this->params->get( 'small_image_height', 50 );
		$this->t['picasa_correct_width_l']	= (int)$this->params->get( 'large_image_width', 640 );
		$this->t['picasa_correct_height_l']	= (int)$this->params->get( 'large_image_height', 480 );
		$this->t['category_box_space'] 		= $this->params->get( 'category_box_space', 0 );
		$this->t['detail_window']			= $this->params->get( 'detail_window', 12 );
		$this->t['display_name']				= $this->params->get( 'display_name', 1);
		$this->t['display_rating']			= $this->params->get( 'display_rating', 0 );
		$this->t['display_rating_img']		= $this->params->get( 'display_rating_img', 0 );
		$this->t['display_comment']			= $this->params->get( 'display_comment', 0 );
		$this->t['display_comment_img']		= $this->params->get( 'display_comment_img', 0 );
		$this->t['display_subcategory']		= $this->params->get( 'display_subcategory', 1 );
		$this->t['display_icon_detail'] 		= $this->params->get( 'display_icon_detail', 1 );
		$this->t['display_icon_download'] 	= $this->params->get( 'display_icon_download', 2 );
		$this->t['display_icon_pc']			= $this->params->get( 'display_icon_pc', 0 );
		$this->t['display_icon_vm']			= $this->params->get( 'display_icon_vm', 0 );
		$this->t['display_img_desc_box']		= $this->params->get( 'display_img_desc_box', 0 );
		$this->t['diff_thumb_height']		= $this->params->get( 'diff_thumb_height', 0 );
		$this->t['overlib_attributes']		= $this->params->get( 'overlib_attributes', "BELOW, RIGHT, CSSCLASS, TEXTFONTCLASS, 'fontPhocaClass', FGCLASS, 'fgPhocaClass', BGCLASS, 'bgPhocaClass', CAPTIONFONTCLASS,'capfontPhocaClass', CLOSEFONTCLASS, 'capfontclosePhocaClass'");
		$this->t['responsive']				= $this->params->get( 'responsive', 0 );
		$this->t['bootstrap_icons']			= $this->params->get( 'bootstrap_icons', 0 );
		$this->t['equal_heights']			= $this->params->get( 'equal_heights', 0 );
		$this->t['photoswipe_display_caption']= $this->params->get( 'photoswipe_display_caption', 1 );
		$this->t['masonry_center']			= $this->params->get( 'masonry_center', 0 );
		$this->t['map_type']					= $this->params->get( 'map_type', 2 );

		// Switch image JS
		$this->t['basic_image']	= '';
		if ($this->t['switch_image'] == 1) {
			$this->t['wait_image']	= $this->t['path']->image_rel_front_full . 'icon-switch.gif';
			$this->t['basic_image']	= $this->t['path']->image_rel_front_full . 'phoca_thumb_l_no_image.png';
			$document->addCustomTag(PhocaGalleryRenderFront::switchImage($this->t['wait_image']));
			$basic_imageSelected = 0; // we have not selected the basic image yet
		}






		$display_cat_name_breadcrumbs 			= $this->params->get( 'display_cat_name_breadcrumbs', 1 );


		$popup_width 							= $this->params->get( 'front_modal_box_width', 680 );
		$popup_height 							= $this->params->get( 'front_modal_box_height', 560 );


		$this->t['maxuploadchar']			= $this->params->get( 'max_upload_char', 1000 );
		$this->t['maxcommentchar']			= $this->params->get( 'max_comment_char', 1000 );
		$this->t['maxcreatecatchar']			= $this->params->get( 'max_create_cat_char', 1000 );
		$this->t['commentwidth']				= $this->params->get( 'comment_width', 500 );

		$this->t['displaycategorygeotagging']= $this->params->get( 'display_category_geotagging', 0 );
		$this->t['displaycategorystatistics']= $this->params->get( 'display_category_statistics', 0 );
		// Used for Highslide JS (only image)
		$this->t['displaydescriptiondetail']	= $this->params->get( 'display_description_detail', 0 );
		$this->t['display_title_description']= $this->params->get( 'display_title_description', 0 );

		$this->t['charlengthname'] 			= $this->params->get( 'char_length_name', 15);
		$this->t['char_cat_length_name'] 	= $this->params->get( 'char_cat_length_name', 9);
		$this->t['display_icon_geo']			= $this->params->get( 'display_icon_geotagging', 0 );// Check the category
		$this->t['display_icon_geoimage']	= $this->params->get( 'display_icon_geotagging', 0 );// Check the image
		$this->t['display_camera_info']		= $this->params->get( 'display_camera_info', 0 );



		// PARAMS - Upload
		$this->t['multipleuploadchunk']		= $this->params->get( 'multiple_upload_chunk', 0 );
		$this->t['displaytitleupload']		= $this->params->get( 'display_title_upload', 0 );
		$this->t['displaydescupload'] 		= $this->params->get( 'display_description_upload', 0 );
		$this->t['enablejava'] 				= $this->params->get( 'enable_java', -1 );
		$this->t['enablemultiple'] 			= $this->params->get( 'enable_multiple', 0 );
		$this->t['multipleuploadmethod'] 	= $this->params->get( 'multiple_upload_method', 4 );
		$this->t['multipleresizewidth'] 		= $this->params->get( 'multiple_resize_width', -1 );
		$this->t['multipleresizeheight'] 	= $this->params->get( 'multiple_resize_height', -1 );
		$this->t['javaboxwidth'] 			= $this->params->get( 'java_box_width', 480 );
		$this->t['javaboxheight'] 			= $this->params->get( 'java_box_height', 480 );
		$this->t['large_image_width']		= $this->params->get( 'large_image_width', 640 );
		$this->t['large_image_height']		= $this->params->get( 'large_image_height', 640 );
		$this->t['uploadmaxsize'] 			= $this->params->get( 'upload_maxsize', 3145728 );
		$this->t['uploadmaxsizeread'] 		= PhocaGalleryFile::getFileSizeReadable($this->t['uploadmaxsize']);
		$this->t['uploadmaxreswidth'] 		= $this->params->get( 'upload_maxres_width', 3072 );
		$this->t['uploadmaxresheight'] 		= $this->params->get( 'upload_maxres_height', 2304 );

		$display_description_detail 			= $this->params->get( 'display_description_detail', 0 );
		$description_detail_height 				= $this->params->get( 'description_detail_height', 16 );

		$detail_buttons 						= $this->params->get( 'detail_buttons', 1 );
		//$modal_box_overlay_color 				= $this->params->get( 'modal_box_overlay_color', '#000000' );
		$modal_box_overlay_opacity 				= $this->params->get( 'modal_box_overlay_opacity', 0.3 );
		//$modal_box_border_color 				= $this->params->get( 'modal_box_border_color', '#6b6b6b' );
		//$modal_box_border_width 				= $this->params->get( 'modal_box_border_width', '2' );
		$this->t['enablecooliris']			= $this->params->get( 'enable_cooliris', 0 );
		$highslide_class						= $this->params->get( 'highslide_class', 'rounded-white');
		$highslide_opacity						= $this->params->get( 'highslide_opacity', 0);
		$highslide_outline_type					= $this->params->get( 'highslide_outline_type', 'rounded-white');
		$highslide_fullimg						= $this->params->get( 'highslide_fullimg', 0);
		$highslide_slideshow					= $this->params->get( 'highslide_slideshow', 1);
		$highslide_close_button					= $this->params->get( 'highslide_close_button', 0);
		$this->t['jakslideshowdelay']		= $this->params->get( 'jak_slideshow_delay', 5);
		$this->t['jakorientation']			= $this->params->get( 'jak_orientation', 'none');
		$this->t['jakdescription']			= $this->params->get( 'jak_description', 1);
		$this->t['jakdescriptionheight']		= $this->params->get( 'jak_description_height', 0);
		$this->t['categoryimageordering']	= $this->params->get( 'category_image_ordering', 10 );
		$this->t['externalcommentsystem'] 	= $this->params->get( 'external_comment_system', 0 );



		$display_subcat_page_cv					= $this->params->get( 'display_subcat_page_cv', 0 );
		$this->t['display_back_button_cv'] 				= $this->params->get( 'display_back_button_cv', 1 );
		$this->t['display_categories_back_button_cv'] 		= $this->params->get( 'display_categories_back_button_cv', 1 );


		$medium_image_width_cv 					= (int)$this->params->get( 'medium_image_width', 100 ) + 18;
		$medium_image_height_cv 				= (int)$this->params->get( 'medium_image_height', 100 ) + 18;
		$small_image_width_cv 					= (int)$this->params->get( 'small_image_width', 50 ) + 18;
		$small_image_height_cv 					= (int)$this->params->get( 'small_image_height', 50 ) + 18;
		$this->t['imagetypecv']				= $this->t['image_categories_size_cv'];
		$this->t['overlibimagerate']			= (int)$this->params->get( 'overlib_image_rate', '' );

		$this->t['gallerymetakey'] 			= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 			= $this->params->get( 'gallery_metadesc', '' );
		$this->t['altvalue']		 			= $this->params->get( 'alt_value', 1 );
		$paramsFb = PhocaGalleryFbSystem::getCommentsParams($this->params->get( 'fb_comment_user_id', ''));// Facebook
		$this->t['fb_comment_app_id']		= isset($paramsFb['fb_comment_app_id']) ? $paramsFb['fb_comment_app_id'] : '';
		$this->t['fb_comment_width']			= isset($paramsFb['fb_comment_width']) ? $paramsFb['fb_comment_width'] : 550;
		$this->t['fb_comment_lang'] 			= isset($paramsFb['fb_comment_lang']) ? $paramsFb['fb_comment_lang'] : 'en_US';
		$this->t['fb_comment_count'] 		= isset($paramsFb['fb_comment_count']) ? $paramsFb['fb_comment_count'] : '';
		$this->t['enable_direct_subcat']   = $this->params->get( 'enable_direct_subcat', 0 );
		$this->t['display_comment_nopup']	= $this->params->get( 'display_comment_nopup', 0);
		$this->t['boxplus_theme']			= $this->params->get( 'boxplus_theme', 'lightsquare');
		$this->t['boxplus_bautocenter']		= (int)$this->params->get( 'boxplus_bautocenter', 1);
		$this->t['boxplus_autofit']			= (int)$this->params->get( 'boxplus_autofit', 1);
		$this->t['boxplus_slideshow']		= (int)$this->params->get( 'boxplus_slideshow', 0);
		$this->t['boxplus_loop']				= (int)$this->params->get( 'boxplus_loop', 0);
		$this->t['boxplus_captions']			= $this->params->get( 'boxplus_captions', 'bottom');
		$this->t['boxplus_thumbs']			= $this->params->get( 'boxplus_thumbs', 'inside');
		$this->t['boxplus_duration']			= (int)$this->params->get( 'boxplus_duration', 250);
		$this->t['boxplus_transition']		= $this->params->get( 'boxplus_transition', 'linear');
		$this->t['boxplus_contextmenu']		= (int)$this->params->get( 'boxplus_contextmenu', 1);
		$this->t['enablecustomcss']			= $this->params->get( 'enable_custom_css', 0);
		$this->t['customcss']				= $this->params->get( 'custom_css', '');
		$this->t['display_tags_links'] 		= $this->params->get( 'display_tags_links', 0 );
		$this->t['displaying_tags_true'] 	= 0;//No tag found, if yes, the box will be resized
		$this->t['ytbupload'] 				= $this->params->get( 'youtube_upload', 0 );
		$this->t['ytb_display'] 				= $this->params->get( 'ytb_display', 0 );
		$this->t['enable_multibox']			= $this->params->get( 'enable_multibox', 0);
		$this->t['multibox_height']			= (int)$this->params->get( 'multibox_height', 560 );
		$this->t['multibox_width']			= (int)$this->params->get( 'multibox_width', 980 );
		$this->t['disable_mootools_modal']	= $this->params->get( 'disable_mootools_modal', 0 );

		// CSS
		/*switch($this->t['image_categories_size']) {
			// medium
			case 1:
				$this->t['picasa_correct_width']		= (int)$this->params->get( 'medium_image_width', 100 );
				$this->t['picasa_correct_height']	= (int)$this->params->get( 'medium_image_height', 100 );
				$this->t['imagewidth']				= (int)$this->params->get( 'medium_image_width', 100 );
				$this->t['imageheight']				= (int)$this->params->get( 'medium_image_height', 100 );
				$this->t['class_suffix']				= 'medium';
			break;

			// small
			case 0:
			default:
				$this->t['picasa_correct_width']		= (int)$this->params->get( 'small_image_width', 50 );
				$this->t['picasa_correct_height']	= (int)$this->params->get( 'small_image_height', 50 );
				$this->t['imagewidth']				= (int)$this->params->get( 'small_image_width', 50 );
				$this->t['imageheight'] 				= (int)$this->params->get( 'small_image_height', 50 );
				$this->t['class_suffix']				= 'small';
			break;
		}*/

		// CSS Specific
		/*$s = '.pg-cv {'."\n";
		if ($this->t['phocagallerywidth'] != '') {
			$s .= '   margin: auto;'."\n";
			$s .= '   width: '.$this->t['phocagallerywidth'].'px;'."\n";
		}
		$s .= '}'."\n";

		$s .= '.pg-cv-box {'."\n";
		$s .= '   height: '.$this->t['boxsize']['height'].'px;'."\n";
		$s .= '   width: '.$this->t['boxsize']['width'].'px;"'."\n";
		$s .= '}'."\n";

		$s .= '.pg-cv-box-img {'."\n";
		$s .= '   height: '.$this->t['imageheight'].'px;'."\n";
		$s .= '   width: '.$this->t['imagewidth'].'px;"'."\n";
		$s .= '}'."\n";

		$document->addCustomTag('<style type="text/css">'.$s.'</style>');*/


		// Correct Height
		// Description detail height
		if ($display_description_detail == 1) {
			$popup_height	= $popup_height + $description_detail_height;
		}
		// Detail buttons in detail view
		if ($detail_buttons != 1) {
			$popup_height	= $popup_height - 45;
		}
		if ($this->t['display_rating_img'] == 1) {
			$popup_height	= $popup_height + 35;
		}


		// Youtube video without padding, margin
		if ($this->t['detail_window'] != 7 && $this->t['ytb_display'] == 1) {
			$document->addCustomTag( "<style type=\"text/css\"> \n"
			." #boxplus .boxplus-dialog .boxplus-controlsclose {
				top: -15px !important;
				right: -15px !important;
				margin:0px 0 0 0 !important;
			} \n"
			." </style> \n");

			$popup_width = PhocaGallerySettings::getAdvancedSettings('youtubewidth');
			$popup_height= PhocaGallerySettings::getAdvancedSettings('youtubeheight');
		}

		// Multibox
		if ($this->t['enable_multibox']	== 1) {
			$popup_width 							= $this->t['multibox_width'];
			$popup_height 							= $this->t['multibox_height'];
		}
		if ($this->t['detail_window'] == 4) {
			$popup_height = $popup_height + 12;
		}

		// Comment Image JS
		if ((int)$this->t['display_comment_img'] == 2 || (int)$this->t['display_comment_img'] == 3) {
			PhocaGalleryCommentImage::renderCommentImageJS();
		}
		// Rate Image JS
		if ((int)$this->t['display_rating_img'] == 2) {
			PhocaGalleryRateImage::renderRateImgJS();
		}

		// =======================================================
		// DIFFERENT METHODS OF DISPLAYING THE DETAIL VIEW
		// =======================================================
		// MODAL - will be displayed in case e.g. highslide or shadowbox too, because in there are more links

		if ($this->t['disable_mootools_modal'] != 1) {
			Joomla\CMS\HTML\HTMLHelper::_('behavior.modal', 'a.pg-modal-button');
		}


		$btn = new PhocaGalleryRenderDetailWindow();
		$btn->popupWidth 			= $popup_width;
		$btn->popupHeight 			= $popup_height;
		$btn->mbOverlayOpacity		= $modal_box_overlay_opacity;
		$btn->sbSlideshowDelay		= $this->params->get( 'sb_slideshow_delay', 5 );
		$btn->sbSettings			= $this->params->get( 'sb_settings', "overlayColor: '#000',overlayOpacity:0.5,resizeDuration:0.35,displayCounter:true,displayNav:true" );
		$btn->hsSlideshow			= $highslide_slideshow;
		$btn->hsClass				= $highslide_class;
		$btn->hsOutlineType			= $highslide_outline_type;
		$btn->hsOpacity				= $highslide_opacity;
		$btn->hsCloseButton			= $highslide_close_button;
		$btn->hsFullImg				= $highslide_fullimg;
		$btn->jakDescHeight			= $this->t['jakdescriptionheight'];
		$btn->jakDescWidth			= '';
		$btn->jakOrientation		= $this->t['jakorientation'];
		$btn->jakSlideshowDelay		= $this->t['jakslideshowdelay'];
		$btn->bpTheme 				= $this->t['boxplus_theme'];
		$btn->bpBautocenter 		= (int)$this->t['boxplus_bautocenter'];
		$btn->bpAutofit 			= (int)$this->t['boxplus_autofit'];
		$btn->bpSlideshow 			= (int)$this->t['boxplus_slideshow'];
		$btn->bpLoop 				= (int)$this->t['boxplus_loop'];
		$btn->bpCaptions 			= $this->t['boxplus_captions'];
		$btn->bpThumbs 				= $this->t['boxplus_thumbs'];
		$btn->bpDuration 			= (int)$this->t['boxplus_duration'];
		$btn->bpTransition 			= $this->t['boxplus_transition'];
		$btn->bpContextmenu 		= (int)$this->t['boxplus_contextmenu'];

		$btn->setButtons($this->t['detail_window'], $libraries, $library);
		$this->button = $btn->getB1();
		$this->button2 = $btn->getB2();
		$this->buttonother = $btn->getB3();

		$this->t ['highslideonclick']	= '';// for using with highslide
		if (isset($this->button->highslideonclick)) {
			$this->t ['highslideonclick'] = $this->button->highslideonclick;// TO DO
		}
		$this->t ['highslideonclick2']	= '';
		if (isset($this->button->highslideonclick2)) {
			$this->t ['highslideonclick2'] = $this->button->highslideonclick2;// TO DO
		}


		$folderButton = new JObject();
		$folderButton->set('name', 'image');
		$folderButton->set('options', "");
		// End open window parameters
		// ==================================================================

		// Information about current category
		$this->category			= $this->get('category');


		// Cooliris (Piclens)
		$this->t['start_cooliris'] 	= 0;
		if ($this->t['enablecooliris'] == 1) {
			$this->t['start_cooliris'] = $this->params->get( 'start_cooliris', 0 );
			// CSS - PicLens START
			$document->addCustomTag(PhocaGalleryRenderFront::renderPicLens($this->category->id));
		}

		// PARAMS - Pagination and subcategories on other sites
		// Subcategories will be displayed only on first page if pagination will be used
		$display_subcat_page = $this->params->get( 'display_subcat_page', 0 );
		// On the first site subcategories will be displayed always
		$get['start']	= $app->input->get( 'limitstart', '', 'string' );
		if ($display_subcat_page == 2) {
			$display_subcat_page = 0;// Nowhere
		} else if ($display_subcat_page == 0 && $get['start'] > 0) {
			$display_subcat_page = 0;//in case: second page and param=0
		} else {
			$display_subcat_page = 1;//in case:first page or param==1
		}
		// Categories View in Category View
		if ($display_subcat_page_cv == 2) {
			$display_subcat_page_cv = 0;// Nowhere
		} else if ($display_subcat_page_cv == 0 && $get['start'] > 0) {
			$display_subcat_page_cv = 0;//in case: second page and param=0
		} else {
			$display_subcat_page_cv = 1;//in case:first page or param==1
		}


		// PARAMS - Display Back Buttons
		$display_back_button 			= $this->params->get( 'display_back_button', 1 );
		$display_categories_back_button = $this->params->get( 'display_categories_back_button', 1 );
		// PARAMS - Access Category - display category (subcategory folder or backbutton  to not accessible cat
		$display_access_category 		= $this->params->get( 'display_access_category', 1 );

		// Set page title per category
		if ($this->t['display_cat_name_title'] == 1 && isset($this->category->title)) {
			$document->setTitle($this->params->get( 'page_title') . ' - '. $this->category->title);
		} else {
			$document->setTitle( $this->params->get( 'page_title' ));
		}

		// Breadcrumb display:
		// 0 - only menu link
		// 1 - menu link - category name
		// 2 - only category name
		$this->_addBreadCrumbs( isset($menu->query['id']) ? $menu->query['id'] : 0, $display_cat_name_breadcrumbs);




		// Define image tag attributes
	/*	if (!empty ($this->category->image)) {
			$attribs['align'] = '"'.$this->category->image_position.'"';
			$attribs['hspace'] = '"6"';
			$this->t['image'] = Joomla\CMS\HTML\HTMLHelper::_('image', 'images/stories/'.$this->category->image,'', $attribs);
		}*/


		// Overlib
		$enable_overlib = $this->params->get( 'enable_overlib', 0 );
		if ((int)$enable_overlib > 0) {
			$document->addScript(JURI::base(true).'/media/com_phocagallery/js/overlib/overlib_mini.js');
		}

		// MODEL
		$model		= $this->getModel();

		// Trash
		$this->t['trash']					= 0;
		$this->t['publish_unpublish']		= 0;
		$this->t['approved_not_approved']	= 0;// only to see the info
		// USER RIGHT - DELETE - - - - - - - - - - -
		// 2, 2 means that user access will be ignored in function getUserRight for display Delete button
		$rightDisplayDelete = 0;// default is to null (all users cannot upload)
		if (!empty($this->category)) {
			$rightDisplayDelete = PhocaGalleryAccess::getUserRight('deleteuserid', $this->category->deleteuserid, 2, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), 0);
		}
		if ($rightDisplayDelete == 1) {
			$this->t['trash']				= 1;
			$this->t['publish_unpublish']	= 1;
			$this->t['approved_not_approved']= 1;// only to see the info
		}
		// - - - - - - - - - - - - - - - - - - - - -
		// Upload
		$this->t['displayupload']	= 0;
		// USER RIGHT - UPLOAD - - - - - - - - - - -
		// 2, 2 means that user access will be ignored in function getUserRight for display Delete button
		$rightDisplayUpload = 0;// default is to null (all users cannot upload)
		if (!empty($this->category)) {
			$rightDisplayUpload = PhocaGalleryAccess::getUserRight('uploaduserid', $this->category->uploaduserid, 2, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), 0);
		}

		if ($rightDisplayUpload == 1) {
			$this->t['displayupload']	= 1;
			$document->addCustomTag(PhocaGalleryRenderFront::renderOnUploadCategoryJS());
			$document->addCustomTag(PhocaGalleryRenderFront::renderDescriptionUploadJS((int)$this->t['maxuploadchar']));
		}

		$this->t['displaycreatecat']	= 0;
		if (($rightDisplayUpload == 1) && ($this->t['enable_direct_subcat'] == 1))
		{
			$this->t['displaycreatecat']	= 1;
			$document->addCustomTag(PhocaGalleryRenderFront::renderOnUploadCategoryJS());
			$document->addCustomTag(PhocaGalleryRenderFront::renderDescriptionCreateSubCatJS((int)$this->t['maxcreatecatchar']));
		}

		// - - - - - - - - - - - - - - - - - - - - -

		// USER RIGHT - ACCESS - - - - - - - - - - -
		$rightDisplay = 1;//default is set to 1 (all users can see the category)

		if (!empty($this->category)) {
			$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $this->category->accessuserid, 0, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), $display_access_category);
		}
		if ($rightDisplay == 0) {

			$app->redirect(JRoute::_($this->t['pl'], false), JText::_('COM_PHOCAGALLERY_NOT_AUTHORISED_ACTION'));
			exit;
		}
		// - - - - - - - - - - - - - - - - - - - - -

		// 1. GEOTAGGING CATEGORY
		$this->map['longitude'] 	= '';// will be used for 1. default_geotagging to not display pane and 2. to remove pane (line cca 1554)
		$this->map['latitude'] 		= '';

		if (isset($this->category->latitude) && $this->category->latitude != '' && $this->category->latitude != 0
			&& isset($this->category->longitude) && $this->category->longitude != '' && $this->category->longitude != 0 ) {

			$this->map['longitude']	= $this->category->longitude;
			$this->map['latitude']	= $this->category->latitude;
			$this->map['zoom']		= $this->category->zoom;
			$this->map['geotitle'] 	= $this->category->geotitle;
			$this->map['description'] = $this->category->description;
			if ($this->map['geotitle'] == '') {
				$this->map['geotitle']	= $this->category->title;
			}
		} else {
			$this->t['display_icon_geo'] = 0;
		}


		$this->t['categoryimageordering']	= $this->params->get( 'category_image_ordering', 10 );
		//$this->t['categoryimageorderingcv']	= 10;//$this->params->get( 'category_image_ordering_cv', 10 );
		// Image next to Category in Categories View in Category View is ordered by Random as default
		phocagalleryimport('phocagallery.ordering.ordering');
		$this->categoryImageOrdering = PhocaGalleryOrdering::getOrderingString($this->t['categoryimageordering']);
		//$this->categoryImageOrderingCV = PhocaGalleryOrdering::getOrderingString($this->t['categoryimageorderingcv']);





		// = = = = = = = = = = = = = = = = = = = =
		// BOXES
		// = = = = = = = = = = = = = = = = = = = =

		// Information because of height of box (if they are used not by all images)
		$this->t['display_icon_extlink1_box'] 	= 0;
		$this->t['display_icon_extlink2_box'] 	= 0;
		$this->t['display_icon_vmbox'] 			= 0;
		$this->t['display_icon_pcbox'] 			= 0;
		$this->t['display_icon_geo_box'] 		= 0;

        $iS 	= 0;
		$iCV 	= 0;
		$this->items		= array();// Category View
		$this->itemscv		= array();// Category List (Categories View) in Category View


		// ----------------------------------------
		// PARENT FOLDERS(I) or Back Button STANDARD
		// ----------------------------------------
		/*
		// Set Back Button to CATEGORIES VIEW
		$this->itemsLink	= $menu->getItems('link', 'index.php?option=com_phocagallery&view=categories');

		$itemId	= 0;
		if(isset($this->itemsLink[0])) {
			$itemId = $this->itemsLink[0]->id;
		}
		$backLink = 'index.php?option=com_phocagallery&view=categories&Itemid='.$itemId;*/

		$posItemid		= $posItemidNull = $backLinkItemId = false;
		$backLink 		= PhocaGalleryRoute::getCategoriesRoute();
		$posItemidNull 	= strpos($backLink, "Itemid=0");
		$posItemid 		= strpos($backLink, "Itemid=");
		if ($posItemidNull === false && $posItemid) {
			$backLinkItemId = 1;
		}


		$parentCategory = $this->get('parentcategory');

		if ($display_back_button == 1) {
			if (!empty($parentCategory)) {

				$this->items[$iS] = $parentCategory;
				// USER RIGHT - ACCESS - - - - - - - - - - -
				// Should be the link to parentcategory displayed
				$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $this->items[$iS]->accessuserid, $this->items[$iS]->access, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), $display_access_category);

				// Display Key Icon (in case we want to display unaccessable categories in list view)
				$rightDisplayKey  = 1;
				if ($display_access_category == 1) {
					// we simulate that we want not to display unaccessable categories
					// so we get rightDisplayKey = 0 then the key will be displayed
					if (!empty($parentCategory)) {
						$rightDisplayKey = PhocaGalleryAccess::getUserRight ('accessuserid', $this->items[$iS]->accessuserid, $this->items[$iS]->access, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), 0);
					}
				}
				// - - - - - - - - - - - - - - - - - - - - -

				if ($rightDisplay > 0) {
					$this->items[$iS]->cls						= 'pg-box-parentfolder';
					$this->items[$iS]->iconcls					= 'icon-up';
					$this->items[$iS]->slug			 			= $this->items[$iS]->id . ':' . $this->items[$iS]->alias;
					$this->items[$iS]->item_type				= "parentfolder";
					$this->items[$iS]->linkthumbnailpath 		= PhocaGalleryImageFront::displayBackFolder('medium', $rightDisplayKey);
					$this->items[$iS]->extm						= $this->items[$iS]->linkthumbnailpath;
					$this->items[$iS]->exts						= $this->items[$iS]->linkthumbnailpath;
					$this->items[$iS]->numlinks 				= 0;// We are in category view
					$this->items[$iS]->link 					= JRoute::_('index.php?option=com_phocagallery&view=category&id='. $this->items[$iS]->slug.'&Itemid='. $this->itemId  );
					$this->items[$iS]->button 					= &$folderButton;
					$this->items[$iS]->button->methodname 		= '';
					$this->items[$iS]->display_icon_detail 		= 0;
					$this->items[$iS]->display_icon_download 	= 0;
					$this->items[$iS]->display_name 			= 0;
					$this->items[$iS]->display_icon_vm 			= '';
					$this->items[$iS]->display_icon_pc 			= '';
					$this->items[$iS]->start_cooliris 			= 0;
					$this->items[$iS]->trash					= 0;
					$this->items[$iS]->publish_unpublish		= 0;
					$this->items[$iS]->approved_not_approved	= 0;
					$this->items[$iS]->enable_cooliris			= 0;
					$this->items[$iS]->overlib					= 0;
					$this->items[$iS]->display_icon_geo			= 0;
					$this->items[$iS]->display_icon_commentimg	= 0;
					$this->items[$iS]->type						= 0;
					$this->items[$iS]->camera_info				= 0;
					$this->items[$iS]->display_icon_extlink1	= 0;
					$this->items[$iS]->display_icon_extlink2	= 0;
					$this->items[$iS]->description				= '';
					$this->items[$iS]->altvalue					= JText::_('COM_PHOCAGALLERY_BACK');
					$iS++;
				} else {
					// There is no right to see the data but the object exists (because it was loaded from database
					// Destroy it
					unset($this->items[$iS]);
				}
			} else { // Back button to categories list if it exists
				if ($backLinkItemId != 0 && $display_categories_back_button == 1) {
					$this->items[$iS] 							= new JObject();
					$this->items[$iS]->cls						= 'pg-box-backbtn';
					$this->items[$iS]->iconcls					= 'icon-up';
					$this->items[$iS]->link 					= JRoute::_($backLink);
					$this->items[$iS]->title					= JTEXT::_('COM_PHOCAGALLERY_CATEGORY_LIST');
					$this->items[$iS]->item_type 				= "categorieslist";
					$this->items[$iS]->linkthumbnailpath 		= PhocaGalleryImageFront::displayBackFolder('medium', 1);
					$this->items[$iS]->extm						= $this->items[$iS]->linkthumbnailpath;
					$this->items[$iS]->exts						= $this->items[$iS]->linkthumbnailpath;
					$this->items[$iS]->numlinks 				= 0;// We are in category view
					$this->items[$iS]->button 					= &$folderButton;
					$this->items[$iS]->button->methodname 		= '';
					$this->items[$iS]->display_icon_detail 		= 0;
					$this->items[$iS]->display_icon_download	= 0;
					$this->items[$iS]->display_name 			= 0;
					$this->items[$iS]->display_icon_vm 			= '';
					$this->items[$iS]->display_icon_pc 			= '';
					$this->items[$iS]->start_cooliris 			= 0;
					$this->items[$iS]->trash					= 0;
					$this->items[$iS]->publish_unpublish		= 0;
					$this->items[$iS]->approved_not_approved	= 0;
					$this->items[$iS]->enable_cooliris			= 0;
					$this->items[$iS]->overlib					= 0;
					$this->items[$iS]->display_icon_geo			= 0;
					$this->items[$iS]->display_icon_commentimg	= 0;
					$this->items[$iS]->type						= 0;
					$this->items[$iS]->camera_info				= 0;
					$this->items[$iS]->display_icon_extlink1	= 0;
					$this->items[$iS]->display_icon_extlink2	= 0;
					$this->items[$iS]->description			= '';
					$this->items[$iS]->altvalue				= JText::_('COM_PHOCAGALLERY_BACK');
					$iS++;
				}
			}
		}


		// ----------------------------------------
		// PARENT FOLDERS(II) or Back Button CATEGORIES VIEW IN CATEGORY VIEW
		// ----------------------------------------
		if ($this->t['display_back_button_cv'] == 1 && $this->t['display_categories_cv'] == 1) {
			if (!empty($parentCategory)) {

				$this->itemscv[$iCV] = clone $parentCategory;
				// USER RIGHT - ACCESS - - - - - - - - - - -
				// Should be the link to parentcategory displayed
				$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $this->itemscv[$iCV]->accessuserid, $this->itemscv[$iCV]->access, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), $display_access_category);

				// Display Key Icon (in case we want to display unaccessable categories in list view)
				$rightDisplayKey  = 1;
				if ($display_access_category == 1) {
					// we simulate that we want not to display unaccessable categories
					// so we get rightDisplayKey = 0 then the key will be displayed
					if (!empty($parentCategory)) {
						$rightDisplayKey = PhocaGalleryAccess::getUserRight ('accessuserid', $this->itemscv[$iCV]->accessuserid, $this->itemscv[$iCV]->access, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), 0);
					}
				}
				// - - - - - - - - - - - - - - - - - - - - -

				if ($rightDisplay > 0) {
					$this->itemscv[$iCV]->cls					= 'pg-box-parentfolder-cv';
					$this->itemscv[$iCV]->iconcls				= 'icon-up';
					$this->itemscv[$iCV]->slug 					= $this->itemscv[$iCV]->id.':'.$this->itemscv[$iCV]->alias;
					$this->itemscv[$iCV]->item_type 			= "parentfoldercv";
					$this->itemscv[$iCV]->linkthumbnailpath	= PhocaGalleryImageFront::displayBackFolder('medium', $rightDisplayKey);
					$this->itemscv[$iCV]->extm				= $this->itemscv[$iCV]->linkthumbnailpath;
					$this->itemscv[$iCV]->exts				= $this->itemscv[$iCV]->linkthumbnailpath;
					$this->itemscv[$iCV]->numlinks 			= 0;// We are in category view
					$this->itemscv[$iCV]->link 				= JRoute::_('index.php?option=com_phocagallery&view=category&id='. $this->itemscv[$iCV]->slug.'&Itemid='. $this->itemId  );
					$this->itemscv[$iCV]->type				= 3;
					$this->itemscv[$iCV]->altvalue			= JText::_('COM_PHOCAGALLERY_BACK');
					$iCV++;
				} else {
					// There is no right to see the data but the object exists (because it was loaded from database
					// Destroy it
					unset($this->itemscv[$iCV]);
				}
			} else { // Back button to categories list if it exists
				if ($backLinkItemId != 0 && $this->t['display_categories_back_button_cv'] == 1) {
					$this->itemscv[$iCV] 						= new JObject();
					$this->itemscv[$iCV]->cls					= 'pg-cvcsv-back';
					$this->itemscv[$iCV]->iconcls				= 'icon-up';
					$this->itemscv[$iCV]->link 				= $backLink;
					$this->itemscv[$iCV]->title				= JTEXT::_('COM_PHOCAGALLERY_CATEGORY_LIST');
					$this->itemscv[$iCV]->item_type 			= "categorieslistcv";
					$this->itemscv[$iCV]->linkthumbnailpath	= PhocaGalleryImageFront::displayBackFolder('medium', 1);
					$this->itemscv[$iCV]->extm				= $this->itemscv[$iCV]->linkthumbnailpath;
					$this->itemscv[$iCV]->exts				= $this->itemscv[$iCV]->linkthumbnailpath;
					$this->itemscv[$iCV]->numlinks = 0;// We are in category view
					$this->itemscv[$iCV]->link 				= JRoute::_( $this->itemscv[$iCV]->link );
					$this->itemscv[$iCV]->type				= 3;
					$this->itemscv[$iCV]->altvalue			= htmlspecialchars(JTEXT::_('COM_PHOCAGALLERY_CATEGORY_LIST'));
					$iCV++;
				}
			}
		}


		// ----------------------------------------
		// SUB FOLDERS(1) STANDARD
		// ----------------------------------------
		// Display subcategories on every page
		if ($display_subcat_page == 1) {

			$subCategory = $this->get('subcategory');
			$totalSubCat = count($subCategory);

			if ((int)$this->tagId > 0) {$subCategory = array();}// No subcategories for tag searching

			if (!empty($subCategory)) {
				$this->items[$iS] = &$subCategory;

				for($iSub = 0; $iSub < $totalSubCat; $iSub++) {

					$this->items[$iS] = &$subCategory[$iSub];
					// USER RIGHT - ACCESS - - - - - - - - - -
					$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $this->items[$iS]->accessuserid, $this->items[$iS]->access, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), $display_access_category);

					// Display Key Icon (in case we want to display unaccessable categories in list view)
					$rightDisplayKey  = 1;
					if ($display_access_category == 1) {
						// we simulate that we want not to display unaccessable categories
						// so we get rightDisplayKey = 0 then the key will be displayed
						if (!empty($this->items[$iS])) {
							$rightDisplayKey = PhocaGalleryAccess::getUserRight('accessuserid', $this->items[$iS]->accessuserid, $this->items[$iS]->access, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), 0);
						}
					}
					// - - - - - - - - - - - - - - - - - - - -

					if ($rightDisplay > 0) {
						$this->items[$iS]->cls						= 'pg-box-subfolder';
						$this->items[$iS]->iconcls					= 'category';
						$this->items[$iS]->slug 					= $this->items[$iS]->id.':'.$this->items[$iS]->alias;
						$this->items[$iS]->item_type 				= "subfolder";

						$numlinks 	= $model->getCountItem($this->items[$iS]->id);//Should be get from main subcategories query
						if (isset($numlinks[0]) && $numlinks[0] > 0) {
							$this->items[$iS]->numlinks = (int)$numlinks[0];
						} else {
							$this->items[$iS]->numlinks = 0;
						}
						$extImage = PhocaGalleryImage::isExtImage($this->items[$iS]->extid);



						if (isset($this->items[$iS]->image_id) && $this->items[$iS]->image_id > 0) {
							// User has selected image in category edit
							$selectedImg = PhocaGalleryImageFront::setFileNameByImageId((int)$this->items[$iS]->image_id);

							if (isset($selectedImg->filename) && ($selectedImg->filename != '' && $selectedImg->filename != '-')) {
								$fileThumbnail	= PhocaGalleryImageFront::displayCategoryImageOrFolder($selectedImg->filename, 'medium', $rightDisplayKey);

								$this->items[$iS]->filename = $selectedImg->filename;
								$this->items[$iS]->linkthumbnailpath   = $fileThumbnail->rel;
							} else if (isset($selectedImg->exts) && isset($selectedImg->extm) && $selectedImg->exts != '' && $selectedImg->extm != '') {

								$fileThumbnail		= PhocaGalleryImageFront::displayCategoryExtImgOrFolder($selectedImg->exts, $selectedImg->extm, 'medium', $rightDisplayKey);


								$this->items[$iS]->linkthumbnailpath	= $fileThumbnail->linkthumbnailpath;
								$this->items[$iS]->extw				= $selectedImg->extw;
								$this->items[$iS]->exth				= $selectedImg->exth;
								$this->items[$iS]->extpic			= $fileThumbnail->extpic;
							}

						} else if ($extImage) {
							$imagePic		= new stdClass();
							if ($this->t['categoryimageordering'] != 10) {
								$imagePic		= PhocaGalleryImageFront::getRandomImageRecursive($this->items[$iS]->id, $this->categoryImageOrdering, 1);
								$fileThumbnail	= PhocaGalleryImageFront::displayCategoryExtImgOrFolder($imagePic->exts, $imagePic->extm, 'medium', $rightDisplayKey, 'display_category_icon_image');

							} else {
								$fileThumbnail	= PhocaGalleryImageFront::displayCategoryExtImgOrFolder($this->items[$iS]->exts,$this->items[$iS]->extm, 'medium', $rightDisplayKey, 'display_category_icon_image');


								$imagePic->extw = $this->items[$iS]->extw;
								$imagePic->exth = $this->items[$iS]->exth;
							}
							// in case category is locked or no extm exists

							$this->items[$iS]->linkthumbnailpath	= $fileThumbnail->linkthumbnailpath;
							$this->items[$iS]->extm	= $fileThumbnail->extm;
							$this->items[$iS]->exts	= $fileThumbnail->exts;

							$this->items[$iS]->exthswitch = $this->items[$iS]->extwswitch = 0;
							if ($imagePic->extw != '') {
								$extw 				= explode(',',$imagePic->extw);
								$this->items[$iS]->extw		= $extw[1];
								$this->items[$iS]->extwswitch	= $extw[0];
							}
							if ($imagePic->exth != '') {
								$exth 				= explode(',',$imagePic->exth);
								$this->items[$iS]->exth	= $exth[1];
								$this->items[$iS]->exthswitch	= $exth[0];
							}
							$this->items[$iS]->extpic		= $fileThumbnail->extpic;
						} else {
							if ($this->t['categoryimageordering'] != 10) {
								$randomImage 	= PhocaGalleryImageFront::getRandomImageRecursive($this->items[$iS]->id, $this->categoryImageOrdering);
								$fileThumbnail 	= PhocaGalleryImageFront::displayCategoryImageOrFolder($randomImage, 'medium', $rightDisplayKey, 'display_category_icon_image');


							} else {
								$fileThumbnail 	= PhocaGalleryImageFront::displayCategoryImageOrFolder($this->items[$iS]->filename, 'medium', $rightDisplayKey, 'display_category_icon_image');
							}

							$this->items[$iS]->linkthumbnailpath  	= $fileThumbnail->rel;
						}


						$this->items[$iS]->link 					= JRoute::_('index.php?option=com_phocagallery&view=category&id='. $this->items[$iS]->slug.'&Itemid='. $this->itemId  );
						$this->items[$iS]->button 				= &$folderButton;
						$this->items[$iS]->button->methodname 	= '';
						$this->items[$iS]->display_icon_detail 		= 0;
						$this->items[$iS]->display_icon_download 	= 0;
						$this->items[$iS]->display_name 			= $this->t['display_name'];
						$this->items[$iS]->display_icon_vm 			= '';
						$this->items[$iS]->display_icon_pc 			= '';
						$this->items[$iS]->start_cooliris 			= 0;
						$this->items[$iS]->trash					= 0;
						$this->items[$iS]->publish_unpublish		= 0;
						$this->items[$iS]->approved_not_approved	= 0;
						$this->items[$iS]->enable_cooliris			= 0;
						$this->items[$iS]->overlib					= 0;
						$this->items[$iS]->display_icon_geo			= 0;
						$this->items[$iS]->type						= 1;
						$this->items[$iS]->camera_info				= 0;
						$this->items[$iS]->display_icon_extlink1	= 0;
						$this->items[$iS]->display_icon_extlink2	= 0;
						$this->items[$iS]->description				= '';
						$this->items[$iS]->display_icon_commentimg	= 0;
						$this->items[$iS]->altvalue					= htmlspecialchars($this->items[$iS]->title);
						$iS++;
					} else {
						// There is no right to see the data but the object exists (because it was loaded from database
						// Destroy it
						unset($this->items[$iS]);
					}
				}
			}
		}

		// ----------------------------------------
		// SUB FOLDERS(II) or Back Button CATEGORIES VIEW IN CATEGORY VIEW
		// ----------------------------------------
		//display subcategories on every page
		if ($display_subcat_page_cv == 1 && $this->t['display_categories_cv'] == 1) {
			$subCategory = $this->get('subcategory');
			$totalSubCat = count($subCategory);

			if ((int)$this->tagId > 0) {$subCategory = array();}// No subcategories for tag searching

			if (!empty($subCategory)) {
				$this->itemscv[$iCV] = &$subCategory;

				for($iSub = 0; $iSub < $totalSubCat; $iSub++) {

					$this->itemscv[$iCV] = &$subCategory[$iSub];
					// USER RIGHT - ACCESS - - - - - - - - - -
					$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $this->itemscv[$iCV]->accessuserid, $this->itemscv[$iCV]->access, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), $display_access_category);

					// Display Key Icon (in case we want to display unaccessable categories in list view)
					$rightDisplayKey  = 1;
					if ($display_access_category == 1) {
						// we simulate that we want not to display unaccessable categories
						// so we get rightDisplayKey = 0 then the key will be displayed
						if (!empty($this->itemscv[$iCV])) {
							$rightDisplayKey = PhocaGalleryAccess::getUserRight('accessuserid', $this->itemscv[$iCV]->accessuserid, $this->itemscv[$iCV]->access, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), 0);
						}
					}
					// - - - - - - - - - - - - - - - - - - - -

					if ($rightDisplay > 0) {

						$this->itemscv[$iCV]->cls					= 'pg-cvcsv-name';
						$this->itemscv[$iCV]->iconcls				= 'category';
						$this->itemscv[$iCV]->slug 				= $this->itemscv[$iCV]->id.':'.$this->itemscv[$iCV]->alias;
						$this->itemscv[$iCV]->item_type 			= "subfoldercv";
						$this->itemscv[$iCV]->link 				= JRoute::_('index.php?option=com_phocagallery&view=category&id='. $this->itemscv[$iCV]->slug.'&Itemid='. $this->itemId  );
						$this->itemscv[$iCV]->type				= 4;


						$numlinks = $model->getCountItem($this->itemscv[$iCV]->id);//Should be get from main subcategories query
						if (isset($numlinks[0]) && $numlinks[0] > 0) {
							$this->itemscv[$iCV]->numlinks = (int)$numlinks[0];
						} else {
							$this->itemscv[$iCV]->numlinks = 0;
						}

						$extImage = PhocaGalleryImage::isExtImage($this->itemscv[$iCV]->extid);
						if ($extImage) {
							/*$imagePic		= new stdClass();
							if ($this->t['categoryimageordering'] != 10) {
								$imagePic= PhocaGalleryImageFront::getRandomImageRecursive($this->itemscv[$iCV]->id, $this->categoryImageOrderingCV, 1);
								$fileThumbnail	= PhocaGalleryImageFront::displayCategoryExtImgOrFolder($imagePic->exts, $imagePic->extm, 'medium', $rightDisplayKey, 'display_category_icon_image');
							} else {
								$fileThumbnail	= PhocaGalleryImageFront::displayCategoryExtImgOrFolder($this->itemscv[$iCV]->exts,$this->itemscv[$iCV]->extm, 'medium', $rightDisplayKey, 'display_category_icon_image');
								$imagePic->extw = $this->itemscv[$iCV]->extw;
								$imagePic->exth = $this->itemscv[$iCV]->exth;
							}

							// in case category is locked or no extm exists
							$this->itemscv[$iCV]->linkthumbnailpath	= $fileThumbnail->linkthumbnailpath;
							$this->itemscv[$iCV]->extm	= $fileThumbnail->extm;
							$this->itemscv[$iCV]->exts	= $fileThumbnail->exts;

							$this->itemscv[$iCV]->exthswitch = $this->items[$iCV]->extwswitch = 0;
							if ($imagePic->extw != '') {
								$extw 						= explode(',',$imagePic->extw);
								$this->itemscv[$iCV]->extw		= $extw[1];
								$this->itemscv[$iCV]->extwswitch	= $extw[0];
							}
							if ($imagePic->exth != '') {
								$exth 				= explode(',',$imagePic->exth);
								$this->itemscv[$iCV]->exth		= $exth[1];
								$this->itemscv[$iCV]->exthswitch	= $exth[0];
							}*/
							$this->itemscv[$iCV]->extpic	= '';
						} else {
							/*if ($this->t['categoryimageordering'] != 10) {
								$randomImage 	= PhocaGalleryImageFront::getRandomImageRecursive($this->itemscv[$iCV]->id, $this->categoryImageOrderingCV);
								$fileThumbnail 	= PhocaGalleryImageFront::displayCategoryImageOrFolder($randomImage, 'medium', $rightDisplayKey, 'display_category_icon_image_cv');
							} else {
								$fileThumbnail 	= PhocaGalleryImageFront::displayCategoryImageOrFolder($this->itemscv[$iCV]->filename, 'medium', $rightDisplayKey, 'display_category_icon_image_cv');
							}*/
							$this->itemscv[$iCV]->linkthumbnailpath		= '';
							$this->itemscv[$iCV]->altvalue				= htmlspecialchars($this->items[$iCV]->title);

						}
						$iCV++;
					} else {
						// There is no right to see the data but the object exists (because it was loaded from database
						// Destroy it
						unset($this->itemscv[$iCV]);
					}
				}
			}
		}



		// ----------------------------------------
		// IMAGES
		// ----------------------------------------
		// If user has rights to delete or publish or unpublish, unbublished items should be displayed
		if ($rightDisplayDelete == 1) {
			$images	= $model->getData(1, $this->tagId);
			$this->t['pagination']	= $model->getPagination(1, $this->tagId);
		} else {
			$images	= $model->getData(0, $this->tagId);

			$this->t['pagination']	= $model->getPagination(0, $this->tagId);
		}

		$this->t['ordering']	= $model->getOrdering();

		$totalImg = count($images);

		if ($limitStart > 0 ) {
			$this->t['limitstarturl'] = '&limitstart='.$limitStart;
		} else {
			$this->t['limitstarturl'] = '';
		}

		$this->t['jakdatajs'] = array();
		$this->t['display_icon_commentimg_box'] = 0;
		for($iM = 0; $iM < $totalImg; $iM++) {

			$this->items[$iS] 					= $images[$iM] ;
			$this->items[$iS]->cls				= 'pg-box-image';
			$this->items[$iS]->iconcls			= 'image';
			$this->items[$iS]->slug 			= $this->items[$iS]->id.':'.$this->items[$iS]->alias;
			$this->items[$iS]->item_type 		= "image";
			$this->items[$iS]->linknr 			= '';//Def

			$this->items[$iS]->datasize 		= '';//Def

			$extImage = PhocaGalleryImage::isExtImage($this->items[$iS]->extid);

			// Get file thumbnail or No Image
			$this->items[$iS]->exthswitch = $this->items[$iS]->extwswitch = 0;

			// Mansory
			$iFormat 	= 'medium';
			$iFormatD	= 'large';

			if ($this->items[$iS]->extm != '') {

				$dataSizeW = $this->t['large_image_width'];
				$dataSizeH = $this->t['large_image_height'];
				if ($this->items[$iS]->extw != '') {
					$extw 				= explode(',',$this->items[$iS]->extw);
					$this->items[$iS]->extw	= $extw[1];
					$this->items[$iS]->extwswitch	= $extw[0];
					$dataSizeW = $extw[0];
				}
				if ($this->items[$iS]->exth != '') {
					$exth 				= explode(',',$this->items[$iS]->exth);
					$this->items[$iS]->exth	= $exth[1];
					$this->items[$iS]->exthswitch	= $exth[0];
					$dataSizeH = $exth[0];
				}
				// Photoswipe needs data-size parameter
				if ( $this->t['detail_window'] == 14) {
					$this->items[$iS]->datasize 		= 'data-size="'.(int)$dataSizeW.'x'.(int)$dataSizeH. '"';
				}
				$this->items[$iS]->extpic	= 1;
				$this->items[$iS]->linkthumbnailpath = '';
			} else {

				$dataSizeW = $this->t['large_image_width'];
				$dataSizeH = $this->t['large_image_height'];
				// Different Thumbnail Height
				// Masonry
				if ($this->t['diff_thumb_height'] > 0) {
					//if ($this->items[$iS]->format	== 2) {
					if (isset($this->items[$iS]->format) && $this->items[$iS]->format   == 2) {
						// Portrait
						$iFormat = 'medium1';// format of thumbnail displayed
						$iFormatD = 'large1';// format of thumbnail in detail view
						//$iFormat = 'medium';// by portraits in everycase (medium1 = medium * x2(height))

						//$dataSizeW = $this->t['large_image_width'];
						//$dataSizeH = $this->t['large_image_height'];
					} else {

						// By landscape - landscape
						/*$m2 = mt_rand(0,1);
						if ($m2 == 1) {
							$iFormat = 'medium1';
						}*/
						// Landscape
						$iFormat = 'medium';
						//$dataSizeW = $this->t['large_image_width'];
						//$dataSizeH = $this->t['large_image_height'];
					}
				}

				if ( $this->t['detail_window'] == 14) {
					$this->items[$iS]->datasize 		= 'data-size="'.(int)$dataSizeW.'x'.(int)$dataSizeH. '"';
				}

				$this->items[$iS]->linkthumbnailpath 	= PhocaGalleryImageFront::displayCategoryImageOrNoImage($this->items[$iS]->filename, $iFormat);
			}

			if (isset($parentCategory->params)) {
				$this->items[$iS]->parentcategoryparams = $parentCategory->params;
			}

			// SWITCH IMAGE - Add the first Image as basic image
			if ($this->t['switch_image'] == 1) {
				if ($basic_imageSelected == 0) {
					if ((int)$this->t['switch_width'] > 0 && (int)$this->t['switch_height'] > 0 && $this->t['switch_fixed_size'] == 1 ) {
						$wHArray	= array( 'id' => 'PhocaGalleryobjectPicture', 'border' =>'0', 'width' => $this->t['switch_width'], 'height' => $this->t['switch_height']);
						$wHString	= ' id="PhocaGalleryobjectPicture"  border="0" width="'. $this->t['switch_width'].'" height="'.$this->t['switch_height'].'"';
					} else {
						$wHArray 	= array( 'id' => 'PhocaGalleryobjectPicture', 'border' =>'0');
						$wHString	= ' id="PhocaGalleryobjectPicture"  border="0"';
					}

					if (isset($this->items[$iS]->extpic) && $this->items[$iS]->extpic != '') {
						$this->t['basic_image']	= Joomla\CMS\HTML\HTMLHelper::_( 'image', $this->items[$iS]->extl, '', $wHArray);
					} else {
						$this->t['basic_image']	= Joomla\CMS\HTML\HTMLHelper::_( 'image', str_replace('phoca_thumb_m_','phoca_thumb_l_',$this->items[$iS]->linkthumbnailpath), '', $wHString);

					}
					$basic_imageSelected = 1;
				}
			}


			// link to large thumbnail can be in Masonry effect link to large1 thumbnail (portrait)
			$thumbLink	= PhocaGalleryFileThumbnail::getThumbnailName($this->items[$iS]->filename, $iFormatD);

			// Photoswipe needs data-size parameter
			if ( $this->t['detail_window'] == 14) {
				if (JFile::exists($thumbLink->abs)) {
					$thumbSize	= @getimagesize($thumbLink->abs);
					if (isset($thumbSize[0]) && isset($thumbSize[1])) {
						$this->items[$iS]->datasize 		= 'data-size="'.(int)$thumbSize[0].'x'.(int)$thumbSize[1]. '"';
					}
				}
			}

			$thumbLinkM	= PhocaGalleryFileThumbnail::getThumbnailName($this->items[$iS]->filename, 'medium');
			$imgLinkOrig= JURI::base(true) . '/' .PhocaGalleryFile::getFileOriginal($this->items[$iS]->filename, 1);
			if ($this->t['detail_window'] == 7) {
				$siteLink 	= JRoute::_('index.php?option=com_phocagallery&view=detail&catid='.$this->items[$iS]->catslug.'&id='. $this->items[$iS]->slug.'&Itemid='. $this->itemId  );
			} else {
				$siteLink 	= JRoute::_('index.php?option=com_phocagallery&view=detail&catid='.$this->items[$iS]->catslug.'&id='. $this->items[$iS]->slug.'&tmpl=component'.'&Itemid='. $this->itemId  );
			}
			$imgLink	= $thumbLink->rel;


			if ($extImage) {
				$imgLink		= $this->items[$iS]->extl;
				$imgLinkOrig	= $this->items[$iS]->exto;
			}

			// Detail Window
			if ($this->t['detail_window'] == 2 ) {
				$this->items[$iS]->link 		= $imgLink;
				$this->items[$iS]->link2		= $imgLink;
				$this->items[$iS]->linkother	= $imgLink;
				$this->items[$iS]->linkorig	= $imgLinkOrig;

			} else if ( $this->t['detail_window'] == 3 ) {

				$this->items[$iS]->link 		= $imgLink;
				$this->items[$iS]->link2 		= $imgLink;
				$this->items[$iS]->linkother	= $siteLink;
				$this->items[$iS]->linkorig		= $imgLinkOrig;

			}

			else if ( $this->t['detail_window'] == 5 ) {

				$this->items[$iS]->link 		= $imgLink;
				$this->items[$iS]->link2 		= $siteLink;
				$this->items[$iS]->linkother	= $siteLink;
				$this->items[$iS]->linkorig	= $imgLinkOrig;

			} else if ( $this->t['detail_window'] == 6 ) {

				$this->items[$iS]->link 		= $imgLink;
				$this->items[$iS]->link2 		= $imgLink;
				$this->items[$iS]->linkother	= $siteLink;
				$this->items[$iS]->linkorig	= $imgLinkOrig;

				// jak data js
				switch ($this->t['jakdescription']) {
					case 0:
						$descriptionJakJs = '';
					break;

					case 2:
						$descriptionJakJs = PhocaGalleryText::strTrimAll(addslashes( $this->items[$iS]->description));
					break;

					case 3:
						$descriptionJakJs = PhocaGalleryText::strTrimAll(addslashes($this->items[$iS]->title));
						if ($this->items[$iS]->description != '') {
							$descriptionJakJs .='<br />' .PhocaGalleryText::strTrimAll(addslashes($this->items[$iS]->description));
						}
					break;

					case 1:
					default:
						$descriptionJakJs = PhocaGalleryText::strTrimAll(addslashes($this->items[$iS]->title));
					break;
				}
				$this->items[$iS]->linknr		= $iM;
				$this->t['jakdatajs'][$iS] = "{alt: '".PhocaGalleryText::strTrimAll(addslashes($this->items[$iS]->title))."',";
				if ($descriptionJakJs != '') {
					$this->t['jakdatajs'][$iS] .= "description: '".$descriptionJakJs."',";
				} else {
					$this->t['jakdatajs'][$iS] .= "description: ' ',";
				}


				if ($extImage) {
					$this->t['jakdatajs'][$iS] .= "small: {url: '".$this->items[$iS]->extm."'},"
					."big: {url: '".$this->items[$iS]->extl."'} }";
				} else {
					$this->t['jakdatajs'][$iS] .= "small: {url: '".htmlentities(JURI::base(true).'/'.PhocaGalleryText::strTrimAll(addslashes($thumbLinkM->rel)))."'},"
					."big: {url: '".htmlentities(JURI::base(true).'/'.PhocaGalleryText::strTrimAll(addslashes($imgLink)))."'} }";
				}
			}

			// Added Slimbox URL settings

			else if ( $this->t['detail_window'] == 8 ) {

				$this->items[$iS]->link 		= $imgLink;
				$this->items[$iS]->link2 		= $imgLink;
				$this->items[$iS]->linkother	= $imgLink;
				$this->items[$iS]->linkorig	= $imgLinkOrig;

			}

			else if ( $this->t['detail_window'] == 9 ) {

				$this->items[$iS]->link 		= $siteLink;
				$this->items[$iS]->link2 		= $siteLink;
				$this->items[$iS]->linkother	= $siteLink;
				$this->items[$iS]->linkorig	= $imgLinkOrig;

			}

			else if ( $this->t['detail_window'] == 10 ) {

				$this->items[$iS]->link 		= $imgLink;
				$this->items[$iS]->link2 		= $imgLink;
				$this->items[$iS]->linkother	= $siteLink;
				$this->items[$iS]->linkorig	= $imgLinkOrig;

			}

			else if ( $this->t['detail_window'] == 11 ) {

				$this->items[$iS]->link 		= $siteLink;
				$this->items[$iS]->link2 		= $siteLink;
				$this->items[$iS]->linkother	= $siteLink;
				$this->items[$iS]->linkorig	= $imgLinkOrig;

			}

			else if ( $this->t['detail_window'] == 12 ) {

				$this->items[$iS]->link 		= $imgLink;
				$this->items[$iS]->link2 		= $imgLink;
				$this->items[$iS]->linkother	= $siteLink;
				$this->items[$iS]->linkorig		= $imgLinkOrig;

			} else if ( $this->t['detail_window'] == 14 ) {

				$this->items[$iS]->link 		= $imgLink;
				$this->items[$iS]->link2 		= $siteLink;
				$this->items[$iS]->linkother	= $siteLink;
				$this->items[$iS]->linkorig		= $imgLinkOrig;


				switch ($this->t['photoswipe_display_caption']) {
					case 0:
						$this->items[$iS]->photoswipecaption = '';
						break;

					case 2:
						$this->items[$iS]->photoswipecaption = PhocaGalleryText::strTrimAll(( $this->items[$iS]->description));
						break;

					case 3:
						$this->items[$iS]->photoswipecaption = PhocaGalleryText::strTrimAll(($this->items[$iS]->title));
						if ($this->items[$iS]->description != '') {
							$this->items[$iS]->photoswipecaption .='<br />' .PhocaGalleryText::strTrimAll(($this->items[$iS]->description));
						}
						break;

					case 1:
					default:
						$this->items[$iS]->photoswipecaption = PhocaGalleryText::strTrimAll(($this->items[$iS]->title));
						break;
				}

			}

			else {

				$this->items[$iS]->link 		= $siteLink;
				$this->items[$iS]->link2 		= $siteLink;
				$this->items[$iS]->linkother	= $siteLink;
				$this->items[$iS]->linkorig	= $imgLinkOrig;

			}

			// Buttons, e.g. shadowbox:
			// button - click on image
			// button2 - click on zoom icon (cannot be the same as click on image because of duplicity of images)
			// buttonOther - other detail window like download, geotagging
			$this->items[$iS]->button 			= &$this->button;
			$this->items[$iS]->button2 			= &$this->button2;
			$this->items[$iS]->buttonother 		= &$this->buttonother;

			$this->items[$iS]->display_icon_detail 	= $this->t['display_icon_detail'];
			$this->items[$iS]->display_icon_download= $this->t['display_icon_download'];
			$this->items[$iS]->display_name 		= $this->t['display_name'];
			$this->items[$iS]->display_icon_vm 		= '';
			$this->items[$iS]->display_icon_pc 		= '';
			$this->items[$iS]->start_cooliris 		= $this->t['start_cooliris'] ;
			$this->items[$iS]->type				= 2;

			// Trash icon
			if ($this->t['trash'] == 1) {
				$this->items[$iS]->trash	= 1;
			} else {
				$this->items[$iS]->trash	= 0;
			}

			// Publish Unpublish icon
			if ($this->t['publish_unpublish'] == 1) {
				$this->items[$iS]->publish_unpublish	= 1;
			} else {
				$this->items[$iS]->publish_unpublish	= 0;
			}

			// Publish Unpublish icon
			if ($this->t['approved_not_approved'] == 1) {
				$this->items[$iS]->approved_not_approved	= 1;
			} else {
				$this->items[$iS]->approved_not_approved	= 0;
			}

			// PICLENS
			if($this->t['enablecooliris']) {
				$this->items[$iS]->enable_cooliris	= 1;
			} else {
				$this->items[$iS]->enable_cooliris	= 0;
			}

			// 2. GEOTAGGING IMAGE
			// We have checked the category so if geotagging is enabled
			// and there is no geotagging data for category, then $this->t['display_icon_geo'] = 0;
			// so we need to check it for the image too, we need to set the $this->t['display_icon_geoimage'] for image only
			// we are in loop now
			$this->t['display_icon_geoimagetmp'] = 0;
			if ($this->t['display_icon_geoimage'] == 1) {

				$this->t['display_icon_geoimagetmp'] = 1;
				if (isset($this->items[$iS]->latitude) && $this->items[$iS]->latitude != '' && $this->items[$iS]->latitude != 0
					&& isset($this->items[$iS]->longitude) && $this->items[$iS]->longitude != '' && $this->items[$iS]->longitude != 0 ) {
				} else {
					$this->t['display_icon_geoimagetmp'] = 0;
				}
			}

			// GEOTAGGING
			if($this->t['display_icon_geo'] == 1 || $this->t['display_icon_geoimagetmp'] == 1) {
				$this->items[$iS]->display_icon_geo		= 1;
				$this->t['display_icon_geo_box']	= 1;// because of height of box
			} else {
				$this->items[$iS]->display_icon_geo	= 0;
			}

			// Set it back because of loop
			$this->t['display_icon_geoimagetmp'] = 0;

			// CAMERA INFO
			if($this->t['display_camera_info'] == 1) {
				$this->items[$iS]->camera_info			= 1;
			} else {
				$this->items[$iS]->camera_info			= 0;
			}

			// EXT LINK
			$this->items[$iS]->display_icon_extlink1	= 0;
			if (isset($this->items[$iS]->extlink1)) {
				$this->items[$iS]->extlink1	= explode("|", $this->items[$iS]->extlink1, 4);

				if (isset($this->items[$iS]->extlink1[0]) && $this->items[$iS]->extlink1[0] != '' && isset($this->items[$iS]->extlink1[1])) {
					$this->items[$iS]->display_icon_extlink1		= 1;
					$this->t['display_icon_extlink1_box'] = 1;// because of height of box
					if (!isset($this->items[$iS]->extlink1[2])) {
						$this->items[$iS]->extlink1[2] = '_self';
					}
					if (!isset($this->items[$iS]->extlink1[3]) || $this->items[$iS]->extlink1[3] == 1) {
						//$this->items[$iS]->extlink1[4] = Joomla\CMS\HTML\HTMLHelper::_('image', 'media/com_phocagallery/images/icon-extlink1.png', JText::_($this->items[$iS]->extlink1[1]));
						$this->items[$iS]->extlink1[4] = PhocaGalleryRenderFront::renderIcon('extlink1', 'media/com_phocagallery/images/icon-extlink1.png', JText::_($this->items[$iS]->extlink1[1]));
						$this->items[$iS]->extlink1[5] = '';
					} else {
						$this->items[$iS]->extlink1[4] = $this->items[$iS]->extlink1[1];
						$this->items[$iS]->extlink1[5] = 'style="text-decoration:underline"';
					}
				} else {
					$this->items[$iS]->display_icon_extlink1		= 0;
				}
			}

			$this->items[$iS]->display_icon_extlink2		= 0;
			if (isset($this->items[$iS]->extlink2)) {
				$this->items[$iS]->extlink2	= explode("|", $this->items[$iS]->extlink2, 4);
				if (isset($this->items[$iS]->extlink2[0]) && $this->items[$iS]->extlink2[0] != '' && isset($this->items[$iS]->extlink2[1])) {
					$this->items[$iS]->display_icon_extlink2		= 1;
					$this->t['display_icon_extlink2_box'] = 1;// because of height of box
					if (!isset($this->items[$iS]->extlink2[2])) {
						$this->items[$iS]->extlink2[2] = '_self';
					}
					if (!isset($this->items[$iS]->extlink2[3]) || $this->items[$iS]->extlink2[3] == 1) {
						//$this->items[$iS]->extlink2[4] = Joomla\CMS\HTML\HTMLHelper::_('image', 'media/com_phocagallery/images/icon-extlink2.png', JText::_($this->items[$iS]->extlink2[1]));
						$this->items[$iS]->extlink2[4] = PhocaGalleryRenderFront::renderIcon('extlink2', 'media/com_phocagallery/images/icon-extlink2.png', JText::_($this->items[$iS]->extlink2[1]));
						$this->items[$iS]->extlink2[5] = '';
					}else {
						$this->items[$iS]->extlink2[4] = $this->items[$iS]->extlink2[1];
						$this->items[$iS]->extlink2[5] = 'style="text-decoration:underline"';
					}
				} else {
					$this->items[$iS]->display_icon_extlink2		= 0;
				}
			}


			// OVERLIB
			if (!empty($this->items[$iS]->description)) {
				$divPadding = 'ph-ovrl1';
			} else {
				$divPadding = 'ph-ovrl2';
			}

			// Resize image in overlib by rate
			$wHOutput = array();
			if (isset($this->items[$iS]->extpic) && $this->items[$iS]->extpic != '') {
				if ((int)$this->t['overlibimagerate'] > 0) {
					$imgSize	= @getimagesize($this->items[$iS]->extl);
					$wHOutput	= PhocaGalleryImage::getTransformImageArray($imgSize, $this->t['overlibimagerate']);
				}

				$oImg		= Joomla\CMS\HTML\HTMLHelper::_( 'image', $this->items[$iS]->extl, '' /*htmlspecialchars( addslashes($this->items[$iS]->title)) */, $wHOutput );
			} else {

				$thumbL 	= str_replace ('phoca_thumb_m1_','phoca_thumb_m_',$this->items[$iS]->linkthumbnailpath);
				$thumbL 	= str_replace ('phoca_thumb_m2_','phoca_thumb_m_',$thumbL);
				$thumbL 	= str_replace ('phoca_thumb_m3_','phoca_thumb_m_',$thumbL);
				$thumbL 	= str_replace ('phoca_thumb_m_','phoca_thumb_l_',$thumbL);

				if ((int)$this->t['overlibimagerate'] > 0) {
					$imgSize	= @getimagesize($thumbL);
					$wHOutput	= PhocaGalleryImage::getTransformImageArray($imgSize, $this->t['overlibimagerate']);
				}
				$thumbLI 	= str_replace ('phoca_thumb_m1_','phoca_thumb_m_',$this->items[$iS]->linkthumbnailpath);
				$thumbLI 	= str_replace ('phoca_thumb_m2_','phoca_thumb_m_',$thumbLI);
				$thumbLI 	= str_replace ('phoca_thumb_m3_','phoca_thumb_m_',$thumbLI);
				$oImg		= Joomla\CMS\HTML\HTMLHelper::_( 'image', $thumbL, '' /*$this->items[$iS]->title*/, $wHOutput );
			}

			switch ($enable_overlib) {

				case 1:
				case 4:
					$uBy = '';//Uploaded by ...
					if ($enable_overlib == 4 && isset($this->items[$iS]->usernameno) && $this->items[$iS]->usernameno != '') {
						$uBy = '<div>' . JText::_('COM_PHOCAGALLERY_UPLOADED_BY') . ' <strong>'.$this->items[$iS]->usernameno.'</strong></div>';
					}
					$this->items[$iS]->overlib			= 1;
					$this->items[$iS]->overlib_value 	= "\n\n" ."onmouseover=\"return overlib('".htmlspecialchars( addslashes('<div class="pg-overlib"><center>' . $oImg . "</center></div>" . $uBy ))."', CAPTION, '". htmlspecialchars( addslashes($this->items[$iS]->title))."' ,".htmlspecialchars($this->t['overlib_attributes']).");\""." onmouseout=\"return nd();\"" . "\n";
				break;

				case 2:
				case 5:
					$uBy = '';//Uploaded by ...
					if ($enable_overlib == 5 && isset($this->items[$iS]->usernameno) && $this->items[$iS]->usernameno != '') {
						$uBy = '<div>' . JText::_('COM_PHOCAGALLERY_UPLOADED_BY') . ' <strong>'.$this->items[$iS]->usernameno.'</strong></div>';
					}
					$this->items[$iS]->overlib			= 2;

					$this->items[$iS]->description = str_replace('"', '\'', $this->items[$iS]->description);

					if (strip_tags($this->items[$iS]->description) == $this->items[$iS]->description) {
					   $this->items[$iS]->description      = str_replace("\n", '<br />', $this->items[$iS]->description);
					}

					//$this->items[$iS]->description		= str_replace("\n", '<br />', $this->items[$iS]->description);
					$sA = array(utf8_encode(chr(11)), utf8_encode(chr(160)));
					$eA	= array("\t", "\n", "\r", "\0");
					$this->items[$iS]->description = str_replace($sA, ' ', $this->items[$iS]->description);
					$this->items[$iS]->description = str_replace($eA, '', $this->items[$iS]->description);

					$this->items[$iS]->overlib_value 		= " onmouseover=\"return overlib('".htmlspecialchars( addslashes('<div class="pg-overlib"><div class="'.$divPadding.'">'.$this->items[$iS]->description.'</div></div>'. $uBy))."', CAPTION, '". htmlspecialchars( addslashes($this->items[$iS]->title))."', ".htmlspecialchars($this->t['overlib_attributes']).");\""
				. " onmouseout=\"return nd();\" ";
				break;

				case 3:
				case 6:
					$uBy = '';//Uploaded by ...
					if ($enable_overlib == 6 && isset($this->items[$iS]->usernameno) && $this->items[$iS]->usernameno != '') {
						$uBy = '<div>' . JText::_('COM_PHOCAGALLERY_UPLOADED_BY') . ' <strong>'.$this->items[$iS]->usernameno.'</strong></div>';
					}
					$this->items[$iS]->overlib			= 3;

					$this->items[$iS]->description = str_replace('"', '\'', $this->items[$iS]->description);
					$this->items[$iS]->description		= str_replace("\n", '<br />', $this->items[$iS]->description);
					$sA = array(utf8_encode(chr(11)), utf8_encode(chr(160)));
					$eA	= array("\t", "\n", "\r", "\0");
					$this->items[$iS]->description = str_replace($sA, ' ', $this->items[$iS]->description);
					$this->items[$iS]->description = str_replace($eA, '', $this->items[$iS]->description);

					$this->items[$iS]->overlib_value 		= " onmouseover=\"return overlib('".PhocaGalleryText::strTrimAll(htmlspecialchars( addslashes( '<div class="pg-overlib"><div style="text-align:center"><center>' . $oImg . '</center></div><div class="'.$divPadding.'">' . $this->items[$iS]->description . '</div></div>' . $uBy)))."', CAPTION, '". htmlspecialchars( addslashes($this->items[$iS]->title))."', ".htmlspecialchars($this->t['overlib_attributes']).");\""
					. " onmouseout=\"return nd();\" ";
				break;

				default:
					$this->items[$iS]->overlib			= 0;
					$this->items[$iS]->overlib_value	= '';
				break;
			}

			// Phoca Cart link
			if ($this->t['display_icon_pc'] == 1) {

				phocagalleryimport('phocagallery.phocacart.phocacart');
				$pcLink	= PhocaGalleryPhocaCart::getPcLink($this->items[$iS]->pcproductid, $errorMsg);

				if (!$pcLink) {
					$this->items[$iS]->display_icon_pc	= '';
				} else {
					$this->items[$iS]->display_icon_pc	= 1;
					$this->items[$iS]->pclink			= $pcLink;
					$this->t['display_icon_pcbox']	= 1;// because of height of box
				}

			} else {
				$this->items[$iS]->display_icon_pc = '';
			}
			// End PC Link

			// VirtueMart link
			if ($this->t['display_icon_vm'] == 1) {

				phocagalleryimport('phocagallery.virtuemart.virtuemart');
				$vmLink	= PhocaGalleryVirtueMart::getVmLink($this->items[$iS]->vmproductid, $errorMsg);

				if (!$vmLink) {
					$this->items[$iS]->display_icon_vm	= '';
				} else {
					$this->items[$iS]->display_icon_vm	= 1;
					$this->items[$iS]->vmlink			= $vmLink;
					$this->t['display_icon_vmbox']	= 1;// because of height of box
				}

			} else {
				$this->items[$iS]->display_icon_vm = '';
			}
			// End VM Link

			// V O T E S - IMAGES
			if ((int)$this->t['display_rating_img'] == 1) {
				$this->items[$iS]->votescountimg		= 0;
				$this->items[$iS]->votesaverageimg	= 0;
				$this->items[$iS]->voteswidthimg		= 0;
				$votesStatistics	= PhocaGalleryRateImage::getVotesStatistics((int)$this->items[$iS]->id);
				if (!empty($votesStatistics->count)) {
					$this->items[$iS]->votescountimg = $votesStatistics->count;
				}
				if (!empty($votesStatistics->average)) {
					$this->items[$iS]->votesaverageimg = $votesStatistics->average;
					if ($this->items[$iS]->votesaverageimg > 0) {
						$this->items[$iS]->votesaverageimg 	= round(((float)$this->items[$iS]->votesaverageimg / 0.5)) * 0.5;
						$this->items[$iS]->voteswidthimg		= 16 * $this->items[$iS]->votesaverageimg;
					} else {
						$this->items[$iS]->votesaverageimg = (int)0;// not float displaying
					}

				}
			}


			$this->items[$iS]->display_icon_commentimg	= 0;
			// C O M M E N T S - IMAGES
			if ((int)$this->t['display_comment_img'] == 1 || (int)$this->t['display_comment_img'] == 3) {
				$this->items[$iS]->display_icon_commentimg	= 1;
				$this->t['display_icon_commentimg_box']	= 1;// because of height of box

			}

			// COMMENTS IMAGES, masonry
			if ((int)$this->t['display_comment_img'] == 2 || (int)$this->t['display_comment_img'] == 3) {
				//PhocaGalleryCommentImage::renderCommentImageJS();
				$this->items[$iS]->allready_commented = 0;
				$this->items[$iS]->allready_commented = PhocaGalleryCommentImage::checkUserComment( (int)$this->items[$iS]->id, (int)$this->t['user']->id );
				$this->items[$iS]->comment_items	  = PhocaGalleryCommentImage::displayComment( $this->items[$iS]->id );

			}

			// ALT VALUE
			$altValue	= PhocaGalleryRenderFront::getAltValue($this->t['altvalue'], $this->items[$iS]->title, $this->items[$iS]->description, $this->items[$iS]->metadesc);
			$this->items[$iS]->altvalue				= $altValue;

			// TITLE TAG - Description Output in Title Tag
			$imgAlt = $imgTitle = '';

			// Some methods cannot use Alt because of conflicting with Title and popup methods
			if ($this->t['detail_window'] == 3 || $this->t['detail_window'] == 9 || $this->t['detail_window'] == 10 || $this->t['detail_window'] == 12 ) {
				$imgAlt 	= $this->items[$iS]->altvalue;
				$imgTitle	= $this->items[$iS]->title;
				if ($imgAlt == $imgTitle) {
					$imgAlt = '';
				}
				$this->items[$iS]->oimgalt = $imgAlt;
			} else {
				$this->items[$iS]->oimgalt = $altValue;
			}


			// TITLE TAG - Detail
			if ($this->t['detail_window'] == 9 || $this->t['detail_window'] == 10) {
				$detailAlt 		= $this->items[$iS]->altvalue;
				$detailTitle	= $this->items[$iS]->title;
				if ($detailAlt == $detailTitle) {
					$detailAlt = '';
				}
			} else {
				$detailAlt 		= JText::_('COM_PHOCAGALLERY_IMAGE_DETAIL');
				$detailTitle 	= JText::_('COM_PHOCAGALLERY_IMAGE_DETAIL');
			}
			$this->items[$iS]->oimgaltdetail 		= $detailAlt;
			$this->items[$iS]->oimgtitledetail 	= $detailTitle;

			$titleDesc = '';
			if ($this->t['display_title_description'] == 1) {
				$titleDesc .= $this->items[$iS]->title;
				if ($this->items[$iS]->description != '' && $titleDesc != '') {
					$titleDesc .= ' - ';
				}
			}

			if (($this->t['detail_window'] == 8 || $this->t['detail_window'] == 10 || $this->t['detail_window'] == 12) && $this->t['displaydescriptiondetail'] > 0) {
				$this->items[$iS]->odesctitletag = strip_tags($titleDesc).strip_tags($this->items[$iS]->description);
			} else {
				$this->items[$iS]->odesctitletag = strip_tags($imgTitle);
			}

			// Overlib class
			if ($this->items[$iS]->overlib == 0) {
				$this->items[$iS]->ooverlibclass = array('class' => 'pg-image img img-responsive', 'itemprop' => "thumbnail");
			} else {
				$this->items[$iS]->ooverlibclass = array('class' => 'pimo pg-image img img-responsive img-responsive2', 'itemprop' => "thumbnail");
			}

			// Tags
			$this->items[$iS]->otags = '';
			if ($this->t['display_tags_links'] == 1 || $this->t['display_tags_links'] == 3) {
				$this->items[$iS]->otags = PhocaGalleryTag::displayTags($this->items[$iS]->id);
				if ($this->items[$iS]->otags != '') {
					$this->t['displaying_tags_true'] = 1;
				}
			}


			$iS++;
		}


		// END IMAGES


		// Upload Form - - - - - - - - - - - - -
		// Set FTP form
		$ftp = !JClientHelper::hasCredentials('ftp');

		// PARAMS - Upload size
		$this->t['uploadmaxsize'] = $this->params->get( 'upload_maxsize', 3000000 );


		$sess = JFactory::getSession();
		$this->assignRef('session', $sess);
		//$this->assignRef('uploadmaxsize', $upload_maxsize);
		// END Upload Form - - - - - - - - - - - -


		// V O T E S - CATEGORY
		// Only registered (VOTES + COMMENTS)
		$this->t['not_registered'] 	= true;
		$this->t['name']		= '';
		if ($access > 0) {
			$this->t['not_registered'] 	= false;
			$this->t['name']				= $this->t['user']->name;
		}

		// VOTES Statistics
		if ((int)$this->t['display_rating'] == 1 && (int)$id > 0) {
			$this->t['votescount']		= 0;
			$this->t['votesaverage'] 	= 0;
			$this->t['voteswidth']		= 0;
			$votesStatistics	= PhocaGalleryRateCategory::getVotesStatistics((int)$id);

			if (!empty($votesStatistics->count)) {
				$this->t['votescount'] = $votesStatistics->count;
			}
			if (!empty($votesStatistics->average)) {
				$this->t['votesaverage'] = $votesStatistics->average;
				if ($this->t['votesaverage'] > 0) {
					$this->t['votesaverage'] 	= round(((float)$this->t['votesaverage'] / 0.5)) * 0.5;
					$this->t['voteswidth']		= 22 * $this->t['votesaverage'];
				} else {
					$this->t['votesaverage'] = (int)0;// not float displaying
				}

			}
			if ((int)$this->t['votescount'] > 1) {
				$this->t['votestext'] = 'COM_PHOCAGALLERY_VOTES';
			} else {
				$this->t['votestext'] = 'COM_PHOCAGALLERY_VOTE';
			}

			// Already rated?
			$this->t['alreay_rated']	= PhocaGalleryRateCategory::checkUserVote( (int)$id, (int)$this->t['user']->id );
		}



		// COMMENTS
		if ((int)$this->t['display_comment'] == 1 && (int)$id > 0) {
			$document->addScript(JURI::base(true).'/media/com_phocagallery/js/comments.js');
			$document->addCustomTag(PhocaGalleryRenderFront::renderCommentJS((int)$this->t['maxcommentchar']));

			$this->t['already_commented'] 	= PhocaGalleryCommentCategory::checkUserComment( (int)$id, (int)$this->t['user']->id );
			$commentItem				= PhocaGalleryCommentCategory::displaycomment( (int)$id );

			$this->assignRef( 'commentitem',		$commentItem);
		}



		// - - - - - - - - - - - - - - - -
		// TABS
		// - - - - - - - - - - - - - - - -
		$this->t['displaytabs']	= 0;
		$this->t['currenttab']	= 0;

		if ((int)$id > 0) {
			$displayTabs	= 0;

			// R A T I N G
			if ((int)$this->t['display_rating'] == 0) {
				$currentTab['rating'] = -1;
			} else {
				$currentTab['rating'] = $displayTabs;
				$displayTabs++;
			}

			// C O M M E N T S
			if ((int)$this->t['display_comment'] == 0) {
				$currentTab['comment'] = -1;
			} else {
				$currentTab['comment'] = $displayTabs;
				$displayTabs++;
			}

			// S T A T I S T I C S
			if ((int)$this->t['displaycategorystatistics'] == 0) {
				$currentTab['statistics'] = -1;
			} else {
				$currentTab['statistics'] = $displayTabs;
				$displayTabs++;


				$this->t['displaymaincatstat']			= $this->params->get( 'display_main_cat_stat', 1 );
				$this->t['displaylastaddedcatstat']	= $this->params->get( 'display_lastadded_cat_stat', 1 );
				$this->t['displaymostviewedcatstat']	= $this->params->get( 'display_mostviewed_cat_stat', 1 );
				$this->t['countlastaddedcatstat']		= $this->params->get( 'count_lastadded_cat_stat', 3 );
				$this->t['countmostviewedcatstat']		= $this->params->get( 'count_mostviewed_cat_stat', 3 );


				if ($this->t['displaymaincatstat'] == 1) {
					$numberImgP		= $model->getCountImages($id, 1);
					$this->t['numberimgpub'] 	= $numberImgP->countimg;
					$numberImgU		= $model->getCountImages($id, 0);
					$this->t['numberimgunpub'] = $numberImgU->countimg;
					$this->categoryViewed	= $model->getHits($id);
					$this->t['categoryviewed'] = $this->categoryViewed->catviewed;
				}

				// M O S T   V I E W E D   I M A G E S
				//$this->t['mostviewedimg'] = array();
				if ($this->t['displaymostviewedcatstat'] == 1) {
					$mostViewedImages	= $model->getStatisticsImages($id, 'hits', 'DESC', $this->t['countmostviewedcatstat']);
					for($i = 0; $i <  count($mostViewedImages); $i++) {
						$itemMVI 		=& $mostViewedImages[$i];
						$itemMVI->button 				= &$this->button;
						$itemMVI->button2 				= &$this->button2;
						$itemMVI->buttonother 			= &$this->buttonother;
						$itemMVI->display_icon_detail 	= $this->t['display_icon_detail'];
						$itemMVI->display_name 			= $this->t['display_name'];
						$itemMVI->type		 			= 2;

						$altValue	= PhocaGalleryRenderFront::getAltValue($this->t['altvalue'], $itemMVI->title, $itemMVI->description, $itemMVI->metadesc);
						$itemMVI->altvalue				= $altValue;

						$thumbLink	= PhocaGalleryFileThumbnail::getThumbnailName($itemMVI->filename, 'large');
						$siteLink 	= JRoute::_('index.php?option=com_phocagallery&view=detail&catid='.$this->category->slug.'&id='. $itemMVI->slug.'&tmpl=component'.'&Itemid='. $this->itemId  );
						$imgLink	= JURI::base(true) . '/'.$thumbLink->rel;

						$dataSizeW = $this->t['large_image_width'];
						$dataSizeH = $this->t['large_image_height'];

						switch ($this->t['detail_window']) {
							case 2:
							case 3:
							case 8:
							case 14:
							$itemMVI->link 		= $imgLink;
							break;
							default:
							$itemMVI->link 		= $siteLink;
							break;
						}
						//$this->t['mostviewedimg'][] = $itemMVI;
						if ($itemMVI->extw != '') {
							$extw 				= explode(',',$itemMVI->extw);
							$itemMVI->extw		= $extw[1];
							$dataSizeW 			= $extw[0];
						}
						if ($itemMVI->exth != '') {
							$exth 				= explode(',',$itemMVI->exth);
							$itemMVI->exth	= $exth[1];
							$dataSizeH 		= $exth[0];
						}

						$itemMVI->datasize = '';


						// Photoswipe needs data-size parameter
						if ( $this->t['detail_window'] == 14) {
							if ($itemMVI->extw != '' && $itemMVI->exth != '') {
								$itemMVI->datasize 		= 'data-size="'.(int)$dataSizeW.'x'.(int)$dataSizeH. '"';
							} else {
								if (JFile::exists($thumbLink->abs)) {
									$thumbSize	= @getimagesize($thumbLink->abs);
									if (isset($thumbSize[0]) && isset($thumbSize[1])) {
										$itemMVI->datasize 		= 'data-size="'.(int)$thumbSize[0].'x'.(int)$thumbSize[1]. '"';
									}
								}
							}
						}
					}
					$this->t['mostviewedimg'] = $mostViewedImages;
				}

				// L A S T   A D D E D   I M A G E S
				//$this->t['lastaddedimg'] = array();
				if ($this->t['displaylastaddedcatstat'] == 1) {
					$lastAddedImages	= $model->getStatisticsImages($id, 'date', 'DESC', $this->t['countlastaddedcatstat']);
					for($i = 0; $i <  count($lastAddedImages); $i++) {
						$itemLAI 		=& $lastAddedImages[$i];
						$itemLAI->link 	= JRoute::_('index.php?option=com_phocagallery&view=detail&catid='.$this->category->slug.'&id='. $itemLAI->slug.'&tmpl=component'.'&Itemid='. $this->itemId  );
						$itemLAI->button 				= &$this->button;
						$itemLAI->button2 				= &$this->button2;
						$itemLAI->buttonother 			= &$this->buttonother;
						$itemLAI->display_icon_detail 	= $this->t['display_icon_detail'];
						$itemLAI->display_name 			= $this->t['display_name'];
						$itemLAI->type		 			= 2;

						$altValue	= PhocaGalleryRenderFront::getAltValue($this->t['altvalue'], $itemLAI->title, $itemLAI->description, $itemLAI->metadesc);
						$itemLAI->altvalue				= $altValue;

						$thumbLink	= PhocaGalleryFileThumbnail::getThumbnailName($itemLAI->filename, 'large');
						$siteLink 	= JRoute::_('index.php?option=com_phocagallery&view=detail&catid='.$this->category->slug.'&id='. $itemLAI->slug.'&tmpl=component'.'&Itemid='. $this->itemId  );
						$imgLink	= JURI::base(true) . '/'.$thumbLink->rel;

						$dataSizeW = $this->t['large_image_width'];
						$dataSizeH = $this->t['large_image_height'];

						switch ($this->t['detail_window']) {
							case 2:
							case 3:
							case 8:
							case 14:
							$itemLAI->link 		= $imgLink;
							break;
							default:
							$itemLAI->link 		= $siteLink;
							break;
						}
						//$this->t['lastaddedimg'][] = $itemLAI;

						if ($itemLAI->extw != '') {
							$extw 				= explode(',',$itemLAI->extw);
							$itemLAI->extw		= $extw[1];
							$dataSizeW = $extw[0];
						}
						if ($itemLAI->exth != '') {
							$exth 				= explode(',',$itemLAI->exth);
							$itemLAI->exth	= $exth[1];
							$dataSizeH = $exth[0];
						}

						$itemLAI->datasize = '';


						// Photoswipe needs data-size parameter
						if ( $this->t['detail_window'] == 14) {
							if ($itemLAI->extw != '' && $itemLAI->exth != '') {
								$itemLAI->datasize 		= 'data-size="'.(int)$dataSizeW.'x'.(int)$dataSizeH. '"';
							} else {
								if (JFile::exists($thumbLink->abs)) {
									$thumbSize	= @getimagesize($thumbLink->abs);
									if (isset($thumbSize[0]) && isset($thumbSize[1])) {
										$itemLAI->datasize 		= 'data-size="'.(int)$thumbSize[0].'x'.(int)$thumbSize[1]. '"';
									}
								}
							}
						}
					}

					$this->t['lastaddedimg'] = $lastAddedImages;
				}
			}

			// G E O T A G G I N G
			if ((int)$this->t['displaycategorygeotagging'] == 0) {
				$currentTab['geotagging'] = -1;
			} else if ( $this->map['longitude'] == '') {
				$currentTab['geotagging'] = -1;
			} else if ( $this->map['latitude'] == '') {
				$currentTab['geotagging'] = -1;
			} else {
				$currentTab['geotagging'] = $displayTabs;
				$displayTabs++;

				$this->t['googlemapsapikey'] 			= $this->params->get( 'google_maps_api_key', '' );
				$this->t['categorymapwidth'] 			= $this->params->get( 'category_map_width', '' );
				$this->t['categorymapheight'] 			= $this->params->get( 'category_map_height', 400 );

			}


			if ((int)$this->t['displaycreatecat'] == 0) {
				$currentTab['createsubcategory'] = -1;
			}else {
				$currentTab['createsubcategory'] = $displayTabs;
				$displayTabs++;
			}


			// = = = = = = = = = =
			// U P L O A D
			// = = = = = = = = = =
			$this->t['ftp'] 		= !JClientHelper::hasCredentials('ftp');


			// SEF problem
			$isThereQM = false;
			$isThereQM = preg_match("/\?/i", $this->t['action']);

			if ($isThereQM) {
				$amp = '&'; // will be protected by htmlspecialchars
			} else {
				$amp = '?';
			}
			$isThereTab = false;
			$isThereTab = preg_match("/tab=/i", $this->t['action']);

			if ((int)$this->t['displayupload'] == 0) {
				$currentTab['upload'] = -1;
			}else {
				$currentTab['upload'] = $displayTabs;
				$displayTabs++;
			}


			if ((int)$this->t['ytbupload'] == 0 || (int)$this->t['displayupload'] == 0) {
				$currentTab['ytbupload'] = -1;
			}else {
				$currentTab['ytbupload'] = $displayTabs;
				$displayTabs++;
			}

			if ((int)$this->t['enablemultiple'] < 1 || (int)$this->t['displayupload'] == 0) {
				$currentTab['multipleupload'] = -1;
			}else {
				$currentTab['multipleupload'] = $displayTabs;
				$displayTabs++;
			}

			if ((int)$this->t['enablejava'] < 1 || (int)$this->t['displayupload'] == 0) {
				$currentTab['javaupload'] = -1;
			}else {
				$currentTab['javaupload'] = $displayTabs;
				$displayTabs++;
			}

			$this->t['displaytabs']	= $displayTabs;
			$this->t['currenttab']	= $currentTab;


			// - - - - - - - - - - -
			// Upload
			// - - - - - - - - - - -
			if ((int)$this->t['displayupload'] == 1) {
				$sU							= new PhocaGalleryFileUploadSingle();
				$sU->returnUrl				= htmlspecialchars($this->t['action'] . $amp .'task=upload&'. $this->session->getName().'='.$this->session->getId() .'&'. JSession::getFormToken().'=1&viewback=category&tab='.$this->t['currenttab']['upload']);
				$sU->tab					= $this->t['currenttab']['upload'];
				$this->t['su_output']	= $sU->getSingleUploadHTML(1);
				$this->t['su_url']		= htmlspecialchars($this->t['action'] . $amp .'task=upload&'. $this->session->getName().'='.$this->session->getId() .'&'. JSession::getFormToken().'=1&viewback=category&tab='.$this->t['currenttab']['upload']);

			}

			// - - - - - - - - - - -
			// Youtube Upload (single upload form can be used)
			// - - - - - - - - - - -


			if ((int)$this->t['ytbupload'] == 1 && $this->t['displayupload'] == 1 ) {
				$sYU						= new PhocaGalleryFileUploadSingle();
				$sYU->returnUrl				= htmlspecialchars($this->t['action'] . $amp .'task=ytbupload&'. $this->session->getName().'='.$this->session->getId().'&'. JSession::getFormToken().'=1&viewback=category&tab='.$this->t['currenttab']['ytbupload']);
				$sYU->tab					= $this->t['currenttab']['ytbupload'];
				$this->t['syu_output']	= $sYU->getSingleUploadHTML(1);
				$this->t['syu_url']		= htmlspecialchars($this->t['action'] . $amp .'task=ytbupload&'. $this->session->getName().'='.$this->session->getId().'&'. JSession::getFormToken().'=1&viewback=category&tab='.$this->t['currenttab']['ytbupload']);
			}


			// - - - - - - - - - - -
			// Multiple Upload
			// - - - - - - - - - - -
			// Get infos from multiple upload
			$muFailed						= $app->input->get( 'mufailed', '0', 'int' );
			$muUploaded						= $app->input->get( 'muuploaded', '0', 'int' );
			$this->t['mu_response_msg']	= $muUploadedMsg 	= '';

			if ($muUploaded > 0) {
				$muUploadedMsg = JText::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded;
			}
			if ($muFailed > 0) {
				$muFailedMsg = JText::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed;
			}
			if ($muFailed > 0 && $muUploaded > 0) {
				$this->t['mu_response_msg'] = '<div class="alert alert-info">'
				.JText::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded .'<br />'
				.JText::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed.'</div>';
			} else if ($muFailed > 0 && $muUploaded == 0) {
				$this->t['mu_response_msg'] = '<div class="alert alert-error">'
				.JText::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed.'</div>';
			} else if ($muFailed == 0 && $muUploaded > 0){
				$this->t['mu_response_msg'] = '<div class="alert alert-success">'
				.JText::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded.'</div>';
			} else {
				$this->t['mu_response_msg'] = '';
			}

			if((int)$this->t['enablemultiple']  == 1 && (int)$this->t['displayupload'] == 1) {

				PhocaGalleryFileUploadMultiple::renderMultipleUploadLibraries();
				$mU						= new PhocaGalleryFileUploadMultiple();
				$mU->frontEnd			= 1;
				$mU->method				= $this->t['multipleuploadmethod'];
				$mU->url				= htmlspecialchars($this->t['action'] . $amp .'controller=category&task=multipleupload&'
										 . $this->session->getName().'='.$this->session->getId().'&'
										 . JSession::getFormToken().'=1&tab='.$this->t['currenttab']['multipleupload']);
				$mU->reload				= htmlspecialchars($this->t['action'] . $amp
										. $this->session->getName().'='.$this->session->getId().'&'
										. JSession::getFormToken().'=1&tab='.$this->t['currenttab']['multipleupload']);
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
			if((int)$this->t['enablejava']  == 1 && (int)$this->t['displayupload'] == 1) {
				$jU							= new PhocaGalleryFileUploadJava();
				$jU->width					= $this->t['javaboxwidth'];
				$jU->height					= $this->t['javaboxheight'];
				$jU->resizewidth			= $this->t['multipleresizewidth'];
				$jU->resizeheight			= $this->t['multipleresizeheight'];
				$jU->uploadmaxsize			= $this->t['uploadmaxsize'];
				$jU->returnUrl				= $this->t['action'] . $amp
											. $this->session->getName().'='.$this->session->getId().'&'
											. JSession::getFormToken().'=1&tab='.$this->t['currenttab']['javaupload'];
				$jU->url					= $this->t['action'] . $amp .'controller=category&task=javaupload&amp;'
											. $this->session->getName().'='.$this->session->getId().'&'
											. JSession::getFormToken().'=1&amp;tab='.$this->t['currenttab']['javaupload'];
				$jU->source 				= JURI::root(true).'/media/com_phocagallery/js/jupload/wjhk.jupload.jar';
				$this->t['ju_output']	= $jU->getJavaUploadHTML();

			}
		}

		// ADD STATISTICS
		if ((int)$id > 0) {
			$model->hit($id);
		}

		// ADD JAK DATA CSS style
		if ( $this->t['detail_window'] == 6 ) {
			$document->addCustomTag('<script type="text/javascript">'
			. 'var dataJakJs = ['
			. implode(',', $this->t['jakdatajs'])
			. ']'
			. '</script>');
		}

		// Detail Window - will be popup or not
		if ($this->t['detail_window'] == 7) {
			$this->t['tmplcom']			= '';
			$this->t['tmplcomcomments']	= '';

		} else {
			$this->t['tmplcom'] 			= '&tmpl=component';
			$this->t['tmplcomcomments'] 	= '&tmpl=component';

		}
		if ($this->t['display_comment_nopup'] == 1) {
			$this->t['tmplcomcomments']	= '';
		}


		$this->t['boxsize'] 		= PhocaGalleryImage::setBoxSize($this->t, 2);
		$this->t['boxsizestat'] 	= PhocaGalleryImage::setBoxSize($this->t, 3);

		$masBoxWidth = $this->t['boxsize']['width'] + 20;
		// Masonry effect
		if ($this->t['diff_thumb_height'] == 2) {
			$document->addScript(JURI::base(true).'/media/com_phocagallery/js/masonry/masonry.min.js');

			if ($this->t['masonry_center'] == 1) {
				$document->addScript(JURI::base(true).'/media/com_phocagallery/js/masonry/masonry.initialize.center.js');
			} else if ($this->t['masonry_center'] == 2) {
				$document->addCustomTag('<script type="text/javascript">
				window.onload = function() {
				  var wall = new Masonry( document.getElementById(\'pg-msnr-container\'), {
					isFitWidth: true,
					isAnimated: true
				  });
				};
				</script>');
			} else {
				$document->addScript(JURI::base(true).'/media/com_phocagallery/js/masonry/masonry.initialize.js');
			}


		}

		$this->t['ebc'] = '<div style="text-align:right;color:#ccc;display:block">Powered by <a href="https://www.phoca.cz/phocagallery">Phoca Gallery</a></div>';



		$s = '';


		if ($this->t['responsive'] == 0 ) {
			$wT = 'width';
			$hT = 'height';

			$s .= "\n" . '#phocagallery img {'."\n";
			$s .= '   max-width: none;'."\n";
			$s .= "\n" . '}'."\n";

		} else {

			$wT = 'max-width';
			$hT = 'max-height';
		}

		if ($this->t['equal_heights'] == 1) {
			Joomla\CMS\HTML\HTMLHelper::_('jquery.framework', false);
			$document->addScript(JURI::root(true).'/media/com_phocagallery/js/jquery.equalheights.min.js');
			$document->addScriptDeclaration(
			'jQuery(window).load(function(){
				jQuery(\'.pg-cv-box\').equalHeights();
			});');
		}

		// CSS Specific
		$s .= "\n" . '#phocagallery {'."\n";
		if ($this->t['phocagallery_center'] == 1 || $this->t['phocagallery_center'] == 3) {
			$s .= '   margin: 0 auto; text-align: center;'."\n";
		}
		if ($this->t['phocagallery_width'] != '') {
			$s .= '   width: '.$this->t['phocagallery_width'].'px;'."\n";
		}
		$s .= '}'."\n";

		if ($this->t['phocagallery_center'] == 1 || $this->t['phocagallery_center'] == 3) {
			$s .= "\n" . '#pg-msnr-container {'."\n";
			$s .= '   margin: 0 auto;'."\n";
			$s .= '}'."\n";
		}

		$s .= '.pg-cv-box {'."\n";
		if ($this->t['diff_thumb_height'] > 0) {

		} else {
			$s .= '   '.$hT.': '.$this->t['boxsize']['height'].'px;'."\n";
		}
		$s .= '   '.$wT.': '.$this->t['boxsize']['width'].'px;'."\n";
		$s .= '}'."\n";

		$s .= '.pg-cv-box-stat {'."\n";
		$s .= '   '.$hT.': '.$this->t['boxsizestat']['height'].'px;'."\n";
		$s .= '   '.$wT.': '.$this->t['boxsizestat']['width'].'px;'."\n";
		$s .= '}'."\n";

		$s .= '.pg-cv-box-img {'."\n";

		$s .= '   '.$hT.': '.$this->t['imageheight'].'px;'."\n";
		$s .= '   '.$wT.': '.$this->t['imagewidth'].'px;'."\n";
		$s .= '}'."\n";

		$document->addCustomTag('<style type="text/css">'.$s.'</style>');


		$this->_prepareDocument();

		parent::display($tpl);
	}

	protected function _prepareDocument() {

		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		//$this->params		= $app->getParams();
		$title 		= null;

		$this->t['gallerymetakey'] 		= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 		= $this->params->get( 'gallery_metadesc', '' );

		$menu = $menus->getActive();

		/*if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
		}*/
		if ($menu && $this->params->get('display_menu_link_title', 1) == 1) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}

		$title = $this->params->get('page_title', '');
		



		if (empty($title)) {
			$title = htmlspecialchars_decode($app->get('sitename'));
		} else if ($app->get('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->get('sitename')), $title);

			if ($this->t['display_cat_name_title'] == 1 && isset($this->category->title) && $this->category->title != '') {
				$title = $title .' - ' .  $this->category->title;
			}

		} else if ($app->get('sitename_pagetitles', 0) == 2) {

			if ($this->t['display_cat_name_title'] == 1 && isset($this->category->title) && $this->category->title != '') {
				$title = $title .' - ' .  $this->category->title;
			}

			$title = JText::sprintf('JPAGETITLE', $title, htmlspecialchars_decode($app->get('sitename')));
		}

	/*	if (isset($this->category->title) && $this->category->title != '') {
			$title = $title .' - ' .  $this->category->title;
		} */

		/*if ($this->t['display_cat_name_title'] == 1 && isset($this->category->title) && $this->category->title != '') {
			$title = $title .' - ' .  $this->category->title;
		}*/

		$this->document->setTitle($title);

		if (isset($this->category->metadesc) && $this->category->metadesc != '') {
			$this->document->setDescription($this->category->metadesc);
		} else if ($this->t['gallerymetadesc'] != '') {
			$this->document->setDescription($this->t['gallerymetadesc']);
		} else if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		}

		if (isset($this->category->metadesc) && $this->category->metakey != '') {
			$this->document->setMetadata('keywords', $this->category->metakey);
		} else if ($this->t['gallerymetakey'] != '') {
			$this->document->setMetadata('keywords', $this->t['gallerymetakey']);
		} else if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->get('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}
		// Features added by Bernard Gilly - alphaplug.com
		// load external plugins
		/*$user       = JFactory::getUser();
		$catid      = $this->category->id;
		$db	   = JFactory::getDBO();
		$query = "SELECT owner_id FROM #__phocagallery_categories WHERE `id`='$catid'";
		$db->setQuery( $query );
		$ownerid = $db->loadResult();
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('phocagallery');
		$results = \JFactory::getApplication()->triggerEvent('onViewCategory', array($catid, $ownerid, $user->id) );
		*/

		if (isset($this->category->id) && isset($this->category->owner_id)) {
			$user       = JFactory::getUser();
			//$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('phocagallery');
			$results = \JFactory::getApplication()->triggerEvent('onViewCategory', array((int)$this->category->id, (int)$this->category->owner_id, (int)$user->id) );
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

	}

	/**
	 * Method to add Breadcrubms in Phoca Gallery
	 * @param array $this->category Object array of Category
	 * @param int $rootId Id of Root Category
	 * @param int $displayStyle Displaying of Breadcrubm - Nothing, Category Name, Menu link with Name
	 * @return string Breadcrumbs
	 */
	function _addBreadCrumbs($rootId, $displayStyle)
	{
	    $app = JFactory::getApplication();
		$i = 0;
		$category = $this->category;
	    while (isset($category->id))
	    {
			$crumbList[$i++] = $category;
			if ($category->id == $rootId)
			{
				break;
			}

	        $db = JFactory::getDBO();
	        $query = 'SELECT *' .
	            ' FROM #__phocagallery_categories AS c' .
	            ' WHERE c.id = '.(int) $category->parent_id.
	            ' AND c.published = 1';
	        $db->setQuery($query);
	        $rows = $db->loadObjectList('id');
			if (!empty($rows))
			{
				$category = $rows[$category->parent_id];
			}
			else
			{
				$category = '';
			}
		//	$category = $rows[$category->parent_id];
	    }

	    $pathway 		= $app->getPathway();
		$pathWayItems 	= $pathway->getPathWay();
		$lastItemIndex 	= count($pathWayItems) - 1;

	    for ($i--; $i >= 0; $i--)
	    {
			// special handling of the root category
			if ($crumbList[$i]->id == $rootId)
			{
				switch ($displayStyle)
				{
					case 0:	// 0 - only menu link
						// do nothing
						break;
					case 1:	// 1 - menu link with category name
						// replace the last item in the breadcrumb (menu link title) with the current value plus the category title
						$pathway->setItemName($lastItemIndex, $pathWayItems[$lastItemIndex]->name . ' - ' . $crumbList[$i]->title);
						break;
					case 2:	// 2 - only category name
						// replace the last item in the breadcrumb (menu link title) with the category title
						$pathway->setItemName($lastItemIndex, $crumbList[$i]->title);
						break;
				}
			}
			else
			{
				$pathway->addItem($crumbList[$i]->title, JRoute::_('index.php?option=com_phocagallery&view=category&id='. $crumbList[$i]->id.':'.$crumbList[$i]->alias.'&Itemid='. $this->itemId ));
			}
	    }
	}
}
?>
