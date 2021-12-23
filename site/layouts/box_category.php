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
defined('_JEXEC') or die('Restricted access');
$d      = $displayData;
$item   = $d['item'];
$t      = $d['t'];


echo '<a class="'.$item->button->methodname.'" href="'. $item->link.'">';


if ($item->linkthumbnailpath != false || (isset($item->extid) && (int)$item->extid > 0)) {


    echo '<div class="pg-item-box-image pg-svg-box">';
    echo HTMLHelper::_( 'image', isset($item->extid) & (int)$item->extid > 0 ? $item->extm : $item->linkthumbnailpath, $item->altvalue, array( 'class' => 'pg-image c-Image c-Image--shaded', 'itemprop' => "thumbnail"));
    echo '</div>';
    echo '</a>';

} else {
    echo '<div class="pg-item-box-image pg-svg-box">';

    if (isset($item->rightdisplaykey) && $item->rightdisplaykey == 0) {
        echo '<svg alt="'.$item->altvalue.'" class="ph-si ph-si-lock-medium pg-image c-Image c-Image--shaded" style="width:'.$t['medium_image_width'].'px;height:'.$t['medium_image_height'].'px" itemprop="thumbnail"><use xlink:href="#ph-si-lock"></use></svg>';
    } else {
        echo '<svg alt="'.$item->altvalue.'" class="ph-si ph-si-category-medium pg-image c-Image c-Image--shaded" style="width:'.$t['medium_image_width'].'px;height:'.$t['medium_image_height'].'px" itemprop="thumbnail"><use xlink:href="#ph-si-category"></use></svg>';
    }

    echo '</div>';
    echo '</a>';

}
/*
echo '<div class="pg-item-box-image">';
echo HTMLHelper::_( 'image', isset($item->extid) & (int)$item->extid> 0 ? $item->extm : $item->linkthumbnailpath, $item->altvalue, array( 'class' => 'pg-image c-Image c-Image--shaded', 'itemprop' => "thumbnail"));


echo '</div>';
*/






