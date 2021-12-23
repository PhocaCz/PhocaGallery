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

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die('Restricted access');

echo '<div class="pg-categories-items-box">';


foreach ($this->categories as $k => $item) {

    echo '<div class="pg-category-box">';



    if (isset($item->rightdisplaykey) && $item->rightdisplaykey == 0) {

        echo '<div class="pg-category-box-image pg-svg-box">';
        echo '<svg alt="' . htmlspecialchars($item->title) . '" class="ph-si ph-si-lock-medium pg-image c-Image c-Image--shaded" style="width:' . $this->t['medium_image_width'] . 'px;height:' . $this->t['medium_image_height'] . 'px" itemprop="thumbnail"><use xlink:href="#ph-si-lock"></use></svg>';
        echo '</div>';
    } else {

        if($this->t['image_categories_size'] == 2  || $this->t['image_categories_size'] == 3 || $item->linkthumbnailpath == '') {
            // Folders instead of icons
            echo '<div class="pg-category-box-image pg-svg-box">';
            echo '<a href="' . Route::_($item->link) . '"><svg alt="' . htmlspecialchars($item->title) . '" class="ph-si ph-si-category pg-image c-Image c-Image--shaded" style="width:' . $this->t['imagewidth'] . 'px;height:' . $this->t['imageheight'] . 'px" itemprop="thumbnail"><use xlink:href="#ph-si-category"></use></svg></a>';
            echo '</div>';
        } else {
            // Images
            echo '<div class="pg-category-box-image">';
            echo '<a href="' . Route::_($item->link) . '">' . HTMLHelper::_('image', $item->linkthumbnailpath, $item->title) . '</a>';
            echo '</div>';
        }



    }

    echo '<div class="pg-category-box-info">';
    echo '<div class="pg-category-box-title">';
    echo '<svg class="ph-si ph-si-category"><use xlink:href="#ph-si-category"></use></svg>';
    echo '<a href="' . Route::_($item->link) . '">' . $item->title_self. '</a>';
    echo $item->numlinks > 0 ? ' <span class="pg-category-box-count">(' . $item->numlinks . ')</span>' : '';
    echo '</div>';


    if ($this->t['display_cat_desc_box'] == 1 && $item->description != '') {
        echo '<div class="pg-category-box-description">' . strip_tags($item->description) . '</div>';
    } else if ($this->t['display_cat_desc_box'] == 2 && $item->description != '') {
        echo '<div class="pg-category-box-description">' . (HTMLHelper::_('content.prepare', $item->description, 'com_phocagallery.category')) . '</div>';
    }

    $this->cv = $item;
    echo $this->loadTemplate('rating');

    echo '</div>';// pg-category-box-info
    echo '</div>';// pg-category-box
}
echo '</div>';

?>
