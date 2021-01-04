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
defined('_JEXEC') or die('Restricted access');

$this->cv = new stdClass();
echo '<div id="pg-msnr-container">';

foreach ($this->categories as $ck => $cv) {


    echo '<div class="pg-csv-box">' . "\n";
    echo ' <div class="pg-csv-box-img pg-box1">' . "\n";
    echo '  <div class="pg-box2">' . "\n";
    echo '   <div class="pg-box3">' . "\n";

    echo '<a href="' . $cv->link . '">' . "\n";

    if (isset($cv->mosaic) && $cv->mosaic != '') {
        echo $cv->mosaic;
    } else if (isset($cv->extpic) && $cv->extpic != '') {
        $correctImageRes = PhocaGalleryPicasa::correctSizeWithRate($cv->extw, $cv->exth, $this->t['picasa_correct_width'], $this->t['picasa_correct_height']);
        //echo Joomla\CMS\HTML\HTMLHelper::_( 'image', $cv->linkthumbnailpath, $cv->title, array('width' => $correctImageRes['width'], 'height' => $correctImageRes['height']));
        echo Joomla\CMS\HTML\HTMLHelper::_('image', $cv->linkthumbnailpath, $cv->title, array('style' => 'width:' . $correctImageRes['width'] . 'px;height:' . $correctImageRes['height'] . 'px;'));

    } else {

        echo Joomla\CMS\HTML\HTMLHelper::_('image', $cv->linkthumbnailpath, $cv->title);
    }

    echo '</a>' . "\n";

    echo '    </div>' . "\n";
    echo '  </div>' . "\n";
    echo ' </div>' . "\n";

    if ($this->t['bootstrap_icons'] == 0) {
        $cls  = 'class="pg-csv-name"';
        $icon = '';
    } else {
        $cls  = 'class="pg-csv-name-i"';
        $icon = PhocaGalleryRenderFront::renderIcon('category', '', '') . ' ';
    }
    echo '<div class="pg-box-img-bottom">';
    echo '<div ' . $cls . '>' . $icon . '<a href="' . $cv->link . '">' . PhocaGalleryText::wordDelete($cv->title_self, $this->t['char_cat_length_name'], '...') . '</a>';
    if ($cv->numlinks > 0) {
        echo ' <span class="pg-csv-count">(' . $cv->numlinks . ')</span>' . "\n";
    }
    echo '</div>' . "\n";


    if ($this->t['display_cat_desc_box'] == 1 && $cv->description != '') {
        echo '<div class="pg-csv-descbox">' . strip_tags($cv->description) . '</div>';
    } else if ($this->t['display_cat_desc_box'] == 2 && $cv->description != '') {
        echo '<div class="pg-csv-descbox">' . (Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $cv->description, 'com_phocagallery.category')) . '</div>';
    }

    $this->cv = $cv;
    echo $this->loadTemplate('rating');

    echo '</div>' . "\n";// End pg-box-img-bottom
    echo '</div>' . "\n";// End pg-csv-box
}
echo '</div>';
echo '<div class="ph-cb"></div>';

/*
// Test icons:
$iconsA = array('view','download','geo','bold','italic','underline','camera','comment','comment-a','comment-fb','cart','extlink1','extlinkk2','trash','publish','unpublish','viewed','calendar','vote','statistics','category','subcategory','upload','upload-ytb','upload-multiple','upload-java','user','icon-up-images','icon-up','minus-sign','next','prev','reload','play','stop','pause','off','image','save');
echo '<textarea>';
foreach($iconsA as $k => $v) {
	//echo '<div>'.$v.': '.PhocaGalleryRenderFront::renderIcon($v, '', '').'</div>';
	echo '.ph-icon-'.$v.' {color: #fff;}'. "\n";
}
echo '</textarea>';
*/
?>
