<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Session\Session;


defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\CMS\Uri\Uri;
phocagalleryimport( 'phocagallery.youtube.youtube');
$app	= Factory::getApplication();

$layoutBI 	= new FileLayout('box_image', null, array('component' => 'com_phocagallery'));
//$layoutBIM 	= new FileLayout('box_image_masonry', null, array('component' => 'com_phocagallery'));
$layoutBC 	= new FileLayout('box_category', null, array('component' => 'com_phocagallery'));
$layoutBB 	= new FileLayout('box_back', null, array('component' => 'com_phocagallery'));
$layoutCM 	= new FileLayout('category_modal', null, array('component' => 'com_phocagallery'));
// - - - - - - - - - -
// Images
// - - - - - - - - - -
if (!empty($this->items)) {

    $classBox = ' pg-category-items-box';
    $classItem = 'pg-item-box';
    if ($this->t['display_masonry']  == 1) {
        $classBox = ' pg-masonry';
        $classItem = 'pg-item-box pg-masonry-item';
    }
    echo '<div id="pg-msnr-container" class="pg-photoswipe pg-msnr-container'.$classBox.'" itemscope itemtype="http://schema.org/ImageGallery">' . "\n";


	foreach($this->items as $k => $item) {

		if ($this->checkRights == 1) {
			// USER RIGHT - Access of categories (if file is included in some not accessed category) - - - - -
			// ACCESS is handled in SQL query, ACCESS USER ID is handled here (specific users)
			$rightDisplay	= 0;
			if (!isset($item->cataccessuserid)) {
				$item->cataccessuserid = 0;
			}

			if (isset($item->catid) && isset($item->cataccessuserid) && isset($item->cataccess)) {
				$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $item->cataccessuserid, $item->cataccess, $this->t['user']->getAuthorisedViewLevels(), $this->t['user']->get('id', 0), 0);

			}
		} else {
			$rightDisplay = 1;
		}

		// Display back button to categories list
		if ($item->item_type == 'categorieslist'){
			$rightDisplay = 1;
		}

		if ($rightDisplay == 1) {

		    $this->items[$k]->rightdisplay = 1;

			echo '<div class="'.$classItem.'">'. "\n";// BOX START

			if ($this->t['detail_window'] == 14 && $item->type == 2) {
				echo '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">'. "\n";
			}

			// Image Box (image, category, folder)
            $d          = array();
			$d['item']  = $item;
			$d['t']     = $this->t;
			if ($item->type == 2 ) {
                echo $layoutBI->render($d);
			} else if ($item->type == 1) {


                echo $layoutBC->render($d);
			} else {
			    echo $layoutBB->render($d);
            }


            if ($this->t['detail_window'] == 14 && $item->type == 2){
                if (isset($item->photoswipecaption)) {
                    echo '<figcaption itemprop="caption description">' . $item->photoswipecaption . '</figcaption>'. "\n";
                }
                echo '</figure>';
		    }

            // HOT, NEW
			if ($item->type == 2) {
				//echo PhocaGalleryRenderFront::getOverImageIcons($item->date, $item->hits);

                if ($this->t['display_new'] != 0 || $this->t['display_hot'] != 0) {

                    echo '<div class="pg-category-box-label-box">';

                    if ($this->t['display_new'] != 0) {
                        $dateAdded  = strtotime($item->date, time());
                        $dateToday  = time();
                        $dateExists = $dateToday - $dateAdded;
                        $dateNew    = (int)$this->t['display_new'] * 24 * 60 * 60;
                        if ($dateExists < $dateNew) {
                            echo '<div class="pg-category-box-label-new">' . Text::_('COM_PHOCACART_LABEL_TXT_NEW') . '</div>';
                        }
                    }
                    if ($this->t['display_hot'] != 0) {
                        if ((int)$this->t['display_hot'] <= $item->hits) {
                            echo '<div class="pg-category-box-label-hot">' . Text::_('COM_PHOCACART_LABEL_TXT_HOT') . '</div>';
                        }
                    }

                    echo '</div>';
                }
			}

            echo '<div class="pg-item-box-info">';
            // Category name
            if ($item->type == 1) {
                if ($item->display_name == 1 || $item->display_name == 2) {
                    echo '<div class="pg-item-box-title category">' . "\n";
                    echo '<svg class="ph-si ph-si-category"><use xlink:href="#ph-si-category"></use></svg>' . "\n";
                    echo '<a href="' . Route::_($item->link) . '">' . $item->title . '</a>';
                    echo '</div>' . "\n";
                }
            }

            // Image Name
            if ($item->type == 2) {
                if ($item->display_name == 1 || $item->display_name == 2) {

                    echo '<div class="pg-item-box-title image pg-display-name-'.$item->display_name.'">' . "\n";

                    if ($item->display_name == 1) {
                        echo '<svg class="ph-si ph-si-image"><use xlink:href="#ph-si-image"></use></svg>' . "\n";
                        echo ' <a class="' . $item->class2 . '" title="' . htmlentities($item->oimgtitledetail, ENT_QUOTES, 'UTF-8') . '"'
                            . ' data-img-title="' . $item->title . '" href="' . Route::_($item->link2) . '"';

                        if ($item->onclick2 != '') {
                            echo 'onclick="' . $item->onclick2 . '"';
                        }
                        echo ' >';
                        echo '' . $item->title . '';
                        echo '</a>';

                    }
                    if ($item->display_name == 2) {
                        echo '&nbsp;';
                    }
                    echo '</div>' . "\n";
                }
            }


            // Rate Image
            if ($item->item_type == 'image') {


                if ($this->t['display_rating_img'] == 2) {
                    echo '<div class="pg-category-box-rating">';
                    echo PhocaGalleryRateImage::renderRateImg($item->id, $this->t['display_rating_img'], 1);
                    echo '</div>';
                } else if ($this->t['display_rating_img'] == 1) {
                    echo '<div class="pg-category-box-rating">';
                    echo '<a class="' . $item->class3 . '" title="' . Text::_('COM_PHOCAGALLERY_RATE_IMAGE') . '" data-img-title="' . $item->title . ' - ' . Text::_('COM_PHOCAGALLERY_RATE_IMAGE') . '"'
                        . ' href="' . Route::_('index.php?option=com_phocagallery&view=detail&catid=' . $item->catslug . '&id=' . $item->slug . $this->t['tmplcom'] . '&Itemid=' . $this->itemId) . '"';

                    //echo PhocaGalleryRenderFront::renderAAttributeOther($this->t['detail_window'], $item->buttonother->optionsrating, $this->t['highslideonclick'], $this->t['highslideonclick2']);
                    echo 'onclick="' . $item->onclick4 . '"';
                    echo ' >';

                    echo '<div><ul class="star-rating-small">'
                        . '<li class="current-rating" style="width:' . $item->voteswidthimg . 'px"></li>'
                        . '<li><span class="star1"></span></li>';
                    for ($iV = 2; $iV < 6; $iV++) {
                        echo '<li><span class="stars' . $iV . '"></span></li>';
                    }
                    echo '</ul></div>' . "\n";
                    echo '</a></div>' . "\n";
                }

            }


            if ($item->display_icon_detail == 1 ||
                $item->display_icon_download > 0 ||
                $item->display_icon_pc ||
                $item->trash == 1 ||
                $item->publish_unpublish == 1 ||
                $item->display_icon_geo == 1 ||
                $item->display_icon_commentimg == 1 ||
                $item->camera_info == 1 ||
                $item->display_icon_extlink1 == 1 ||
                $item->display_icon_extlink2 == 1 ||
                $item->camera_info == 1) {


                echo '<div class="pg-item-box-icons-box">';

                // ICON DETAIL
                if ($item->display_icon_detail == 1) {

                    echo ' <a class="' . $item->class2 . '" title="' . htmlentities($item->oimgtitledetail, ENT_QUOTES, 'UTF-8') . '"'
                        . ' data-img-title="' . $item->title . '" href="' . $item->link2 . '"';

                    if ($item->onclick2 != '') {
                        echo 'onclick="' . $item->onclick2 . '"';
                    }
                    echo ' >';
                    echo '<svg class="ph-si ph-si-view"><title>' . $item->oimgaltdetail . '</title><use xlink:href="#ph-si-view"></use></svg>';
                    echo '</a>';
                }

                // ICON DOWNLOAD
                if ($item->display_icon_download > 0) {
                    // Direct Download but not if there is a youtube
                    if ($item->display_icon_download == 2 && $item->videocode == '') {
                        echo ' <a title="' . Text::_('COM_PHOCAGALLERY_IMAGE_DOWNLOAD') . '"'
                            . ' href="' . Route::_('index.php?option=com_phocagallery&view=detail&catid=' . $item->catslug . '&id=' . $item->slug . $this->t['tmplcom'] . '&phocadownload=' . $item->display_icon_download . '&Itemid=' . $this->itemId) . '"';
                    } else {
                        echo ' <a class="' . $item->class3 . '" title="' . Text::_('COM_PHOCAGALLERY_IMAGE_DOWNLOAD') . '" data-img-title="' . $item->title . '"'
                            . ' href="' . Route::_('index.php?option=com_phocagallery&view=detail&catid=' . $item->catslug . '&id=' . $item->slug . $this->t['tmplcom'] . '&phocadownload=' . (int)$item->display_icon_download . '&Itemid=' . $this->itemId) . '"';

                        if ($item->onclick3 != '') {
                            echo 'onclick="' . $item->onclick3 . '"';
                        }

                    }
                    echo ' >';
                    echo '<svg class="ph-si ph-si-download"><use xlink:href="#ph-si-download"></use></svg>';

                    echo '</a>';
                }

                // ICON GEO
                if ($item->display_icon_geo == 1) {
                    echo ' <a class="' . $item->class3 . '" title="' . Text::_('COM_PHOCAGALLERY_GEOTAGGING') . '" data-img-title="' . $item->title . '"'
                        . ' href="' . Route::_('index.php?option=com_phocagallery&view=map&catid=' . $item->catslug . '&id=' . $item->slug . $this->t['tmplcom'] . '&Itemid=' . $this->itemId) . '"';

                    if ($item->onclick3 != '') {
                        echo 'onclick="' . $item->onclick3 . '"';
                    }

                    echo ' >';
                    //echo PhocaGalleryRenderFront::renderIcon('geo', $this->t['icon_path'].'icon-geo.png', JText::_('COM_PHOCAGALLERY_GEOTAGGING'));
                    echo '<svg class="ph-si ph-si-earth"><use xlink:href="#ph-si-earth"></use></svg>';
                    //echo HTMLHelper::_('image', $this->t['icon_path'].'icon-geo.png', JText::_('COM_PHOCAGALLERY_GEOTAGGING'));
                    echo '</a>';
                }

                // ICON EXIF
                if ($item->camera_info == 1) {
                    echo ' <a class="' . $item->class3 . '" title="' . Text::_('COM_PHOCAGALLERY_CAMERA_INFO') . '" data-img-title="' . $item->title . '"'
                        . ' href="' . Route::_('index.php?option=com_phocagallery&view=info&catid=' . $item->catslug . '&id=' . $item->slug . $this->t['tmplcom'] . '&Itemid=' . $this->itemId) . '"';

                    if ($item->onclick3 != '') {
                        echo 'onclick="' . $item->onclick3 . '"';
                    }
                    echo ' >';
                    //echo HTMLHelper::_('image', $this->t['icon_path'].'icon-info.png', JText::_('COM_PHOCAGALLERY_CAMERA_INFO'));
                    //echo PhocaGalleryRenderFront::renderIcon('camera', $this->t['icon_path'].'icon-info.png', JText::_('COM_PHOCAGALLERY_CAMERA_INFO'));
                    echo '<svg class="ph-si ph-si-camera"><use xlink:href="#ph-si-camera"></use></svg>';
                    echo '</a>';
                }

                // ICON COMMENT
                if ($item->display_icon_commentimg == 1) {
                    if ($this->t['detail_window'] == 7 || $this->t['display_comment_nopup'] == 1) {
                        $tClass = '';
                    } else {
                        $tClass = 'class="' . $item->class3 . '"';
                    }
                    //echo ' <a '.$tClass.' title="'.JText::_('COM_PHOCAGALLERY_COMMENT_IMAGE').'"'
                    //	.' href="'. JRoute::_('index.php?option=com_phocagallery&view=comment&catid='.$item->catslug.'&id='.$item->slug.$this->t['tmplcomcomments'].'&Itemid='. $this->itemId ).'"';

                    echo ' <a ' . $tClass . ' title="' . Text::_('COM_PHOCAGALLERY_COMMENT_IMAGE') . '" data-img-title="' . $item->title . '"'
                        . ' href="' . Route::_('index.php?option=com_phocagallery&view=detail&catid=' . $item->catslug . '&id=' . $item->slug . $this->t['tmplcomcomments'] . '&Itemid=' . $this->itemId) . '"';

                    if ($this->t['display_comment_nopup'] == 1) {
                        echo '';
                    } else {
                        if ($item->onclick3 != '') {
                            echo 'onclick="' . $item->onclick3 . '"';
                        }
                    }
                    echo ' >';
                    // If you go from RSS or administration (e.g. jcomments) to category view, you will see already commented image (animated icon)
                    $cimgid = $app->getInput()->get('cimgid', 0, 'int');
                    if ($cimgid > 0) {
                        //echo HTMLHelper::_('image', $this->t['icon_path'].'icon-comment-a.gif', JText::_('COM_PHOCAGALLERY_COMMENT_IMAGE'));
                        //echo PhocaGalleryRenderFront::renderIcon('comment-a', $this->t['icon_path'].'icon-comment-a.gif', JText::_('COM_PHOCAGALLERY_COMMENT_IMAGE'), 'ph-icon-animated');
                        echo '<svg class="pg-icon ph-icon-active pg-icon-comment-image"><use xlink:href="#ph-si-comment"></use></svg>';
                    } else {
                        //$commentImg = ($this->t['externalcommentsystem'] == 2) ? 'icon-comment-fb-small' : 'icon-comment';
                        //echo HTMLHelper::_('image', $this->t['icon_path'].$commentImg.'.png', JText::_('COM_PHOCAGALLERY_COMMENT_IMAGE'));
                        //if ($this->t['externalcommentsystem'] == 2) {
                        //	echo PhocaGalleryRenderFront::renderIcon('comment-fb', $this->t['icon_path'].'icon-comment-fb-small.png', JText::_('COM_PHOCAGALLERY_COMMENT_IMAGE'), 'ph-icon-fb');
                        //} else {
                        //echo PhocaGalleryRenderFront::renderIcon('comment', $this->t['icon_path'].'icon-comment.png', JText::_('COM_PHOCAGALLERY_COMMENT_IMAGE'));
                        echo '<svg class="ph-si ph-si-comment-image"><use xlink:href="#ph-si-comment"></use></svg>';
                        //}
                    }
                    echo '</a>';
                }

                // ICON EXTERNAL LINK 1
                if ($item->display_icon_extlink1 == 1) {

                    $pos10    = strpos($item->extlink1[0], 'http://');
                    $pos20    = strpos($item->extlink1[0], 'https://');
                    $extLink1 = 'http://' . $item->extlink1[0];
                    if ($pos10 === 0) {
                        $extLink1 = $item->extlink1[0];
                    } else if ($pos20 === 0) {
                        $extLink1 = $item->extlink1[0];
                    }

                    echo ' <a title="' . $item->extlink1[1] . '"'
                        . ' href="' . $extLink1 . '" target="' . $item->extlink1[2] . '" ' . $item->extlink1[5] . '>'
                        . $item->extlink1[4] . '</a>';
                }

                // ICON EXTERNAL LINK 2
                if ($item->display_icon_extlink2 == 1) {

                    $pos11    = strpos($item->extlink2[0], 'http://');
                    $pos21    = strpos($item->extlink2[0], 'https://');
                    $extLink2 = 'http://' . $item->extlink2[0];
                    if ($pos11 === 0) {
                        $extLink2 = $item->extlink2[0];
                    } else if ($pos21 === 0) {
                        $extLink2 = $item->extlink2[0];
                    }

                    echo ' <a title="' . $item->extlink2[1] . '"'
                        . ' href="' . $extLink2 . '" target="' . $item->extlink2[2] . '" ' . $item->extlink2[5] . '>'
                        . $item->extlink2[4] . '</a>';

                }

                // ICON Phoca Cart Product
                if ($item->display_icon_pc == 1) {
                    echo ' <a title="' . Text::_('COM_PHOCAGALLERY_ESHOP') . '" href="' . Route::_($item->pclink) . '">';
                    //echo HTMLHelper::_('image', $this->t['icon_path'].'icon-cart.png', JText::_('COM_PHOCAGALLERY_ESHOP'));
                    //echo PhocaGalleryRenderFront::renderIcon('cart', $this->t['icon_path'].'icon-cart.png', JText::_('COM_PHOCAGALLERY_ESHOP'));
                    echo '<svg class="ph-si ph-si-cart"><title>' . Text::_('COM_PHOCAGALLERY_ESHOP') . '</title><use xlink:href="#ph-si-cart"></use></svg>';
                    echo '</a>';
                }


                // ICON Trash for private categories
                if ($item->trash == 1) {

                    $url = PhocaGalleryRoute::getCategoryRoute($item->catid, $item->catalias);
                    $url .= '&removeid=' . (int)$item->id . '&controller=category&task=remove&' . Session::getFormToken() . '=1';

                    echo ' <a onclick="return confirm(\'' . Text::_('COM_PHOCAGALLERY_WARNING_DELETE_ITEMS') . '\')" title="' . Text::_('COM_PHOCAGALLERY_DELETE') . '" href="' . Route::_($url) . $this->t['limitstarturl'] . '">';
                    //echo HTMLHelper::_('image', $this->t['icon_path'].'icon-trash.png', JText::_('COM_PHOCAGALLERY_DELETE'));
                    //echo PhocaGalleryRenderFront::renderIcon('trash', $this->t['icon_path'].'icon-trash.png', JText::_('COM_PHOCAGALLERY_DELETE'));
                    echo '<svg class="ph-si ph-si-trash"><title>' . Text::_('COM_PHOCAGALLERY_DELETE') . '</title><use xlink:href="#ph-si-trash"></use></svg>';
                    echo '</a>';
                }

                // ICON Publish Unpublish for private categories
                if ($item->publish_unpublish == 1) {
                    if ($item->published == 1) {


                        $url = PhocaGalleryRoute::getCategoryRoute($item->catid, $item->catalias);
                        $url .= '&publishid=' . (int)$item->id . '&controller=category&task=unpublish&' . Session::getFormToken() . '=1';
                        echo ' <a title="' . Text::_('COM_PHOCAGALLERY_UNPUBLISH') . '" href="' . Route::_($url) . $this->t['limitstarturl'] . '">';

                        //echo HTMLHelper::_('image', $this->t['icon_path'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_UNPUBLISH'));
                        //echo PhocaGalleryRenderFront::renderIcon('publish', $this->t['icon_path'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_UNPUBLISH'));
                        echo '<svg class="ph-si ph-si-enabled"><title>' . Text::_('COM_PHOCAGALLERY_UNPUBLISH') . '</title><use xlink:href="#ph-si-enabled"></use></svg>';
                        echo '</a>';
                    }
                    if ($item->published == 0) {

                        $url = PhocaGalleryRoute::getCategoryRoute($item->catid, $item->catalias);
                        $url .= '&publishid=' . (int)$item->id . '&controller=category&task=publish&' . Session::getFormToken() . '=1';
                        echo ' <a title="' . Text::_('COM_PHOCAGALLERY_PUBLISH') . '" href="' . Route::_($url) . $this->t['limitstarturl'] . '">';

                        //echo HTMLHelper::_('image', $this->t['icon_path'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_PUBLISH'));
                        //echo PhocaGalleryRenderFront::renderIcon('unpublish', $this->t['icon_path'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_PUBLISH'));
                        echo '<svg class="ph-si ph-si-disabled"><title>' . Text::_('COM_PHOCAGALLERY_PUBLISH') . '</title><use xlink:href="#ph-si-disabled"></use></svg>';
                        echo '</a>';

                    }
                }

                // ICON Approve
                if ($item->approved_not_approved == 1) {
                    // Display the information about Approving too:
                    if ($item->approved == 1) {
                        echo ' <span title="' . Text::_('COM_PHOCAGALLERY_IMAGE_APPROVED') . '">'
                            //.PhocaGalleryRenderFront::renderIcon('publish', $this->t['icon_path'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_APPROVED'))
                            . '<svg class="ph-si ph-si-enabled"><title>' . Text::_('COM_PHOCAGALLERY_APPROVED') . '</title><use xlink:href="#ph-si-enabled"></use></svg>'
                            . '</span>';
                    }
                    if ($item->approved == 0) {
                        echo ' <span title="' . Text::_('COM_PHOCAGALLERY_IMAGE_NOT_APPROVED') . '">'
                            //.PhocaGalleryRenderFront::renderIcon('unpublish', $this->t['icon_path'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_NOT_APPROVED'))
                            . '<svg class="ph-si ph-si-disabled"><title>' . Text::_('COM_PHOCAGALLERY_NOT_APPROVED') . '</title><use xlink:href="#ph-si-disabled"></use></svg>'
                            . '</span>';

                    }
                }

                echo '</div>';


                /*echo '<div class="pg-icon-detail">';


        /*	if ($item->type == 2 && ($this->t['display_comment_img'] == 2 || $this->t['display_comment_img'] == 3)) {
                echo '<div class="pg-cv-comment-img-box">';

                if (isset($item->comment_items)) {

                    foreach($item->comment_items as $cok => $cov) {
                        echo '<div class="pg-cv-comment-img-box-item">';
                        echo '<div class="pg-cv-comment-img-box-avatar">';
                        $img = '<div style="width: 20px; height: 20px;">&nbsp;</div>';
                        if (isset($cov->avatar) && $cov->avatar != '') {
                            $pathAvatarAbs	= $this->t['path']->avatar_abs  .'thumbs/phoca_thumb_s_'. $cov->avatar;
                            $pathAvatarRel	= $this->t['path']->avatar_rel . 'thumbs/phoca_thumb_s_'. $cov->avatar;
                            if (PhocaGalleryFile::exists($pathAvatarAbs)){
                                $avSize = getimagesize($pathAvatarAbs);
                                $avRatio = $avSize[0]/$avSize[1];
                                $avHeight = 20;
                                $avWidth = 20 * $avRatio;
                                $img = '<img src="'.Uri::base().'/'.$pathAvatarRel.'" width="'.(int)$avWidth.'" height="'.(int)$avHeight.'" alt="" />';
                            }
                        }
                        echo $img;
                        echo '</div>';
                        echo '<div class="pg-cv-comment-img-box-comment">'.$cov->name.': '.$cov->comment.'</div>';
                        echo '<div style="clear:both"></div>';
                        echo '</div>';
                    }
                }
                echo '<div id="pg-cv-comment-img-box-result'.$item->id.'"></div>';//AJAX
                //echo '<div id="pg-cv-comment-img-box-newcomment'.$item->id.'"></div>';//AJAX

                // href="javascript:void(0);"
                echo '<div class="pg-tb-m5"><button class="btn btn-mini" onclick="javascript:document.getElementById(\'pg-cv-add-comment-img'.$item->id.'\').style.display = \'block\';var wall = new Masonry( document.getElementById(\'pg-msnr-container\'), {});">'.Text::_('COM_PHOCAGALLERY_COMMENT').'</button></div>';
                echo '<div id="pg-cv-add-comment-img'.$item->id.'" class="pg-cv-add-comment-img">';

                if (isset($item->allready_commented)) {
                    if ($item->allready_commented == 1) {
                        echo '<p>'.Text::_('COM_PHOCAGALLERY_COMMENT_ALREADY_SUBMITTED').'</p>';
                    } else if ($this->t['not_registered']) {
                        echo '<p>'.Text::_('COM_PHOCAGALLERY_COMMENT_ONLY_REGISTERED_LOGGED_SUBMIT_COMMENT').'</p>';

                    } else {

                        ///echo '<form id="pgcvcommentimg'.$item->id.'" method="post" >';
                        echo '<textarea name="pg-cv-comments-editor-img'.(int)$item->id.'" id="pg-cv-comments-editor-img'.(int)$item->id.'"  rows="2"  class= "comment-input" ></textarea>';

                        echo '<button onclick="pgCommentImage('.(int)$item->id.', '.$this->t['diff_thumb_height'].', \'pg-msnr-container\');document.getElementById(\'pg-cv-add-comment-img'.$item->id.'\').style.display = \'none\';var wall = new Masonry( document.getElementById(\'pg-msnr-container\'), {});" class="btn btn-small" type="submit" id="phocagallerycommentssubmitimg">'. Text::_('COM_PHOCAGALLERY_SUBMIT_COMMENT').'</button>';
                        ?>
                        <input type="hidden" name="catid" value="<?php echo $item->catid ?>"/>
                        <input type="hidden" name="imgid" value="<?php echo $item->id ?>"/>
                        <input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/> <?php
                        echo HTMLHelper::_( 'form.token' );
                        ///echo '</form>';
                    }
                }

                echo '</div>';
                echo '</div>';*/

            }


            // Tags
            if ($item->type == 2 && isset($item->otags) && $item->otags != '') {
                echo '<div class="ph-cb"></div>';
                echo '<div class="pg-item-box-tags">' . $item->otags . '</div>' . "\n";
            }

            echo '<div class="ph-cb"></div>';
            // Description in Box
            if ($this->t['display_img_desc_box'] == 1 && $item->description != '') {

                echo '<div class="pg-item-box-descripton">' . strip_tags($item->description) . '</div>' . "\n";
            } else if ($this->t['display_img_desc_box'] == 2 && $item->description != '') {

                echo '<div class="pg-item-box-descripton">' . (HTMLHelper::_('content.prepare', $item->description, 'com_phocagallery.image')) . '</div>' . "\n";
            }

            echo '</div>';// ph-item-box-info
			echo '</div>'; // BOX END

		}
	}

	echo '</div>'; // End category box items

    // Modal
    $d          = array();
    $d['t']     = $this->t;
	echo $layoutCM->render($d);


} else {
	//echo JText::_('COM_PHOCAGALLERY_THERE_IS_NO_IMAGE');
}
