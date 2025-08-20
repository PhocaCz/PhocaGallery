<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
jimport( 'joomla.application.component.view');
jimport( 'joomla.filesystem.file' );
phocagalleryimport('phocagallery.access.access');
phocagalleryimport('phocagallery.path.path');
phocagalleryimport('phocagallery.file.file');
phocagalleryimport('phocagallery.render.renderinfo');
phocagalleryimport('phocagallery.picasa.picasa');
phocagalleryimport('phocagallery.image.imagefront');
phocagalleryimport('phocagallery.ordering.ordering');
phocagalleryimport('phocagallery.render.rendermaposm');

class PhocaGalleryViewCategories extends HtmlView
{
	public 		$t;
	protected 	$params;
	public 		$cv;

	public function display($tpl = null) {

		$app 						= Factory::getApplication();
		$user 						= Factory::getUser();
		$uri 						= \Joomla\CMS\Uri\Uri::getInstance();
		$path						= PhocaGalleryPath::getPath();
		$this->params				= $app->getParams();
		$this->tGeo					= array();
		$this->t					= array();
		$this->itemId				= $app->getInput()->get('Itemid', 0, 'int');
		$document					= Factory::getDocument();
		$library 					= PhocaGalleryLibrary::getLibrary();
		$this->t['action']			= $uri->toString();

		// CSS
		PhocaGalleryRenderFront::renderAllCSS();

		// Params
		$this->t['display_name']				= 1;//$this->params->get( 'display_name', 1);
		$this->t['image_categories_size']		= $this->params->get( 'image_categories_size', 1);
		$display_categories_geotagging 			= $this->params->get( 'display_categories_geotagging', 0 );
		$display_access_category 				= $this->params->get( 'display_access_category', 1 );
		$display_empty_categories				= $this->params->get( 'display_empty_categories', 0 );
		$hideCatArray							= explode( ',', trim( $this->params->get( 'hide_categories', '' ) ) );
		$showCatArray    						= explode( ',', trim( $this->params->get( 'show_categories', '' ) ) );
		$showParentCatArray    					= explode( ',', trim( $this->params->get( 'show_parent_categories', '' ) ) );
		$this->t['categoriesimageordering']		= $this->params->get( 'categories_image_ordering', 10 );
		$this->t['categoriesdisplayavatar']		= $this->params->get( 'categories_display_avatar');
		$this->t['categories_description'] 		= $this->params->get( 'categories_description', '' );
		$this->t['phocagallery_width']			= $this->params->get( 'phocagallery_width', '');
		$this->t['phocagallery_center']			= $this->params->get( 'phocagallery_center', 0);
		$this->t['display_rating']				= $this->params->get( 'display_rating', 0 );
		$this->t['categories_box_space']		= $this->params->get( 'categories_box_space', '');
		$this->t['display_cat_desc_box']		= $this->params->get( 'display_cat_desc_box', 0);
		//$this->t['char_cat_length_name'] 		= $this->params->get( 'char_cat_length_name', 9);
		//$this->t['categories_mosaic_images'] 	= $this->params->get( 'categories_mosaic_images', 0);
		//$this->t['diff_thumb_height']			= $this->params->get( 'diff_thumb_height', 0 );
		$this->t['responsive']					= $this->params->get( 'responsive', 0 );
		$this->t['bootstrap_icons']				= $this->params->get( 'bootstrap_icons', 0 );
		$this->t['equal_heights']				= $this->params->get( 'equal_heights', 0 );
		$this->t['masonry_center']				= $this->params->get( 'masonry_center', 0 );
		$this->t['map_type']					= $this->params->get( 'map_type', 2 );
		$this->t['display_feed']				= $this->params->get('display_feed', 1);

		$this->t['medium_image_width']			= $this->params->get( 'medium_image_width', 256 );
		$this->t['medium_image_height'] 		= $this->params->get( 'medium_image_height', 192 );

		// L E G A C Y ===
		/*$this->t['equalpercentagewidth']		= $this->params->get( 'equal_percentage_width', 1);
		$this->t['categoriesboxwidth']		= $this->params->get( 'categories_box_width','33%');
		$this->t['categoriescolumns'] 		= $this->params->get( 'categories_columns', 1 );
		$this->t['displayrating']			= $this->params->get( 'display_rating', 0 );
		$this->t['display_image_categories']	= $this->params->get( 'display_image_categories', 1 );
		if ($this->t['display_image_categories'] == 1) {

		} else {
			// If legacy no different height, no mosaic
			$this->t['diff_thumb_height'] = 0;
			$this->t['categories_mosaic_images'] = 0;
		}*/

		// END L E G A C Y ===
		switch($this->t['image_categories_size']) {
			// medium
			case 1:
			case 3:
				$this->t['picasa_correct_width']		= (int)$this->params->get( 'medium_image_width', 256 );
				$this->t['picasa_correct_height']	= (int)$this->params->get( 'medium_image_height', 192 );
				$this->t['imagewidth']				= (int)$this->params->get( 'medium_image_width', 256 );
				$this->t['imageheight']				= (int)$this->params->get( 'medium_image_height', 192 );
				$this->t['class_suffix']				= 'medium';

				/*if ($this->t['categories_mosaic_images'] == 1) {
					$this->t['imagewidth']				= (int)$this->params->get( 'medium_image_width', 256 ) * 3;
					$this->t['imageheight']				= (int)$this->params->get( 'medium_image_height', 192 ) * 2;
				}*/
			break;

			// small
			case 0:
			case 2:
			default:
				$this->t['picasa_correct_width']		= (int)$this->params->get( 'small_image_width', 128 );
				$this->t['picasa_correct_height']	= (int)$this->params->get( 'small_image_height', 96 );
				$this->t['imagewidth']				= (int)$this->params->get( 'small_image_width', 128 );
				$this->t['imageheight'] 				= (int)$this->params->get( 'small_image_height', 96 );
				$this->t['class_suffix']				= 'small';

				/*if ($this->t['categories_mosaic_images'] == 1) {
					$this->t['imagewidth']				= (int)$this->params->get( 'small_image_width', 128 ) * 3;
					$this->t['imageheight']				= (int)$this->params->get( 'small_image_height', 96 ) * 2;
				}*/
			break;
		}


		$this->t['boxsize'] 		= PhocaGalleryImage::setBoxSize($this->t, 1);






		// Image next to Category in Categories View is ordered by Random as default
		$categoriesImageOrdering = PhocaGalleryOrdering::getOrderingString($this->t['categoriesimageordering']);

		// MODEL
		$model					= $this->getModel();
		$this->t['ordering']	= $model->getOrdering();
		$this->categories		= $this->get('data');


		// Add link and unset the categories which user cannot see (if it is enabled in params)
		// If it will be unset while access view, we must sort the keys from category array - ACCESS
		$unSet = 0;

		foreach ($this->categories as $key => $item) {

			// Unset empty categories if it is set
			if ($display_empty_categories == 0) {
				if($this->categories[$key]->numlinks < 1) {
					unset($this->categories[$key]);
					$unSet 		= 1;
					continue;
				}
			}

			// Set only selected category ID
			if (!empty($showCatArray[0]) && is_array($showCatArray)) {
				$unSetHCA = 0;

				foreach ($showCatArray as $valueHCA) {

					if((int)trim($valueHCA) == $this->categories[$key]->id) {
						$unSetHCA 	= 0;
						$unSet 		= 0;
						break;
					} else {
						$unSetHCA 	= 1;
						$unSet 		= 1;
                    }
                }
				if ($unSetHCA == 1) {
					unset($this->categories[$key]);
					continue;
				}
			}

			// Unset hidden category
			if (!empty($hideCatArray) && is_array($hideCatArray)) {
				$unSetHCA = 0;
				foreach ($hideCatArray as $valueHCA) {
					if((int)trim($valueHCA) == $this->categories[$key]->id) {
						unset($this->categories[$key]);
						$unSet 		= 1;
						$unSetHCA 	= 1;
						break;
					}
				}
				if ($unSetHCA == 1) {
					continue;
				}
			}

			// Unset not set parent categories - only categories which have specific parent id will be displayed
			if (!empty($showParentCatArray[0]) && is_array($showParentCatArray)) {
				$unSetPHCA = 0;

				foreach ($showParentCatArray as $valuePHCA) {

					if((int)trim($valuePHCA) == $this->categories[$key]->parent_id) {
						$unSetPHCA 	= 0;
						//$unSet  	= 0;
						break;
					} else {
						$unSetPHCA 	= 1;
						$unSet		= 1;
                    }
                }
				if ($unSetPHCA == 1) {
					unset($this->categories[$key]);
					continue;
				}
			}

			// Link
			$this->categories[$key]->link = PhocaGalleryRoute::getCategoryRoute($item->id, $item->alias);


			// USER RIGHT - ACCESS - - - - -
			// First Check - check if we can display category
			$rightDisplay	= 1;
			if (!empty($this->categories[$key])) {

				$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $this->categories[$key]->accessuserid, $this->categories[$key]->access, $user->getAuthorisedViewLevels(), $user->get('id', 0), $display_access_category);
			}
			// Second Check - if we can display hidden category, set Key icon for them
			//                if we don't have access right to see them
			// Display Key Icon (in case we want to display unaccessable categories in list view)
			$rightDisplayKey  = 1;

			if ($display_access_category == 1) {
				// we simulate that we want not to display unaccessable categories
				// so if we get rightDisplayKey = 0 then the key will be displayed
				if (!empty($this->categories[$key])) {
					$rightDisplayKey = PhocaGalleryAccess::getUserRight('accessuserid', $this->categories[$key]->accessuserid, $this->categories[$key]->access, $user->getAuthorisedViewLevels(), $user->get('id', 0), 0); // 0 - simulation
				}
			}

			// Is Ext Image Album?
			$extCategory = PhocaGalleryImage::isExtImage($this->categories[$key]->extid, $this->categories[$key]->extfbcatid);

			// DISPLAY AVATAR, IMAGE(ordered), IMAGE(not ordered, not recursive) OR FOLDER ICON
			$displayAvatar = 0;
			if($this->t['categoriesdisplayavatar'] == 1 && isset($this->categories[$key]->avatar) && $this->categories[$key]->avatar !='' && $this->categories[$key]->avatarapproved == 1 && $this->categories[$key]->avatarpublished == 1) {
				$sizeString = PhocaGalleryImageFront::getSizeString($this->t['image_categories_size']);
				$pathAvatarAbs	= $path->avatar_abs  .'thumbs/phoca_thumb_'.$sizeString.'_'. $this->categories[$key]->avatar;
				$pathAvatarRel	= $path->avatar_rel . 'thumbs/phoca_thumb_'.$sizeString.'_'. $this->categories[$key]->avatar;
				if (PhocaGalleryFile::exists($pathAvatarAbs)){

					$this->categories[$key]->linkthumbnailpath	=  $pathAvatarRel;
					$this->categories[$key]->rightdisplaykey				= $rightDisplayKey;
					$displayAvatar = 1;
				}
			}

			if ($displayAvatar == 0) {

				if ($extCategory) {


					$this->categories[$key]->rightdisplaykey				= $rightDisplayKey;
					if ($this->t['categoriesimageordering'] != 10) {
						$imagePic		= PhocaGalleryImageFront::getRandomImageRecursive($this->categories[$key]->id, $categoriesImageOrdering, 1);
						if ($rightDisplayKey == 0) {
							$imagePic = new StdClass();
							$imagePic->exts = '';
							$imagePic->extm = '';
							$imagePic->extw = '';
							$imagePic->exth = '';
						}
						$fileThumbnail	= PhocaGalleryImageFront::displayCategoriesExtImgOrFolder($imagePic->exts,$imagePic->extm, $imagePic->extw,$imagePic->exth, $this->t['image_categories_size'], $rightDisplayKey);

						if ($rightDisplayKey == 0) {
								$this->categories[$key]->rightdisplaykey = 0;// Lock folder will be displayed
								$this->categories[$key]->linkthumbnailpath = '';
							} else if (!$fileThumbnail) {
								$this->categories[$key]->linkthumbnailpath = '';// Standard folder will be displayed
							} else {
								$this->categories[$key]->linkthumbnailpath	= $fileThumbnail->rel;
								$this->categories[$key]->extw				= $fileThumbnail->extw;
								$this->categories[$key]->exth				= $fileThumbnail->exth;
								$this->categories[$key]->extpic				= $fileThumbnail->extpic;
							}

					} else {
						$fileThumbnail		= PhocaGalleryImageFront::displayCategoriesExtImgOrFolder($this->categories[$key]->exts,$this->categories[$key]->extm, $this->categories[$key]->extw, $this->categories[$key]->exth, $this->t['image_categories_size'], $rightDisplayKey);

						if ($rightDisplayKey == 0) {
								$this->categories[$key]->rightdisplaykey = 0;// Lock folder will be displayed
								$this->categories[$key]->linkthumbnailpath = '';
							} else if (!$fileThumbnail) {
								$this->categories[$key]->linkthumbnailpath = '';// Standard folder will be displayed
							} else {
								$this->categories[$key]->linkthumbnailpath	= $fileThumbnail->rel;
								$this->categories[$key]->extw				= $fileThumbnail->extw;
								$this->categories[$key]->exth				= $fileThumbnail->exth;
								$this->categories[$key]->extpic				= $fileThumbnail->extpic;
							}


					}




				} else {

					$this->categories[$key]->rightdisplaykey				= $rightDisplayKey;

					if (isset($item->image_id) && $item->image_id > 0) {
						// User has selected image in category edit
						$selectedImg = PhocaGalleryImageFront::setFileNameByImageId((int)$item->image_id);


						if (isset($selectedImg->filename) && ($selectedImg->filename != '' && $selectedImg->filename != '-')) {
							$fileThumbnail	= PhocaGalleryImageFront::displayCategoriesImageOrFolder($selectedImg->filename, $this->t['image_categories_size'], $rightDisplayKey);

							if ($rightDisplayKey == 0) {
								$this->categories[$key]->rightdisplaykey = 0;// Lock folder will be displayed
								$this->categories[$key]->linkthumbnailpath = '';
							} else if (!$fileThumbnail) {
								$this->categories[$key]->linkthumbnailpath = '';// Standard folder will be displayed
							} else {
								$this->categories[$key]->filename          = $selectedImg->filename;
								$this->categories[$key]->linkthumbnailpath = $fileThumbnail->rel;
							}


						} else if (isset($selectedImg->exts) && isset($selectedImg->extm) && $selectedImg->exts != '' && $selectedImg->extm != '') {
							$fileThumbnail		= PhocaGalleryImageFront::displayCategoriesExtImgOrFolder($selectedImg->exts, $selectedImg->extm, $selectedImg->extw, $selectedImg->exth, $this->t['image_categories_size'], $rightDisplayKey);



							if ($rightDisplayKey == 0) {
								$this->categories[$key]->rightdisplaykey = 0;// Lock folder will be displayed
								$this->categories[$key]->linkthumbnailpath = '';
							} else if (!$fileThumbnail) {
								$this->categories[$key]->linkthumbnailpath = '';// Standard folder will be displayed
							} else {
								$this->categories[$key]->linkthumbnailpath	= $fileThumbnail->rel;
								$this->categories[$key]->extw				= $fileThumbnail->extw;
								$this->categories[$key]->exth				= $fileThumbnail->exth;
								$this->categories[$key]->extpic				= $fileThumbnail->extpic;
							}

						}

					} else {
						// Standard Internal Image
						if ($this->t['categoriesimageordering'] != 10) {
							$this->categories[$key]->filename	= PhocaGalleryImageFront::getRandomImageRecursive($this->categories[$key]->id, $categoriesImageOrdering);
						}
						$fileThumbnail	= PhocaGalleryImageFront::displayCategoriesImageOrFolder($this->categories[$key]->filename, $this->t['image_categories_size'], $rightDisplayKey);

						if ($rightDisplayKey == 0) {
							$this->categories[$key]->rightdisplaykey = 0;// Lock folder will be displayed
							$this->categories[$key]->linkthumbnailpath = '';
						} else if (!$fileThumbnail) {
							$this->categories[$key]->linkthumbnailpath = '';// Standard folder will be displayed
						} else {
							$this->categories[$key]->linkthumbnailpath = $fileThumbnail->rel;
						}



					}


				}
			}

			if ($rightDisplay == 0) {
				unset($this->categories[$key]);
				$unSet = 1;
			}
			// - - - - - - - - - - - - - - -

		}

		// ACCESS - - - - - -
		// In case we unset some category from the list, we must sort the array new
		if ($unSet == 1) {
			$this->categories = array_values($this->categories);
		}
		// - - - - - - - - - - - - - - - -

		// Do Pagination - we can do it after reducing all unneeded $this->categories, not before
		$totalCount 				= count($this->categories);
		$model->setTotal($totalCount);
		$this->t['pagination']	= $this->get('pagination');
		$this->categories 			= array_slice($this->categories,(int)$this->t['pagination']->limitstart, (int)$this->t['pagination']->limit);
		// - - - - - - - - - - - - - - - -



		// L E G A C Y ===
	/*	$this->t['countcategories'] 	= count($this->categories);
		$this->t['begin']			= array();
		$this->t['end']				= array();
		$this->t['begin'][0]			= 0;// first
		// Prevent from division by zero error message
		if ((int)$this->t['categoriescolumns'] == 0) {
			$this->t['categoriescolumns'] = 1;
		}
		$this->t['begin'][1]			= ceil ($this->t['countcategories'] / (int)$this->t['categoriescolumns']);
		$this->t['end'][0]			= $this->t['begin'][1] -1;


		for ( $j = 2; $j < (int)$this->t['categoriescolumns']; $j++ ) {
			$this->t['begin'][$j]	= ceil(($this->t['countcategories'] / (int)$this->t['categoriescolumns']) * $j);
			$this->t['end'][$j-1]	= $this->t['begin'][$j] - 1;
		}
		$this->t['end'][$j-1]		= $this->t['countcategories'] - 1;// last
		$this->t['endfloat']			= $this->t['countcategories'] - 1;

		if($this->t['equalpercentagewidth'] == 1) {
			$fixedWidth						= 100 / (int)$this->t['categoriescolumns'];
			$this->t['fixedwidthstyle1']	= 'width:'.$fixedWidth.'%;';
			$this->t['fixedwidthstyle2']	= 'width:'.$fixedWidth.'%;';
		} else {
			$this->t['fixedwidthstyle1']	= '';//'margin: 10px;';
			$this->t['fixedwidthstyle2']	= '';//'margin: 0px;';
		}*/
		// END L E G A C Y ===








		$this->_prepareDocument();


		if ($display_categories_geotagging == 1) {

			// Params
			$this->tGeo['categorieslng'] 		= $this->params->get( 'categories_lng', '' );
			$this->tGeo['categorieslat'] 		= $this->params->get( 'categories_lat', '' );
			$this->tGeo['categorieszoom'] 		= $this->params->get( 'categories_zoom', 2 );
			$this->tGeo['googlemapsapikey'] 	= $this->params->get( 'google_maps_api_key', '' );
			$this->tGeo['categoriesmapwidth'] 	= $this->params->get( 'categories_map_width', '' );
			$this->tGeo['categoriesmapheight'] = $this->params->get( 'categorires_map_height', 500 );

			// If no lng and lat will be added, Phoca Gallery will try to find it in categories
			if ($this->tGeo['categorieslat'] == '' || $this->tGeo['categorieslng'] == '') {
				phocagalleryimport('phocagallery.geo.geo');
				$latLng = PhocaGalleryGeo::findLatLngFromCategory($this->categories);
				$this->tGeo['categorieslng'] = $latLng['lng'];
				$this->tGeo['categorieslat'] = $latLng['lat'];
			}
			$this->tmplGeo =	$this->tGeo;

			if ($this->t['map_type'] == 2) {
				parent::display('map_osm');
			} else {
				parent::display('map');
			}

		} else {
			parent::display($tpl);
		}
	}

	protected function _prepareDocument() {

		$app		= Factory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		$title 		= null;

		$this->t['gallerymetakey'] 		= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 		= $this->params->get( 'gallery_metadesc', '' );

		$menu = $menus->getActive();
		/*if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('JGLOBAL_ARTICLES'));
		}*/

		if ($menu && $this->params->get('display_menu_link_title', 1) == 1) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
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

		// Features added by Bernard Gilly - alphaplug.com
		// load external plugins
		//$dispatcher = JDispatcher::getInstance();
		PluginHelper::importPlugin('phocagallery');
		$results = $app->triggerEvent('onViewCategories', array() );
	}
}
?>
