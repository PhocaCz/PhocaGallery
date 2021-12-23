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

// Make SVG image the same responsive like the standard images - get the same ratio like images for the box and have the max width of the images
$styleDiv = ' style="max-width:'.$t['medium_image_width'].'px;aspect-ratio: '.(int)$t['medium_image_width'].'/'.(int)$t['medium_image_height']. '"';
$styleSVG = ' style="width:100%;height:100%"';

echo '<a class="'.$item->button->methodname.'" href="'. $item->link.'">';
echo '<div class="pg-item-box-image pg-svg-box"'.$styleDiv.'>';
if (isset($item->rightdisplaykey) && $item->rightdisplaykey == 0) {
    echo '<svg alt="'.$item->altvalue.'" class="ph-si ph-si-lock-medium pg-image c-Image c-Image--shaded"'.$styleSVG.' itemprop="thumbnail"><use xlink:href="#ph-si-lock"></use></svg>';
} else {
    echo '<svg alt="'.$item->altvalue.'" class="ph-si ph-si-back-medium pg-image c-Image c-Image--shaded"'.$styleSVG.' itemprop="thumbnail"><use xlink:href="#ph-si-back"></use></svg>';
}
echo '</div>';
echo '</a>';


