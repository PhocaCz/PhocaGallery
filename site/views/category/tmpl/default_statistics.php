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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;


defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

$layoutBI 	= new FileLayout('box_image', null, array('component' => 'com_phocagallery'));

if ($this->t['detail_window'] == 14) {
		 echo '<div id="phocagallery-statistics" class="pg-photoswipe" itemscope itemtype="http://schema.org/ImageGallery">';
    //echo '<div id="phocagallery-statistics">';
	} else {
		echo '<div id="phocagallery-statistics">';
	}



	if ($this->t['displaymaincatstat']) {
		echo '<h4>'.Text::_('COM_PHOCAGALLERY_CATEGORY').'</h4>'
		.'<table>'
		.'<tr><td>'.Text::_('COM_PHOCAGALLERY_NR_PUBLISHED_IMG_CAT') .': </td>'
		.'<td>'.$this->t['numberimgpub'].'</td></tr>'
		.'<tr><td>'.Text::_('COM_PHOCAGALLERY_NR_UNPUBLISHED_IMG_CAT') .': </td>'
		.'<td>'.$this->t['numberimgunpub'].'</td></tr>'
		.'<tr><td>'.Text::_('COM_PHOCAGALLERY_CATEGORY_VIEWED') .': </td>'
		.'<td>'.$this->t['categoryviewed'].' x</td></tr>'
		.'</table>';
	}

// MOST VIEWED
if ($this->t['displaymostviewedcatstat']) {



	echo '<h4>'.Text::_('COM_PHOCAGALLERY_MOST_VIEWED_IMG_CAT').'</h4>';

	echo '<div id="pg-msnr-container" class="pg-photoswipe pg-msnr-container pg-category-items-box" itemscope itemtype="http://schema.org/ImageGallery">' . "\n";

	if (!empty($this->t['mostviewedimg'])) {
		foreach($this->t['mostviewedimg'] as $key => $item) {


		    echo '<div class="pg-item-box">'. "\n";// BOX START

			if ($this->t['detail_window'] == 14 && $item->type == 2) {
				echo '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">'. "\n";
			}

			// Image Box (image, category, folder)
            $d          = array();
			$d['item']  = $item;
			$d['t']     = $this->t;
			echo $layoutBI->render($d);



            if ($this->t['detail_window'] == 14 && $item->type == 2){
                if (isset($item->photoswipecaption)) {
                    echo '<figcaption itemprop="caption description">' . $item->photoswipecaption . '</figcaption>'. "\n";
                }
                echo '</figure>';
		    }

            // Image Name
            echo '<div class="pg-item-box-title image">'. "\n";
            echo '<svg class="ph-si ph-si-image"><use xlink:href="#ph-si-image"></use></svg>'. "\n";
            echo $item->title;
            echo '<div class="pg-item-box-stats-value">'.$item->hits.' <small>x</small></div>';
            echo '</div>'. "\n";

            echo '</div>';

		}

	}

	echo '</div>';

} // END MOST VIEWED


// LAST ADDED
if ($this->t['displaylastaddedcatstat']) {


	echo '<h4>'.Text::_('COM_PHOCAGALLERY_LAST_ADDED_IMG_CAT').'</h4>';

	echo '<div id="pg-msnr-container" class="pg-photoswipe pg-msnr-container pg-category-items-box" itemscope itemtype="http://schema.org/ImageGallery">' . "\n";

	if (!empty($this->t['lastaddedimg'])) {
		foreach($this->t['lastaddedimg'] as $key => $item) {


		    echo '<div class="pg-item-box">'. "\n";// BOX START

			if ($this->t['detail_window'] == 14 && $item->type == 2) {
				echo '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">'. "\n";
			}

			// Image Box (image, category, folder)
            $d          = array();
			$d['item']  = $item;
			$d['t']     = $this->t;
			echo $layoutBI->render($d);



            if ($this->t['detail_window'] == 14 && $item->type == 2){
                if (isset($item->photoswipecaption)) {
                    echo '<figcaption itemprop="caption description">' . $item->photoswipecaption . '</figcaption>'. "\n";
                }
                echo '</figure>';
		    }

            // Image Name
            echo '<div class="pg-item-box-title image">'. "\n";
            echo '<svg class="ph-si ph-si-image"><use xlink:href="#ph-si-image"></use></svg>'. "\n";
            echo $item->title;
            echo '<div class="pg-item-box-stats-value">'.HTMLHelper::Date($item->date, "d. m. Y").' <small>x</small></div>';
            echo '</div>'. "\n";

            echo '</div>';

		}

	}

	echo '</div>';

} // END LAST ADDED
/*
// LAST ADDED
if ($this->t['displaylastaddedcatstat']) {


	echo '<h4>'.Text::_('COM_PHOCAGALLERY_LAST_ADDED_IMG_CAT').'</h4>';

	if (!empty($this->t['lastaddedimg'])) {

		foreach($this->t['lastaddedimg'] as $key => $value) {

			$extImage = PhocaGalleryImage::isExtImage($value->extid);
			if ($extImage) {
				$correctImageRes = PhocaGalleryPicasa::correctSizeWithRate($value->extw, $value->exth, $this->t['picasa_correct_width_m'], $this->t['picasa_correct_height_m']);
			}

			?><div class="pg-cv-box pg-cv-box-stat">
				<div class="pg-cv-box-img pg-box1">
					<div class="pg-box2">
						<div class="pg-box3"><?php

							if ($this->t['detail_window'] == 14) {
								echo '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">';
							}

							?><a class="<?php echo $value->button->methodname; ?>"<?php
							echo ' href="'. $value->link.'"';

							//Correction (to not be in conflict - statistics vs. standard images)
							// e.g. shadowbox shadowbox[PhocaGallery] --> shadowbox[PhocaGallery3]
							$options4 = str_replace('[PhocaGallery]', '[PhocaGallery4]', $value->button->options);

							echo PhocaGalleryRenderFront::renderAAttributeStat($this->t['detail_window'], $options4, '', $this->t['highslideonclick'], $this->t['highslideonclick2'], '', $this->category->alias, 'la');

							if (isset($value->datasize)) {
								echo ' '. $value->datasize;
							}

							echo ' >';
							if ($extImage) {
								echo HTMLHelper::_( 'image', $value->linkthumbnailpath, $value->altvalue, array('width' => $correctImageRes['width'], 'height' => $correctImageRes['height'], 'class' => 'pg-image', 'itemprop' => "thumbnail"));
							} else {
								echo HTMLHelper::_( 'image', $value->linkthumbnailpath, $value->altvalue, array('class' => 'pg-image', 'itemprop' => "thumbnail") );
							}
							?></a><?php

							if ($this->t['detail_window'] == 14) {
								if ($this->t['photoswipe_display_caption'] == 1) {
									echo '<figcaption itemprop="caption description">'. $value->title.'</figcaption>';
								}
								echo '</figure>';
							}
						?></div>
					</div>
				</div><?php

			// subfolder
			if ($value->type == 1) {
				if ($value->display_name == 1 || $value->display_name == 2) {
					echo '<div class="pg-name">'.$value->title.'</div>';
				}
			}
			// image
			if ($value->type == 2) {
				if ($value->display_name == 1) {
					echo '<div class="pg-name">'.$value->title.'</div>';
				}
				if ($value->display_name == 2) {
					echo '<div class="pg-name">&nbsp;</div>';
				}
			}

			echo '<div class="detail" style="margin-top:2px;text-align:left">';
			//echo JHtml::_('image', 'media/com_phocagallery/images/icon-date.png', JText::_('COM_PHOCAGALLERY_IMAGE_DETAIL'));
			echo PhocaGalleryRenderFront::renderIcon('calendar', 'media/com_phocagallery/images/icon-date.png', Text::_('COM_PHOCAGALLERY_IMAGE_DETAIL'));
			echo '&nbsp;&nbsp; '.HTMLHelper::Date($value->date, "d. m. Y");
			echo '</div>';
			echo '<div class="ph-cb"></div>';
			echo '</div>';
		}
		echo '<div class="ph-cb"></div>';
	}
}// END MOST VIEWED
*/
echo '</div>'. "\n";
?>
