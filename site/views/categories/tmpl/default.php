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
use Joomla\CMS\Router\Route;
$layoutSVG 	= new FileLayout('svg_definitions', null, array('component' => 'com_phocagallery'));

// SVG Definitions
$d          = array();
echo $layoutSVG->render($d);

echo '<div id="phocagallery" class="pg-categories-view'.$this->params->get( 'pageclass_sfx' ).' pg-csv">';

if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<div class="page-header"><h1>'. $this->escape($this->params->get('page_heading')) . '</h1></div>';
}


if ($this->t['display_feed'] == 1 || $this->t['display_feed'] == 2 ) {

    echo '<div class="pg-top-icons">';
    echo '<a href="' . Route::_(PhocaGalleryRoute::getFeedRoute('categories')) . '" title="' . Text::_('COM_PHOCAGALLERY_RSS') . '"><svg class="ph-si ph-si-feed"><use xlink:href="#ph-si-feed"></use></svg></a>';
    echo '</div>';
    echo '<div class="ph-cb"></div>';
}

if ($this->t['categories_description'] != '') {
	echo '<div class="pg-categories-desc" >'.HTMLHelper::_('content.prepare', $this->t['categories_description']).'</div>';
}

echo $this->loadTemplate('categories');
echo $this->loadTemplate('pagination');
echo PhocaGalleryUtils::getExtInfo();
echo '</div>';
