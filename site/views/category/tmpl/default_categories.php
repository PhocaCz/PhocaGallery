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

defined('_JEXEC') or die('Restricted access');
echo '<div id="phocagallery-categories-detail" class="pg-category-categories-top-box">'."\n";
foreach ($this->itemscv as $k => $item) {

	echo '<div class="pg-category-categories-top-box-title">';


	if ($item->type == 3) {
	    echo '<svg class="ph-si ph-si-category-top-back"><use xlink:href="#ph-si-back"></use></svg>';
    } else {
	    echo '<svg class="ph-si ph-si-category-top-category"><use xlink:href="#ph-si-category"></use></svg>';
    }



    echo '<a href="' . Route::_($item->link) . '">' . $item->title. '</a>';
    echo $item->numlinks > 0 ? ' <span class="pg-category-box-count">(' . $item->numlinks . ')</span>' : '';
    echo '</div>';
}
echo '</div>'."\n";
?>
