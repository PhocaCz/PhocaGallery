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
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');

$layoutSVG 	= new FileLayout('svg_definitions', null, array('component' => 'com_phocagallery'));

// SVG Definitions
$d          = array();
echo $layoutSVG->render($d);


if ($this->t['detailwindow'] == 7) {
	echo '<div class="pg-detail-top-box-back-title">';
	echo '<a href="'.Route::_('index.php?option=com_phocagallery&view=category&id='. $this->info->catslug.'&Itemid='. $this->itemId).'"';
	echo ' title="'.Text::_( 'COM_PHOCAGALLERY_BACK_TO_CATEGORY' ).'">';
	echo '<svg class="ph-si ph-si-detail-top-back"><use xlink:href="#ph-si-back"></use></svg>';
	echo '</a></div>';
}


echo '<div id="phoca-exif" class="pg-info-view'.$this->params->get( 'pageclass_sfx' ).'">'
.'<h1 class="phocaexif">'.Text::_('COM_PHOCAGALLERY_EXIF_INFO').':</h1>'
.'<table>'
.$this->t['infooutput']
.'</table>'
.'</div>';
if ($this->t['detailwindow'] == 7) {
    echo PhocaGalleryUtils::getExtInfo();
}
