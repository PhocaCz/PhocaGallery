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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

HTMLHelper::_('jquery.framework', false);

$layoutSVG 	= new FileLayout('svg_definitions', null, array('component' => 'com_phocagallery'));

// SVG Definitions
$d          = array();
echo $layoutSVG->render($d);


$document	= Factory::getDocument();
// jQuery(\'input[type=file]\').click(function(){
$document->addScriptDeclaration(
'jQuery(document).ready(function(){
	jQuery(\'.phfileuploadcheckcat\').click(function(){
	if( !jQuery(\'#filter_catid_image\').val() || jQuery(\'#filter_catid_image\').val() == 0) { 
		alert(\''.Text::_('COM_PHOCAGALLERY_PLEASE_SELECT_CATEGORY', true).'\'); return false;
	} else {
		return true;
	}
})});'
);

echo '<div id="phocagallery-ucp" class="pg-ucp-view'.$this->params->get( 'pageclass_sfx' ).'">'. "\n";

$heading = '';
if ($this->params->get( 'page_title' ) != '') {
	$heading .= $this->params->get( 'page_title' );
}

if ($this->t['showpageheading'] != 0) {
	if ( $heading != '') {
	    echo '<h1>'
	        .$this->escape($heading)
			.'</h1>';
	}
}
$tab = 0;
switch ($this->t['tab']) {
	case 'up':
		$tab = 1;
	break;

	case 'cc':
	default:
		$tab = 0;
	break;
}

echo '<div>&nbsp;</div>';

if ($this->t['displaytabs'] > 0) {
	//echo '<div id="phocagallery-pane">';

	$tabItems = array();
	phocagalleryimport('phocagallery.render.rendertabs');
	$tabs = new PhocaGalleryRenderTabs();
	echo $tabs->startTabs();

	$tabItems[0] = array('id' => 'user', 'title' => Text::_('COM_PHOCAGALLERY_USER'), 'image' => 'user', 'icon' => 'user');
	$tabItems[1] = array('id' => 'category', 'title' => $this->t['categorycreateoredithead'], 'image' => 'folder-small', 'icon' => 'category');
	$tabItems[2] = array('id' => 'subcategories', 'title' => Text::_('COM_PHOCAGALLERY_SUBCATEGORIES'), 'image' => 'subcategories', 'icon' => 'category');
	$tabItems[3] = array('id' => 'images', 'title' => Text::_('COM_PHOCAGALLERY_IMAGES'), 'image' => 'images', 'icon' => 'image');

	$tabs->setActiveTab(isset($tabItems[$this->t['tab']]['id']) ? $tabItems[$this->t['tab']]['id'] : 0);
	echo $tabs->renderTabsHeader($tabItems);

	echo $tabs->startTab('user');
	echo $this->loadTemplate('user');
	echo $tabs->endTab();

	echo $tabs->startTab('category');
	echo $this->loadTemplate('category');
	echo $tabs->endTab();

	echo $tabs->startTab('subcategories');
	echo $this->loadTemplate('subcategories');
	echo $tabs->endTab();

	echo $tabs->startTab('images');
	echo $this->loadTemplate('images');
	echo $tabs->endTab();

	echo $tabs->endTabs();

	/*
	echo HTMLHelper::_('tabs.start', 'config-tabs-com_phocagallery-user', array('useCookie'=>1, 'startOffset'=> $this->t['tab']));
	echo HTMLHelper::_('tabs.panel', PhocaGalleryRenderFront::renderIcon('user', $this->t['pi'].'icon-user.png', '') . '&nbsp;'.Text::_('COM_PHOCAGALLERY_USER'), 'user' );
	echo $this->loadTemplate('user');

	echo HTMLHelper::_('tabs.panel', PhocaGalleryRenderFront::renderIcon('category', $this->t['pi'].'icon-folder-small.png', '') . '&nbsp;'.$this->t['categorycreateoredithead'], 'category' );
	echo $this->loadTemplate('category');

	echo HTMLHelper::_('tabs.panel', PhocaGalleryRenderFront::renderIcon('subcategory', $this->t['pi'].'icon-subcategories.png', ''). '&nbsp;'.Text::_('COM_PHOCAGALLERY_SUBCATEGORIES'), 'subcategories' );
	echo $this->loadTemplate('subcategories');

	echo HTMLHelper::_('tabs.panel', PhocaGalleryRenderFront::renderIcon('image', $this->t['pi'].'icon-images.png', ''). '&nbsp;'.Text::_('COM_PHOCAGALLERY_IMAGES'), 'images' );
	echo $this->loadTemplate('images');

	echo JHtml::_('tabs.end');*/
	//echo '</div>';
}
echo '<div>&nbsp;</div>';
echo PhocaGalleryUtils::getExtInfo();
echo '</div>';
?>
