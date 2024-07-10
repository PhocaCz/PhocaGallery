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


echo '<a class="'.$item->class.'" href="'. $item->link.'" data-img-title="'.$item->title.'" id="pgImg'.$item->id.'"';

if (isset($item->onclick) && $item->onclick != '') {
    echo ' onclick="'.$item->onclick.'"';
}

if (isset($item->itemprop) && $item->itemprop != '') {
    echo ' itemprop="'.$item->itemprop.'"';
}

if (isset($item->datasize)) { echo ' '. $item->datasize;}

if (isset($item->videocode) && $item->videocode != '' && $item->videocode != '0') {
    echo ' data-type="video" data-video="<div class=\'ph-pswp-wrapper\'><div class=\'ph-pswp-video-wrapper\'>' . str_replace('"', "'", PhocaGalleryYoutube::displayVideo($item->videocode)) . '</div></div>"';
}

echo ' >';

echo '<div class="pg-item-box-image">';
echo HTMLHelper::_( 'image', isset($item->extid) & $item->extid !=  '' ? $item->extm : $item->linkthumbnailpath, $item->oimgalt, array( 'class' => 'pg-image c-Image c-Image--shaded', 'itemprop' => "thumbnail"));

echo '</div>';

echo '</a>';
