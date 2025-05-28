<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Filesystem\File;
use Joomla\CMS\Session\Session;
jimport( 'joomla.client.helper' );
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pane' );
phocagalleryimport('phocagallery.file.fileupload');
phocagalleryimport( 'phocagallery.file.fileuploadmultiple' );
phocagalleryimport( 'phocagallery.file.fileuploadsingle' );
phocagalleryimport( 'phocagallery.file.fileuploadjava' );
phocagalleryimport('phocagallery.avatar.avatar');
phocagalleryimport('phocagallery.render.renderadmin');
phocagalleryimport('phocagallery.html.category');
//phocagalleryimport('phocagallery.pagination.paginationuser');
use Joomla\String\StringHelper;

class PhocaGalleryViewUser extends HtmlView
{
	protected $_context_subcat		= 'com_phocagallery.phocagalleryusersubcat';
	protected $_context_image			= 'com_phocagallery.phocagalleryuserimage';
	protected $t;

	function display($tpl = null) {

		$uri        		= Uri::getInstance();
		$app				= Factory::getApplication();
		$document			= Factory::getDocument();
		$menus				= $app->getMenu();
		$menu				= $menus->getActive();
		$this->params		= $app->getParams();
		$user 				= Factory::getUser();
		$path				= PhocaGalleryPath::getPath();
		$this->itemId			= $app->input->get('Itemid', 0, 'int');

		$neededAccessLevels	= PhocaGalleryAccess::getNeededAccessLevels();
		$access				= PhocaGalleryAccess::isAccess($user->getAuthorisedViewLevels(), $neededAccessLevels);



		$this->t['pi']		= 'media/com_phocagallery/images/';
		$this->t['pp']		= 'index.php?option=com_phocagallery&view=user&controller=user';
		$this->t['pl']		= 'index.php?option=com_users&view=login&return='.base64_encode($this->t['pp'].'&Itemid='. $this->itemId);
		// LIBRARY
		$library 							= PhocaGalleryLibrary::getLibrary();
		//$libraries['pg-css-ie'] 			= $library->getLibrary('pg-css-ie');

		// Only registered users
		if (!$access) {
			$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_NOT_AUTHORISED_ACTION'), 'error');
			$app->redirect(Route::_($this->t['pl'], false));
			exit;
		}

		$this->t['gallerymetakey'] 		= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 		= $this->params->get( 'gallery_metadesc', '' );
		if ($this->t['gallerymetakey'] != '') {
			$document->setMetaData('keywords', $this->t['gallerymetakey']);
		}
		if ($this->t['gallerymetadesc'] != '') {
			$document->setMetaData('description', $this->t['gallerymetadesc']);
		}

		PhocaGalleryRenderFront::renderAllCSS();

		// Custom order
		// administrator\components\com_phocagallery\libraries\phocagallery\html\grid.php replaces
		// libraries\cms\html\grid.php (libraries\joomla\grid\grid.php) and the javascript:
		// media\system\js\core-uncompressed.js (core.js)
		PhocaGalleryGrid::renderSortJs();



		// = = = = = = = = = = =
		// PANE
		// = = = = = = = = = = =
		// - - - - - - - - - -
		// ALL TABS
		// - - - - - - - - - -
		// UCP is disabled (security reasons)

		if ((int)$this->params->get( 'enable_user_cp', 0 ) == 0) {
			$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_UCP_DISABLED'), 'error');
			$app->redirect(Uri::base(true));
			exit;
		}

		$this->t['tab'] 					= $app->input->get('tab', 0, 'string');

		$this->t['maxuploadchar']		= $this->params->get( 'max_upload_char', 1000 );
		$this->t['maxcreatecatchar']		= $this->params->get( 'max_create_cat_char', 1000 );
		$this->t['showpageheading'] 		= $this->params->get( 'show_page_heading', 1 );
		$this->t['javaboxwidth'] 		= $this->params->get( 'java_box_width', 480 );
		$this->t['javaboxheight'] 		= $this->params->get( 'java_box_height', 480 );
		$this->t['enableuploadavatar'] 	= $this->params->get( 'enable_upload_avatar', 1 );
		$this->t['uploadmaxsize'] 		= $this->params->get( 'upload_maxsize', 3145728 );
		$this->t['uploadmaxsizeread'] 	= PhocaGalleryFile::getFileSizeReadable($this->t['uploadmaxsize']);
		$this->t['uploadmaxreswidth'] 	= $this->params->get( 'upload_maxres_width', 3072 );
		$this->t['uploadmaxresheight'] 	= $this->params->get( 'upload_maxres_height', 2304 );
		$this->t['multipleuploadchunk']	= $this->params->get( 'multiple_upload_chunk', 0 );
		$this->t['displaytitleupload']	= $this->params->get( 'display_title_upload', 0 );
		$this->t['displaydescupload'] 	= $this->params->get( 'display_description_upload', 0 );
		$this->t['enablejava'] 			= $this->params->get( 'enable_java', -1);
		$this->t['enablemultiple'] 		= $this->params->get( 'enable_multiple', 0 );
		$this->t['ytbupload'] 			= $this->params->get( 'youtube_upload', 0 );
		$this->t['multipleuploadmethod'] = $this->params->get( 'multiple_upload_method', 4 );
		$this->t['multipleresizewidth'] 	= $this->params->get( 'multiple_resize_width', -1 );
		$this->t['multipleresizeheight'] = $this->params->get( 'multiple_resize_height', -1 );
		$this->t['usersubcatcount']		= $this->params->get( 'user_subcat_count', 5 );
		$this->t['userimagesmaxspace']	= $this->params->get( 'user_images_max_size', 20971520 );

		$this->t['iepx']				= '<div style="font-size:1px;height:1px;margin:0px;padding:0px;">&nbsp;</div>';

		//Subcateogry
		$this->t['parentid']			= $app->input->get('parentcategoryid', 0, 'int');

		//$document->addScript(JUri::base(true).'/media/com_phocagallery/js/comments.js');
		$document->addCustomTag(PhocaGalleryRenderFront::renderOnUploadJS());
		$document->addCustomTag(PhocaGalleryRenderFront::renderDescriptionCreateCatJS((int)$this->t['maxcreatecatchar']));
		$document->addCustomTag(PhocaGalleryRenderFront::userTabOrdering());// SubCategory + Image
		$document->addCustomTag(PhocaGalleryRenderFront::renderDescriptionCreateSubCatJS((int)$this->t['maxcreatecatchar']));
		$document->addCustomTag(PhocaGalleryRenderFront::saveOrderUserJS());

		$model 						= $this->getModel('user');
		$ownerMainCategory			= $model->getOwnerMainCategory($user->id);


		$this->t['usertab'] 				= 1;
		$this->t['createcategory'] 		= 1;
		$this->t['createsubcategory'] 	= 1;
		$this->t['images'] 				= 1;
		$this->t['displayupload'] 		= 1;


		// Tabs
		$displayTabs	= 0;

		if ((int)$this->t['usertab'] == 0) {
			$currentTab['user'] = -1;
		} else {
			$currentTab['user'] = $displayTabs;
			$displayTabs++;
		}

		if ((int)$this->t['createcategory'] == 0) {
			$currentTab['createcategory'] = -1;
		} else {
			$currentTab['createcategory'] = $displayTabs;
			$displayTabs++;
		}

		if ((int)$this->t['createsubcategory'] == 0) {
			$currentTab['createsubcategory'] = -1;
		} else {
			$currentTab['createsubcategory'] = $displayTabs;
			$displayTabs++;
		}


		if ((int)$this->t['displayupload'] == 0) {
			$currentTab['images'] = -1;
		}else {
			$currentTab['images'] = $displayTabs;
			$displayTabs++;
		}

		$this->t['displaytabs']	= $displayTabs;
		$this->t['currenttab']	= $currentTab;


		// ACTION
		$this->t['action']	= $uri->toString();
		$this->t['ftp'] 		= !ClientHelper::hasCredentials('ftp');
		$sess = Factory::getSession();
		$this->session = $sess;


		// SEF problem
		$isThereQM = false;
		$isThereQM = preg_match("/\?/i", $this->t['action']);
		if ($isThereQM) {
			$amp = '&';// will be translated to htmlspecialchars
		} else {
			$amp = '?';
		}

		$this->t['actionamp']	=	$this->t['action'] . $amp;
		$this->t['istheretab'] = false;
		$this->t['istheretab'] = preg_match("/tab=/i", $this->t['action']);





		// EDIT - subcategory, image
		$this->t['task'] 		= $app->input->get( 'task', '', 'string');
		$id 						= $app->input->get( 'id', '', 'string');
		$idAlias					= $id;


		// - - - - - - - - - - -
		// USER (AVATAR)
		// - - - - - - - - - - -

		$this->t['user'] 				= $user->name;
		$this->t['username']			= $user->username;
		$this->t['useravatarimg']		= HTMLHelper::_('image', $this->t['pi'].'phoca_thumb_m_no_image.png', '');
		$this->t['useravatarapproved'] = 0;
		$userAvatar					= $model->getUserAvatar($user->id);

		if ($userAvatar) {
			$pathAvatarAbs	= $path->avatar_abs  .'thumbs/phoca_thumb_m_'. $userAvatar->avatar;
			$pathAvatarRel	= $path->avatar_rel . 'thumbs/phoca_thumb_m_'. $userAvatar->avatar;
			if (PhocaGalleryFile::exists($pathAvatarAbs)){
				$this->t['useravatarimg']	= '<img src="'.Uri::base(true) . '/' . $pathAvatarRel.'?imagesid='.md5(uniqid(time())).'" alt="" />';
				$this->t['useravatarapproved']	= 	$userAvatar->approved;
			}
		}

		if ($ownerMainCategory) {
			$this->t['usermaincategory'] =  $ownerMainCategory->title;
		} else {
			$this->t['usermaincategory'] =  '<svg class="ph-si ph-si-disabled"><title>'.Text::_('COM_PHOCAGALLERY_NOT_CREATED').'</title><use xlink:href="#ph-si-disabled"></use></svg>'
			.' ('.Text::_('COM_PHOCAGALLERY_NOT_CREATED').')';
		}
		$this->t['usersubcategory'] 		= $model->getCountUserSubCat($user->id);
		$this->t['usersubcategoryleft']	= (int)$this->t['usersubcatcount'] - (int)$this->t['usersubcategory'];
		if ((int)$this->t['usersubcategoryleft'] < 0) {$this->t['usersubcategoryleft'] = 0;}
		$this->t['userimages']				= $model->getCountUserImage($user->id);
		$this->t['userimagesspace']		= $model->getSumUserImage($user->id);
		$this->t['userimagesspaceleft']	= (int)$this->t['userimagesmaxspace'] - (int)$this->t['userimagesspace'];
		if ((int)$this->t['userimagesspaceleft'] < 0) {$this->t['userimagesspaceleft'] = 0;}
		$this->t['userimagesspace']		= PhocaGalleryFile::getFileSizeReadable($this->t['userimagesspace']);
		$this->t['userimagesspaceleft']	= PhocaGalleryFile::getFileSizeReadable($this->t['userimagesspaceleft']);
		$this->t['userimagesmaxspace']		= PhocaGalleryFile::getFileSizeReadable($this->t['userimagesmaxspace']);


		// - - - - - - - - - - -
		// MAIN CATEGORY
		// - - - - - - - - - - -
		$ownerMainCategory 	= $model->getOwnerMainCategory($user->id);
		if (!empty($ownerMainCategory->id)) {
			if ((int)$ownerMainCategory->published == 1) {
				$this->t['categorycreateoredithead']	= Text::_('COM_PHOCAGALLERY_MAIN_CATEGORY');
				$this->t['categorycreateoredit']		= Text::_('COM_PHOCAGALLERY_EDIT');
				$this->t['categorytitle']				= $ownerMainCategory->title;
				$this->t['categoryapproved']			= $ownerMainCategory->approved;
				$this->t['categorydescription']		= $ownerMainCategory->description;
				$this->t['categorypublished']			= 1;
			} else {
				$this->t['categorypublished']			= 0;
			}
		} else {
			$this->t['categorycreateoredithead']	= Text::_('COM_PHOCAGALLERY_MAIN_CATEGORY');
			$this->t['categorycreateoredit']		= Text::_('COM_PHOCAGALLERY_CREATE');
			$this->t['categorytitle']				= '';
			$this->t['categorydescription']		= '';
			$this->t['categoryapproved']			= '';
			$this->t['categorypublished']			= -1;
		}


		// - - - - - - - - - - -
		// SUBCATEGORY
		// - - - - - - - - - - -

		$lists_subcat = array();
		if (!empty($ownerMainCategory->id)) {

			// EDIT
			$this->t['categorysubcatedit'] = $model->getCategory((int)$id, $user->id);
			$this->t['displaysubcategory'] = 1;

			// Get All Data - Subcategories
			$this->t['subcategoryitems'] 		= $model->getDataSubcat($user->id);
			$this->t['subcategorytotal'] 		= count($this->t['subcategoryitems']);
			$model->setTotalSubCat($this->t['subcategorytotal']);
			$this->t['subcategorypagination'] 	= $model->getPaginationSubCat($user->id);
			$this->t['subcategoryitems'] 		= array_slice($this->t['subcategoryitems'],(int)$this->t['subcategorypagination']->limitstart, (int)$this->t['subcategorypagination']->limit);

			$filter_published_subcat	= $app->getUserStateFromRequest( $this->_context_subcat.'.filter_published',	'filter_published_subcat', '',	'word' );
			$filter_catid_subcat	= $app->getUserStateFromRequest( $this->_context_subcat.'.filter_catid',	'filter_catid_subcat',	0, 'int' );

			$filter_order_subcat	= $app->getUserStateFromRequest( $this->_context_subcat.'.filter_order',	'filter_order_subcat',	'a.ordering', 'cmd' );
			$filter_order_Dir_subcat= $app->getUserStateFromRequest( $this->_context_subcat.'.filter_order_Dir',	'filter_order_Dir_subcat',	'',	'word' );
			$search_subcat			= $app->getUserStateFromRequest( $this->_context_subcat.'.search', 'phocagallerysubcatsearch', '',	'string' );
			if (strpos($search_subcat, '"') !== false) {
				$search_subcat = str_replace(array('=', '<'), '', $search_subcat);
			}
			$search_subcat			= StringHelper::strtolower( $search_subcat );

			$categories 				= $model->getCategoryList($user->id);


			if (!empty($categories)) {
				$javascript 	= 'class="form-select" onchange="document.phocagallerysubcatform.submit();"';
				$tree = array();
				$text = '';
				$tree = PhocaGalleryCategoryhtml::CategoryTreeOption($categories, $tree,0, $text, -1);

				array_unshift($tree, HTMLHelper::_('select.option', '0', '- '.Text::_('COM_PHOCAGALLERY_SELECT_CATEGORY').' -', 'value', 'text'));
				$lists_subcat['catid'] = HTMLHelper::_( 'select.genericlist', $tree, 'filter_catid_subcat',  $javascript , 'value', 'text', $filter_catid_subcat );
			}

			$this->t['parentcategoryid']	= $filter_catid_subcat;

			// state filter
			//$lists['state']		= JHtml::_('grid.state',  $filter_published );
			$state_subcat[] 		= HTMLHelper::_('select.option',  '', '- '. Text::_( 'COM_PHOCAGALLERY_SELECT_STATE' ) .' -' );
			$state_subcat[] 		= HTMLHelper::_('select.option',  'P', Text::_( 'COM_PHOCAGALLERY_PUBLISHED' ) );
			$state_subcat[] 		= HTMLHelper::_('select.option',  'U', Text::_( 'COM_PHOCAGALLERY_UNPUBLISHED') );
			$lists_subcat['state']	= HTMLHelper::_('select.genericlist',   $state_subcat, 'filter_published_subcat', 'class="form-select" size="1" onchange="document.phocagallerysubcatform.submit();"', 'value', 'text', $filter_published_subcat );

			// table ordering
			$lists_subcat['order_Dir'] 	= $filter_order_Dir_subcat;
			$lists_subcat['order'] 		= $filter_order_subcat;

			$this->t['subcategoryordering'] = ($lists_subcat['order'] == 'a.ordering');//Ordering allowed ?

			// search filter
			$lists_subcat['search']		= $search_subcat;
		} else {
			$this->t['displaysubcategory'] = 0;
		}

		// - - - - - - - - - - -
		// IMAGES
		// - - - - - - - - - - -
		$lists_image = array();
		if (!empty($ownerMainCategory->id)) {
			$catAccess		= PhocaGalleryAccess::getCategoryAccess((int)$ownerMainCategory->id);

			// EDIT
			$this->t['imageedit'] 			= $model->getImage((int)$id, $user->id);

			$this->t['imageitems'] 		= $model->getDataImage($user->id);
			$this->t['imagetotal'] 		= $model->getTotalImage($user->id);
			$this->t['imagepagination'] 	= $model->getPaginationImage($user->id);

			$filter_published_image	= $app->getUserStateFromRequest( $this->_context_image.'.filter_published',	'filter_published_image', '',	'word' );
			$filter_catid_image	= $app->getUserStateFromRequest( $this->_context_image.'.filter_catid',	'filter_catid_image',	0, 'int' );
			$filter_order_image	= $app->getUserStateFromRequest( $this->_context_image.'.filter_order',	'filter_order_image',	'a.ordering', 'cmd' );
			$filter_order_Dir_image= $app->getUserStateFromRequest( $this->_context_image.'.filter_order_Dir',	'filter_order_Dir_image',	'',	'word' );
			$search_image			= $app->getUserStateFromRequest( $this->_context_image.'.search', 'phocagalleryimagesearch', '',	'string' );
			if (strpos($search_image, '"') !== false) {
				$search_image = str_replace(array('=', '<'), '', $search_image);
			}
			$search_image			= StringHelper::strtolower( $search_image );

			$categoriesImage 		= $model->getCategoryList($user->id);
			if (!empty($categoriesImage)) {
				//$javascript     = 'class="form-control" size="1" onchange="document.phocagalleryimageform.submit();"';
$javascript     = 'class="form-select" size="1" onchange="document.getElementById(\'phocagalleryimageform\').submit();"';
				$tree = array();
				$text = '';
				$tree = PhocaGalleryCategoryhtml::CategoryTreeOption($categoriesImage, $tree,0, $text, -1);

				array_unshift($tree, HTMLHelper::_('select.option', '0', '- '.Text::_('COM_PHOCAGALLERY_SELECT_CATEGORY').' -', 'value', 'text'));
				$lists_image['catid'] = HTMLHelper::_( 'select.genericlist', $tree, 'filter_catid_image',  $javascript , 'value', 'text', $filter_catid_image );
			}

			// state filter
			$state_image[] 		= HTMLHelper::_('select.option',  '', '- '. Text::_( 'COM_PHOCAGALLERY_SELECT_STATE' ) .' -' );
			$state_image[] 		= HTMLHelper::_('select.option', 'P', Text::_( 'COM_PHOCAGALLERY_FIELD_PUBLISHED_LABEL' ) );
			$state_image[] 		= HTMLHelper::_('select.option', 'U', Text::_( 'COM_PHOCAGALLERY_FIELD_UNPUBLISHED_LABEL') );
			$lists_image['state']	= HTMLHelper::_('select.genericlist',   $state_image, 'filter_published_image', 'class="form-select" size="1" onchange="document.getElementById(\'phocagalleryimageform\').submit();"', 'value', 'text', $filter_published_image );

			// table ordering
			$lists_image['order_Dir'] 	= $filter_order_Dir_image;
			$lists_image['order'] 		= $filter_order_image;

			$this->t['imageordering']		= ($lists_image['order'] == 'a.ordering');//Ordering allowed ?

			// search filter
			$lists_image['search']		= $search_image;
			$this->t['catidimage']			= $filter_catid_image;

			// Upload
			$this->t['displayupload']	= 0;
			// USER RIGHT - UPLOAD - - - - - - - - - - -
			// 2, 2 means that user access will be ignored in function getUserRight for display Delete button
			$rightDisplayUpload = 0;// default is to null (all users cannot upload)
			if (!empty($catAccess)) {
				$rightDisplayUpload = PhocaGalleryAccess::getUserRight('uploaduserid', $catAccess->uploaduserid, 2, $user->getAuthorisedViewLevels(), $user->get('id', 0), 0);
			}
			if ($rightDisplayUpload == 1) {
				$this->t['displayupload']	= 1;
				$document->addCustomTag(PhocaGalleryRenderFront::renderDescriptionUploadJS((int)$this->t['maxuploadchar']));
			}
			// - - - - - - - - - - - - - - - - - - - - -

			// USER RIGHT - ACCESS - - - - - - - - - - -
			$rightDisplay = 1;//default is set to 1 (all users can see the category)
			if (!empty($catAccess)) {

				$rightDisplay = PhocaGalleryAccess::getUserRight ('accessuserid', $catAccess->accessuserid, $catAccess->access, $user->getAuthorisedViewLevels(), $user->get('id', 0), 1);
			}
			if ($rightDisplay == 0) {
				$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_NOT_AUTHORISED_ACTION'), 'error');
				$app->redirect(Route::_($this->t['pl'], false));
				exit;
			}
			// - - - - - - - - - - - - - - - - - - - - -

			// = = = = = = = = = =
			// U P L O A D
			// = = = = = = = = = =


			// - - - - - - - - - - -
			// Upload
			// - - - - - - - - - - -
			if ((int)$this->t['displayupload'] == 1) {
				$sU							= new PhocaGalleryFileUploadSingle();
				$sU->returnUrl				= Route::_($this->t['action'] . $amp .'task=upload&'. $this->session->getName().'='.$this->session->getId()
											.'&'. Session::getFormToken().'=1&viewback=category&tab='.$this->t['currenttab']['images']);
				$sU->tab					= $this->t['currenttab']['images'];
				$this->t['su_output']	= $sU->getSingleUploadHTML(1);
				$this->t['su_url']		= Route::_($this->t['action'] . $amp .'task=upload&'. $this->session->getName().'='.$this->session->getId()
											.'&'. Session::getFormToken().'=1&viewback=category&tab='.$this->t['currenttab']['images']);
			}

			// - - - - - - - - - - -
			// Youtube Upload (single upload form can be used)
			// - - - - - - - - - - -
			if ((int)$this->t['ytbupload'] > 0) {
				$sYU						= new PhocaGalleryFileUploadSingle();
				$sYU->returnUrl				= Route::_($this->t['action'] . $amp .'task=ytbupload&'. $this->session->getName().'='.$this->session->getId()
											.'&'. Session::getFormToken().'=1&viewback=category&tab='.$this->t['currenttab']['images']);
				$sYU->tab					= $this->t['currenttab']['images'];
				$this->t['syu_output']	= $sYU->getSingleUploadHTML(1);
				$this->t['syu_url']		= Route::_($this->t['action'] . $amp .'task=ytbupload&'. $this->session->getName().'='.$this->session->getId()
											.'&'. Session::getFormToken().'=1&viewback=category&tab='.$this->t['currenttab']['images']);
			}


			// - - - - - - - - - - -
			// Multiple Upload
			// - - - - - - - - - - -
			// Get infos from multiple upload
			$muFailed						= $app->input->get( 'mufailed', '0', 'int' );
			$muUploaded						= $app->input->get( 'muuploaded', '0', 'int' );
			$this->t['mu_response_msg']	= $muUploadedMsg 	= '';

			if ($muUploaded > 0) {
				$muUploadedMsg = Text::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded;
			}
			if ($muFailed > 0) {
				$muFailedMsg = Text::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed;
			}
			if ($muFailed > 0 && $muUploaded > 0) {
				$this->t['mu_response_msg'] = '<div class="alert alert-info">'
				.Text::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded .'<br />'
				.Text::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed.'</div>';
			} else if ($muFailed > 0 && $muUploaded == 0) {
				$this->t['mu_response_msg'] = '<div class="alert alert-error alert-danger">'
				.Text::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed.'</div>';
			} else if ($muFailed == 0 && $muUploaded > 0){
				$this->t['mu_response_msg'] = '<div class="alert alert-success">'
				.Text::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded.'</div>';
			} else {
				$this->t['mu_response_msg'] = '';
			}

			if((int)$this->t['enablemultiple']  == 1 && (int)$this->t['displayupload'] == 1) {

				PhocaGalleryFileUploadMultiple::renderMultipleUploadLibraries();
				$mU						= new PhocaGalleryFileUploadMultiple();
				$mU->frontEnd			= 2;
				$mU->method				= $this->t['multipleuploadmethod'];
				$mU->url				= Route::_($this->t['action'] . $amp .'controller=user&task=multipleupload&'
										 . $this->session->getName().'='.$this->session->getId().'&'
										 . Session::getFormToken().'=1&tab='.$this->t['currenttab']['images']
										 . '&catid='.$this->t['catidimage']);
				$mU->reload				= Route::_($this->t['action'] . $amp
										. $this->session->getName().'='.$this->session->getId().'&'
										. Session::getFormToken().'=1&tab='.$this->t['currenttab']['images']);
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
				$jU->returnUrl				= Route::_($this->t['action'] . $amp
											. $this->session->getName().'='.$this->session->getId().'&'
											. Session::getFormToken().'=1&tab='.$this->t['currenttab']['images']);
				$jU->url					= Route::_($this->t['action'] . $amp .'controller=user&task=javaupload&'
											. $this->session->getName().'='.$this->session->getId().'&'
											. Session::getFormToken().'=1&tab='.$this->t['currenttab']['images']
											. '&catid='.$this->t['catidimage']);
				$jU->source 				= Uri::root(true).'/media/com_phocagallery/js/jupload/wjhk.jupload.jar';
				$this->t['ju_output']	= $jU->getJavaUploadHTML();

			}

		} else {
			$this->t['displayupload'] = 0;
		}

		if (!empty($ownerMainCategory->id)) {
			$this->t['ps']	= '&tab='. $this->t['currenttab']['createsubcategory']
					. '&limitstartsubcat='.$this->t['subcategorypagination']->limitstart
					. '&limitstartimage='.$this->t['imagepagination']->limitstart;
		} else {
			$this->t['ps']	= '&tab='. $this->t['currenttab']['createsubcategory'];
		}

		if (!empty($ownerMainCategory->id)) {
			$this->t['psi']	= '&tab='. $this->t['currenttab']['images']
					. '&limitstartsubcat='.$this->t['subcategorypagination']->limitstart
					. '&limitstartimage='.$this->t['imagepagination']->limitstart;
		} else {
			$this->t['psi']	= '&tab='. $this->t['currenttab']['images'];
		}

		// ASIGN
		$this->listssubcat =	$lists_subcat;
		$this->listsimage =		$lists_image;
		//$this->assignRef( 'tmpl', $this->t);
		//$this->assignRef( 'params', $this->params);
		$sess = Factory::getSession();
		$this->session = $sess;
		$this->_prepareDocument();
		parent::display($tpl);
	}

	protected function _prepareDocument() {

		$app		= Factory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		$this->params	= $app->getParams();
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
		} else if ($app->get('sitename_pagetitles', 0) == 2) {
			$title = Text::sprintf('JPAGETITLE', $title, htmlspecialchars_decode($app->get('sitename')));
		}

		$this->document->setTitle($title);

		if ($this->t['gallerymetadesc'] != '') {
			$this->document->setDescription($this->t['gallerymetadesc']);
		} else if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		}

		if ($this->t['gallerymetakey'] != '') {
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
	}
}
?>
