<?php
/**
 * @package   Phoca Gallery
 * @author    Jan Pavelka - https://www.phoca.cz
 * @copyright Copyright (C) Jan Pavelka https://www.phoca.cz
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 and later
 * @cms       Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Object\CMSObject;
use Joomla\Filesystem\File;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

class PhocaGalleryImageFront
{
	/* OBSOLETE
	 * IMAGE BACKGROUND - CATEGORIES VIEW - INTERNAL IMAGE
	 * 0-small,1-medium,2-smallFolder,3-mediumFolder,4-smallShadow,5-mediumShadow,6-smallFolderShadow,7-mediumFolderShadow
	 */
	public static function getCategoriesImageBackground($imgCatSize, $smallImgHeigth, $smallImgWidth, $mediumImgHeight, $mediumImgWidth) {
	/*
		phocagalleryimport('phocagallery.image.image');
		phocagalleryimport('phocagallery.path.path');
		$path		= PhocaGalleryPath::getPath();
		$imgBg 		= new CMSObject();

		switch ($imgCatSize) {
			case 4:
			case 6:
				$imgBg->image = 'background: url(\''
				.$path->image_rel_front_full . 'shadow3.png'.'\') 50% 50% no-repeat;height:'
				.$smallImgHeigth.'px;width:'.$smallImgWidth.'px;';
				$imgBg->width = $smallImgWidth + 20;//Categories Detailed View
			break;

			case 5:
			case 7:
				$imgBg->image = 'background: url(\''
				.$path->image_rel_front_full . 'shadow1.png'.'\') 50% 50% no-repeat;height:'
				.$mediumImgHeight.'px;width:'.$mediumImgWidth.'px;';
				$imgBg->width = $mediumImgWidth + 20;//Categories Detailed View
			break;

			case 1:
			case 3:
				$imgBg->image 	= 'width:'.$mediumImgWidth.'px;';
				$imgBg->width	= $mediumImgWidth +20;//Categories Detailed View
			break;

			case 0:
			case 2:
			Default:
				$imgBg->image 	= 'width:'.$smallImgWidth.'px;';
				$imgBg->width	= $smallImgWidth + 20;//Categories Detailed View
			break;
		}
		return $imgBg;*/
	}

	/*
	 * IMAGE OR FOLDER - CATEGORIES VIEW - INTERNAL IMAGE
	 * 0-small,1-medium,2-smallFolder,3-mediumFolder,4-smallShadow,5-mediumShadow,6-smallFolderShadow,7-mediumFolderShadow
	 */
	public static function displayCategoriesImageOrFolder ($filename, $imgCategoriesSize, $rightDisplayKey = 0) {

		phocagalleryimport('phocagallery.image.image');
		phocagalleryimport('phocagallery.path.path');
		phocagalleryimport('phocagallery.file.filethumbnail');


		$path		= PhocaGalleryPath::getPath();

		// if category is not accessable, display the key in the image:
		$key = '';
		if ((int)$rightDisplayKey == 0) {
			//$key = '-key';
			return false;
		}
		$fileThumbnail = '';
		switch ($imgCategoriesSize) {
			// user wants to display only icon folder (parameters) medium
			case 3:
			case 7:
				return false;
				//$fileThumbnail 		= PhocaGalleryFileThumbnail::getThumbnailName($filename, 'medium');
			//$fileThumbnail->rel	= $path->image_rel_front . 'icon-folder-medium'.$key.'.png';
			break;
			// user wants to display only icon folder (parameters) small
			case 2:
			case 6:
				return false;
			//$fileThumbnail 		= PhocaGalleryFileThumbnail::getThumbnailName($filename, 'small');
			//$fileThumbnail->rel	= $path->image_rel_front . 'icon-folder-small-main'.$key.'.png';
			break;

			// standard medium image next to category in categories view - if the file doesn't exist, it will be displayed folder icon
			case 1:
			case 5:
			$fileThumbnail = PhocaGalleryFileThumbnail::getThumbnailName($filename, 'medium');
			if (!PhocaGalleryFile::exists($fileThumbnail->abs) || $rightDisplayKey == 0) {
				return false;
				//$fileThumbnail->rel	= $path->image_rel_front . 'icon-folder-medium'.$key.'.png';
			}
			break;

			// standard small image next to category in categories view - if the file doesn't exist, it will be displayed folder icon
			case 0:
			case 4:
			$fileThumbnail = PhocaGalleryFileThumbnail::getThumbnailName($filename, 'small');
			if (!PhocaGalleryFile::exists($fileThumbnail->abs) || $rightDisplayKey == 0) {
				return false;
				//$fileThumbnail->rel	= $path->image_rel_front . 'icon-folder-small-main'.$key.'.png';
			}
			break;
		}

		return $fileThumbnail;
	}

	/*
	 * IMAGE OR FOLDER - CATEGORIES VIEW - EXTERNAL IMAGE
	 * 0-small,1-medium,2-smallFolder,3-mediumFolder,4-smallShadow,5-mediumShadow,6-smallFolderShadow,7-mediumFolderShadow
	 */
	public static function displayCategoriesExtImgOrFolder ($exts, $extm, $extw, $exth, $imgCategoriesSize, $rightDisplayKey = 0) {

		phocagalleryimport('phocagallery.image.image');
		phocagalleryimport('phocagallery.path.path');
		phocagalleryimport('phocagallery.file.filethumbnail');
		$path		= PhocaGalleryPath::getPath();


		$fileThumbnail =  new CMSObject;
		$fileThumbnail->rel 	= '';
		$fileThumbnail->extw 	= '';
		$fileThumbnail->exth 	= '';
		$fileThumbnail->extpic 	= false;
		$extw = explode(',',$extw);
		$exth = explode(',',$exth);


		$paramsC 	= ComponentHelper::getParams('com_phocagallery');


		if (!isset($extw[0])) {$extw[0] = $paramsC->get( 'large_image_width', 640 );}
		if (!isset($extw[1])) {$extw[1] = $paramsC->get( 'medium_image_width', 256 );}
		if (!isset($extw[2])) {$extw[2] = $paramsC->get( 'small_image_width', 128 );}
		if (!isset($exth[0])) {$exth[0] = $paramsC->get( 'large_image_height', 480 );}
		if (!isset($exth[1])) {$exth[1] = $paramsC->get( 'medium_image_height', 192 );}
		if (!isset($exth[2])) {$exth[2] = $paramsC->get( 'small_image_height', 96 );}

		// if category is not accessable, display the key in the image:
		$key = '';
		if ((int)$rightDisplayKey == 0) {
			$key = '-key';
			return false;
		}

		switch ($imgCategoriesSize) {
			// user wants to display only icon folder (parameters) medium
			case 3:
			case 7:
			return false;
				//$fileThumbnail->rel	= $path->image_rel_front . 'icon-folder-medium'.$key.'.png';

			break;
			// user wants to display only icon folder (parameters) small
			case 2:
			case 6:
				return false;
			//$fileThumbnail->rel	= $path->image_rel_front . 'icon-folder-small-main'.$key.'.png';

		break;

			// standard medium image next to category in categories view - if the file doesn't exist, it will be displayed folder icon
			case 1:
			case 5:
			if ($extm == '' || (int)$rightDisplayKey == 0) {
				return false;
				//$fileThumbnail->rel		= $path->image_rel_front . 'icon-folder-medium'.$key.'.png';
			} else {
				$fileThumbnail->rel 	= $extm;
				$fileThumbnail->extw 	= $extw[1];
				$fileThumbnail->exth 	= $exth[1];
				$fileThumbnail->extpic 	= true;
			}
			break;

			// standard small image next to category in categories view - if the file doesn't exist, it will be displayed folder icon
			case 0:
			case 4:
			if ($exts == '' || (int)$rightDisplayKey == 0) {
				return false;
				//$fileThumbnail->rel		= $path->image_rel_front . 'icon-folder-small-main'.$key.'.png';
			}else {
				$fileThumbnail->rel 	= $exts;
				$fileThumbnail->extw 	= $extw[2];
				$fileThumbnail->exth 	= $exth[2];
				$fileThumbnail->extpic 	= true;
			}
			break;
		}
		return $fileThumbnail;
	}

	/*
	 * IMAGE OR FOLDER - CATEGORY VIEW - INTERNAL IMAGE
	 */
	public static function displayCategoryImageOrFolder ($filename, $size, $rightDisplayKey, $param= 'display_category_icon_image') {

		phocagalleryimport('phocagallery.image.image');
		phocagalleryimport('phocagallery.path.path');
		phocagalleryimport('phocagallery.file.filethumbnail');

		$paramsC = ComponentHelper::getParams('com_phocagallery') ;

		$path						= PhocaGalleryPath::getPath();
		$fileThumbnail				= PhocaGalleryFileThumbnail::getThumbnailName($filename, $size);
		$displayCategoryIconImage	= $paramsC->get( $param, 0 );



		//Thumbnail_file doesn't exists or user wants to display folder icon or if category is not accessable, display the key in the image:
		if ((int)$rightDisplayKey == 0 || !PhocaGalleryFile::exists($fileThumbnail->abs) ||  $displayCategoryIconImage != 1) {
				$fileThumbnail->rel 				= false;
				$fileThumbnail->linkthumbnailpath	= false;
				$fileThumbnail->extid				= 0;
				$fileThumbnail->extpic				= false;
				$fileThumbnail->extm				= false;
				$fileThumbnail->exts				= false;

		}

		return $fileThumbnail;
	}


	/*
	* BACK FOLDER - CATEGORY VIEW
	*//*
	public static function displayBackFolder ($size, $rightDisplayKey) {

		$fileThumbnail = new CMSObject;

		// if category is not accessable, display the key in the image:
		$key = '';
		if ((int)$rightDisplayKey == 0) {
			$key = '-key';
		}

		phocagalleryimport('phocagallery.image.image');
		phocagalleryimport('phocagallery.path.path');
		$path				= PhocaGalleryPath::getPath();
		$fileThumbnail->abs = '';
		$paramsC 			= ComponentHelper::getParams('com_phocagallery') ;

		if ( $paramsC->get( 'image_background_shadow' ) != 'None' ) {
			$fileThumbnail->rel	= $path->image_rel_front . 'icon-up-images'.$key.'.png';
		} else {
			$fileThumbnail->rel	= $path->image_rel_front . 'icon-up-images'.$key.'.png';
		}

		return $fileThumbnail->rel;
	}*/


	/*
	 * IMAGE OR FOLDER - CATEGORY VIEW - EXTERNAL IMAGE
	 */
	public static function displayCategoryExtImgOrFolder ($extS, $extM, $size, $rightDisplayKey, $param= 'display_category_icon_image') {

		phocagalleryimport('phocagallery.image.image');
		phocagalleryimport('phocagallery.path.path');

		$paramsC = ComponentHelper::getParams('com_phocagallery') ;
		$path				= PhocaGalleryPath::getPath();


		$fileThumbnail = new CMSObject();
		$fileThumbnail->extm				= $extM;
		$fileThumbnail->exts				= $extS;
		$fileThumbnail->linkthumbnailpath	= $extS; // in case external image doesn't exist or the category is locked
		$displayCategoryIconImage	= $paramsC->get( $param, 0 );
		$imageBackgroundShadow 		= $paramsC->get( 'image_background_shadow', 'None' );

		// if category is not accessable, display the key in the image:
		$key = '';
		if ((int)$rightDisplayKey == 0) {
			$key = '-key';
		}

		//Thumbnail_file doesn't exists or user wants to display folder icon
		$fileThumbnail->extpic = true;
		if ($size == 'medium') {
			if ($extM == '' || (int)$rightDisplayKey == 0 || $displayCategoryIconImage != 1) {
				if ( $imageBackgroundShadow != 'None') {
					$fileThumbnail->linkthumbnailpath	= $path->image_rel_front . 'icon-folder-medium'.$key.'.png';
				} else {
					$fileThumbnail->linkthumbnailpath	= $path->image_rel_front . 'icon-folder-medium'.$key.'.png';
				}
				$fileThumbnail->extpic = false;
			}
		}

		if ($size == 'small') {
			if ($extS == '' || (int)$rightDisplayKey == 0 || $displayCategoryIconImage != 1) {
				if ( $imageBackgroundShadow != 'None') {
					$fileThumbnail->linkthumbnailpath	= $path->image_rel_front . 'icon-folder-small'.$key.'.png';
				} else {
					$fileThumbnail->linkthumbnailpath	= $path->image_rel_front . 'icon-folder-small'.$key.'.png';
				}
				$fileThumbnail->extpic = false;
			}
		}
		return $fileThumbnail;
	}

	/*
	 * IMAGE OR FOLDER - CATEGORIES VIEW IN CATEGORY VIEW- INTERNAL IMAGE
	 * 0-small,1-medium,2-smallFolder,3-mediumFolder,4-smallShadow,5-mediumShadow,6-smallFolderShadow,7-mediumFolderShadow
	 * We now the path from CATEGORY VIEW, we only change the path for CATEGORIES VIEW
	 * If there is a folder icon - medium to small main, if image - phoca_thumb_m to phoca_thumb_s
	 */
	public static function displayCategoriesCVImageOrFolder ($linkThumbnailPath, $imgCategoriesSizeCV) {

		switch((int)$imgCategoriesSizeCV) {
			case 0:
			case 2:
			case 4:
			case 6:
				$imageThumbnail = str_replace('medium', 'small-main', $linkThumbnailPath);

				$imageThumbnail = str_replace('phoca_thumb_m_', 'phoca_thumb_s_', $imageThumbnail);
			break;
			Default:
				$imageThumbnail = str_replace('small-main', 'medium', $linkThumbnailPath);
				$imageThumbnail = str_replace('phoca_thumb_s_', 'phoca_thumb_m_', $imageThumbnail);
			break;
		}
		return $imageThumbnail;
	}

	/*
	 * IMAGE OR FOLDER - CATEGORIES VIEW IN CATEGORY VIEW- EXTERNAL IMAGE
	 * 0-small,1-medium,2-smallFolder,3-mediumFolder,4-smallShadow,5-mediumShadow,6-smallFolderShadow,7-mediumFolderShadow
	 */
	public static function displayCategoriesCVExtImgOrFolder ($linkThumbnailPathM, $linkThumbnailPathS, $linkThumbnailPath, $imgCategoriesSizeCV) {
		switch((int)$imgCategoriesSizeCV) {
			case 0:
			case 2:
			case 4:
			case 6:
				if ($linkThumbnailPathS != '') {
					$imageThumbnail = $linkThumbnailPathS;
				} else {
					$imageThumbnail = str_replace('medium', 'small-main', $linkThumbnailPath);
					$imageThumbnail = str_replace('phoca_thumb_m_', 'phoca_thumb_s_', $imageThumbnail);
				}

			break;
			Default:
				if ($linkThumbnailPathM != '') {
					$imageThumbnail = $linkThumbnailPathM;
				} else {
					$imageThumbnail = str_replace('small-main', 'medium', $linkThumbnailPath);
					$imageThumbnail = str_replace('phoca_thumb_s_', 'phoca_thumb_m_', $imageThumbnail);
				}

			break;
		 }
		 return $imageThumbnail;
	}


	/*
	 * IMAGE OR NO IMAGE - CATEGORY VIEW - INTERNAL IMAGE
	 */
	public static function displayCategoryImageOrNoImage ($filename, $size) {

		phocagalleryimport('phocagallery.image.image');
		phocagalleryimport('phocagallery.path.path');
		phocagalleryimport('phocagallery.file.filethumbnail');
		$path			= PhocaGalleryPath::getPath();
		$fileThumbnail	= PhocaGalleryFileThumbnail::getThumbnailName($filename, $size);


		//Thumbnail_file doesn't exists
		if (!PhocaGalleryFile::exists($fileThumbnail->abs)) {
			switch ($size) {
				case 'large':
				$fileThumbnail->rel	= $path->image_rel_front . 'phoca_thumb_l_no_image.png';
				break;
				case 'medium':
				$fileThumbnail->rel	= $path->image_rel_front . 'phoca_thumb_m_no_image.png';
				break;
				Default:
				case 'small':
				$fileThumbnail->rel	= $path->image_rel_front . 'phoca_thumb_s_no_image.png';
				break;
			}
		}
		return $fileThumbnail->rel;
	}



	public static function getCategoryImages($categoryid, $categoryImageOrdering = '') {

		$db 	=Factory::getDBO();
		$user 	= Factory::getUser();
		$image 	= '';

		if (!is_array($categoryImageOrdering) && is_numeric($categoryImageOrdering)) {
            $categoryImageOrdering = PhocaGalleryOrdering::getOrderingString($categoryImageOrdering);
        }

		// We need to get a list of all subcategories in the given category
		if ($categoryImageOrdering['column'] == '') {
			$ordering  =  ' ORDER BY RAND()';
		} else {
			// This is special case where we change category to image
			$ordering = ' ORDER BY a.'.$categoryImageOrdering['column'] . ' ' .$categoryImageOrdering['sort'];
		}


         $query = 'SELECT a.id, a.filename, a.exts, a.extm, a.extw, a.exth, a.extid, a.title, a.description, a.metadesc, c.accessuserid as cataccessuserid, c.access as cataccess' .
            ' FROM #__phocagallery AS a' .
			' LEFT JOIN #__phocagallery_categories AS c ON a.catid = c.id'.
            ' WHERE a.catid = '.(int) $categoryid.
            ' AND a.published = 1'.
			' AND a.approved = 1'.
            $ordering.
			' LIMIT 0,5';
		$db->setQuery($query);
	    $images = $db->loadObjectList();


		// Test the user rights to display random image as category image
		$rightDisplay = 1;//default is set to 1 (all users can see the category)

		if (isset($images[0]->cataccessuserid) && isset($images[0]->cataccess)) {
			$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $images[0]->cataccessuserid, $images[0]->cataccess, $user->getAuthorisedViewLevels(), $user->get('id', 0), 0);
		}

		if ($rightDisplay == 0) {
			$images = array();
			$images[0] = new StdClass();
			$images[0]->notaccess = 1;
		}

        return $images;


    }

	/*
	 * RANDOM IMAGE OR IMAGE ORDERED BY PARAM - CATEGORIES VIEW, CATEGORY VIEW
	 * $extImage - for example Picasa image
	 * $extImageSize - 1 - small, 2 - medium, 3 - large
	 * Is called random but the ordering can be set
	 */
	public static function getRandomImageRecursive($categoryid, $categoryImageOrdering = '', $extImage = 0, $extImageSize = 1) {

		$db 	=Factory::getDBO();
		$user 	= Factory::getUser();
		$image 	= new stdClass();

		if (!is_array($categoryImageOrdering) && is_numeric($categoryImageOrdering)) {
            $categoryImageOrdering = PhocaGalleryOrdering::getOrderingString($categoryImageOrdering);
        }

		// We need to get a list of all subcategories in the given category
		if ($categoryImageOrdering['column'] == '') {
			$ordering = $orderingRandomCat =  ' ORDER BY RAND()';
		} else {
			// This is special case where we change category to image
			$ordering = ' ORDER BY a.'.$categoryImageOrdering['column'] . ' ' .$categoryImageOrdering['sort'];
			$orderingRandomCat = ' ORDER BY c.ordering'; //TO DO - can be changed to category_ordering parameter
		}



        $query = 'SELECT a.id, a.filename, a.exts, a.extm, a.extw, a.exth, a.extid, c.accessuserid as cataccessuserid, c.access as cataccess' .
            ' FROM #__phocagallery AS a' .
			' LEFT JOIN #__phocagallery_categories AS c ON a.catid = c.id'.
            ' WHERE a.catid = '.(int) $categoryid.
            ' AND a.published = 1'.
			' AND a.approved = 1'.
            $ordering.
			' LIMIT 0,1';
		$db->setQuery($query);
	    $images = $db->loadObjectList();


		// Test the user rights to display random image as category image
		$rightDisplay = 1;//default is set to 1 (all users can see the category)

		if (isset($images[0]->cataccessuserid) && isset($images[0]->cataccess)) {
			$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $images[0]->cataccessuserid, $images[0]->cataccess, $user->getAuthorisedViewLevels(), $user->get('id', 0), 0);
		}


		if ($rightDisplay == 0) {
			$images = array();
		}

        if (!isset($images) || empty($images)) {


			$image->exts		= '';
			$image->extm		= '';
			$image->exth		= '';
			$image->extw		= '';
            $image->filename 	= '';


			// TO DO, if we find no image in subcategory we look at its subcategory (subcategory of subcategory)
			// no to look if there is some subcategory on the same level
            $subCategories = PhocaGalleryImageFront::getRandomCategory($categoryid, $ordering);

			foreach ($subCategories as $subCategory) {

                $image = PhocaGalleryImageFront::getRandomImageRecursive($subCategory->id, $categoryImageOrdering, $extImage, $extImageSize);

				// external image - e.g. Picasa
				if ($extImage == 1) {
					if ($extImageSize == 2) {
						if (isset($image->extm) && $image->extm != '') {
							break;
						}
					} else {
						if (isset($image->exts) && $image->exts != '') {
							break;
						}
					}
				} else {
					if (isset($image->filename) && $image->filename != '') {
						break;
					}
				}
            }
        } else {
            $image = $images[0] ;
        }

		if ($extImage == 1) {
			return $image;
		} else {
			if(isset($image->filename)) {
				return $image->filename;
			} else {
				return $image;
			}
		}
    }

	public static function getRandomCategory($parentid, $ordering = ' ORDER BY RAND()') {
        $db 	=Factory::getDBO();

		$groups = Factory::getUser()->getAuthorisedViewLevels();
		if (count($groups)) {
			$access = ' AND a.access IN(' . implode(',', $groups) . ')';
		} else {
			$access = '';
		}

		$query = 'SELECT a.id, a.extid' .
            ' FROM #__phocagallery_categories AS a' .
            ' WHERE a.parent_id = '.(int) $parentid .
            ' AND a.published = 1 ' .
			$access .
            $ordering;
		$db->setQuery($query);
	    $images = $db->loadObjectList();

        return $images;
    }


	public static function getSizeString($size) {
		switch((int)$size) {
			case 3: case 7: case 1: case 5:
			$output = 'm';
			break;

			case 2: case 6: case 0: case 4: Default:
			$output = 's';
			break;
		}
		return $output;
	}

	public static function renderMosaic($images, $size = 0, $extImg = 0, $w = 100, $h = 100) {

		$o = '';
		phocagalleryimport('phocagallery.file.filethumbnail');
		$count 	= count($images);
		$m1 	= mt_rand(0,1);


		switch($count) {
			case 1:
				$a = 1;//array(1);
			break;
			case 2:
				$a = 2;//array(2);
			break;
			case 3:
				$at = array(3, 4);
				$ar = array_rand($at);
				$a 	= $at[$ar];
			break;
			case 4:
				$at = array(3, 4, 5);
				$ar = array_rand($at);
				$a 	= $at[$ar];
			break;
			case 5:
				$at = array(3,4,5,6,7);
				$ar = array_rand($at);
				$a 	= $at[$ar];
			break;
		}

		// NOT ACCESS
		if (isset($images[0]->notaccess) && $images[0]->notaccess == 1) {
			$stNA = 'width: '.($w ).'px; height: '.($h ).'px; margin: 0 auto;';
			$o .= '<div style="text-align: center;'.$stNA.'">';
			$o .= '<div class="pg-multi-img" style="margin: 0 auto;'.$stNA.'" ><img src="'.Uri::base(true).'/media/com_phocagallery/images/icon-folder-medium-key.png" style="margin: 0 auto;'.$stNA.'" alt="" /></div>';
			$o .= '</div>';
			return $o;
		}

		if (isset($a)) {
			$i 	= self::getMosaicFields($a, $images, $size, $extImg, $w, $h);

			$m2 = mt_rand(0,1);

			$o .= '<div style="width: '.$i['w'].'px; height: '.$i['h'].'px">';
			if ($m2 == 1) {
				$o .= '<div style="float:left;width:'.$i['w1'].'px;">';
				$o .= $i['b1'];
				$o .= '</div>';
				$o .= '<div style="float:left;width:'.$i['w2'].'px;">';
				$o .= $i['b2'];
				$o .= '</div>';
			} else {
				$o .= '<div style="float:right;width:'.$i['w1'].'px;">';
				$o .= $i['b1'];
				$o .= '</div>';
				$o .= '<div style="float:right;width:'.$i['w2'].'px;">';
				$o .= $i['b2'];
				$o .= '</div>';
			}
			$o .= '</div>';
		} else {
			$o .= '<div></div>';
		}

		return $o;
	}



	public static function getMosaicFields($a, $images, $size = 0, $extImg = 0, $w = 100, $h = 100) {

		$paramsC 	= ComponentHelper::getParams('com_phocagallery');
		$alt_value	= $paramsC->get( 'alt_value', 1 );

		if ($size == 1) {
			$i0 = 'medium';// |
			$i1 = 'medium1';// ||
			$i2 = 'medium2';// --
			$i3 = 'medium3';// ||--
		} else {
			$i0 = 'small';// |
			$i1 = 'small1';// ||
			$i2 = 'small2';// --
			$i3 = 'small3';// ||--
		}

		$o = array();
		switch($a) {
			case 1:

																						$altValue0	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[0]->title, $images[0]->description, $images[0]->metadesc);
			if ($extImg == 1) {
				$o['w'] = (int)$w * 3;
				$o['h'] = (int)$h;
				$o['w1']= (int)$w * 2;
				$o['w2']= (int)$w;

				$wi	= $w * 2; $hi = $h * 2;
				$attr = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$wi	= $w ; $hi = $h;
				$attr1 = 'style="width:'.$wi.'px;height:'.$hi.'px"';

				$o['b1']= '<span class="pg-multi-img"><img src="'.$images[0]->extm.'" '.$attr.' alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.$images[0]->extm.'" '.$attr1.' alt="'.$altValue0.'" /></span>';
			} else {

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[0]->filename, $i3);
				$i[0][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[0]->filename, $i1);
				$iS 	= @getimagesize($t->abs); //INHIBITERROR
				$i[1][0]= $t->rel;
				$i[1][1]= $iS[0];
				$i[1][2]= $iS[1];

				$o['w'] = (int)$i[1][1] * 3;
				$o['h'] = (int)$i[1][2];
				$o['w1']= (int)$i[1][1] * 2;
				$o['w2']= (int)$i[1][1];

				$o['b1']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[0][0].'" alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[1][0].'" alt="'.$altValue0.'" /></span>';
			}
			return $o;
			break;

			case 2:
				$altValue0	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[0]->title, $images[0]->description, $images[0]->metadesc);
			$altValue1	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[1]->title, $images[1]->description, $images[1]->metadesc);
			if ($extImg == 1) {
				$o['w'] = (int)$w * 3;
				$o['h'] = (int)$h;
				$o['w1']= (int)$w * 2;
				$o['w2']= (int)$w;

				$wi	= $w * 2; $hi = $h * 2;
				$attr = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$wi	= $w ; $hi = $h * 2;
				$attr1 = 'style="width:'.$wi.'px;height:'.$hi.'px"';

				$o['b1']= '<span class="pg-multi-img"><img src="'.$images[0]->extm.'" '.$attr.' alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.$images[1]->extm.'" '.$attr1.' alt="'.$altValue1.'" /></span>';
			} else {

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[0]->filename, $i3);
				$i[0][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[1]->filename, $i1);
				$iS 	= @getimagesize($t->abs); //INHIBITERROR
				$i[1][0]= $t->rel;
				$i[1][1]= $iS[0];
				$i[1][2]= $iS[1];

				$o['w'] = (int)$i[1][1] * 3;
				$o['h'] = (int)$i[1][2];
				$o['w1']= (int)$i[1][1] * 2;
				$o['w2']= (int)$i[1][1];

				$o['b1']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[0][0].'" alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[1][0].'" alt="'.$altValue1.'" /></span>';
			}
			return $o;
			break;

			case 3:
				$altValue0	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[0]->title, $images[0]->description, $images[0]->metadesc);
			$altValue1	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[1]->title, $images[1]->description, $images[1]->metadesc);
			$altValue2	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[2]->title, $images[2]->description, $images[2]->metadesc);

			if ($extImg == 1) {

				$o['w'] = (int)$w * 3;
				$o['h'] = (int)$h * 2;
				$o['w1']= (int)$w * 2;
				$o['w2']= (int)$w;

				$wi	= $w * 2; $hi = $h * 2;
				$attr = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$wi	= $w ; $hi = $h;
				$attr1 = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$attr2 = 'style="width:'.$wi.'px;height:'.$hi.'px"';

				$o['b1']= '<span class="pg-multi-img"><img src="'.$images[0]->extm.'" '.$attr.' alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.$images[1]->extm.'" '.$attr1.' alt="'.$altValue1.'" /></span>'.
					  '<span class="pg-multi-img"><img src="'.$images[2]->extm.'" '.$attr2.' alt="'.$altValue2.'" /></span>';
			} else {

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[0]->filename, $i3);
				$i[0][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[1]->filename, $i0);
				$i[1][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[2]->filename, $i0);
				$iS 	= @getimagesize($t->abs); //INHIBITERROR
				$i[2][0]= $t->rel;
				$i[2][1]= $iS[0];
				$i[2][2]= $iS[1];

				$o['w'] = (int)$i[2][1] * 3;
				$o['h'] = (int)$i[2][2] * 2;
				$o['w1']= (int)$i[2][1] * 2;
				$o['w2']= (int)$i[2][1];

				$o['b1']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[0][0].'" alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[1][0].'" alt="'.$altValue1.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[2][0].'" alt="'.$altValue2.'" /></span>';
			}
			return $o;
			break;

			case 4:
				$altValue0	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[0]->title, $images[0]->description, $images[0]->metadesc);
			$altValue1	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[1]->title, $images[1]->description, $images[1]->metadesc);
			$altValue2	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[2]->title, $images[2]->description, $images[2]->metadesc);
				//$altValue3	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[3]->title, $images[3]->description, $images[3]->metadesc);

			if ($extImg == 1) {

				$o['w'] = (int)$w * 3;
				$o['h'] = (int)$h;
				$o['w1']= (int)$w;
				$o['w2']= (int)$w * 2;

				$wi	= $w; $hi = $h * 2;
				$attr = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$wi	= $w * 2; $hi = $h;
				$attr1 = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$attr2 = 'style="width:'.$wi.'px;height:'.$hi.'px"';

				$o['b1']= '<span class="pg-multi-img"><img src="'.$images[0]->extm.'" '.$attr.' alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.$images[1]->extm.'" '.$attr1.' alt="'.$altValue1.'" /></span>'.
					  '<span class="pg-multi-img"><img src="'.$images[2]->extm.'" '.$attr2.' alt="'.$altValue2.'" /></span>';
			} else {
				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[0]->filename, $i1);
				$iS 	= @getimagesize($t->abs); //INHIBITERROR
				$i[0][0]= $t->rel;
				$i[0][1]= $iS[0];
				$i[0][2]= $iS[1];

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[1]->filename, $i2);
				$i[1][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[2]->filename, $i2);
				$i[2][0]= $t->rel;


				$o['w'] = (int)$i[0][1] * 3;
				$o['h'] = (int)$i[0][2];
				$o['w1']= (int)$i[0][1];
				$o['w2']= (int)$i[0][1] * 2;


				$o['b1']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[0][0].'" alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[1][0].'" alt="'.$altValue1.'" /></span>'.
					  '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[2][0].'" alt="'.$altValue2.'" /></span>';
			}
			return $o;
			break;

			case 5:
																								$altValue0	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[0]->title, $images[0]->description, $images[0]->metadesc);
			$altValue1	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[1]->title, $images[1]->description, $images[1]->metadesc);
			$altValue2	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[2]->title, $images[2]->description, $images[2]->metadesc);
			$altValue3	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[3]->title, $images[3]->description, $images[3]->metadesc);

			if ($extImg == 1) {

				$o['w'] = (int)$w * 3;
				$o['h'] = (int)$h * 2;
				$o['w1']= (int)$w;
				$o['w2']= (int)$w * 2;

				$wi	= $w; $hi = $h * 2;
				$attr = 'style="width:'.$wi.'px; height:'.$hi.'px"';
				$wi	= $w; $hi = $h;
				$attr1 = 'style="width:'.$wi.'px; height:'.$hi.'px"';
				$attr2 = 'style="width:'.$wi.'px; height:'.$hi.'px"';
				$wi	= $w * 2; $hi = $h;
				$attr3 = 'style="width:'.$wi.'px; height:'.$hi.'px"';

				$o['b1']= '<span class="pg-multi-img"><img src="'.$images[0]->extm.'" '.$attr.' alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.$images[1]->extm.'" '.$attr1.' alt="'.$altValue1.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.$images[2]->extm.'" '.$attr2.' alt="'.$altValue2.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.$images[3]->extm.'" '.$attr3.' alt="'.$altValue3.'" /></span>';
			} else {
				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[0]->filename, $i1);
				$iS 	= @getimagesize($t->abs); //INHIBITERROR
				$i[0][0]= $t->rel;
				$i[0][1]= $iS[0];
				$i[0][2]= $iS[1];

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[1]->filename, $i0);
				$i[1][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[2]->filename, $i0);
				$i[2][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[3]->filename, $i2);
				$i[3][0]= $t->rel;

				$o['w'] = (int)$i[0][1] * 3;
				$o['h'] = (int)$i[0][2];
				$o['w1']= (int)$i[0][1];
				$o['w2']= (int)$i[0][1] * 2;


				$o['b1']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[0][0].'" alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[1][0].'" alt="'.$altValue1.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[2][0].'" alt="'.$altValue2.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[3][0].'" alt="'.$altValue3.'" /></span>';
			}
			return $o;
			break;

			case 6:
																							$altValue0	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[0]->title, $images[0]->description, $images[0]->metadesc);
			$altValue1	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[1]->title, $images[1]->description, $images[1]->metadesc);
			$altValue2	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[2]->title, $images[2]->description, $images[2]->metadesc);
			$altValue3	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[3]->title, $images[3]->description, $images[3]->metadesc);
			$altValue4	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[4]->title, $images[4]->description, $images[4]->metadesc);

			if ($extImg == 1) {

				$o['w'] = (int)$w * 3;
				$o['h'] = (int)$h;
				$o['w1']= (int)$w;
				$o['w2']= (int)$w * 2;

				$wi	= $w; $hi = $h * 2;

				$attr = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$wi	= $w; $hi = $h;
				$attr1 = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$attr2 = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$attr3 = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$attr4 = 'style="width:'.$wi.'px;height:'.$hi.'px"';

				$o['b1']= '<span class="pg-multi-img"><img src="'.$images[0]->extm.'" '.$attr.' alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.$images[1]->extm.'" '.$attr1.' alt="'.$altValue1.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.$images[2]->extm.'" '.$attr2.' alt="'.$altValue2.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.$images[3]->extm.'" '.$attr3.' alt="'.$altValue3.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.$images[4]->extm.'" '.$attr4.' alt="'.$altValue4.'" /></span>';
			} else {
				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[0]->filename, $i1);
				$iS 	= @getimagesize($t->abs); //INHIBITERROR
				$i[0][0]= $t->rel;
				$i[0][1]= $iS[0];
				$i[0][2]= $iS[1];

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[1]->filename, $i0);
				$i[1][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[2]->filename, $i0);
				$i[2][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[3]->filename, $i0);
				$i[3][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[4]->filename, $i0);
				$i[4][0]= $t->rel;

				$o['w'] = (int)$i[0][1] * 3;
				$o['h'] = (int)$i[0][2];
				$o['w1']= (int)$i[0][1];
				$o['w2']= (int)$i[0][1] * 2;


				$o['b1']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[0][0].'" alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[1][0].'" alt="'.$altValue1.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[2][0].'" alt="'.$altValue2.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[3][0].'" alt="'.$altValue3.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[4][0].'" alt="'.$altValue4.'" /></span>';
			}
			return $o;
			break;

			case 7:
							$altValue0	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[0]->title, $images[0]->description, $images[0]->metadesc);
			$altValue1	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[1]->title, $images[1]->description, $images[1]->metadesc);
			$altValue2	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[2]->title, $images[2]->description, $images[2]->metadesc);
			$altValue3	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[3]->title, $images[3]->description, $images[3]->metadesc);
			//$altValue4	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[4]->title, $images[4]->description, $images[4]->metadesc);
			//$altValue5	= PhocaGalleryRenderFront::getAltValue($alt_value, $images[5]->title, $images[5]->description, $images[5]->metadesc);

			if ($extImg == 1) {

				$o['w'] = (int)$w * 3;
				$o['h'] = (int)$h;
				$o['w1']= (int)$w;
				$o['w2']= (int)$w * 2;

				$wi	= $w; $hi = $h * 2;
				$attr = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$attr1 = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$wi	= $w; $hi = $h;
				$attr2 = 'style="width:'.$wi.'px;height:'.$hi.'px"';
				$attr3 = 'style="width:'.$wi.'px;height:'.$hi.'px"';

				$o['b1']= '<span class="pg-multi-img"><img src="'.$images[0]->extm.'" '.$attr.' alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.$images[1]->extm.'" '.$attr1.' alt="'.$altValue1.'" /></span>'.
					  '<span class="pg-multi-img"><img src="'.$images[2]->extm.'" '.$attr2.' alt="'.$altValue2.'" /></span>'.
					  '<span class="pg-multi-img"><img src="'.$images[3]->extm.'" '.$attr3.' alt="'.$altValue3.'" /></span>';
			} else {
				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[0]->filename, $i1);
				$iS 	= @getimagesize($t->abs); //INHIBITERROR
				$i[0][0]= $t->rel;
				$i[0][1]= $iS[0];
				$i[0][2]= $iS[1];

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[1]->filename, $i1);
				$i[1][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[2]->filename, $i0);
				$i[2][0]= $t->rel;

				$t 		= PhocaGalleryFileThumbnail::getThumbnailName($images[3]->filename, $i0);
				$i[3][0]= $t->rel;


				$o['w'] = (int)$i[0][1] * 3;
				$o['h'] = (int)$i[0][2];
				$o['w1']= (int)$i[0][1];
				$o['w2']= (int)$i[0][1] * 2;


				$o['b1']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[0][0].'" alt="'.$altValue0.'" /></span>';
				$o['b2']= '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[1][0].'" alt="'.$altValue1.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[2][0].'" alt="'.$altValue2.'" /></span>'.
						  '<span class="pg-multi-img"><img src="'.Uri::base(true).'/'. $i[3][0].'" alt="'.$altValue3.'" /></span>';
			}
			return $o;
			break;
		}


	}

	public static function setFileNameByImageId($id = 0) {

		$f = '';
		if ((int)$id > 0) {
			$db 	= Factory::getDBO();
			$query = ' SELECT a.filename, a.extid, a.exts, a.extm, a.extw, a.exth'
					.' FROM #__phocagallery AS a'
					.' WHERE a.id = '.(int)$id
					.' ORDER BY a.id'
					.' LIMIT 1';
			$db->setQuery($query);
			$f = $db->loadObject();
		}

		return $f;
	}


	public static function assignValues(&$item, $t) {



		$thumbLink		= PhocaGalleryFileThumbnail::getThumbnailName($item->filename, 'large');
		$imgLinkOrig	= Uri::base(true) . '/' . PhocaGalleryFile::getFileOriginal($item->filename, 1);


		if ($t['detail_window'] == 7) {
			$siteLink = Route::_('index.php?option=com_phocagallery&view=detail&catid=' . $item->catslug . '&id=' . $item->slug . '&Itemid=' . $t['itemid']);
		} else {
			$siteLink = Route::_('index.php?option=com_phocagallery&view=detail&catid=' . $item->catslug . '&id=' . $item->slug . '&tmpl=component' . '&Itemid=' . $t['itemid']);
		}
		$imgLink = $thumbLink->rel;


		$extImage = PhocaGalleryImage::isExtImage($item->extid);
		if ($extImage) {
			$imgLink     = $item->extl;
			$imgLinkOrig = $item->exto;
		}

		// Detail Window
		if ($t['detail_window'] == 0) {
			// BS MODAL
			$item->class		= 'pg-bs-modal-button';
			$item->class2		= $item->class;
			$item->class3		= $item->class;
			$item->link 		= $siteLink;
			$item->link2 		= $siteLink;//'javascript:void(0)';
			$item->link3		= $siteLink;
			$item->onclick		= '';
			$item->onclick2		= $item->onclick;
			$item->onclick3		= $item->onclick;
			$item->onclick4		= $item->onclick;// Rating
			$item->linkorig		= $imgLinkOrig;

		} else if ($t['detail_window'] == 1) {
			// STANDARD POPUP
			$item->class		= 'pg-js-popup-button';
			$item->class2		= $item->class;
			$item->class3		= $item->class;
			$item->onclick		= "window.open(this.href,'win2','width=".$t['popup_width'].",height=".$t['popup_height'].",scrollbars=yes,menubar=no,resizable=yes'); return false;";
			$item->onclick2		= $item->onclick;
			$item->onclick3		= $item->onclick;
			$item->onclick4		= $item->onclick;// Rating
			$item->link 		= $siteLink;
			$item->link2 		= $siteLink;
			$item->link3		= $siteLink;
			$item->linkorig		= $imgLinkOrig;

		} else if ( $t['detail_window'] == 12 ) {
			// MAGNIFIC

			$item->class		= 'pg-magnific-button';
			$item->class2		= 'pg-magnific2-button';
			$item->class3		= 'pg-magnific3-button';
			$item->link 		= $imgLink;
			$item->link2 		= 'javascript:void(0)';//$imgLink;
			$item->link3		= $siteLink;
			$item->linkorig		= $imgLinkOrig;
			$item->onclick		= '';
			$item->onclick2		= 'document.getElementById(\'pgImg'.$item->id.'\').click();';
			$item->onclick3		= $item->onclick;
			$item->onclick4		= $item->onclick;// Rating

		} else if ( $t['detail_window'] == 14 ) {
			// PHOTOSWIPE
			$item->class		= 'pg-photoswipe-button';
			$item->class2		= 'pg-photoswipe-button-copy';
			$item->class3		= 'pg-bs-modal-button';
			$item->link 		= $imgLink;
			$item->link2 		= 'javascript:void(0)';
			$item->link3		= $siteLink;
			$item->linkorig		= $imgLinkOrig;
			$item->onclick		= '';
			$item->itemprop		= 'contentUrl';
			$item->onclick2		= 'document.getElementById(\'pgImg'.$item->id.'\').click();';
			$item->onclick3		= $item->onclick;
			$item->onclick4		= $item->onclick;// Rating


			switch ($t['photoswipe_display_caption']) {
				case 0:
					$item->photoswipecaption = '';
					break;

				case 2:
					$item->photoswipecaption = PhocaGalleryText::strTrimAll(( $item->description));
					break;

				case 3:
					$item->photoswipecaption = PhocaGalleryText::strTrimAll(($item->title));
					if ($item->description != '') {
						$item->photoswipecaption .='<br />' .PhocaGalleryText::strTrimAll(($item->description));
					}
					break;

				case 1:
				default:
					$item->photoswipecaption = PhocaGalleryText::strTrimAll(($item->title));
					break;
			}

		} else {
			$item->class		= 'pg-nopopup-button';
			$item->class2		= $item->class;
			$item->class3		= $item->class;;
			$item->link 		= $siteLink;
			$item->link2 		= $siteLink;
			$item->link3		= $siteLink;
			$item->linkorig		= $imgLinkOrig;
			$item->onclick		= '';
			$item->onclick2		= $item->onclick;
			$item->onclick3		= $item->onclick;
			$item->onclick4		= $item->onclick;// Rating


		}

		$item->display_icon_detail 	= $t['display_icon_detail'];
		$item->display_icon_download= $t['display_icon_download'];
		$item->display_name 		= $t['display_name'];
		$item->display_icon_vm 		= '';
		$item->display_icon_pc 		= '';
		$item->start_cooliris 		= $t['start_cooliris'] ;
		$item->type					= 2;



		// ALT VALUE
		$altValue	= PhocaGalleryRenderFront::getAltValue($t['altvalue'], $item->title, $item->description, $item->metadesc);
		$item->altvalue				= $altValue;

		// TITLE TAG - Description Output in Title Tag
		$imgAlt = $imgTitle = '';

		// Some methods cannot use Alt because of conflicting with Title and popup methods
		if ($t['detail_window'] == 3 || $t['detail_window'] == 9 || $t['detail_window'] == 10 || $t['detail_window'] == 12 ) {
			$imgAlt 	= $item->altvalue;
			$imgTitle	= $item->title;
			if ($imgAlt == $imgTitle) {
				$imgAlt = '';
			}
			$item->oimgalt = $imgAlt;
		} else {
			$item->oimgalt = $altValue;
		}


		// TITLE TAG - Detail
		if ($t['detail_window'] == 9 || $t['detail_window'] == 10) {
			$detailAlt 		= $item->altvalue;
			$detailTitle	= $item->title;
			if ($detailAlt == $detailTitle) {
				$detailAlt = '';
			}
		} else {
			$detailAlt 		= Text::_('COM_PHOCAGALLERY_IMAGE_DETAIL');
			$detailTitle 	= Text::_('COM_PHOCAGALLERY_IMAGE_DETAIL');
		}
		$item->oimgaltdetail 		= $detailAlt;
		$item->oimgtitledetail 	= $detailTitle;

		$titleDesc = '';
		if ($t['display_title_description'] == 1) {
			$titleDesc .= $item->title;
			if ($item->description != '' && $titleDesc != '') {
				$titleDesc .= ' - ';
			}
		}

		if (($t['detail_window'] == 8 || $t['detail_window'] == 10 || $t['detail_window'] == 12) && $t['displaydescriptiondetail'] > 0) {
			$item->odesctitletag = strip_tags((string)$titleDesc).strip_tags((string)$item->description);
		} else {
			$item->odesctitletag = strip_tags((string)$imgTitle);
		}


	}

}
?>
