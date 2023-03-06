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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;

$layoutSVG 	= new FileLayout('svg_definitions', null, array('component' => 'com_phocagallery'));
$layoutC 	= new FileLayout('comments', null, array('component' => 'com_phocagallery'));

// SVG Definitions
$d          = array();
echo $layoutSVG->render($d);

if ($this->t['ytb_display'] == 1) {

	/*$document	= Factory::getDocument();
	/$document->addCustomTag( "<style type=\"text/css\"> \n"
			." body {overflow:hidden;} \n"
			." </style> \n");*/

	echo '<div class="pg-ytb-full">'.$this->item->videocode.'</div>';
} else {




	echo '<div id="phocagallery" class="pg-detail-item-box'.$this->params->get( 'pageclass_sfx' ).'">';


if ($this->t['detailwindow'] == 7) {
	echo '<div class="pg-detail-top-box-back-title">';
	echo '<a href="'.Route::_('index.php?option=com_phocagallery&view=category&id='. $this->item->catslug.'&Itemid='. $this->itemId).'"';
	echo ' title="'.Text::_( 'COM_PHOCAGALLERY_BACK_TO_CATEGORY' ).'">';
	echo '<svg class="ph-si ph-si-detail-top-back"><use xlink:href="#ph-si-back"></use></svg>';
	echo '</a></div>';
}

switch ($this->t['detailwindow']) {
	case 4:
	case 7:
	case 9:
	case 10:
	case 11:
		$closeImage 	= $this->item->linkimage;
		$closeButton 	= '';
	break;


	default:
		$closeButton 	= str_replace("%onclickclose%", $this->t['detailwindowclose'], $this->item->closebutton);
		$closeImage 	= '<a href="#" onclick="'.$this->t['detailwindowclose'].'" style="margin:auto;padding:0">'.$this->item->linkimage.'</a>';
	break;

}

$classSuffix = ' popup';
if ($this->t['detailwindow'] == 7) {
	$classSuffix = ' no-popup';
}
/*
echo '<div class="ph-mc" style="padding-top:10px">'
	.'<table border="0" class="ph-w100 ph-mc" cellpadding="0" cellspacing="0">'
	.'<tr>'
	.'<td colspan="6" align="center" valign="middle"'
	.' style="'.$iH.'vertical-align: middle;" >'
	.'<div id="phocaGalleryImageBox" style="'.$iW.'margin: auto;padding: 0;">'
	.$closeImage;
*/


//echo '<div class="pg-detail-item-image-box">'.$closeImage.'</div>';

echo '<div class="pg-ytb-detail">'.$this->item->videocode.'</div>';



$titleDesc = '';
if ($this->t['display_title_description'] == 1) {
	$titleDesc .= $this->item->title;
	if ($this->item->description != '' && $titleDesc != '') {
		$titleDesc .= ' - ';
	}
}

// Lightbox Description
if ($this->t['displaydescriptiondetail'] == 2 && (!empty($this->item->description) || !empty($titleDesc))){

	echo '<div class="pg-detail-item-desc-box">' .(HTMLHelper::_('content.prepare', $titleDesc . $this->item->description, 'com_phocagallery.item')).'</div>';
}


/*
if ($this->t['detailbuttons'] == 1){
	echo '<div class="pg-detail-item-button-box">'
	.'<td align="left" width="30%" style="padding-left:48px">'.$this->item->prevbutton.'</td>'
	.'<td align="center">'.$this->item->slideshowbutton.'</td>'
	.'<td align="center">'.str_replace("%onclickreload%", $this->t['detailwindowreload'], $this->item->reloadbutton).'</td>'
	. $closeButton
	//.'<td align="right" width="30%" style="padding-right:48px">'.$this->item->nextbutton.'</td>'
	.'</div>';
}
*/




if ((isset($this->itemnext[0]) && $this->itemnext[0])  || (isset($this->itemprev[0]) && $this->itemprev[0])) {

	$suffix = '';
	if ($this->t['tmpl'] == 'component') {
		$suffix = 'tmpl=component';
	}

	echo '<div class="pg-detail-nav-box">';
	if(isset($this->itemprev[0]) && $this->itemprev[0]) {
		$p = $this->itemprev[0];
		$linkPrev = Route::_(PhocaGalleryRoute::getImageRoute($p->id, $p->catid, $p->alias, $p->categoryalias, 'detail', $suffix));
		echo '<div class="ph-left"><a href="'.$linkPrev.'" class="btn btn-primary ph-image-navigation" role="button"><svg class="ph-si ph-si-prev-btn"><use xlink:href="#ph-si-prev"></use></svg> '.Text::_('COM_PHOCAGALLERY_PREVIOUS').'</a></div>';
	}

	if(isset($this->itemnext[0]) && $this->itemnext[0]) {
		$n = $this->itemnext[0];
		$linkNext = Route::_(PhocaGalleryRoute::getImageRoute($n->id, $n->catid, $n->alias, $n->categoryalias, 'detail', $suffix));
		echo '<div class="ph-right"><a href="'.$linkNext.'" class="btn btn-primary ph-image-navigation" role="button">'.Text::_('COM_PHOCAGALLERY_NEXT').' <svg class="ph-si ph-si-next-btn"><use xlink:href="#ph-si-next"></use></svg></a></div>';
	}

	echo '<div class="ph-cb"></div>';
	echo '</div>';

}

echo $this->loadTemplate('rating');

// Tags
if ($this->t['displaying_tags_output'] != '') {
	echo '<div class="pg-detail-item-tag-box">'.$this->t['displaying_tags_output'].'</div>';
}
if ($this->t['display_comment_img'] == 1 || $this->t['display_comment_img'] == 3 || ($this->t['display_comment_img'] == 2 && $this->t['tmpl'] == 'component')) {

	$d          = array();
	$d['t']     = $this->t;

	$d['form']['task']          = 'comment';
	$d['form']['view']          = 'detail';
	$d['form']['controller']    = 'detail';
	$d['form']['tab']           = '';
	$d['form']['id']            = $this->item->id;
	$d['form']['catid']         = $this->item->catid;
	$d['form']['itemid']        = $this->itemId;

	echo $layoutC->render($d);



	/*if ($this->t['externalcommentsystem'] == 1) {
		if (ComponentHelper::isEnabled('com_jcomments', true)) {
			include_once(JPATH_BASE.'/components/com_jcomments/jcomments.php');
			echo JComments::showComments($this->item->id, 'com_phocagallery_images', Text::_('COM_PHOCAGALLERY_IMAGE') .' '. $this->item->title);
		}
	} else if ($this->t['externalcommentsystem'] == 2) {
		echo $this->loadTemplate('comments-fb');
	}*/
    echo PhocaGalleryUtils::getExtInfo();
}
echo '</div>';








}



/*echo '<div id="phocagallery" class="pg-detail-view'.$this->params->get( 'pageclass_sfx' ).'">';
	if ($this->t['backbutton'] != '') {
		echo $this->t['backbutton'];
	}

	echo '<table border="0" style="width:'.$this->t['boxlargewidth'].'px;height:'.$this->t['boxlargeheight'].'px;">'
		.'<tr>'
		.'<td colspan="5" class="pg-center" align="center" valign="middle">'
		.$this->item->videocode
		.'</td>'
		.'</tr>';

	$titleDesc = '';
	if ($this->t['display_title_description'] == 1) {
		$titleDesc .= $this->item->title;
		if ($this->item->description != '' && $titleDesc != '') {
			$titleDesc .= ' - ';
		}
	}

	// Standard Description
	if ($this->t['displaydescriptiondetail'] == 1) {
		echo '<tr>'
		.'<td colspan="6" align="left" valign="top" class="pg-dv-desc">'
		.'<div class="pg-dv-desc">'
		. $titleDesc . $this->item->description . '</div>'
		.'</td>'
		.'</tr>';
	}

	if ($this->t['detailbuttons'] == 1){
		echo '<tr>'
		.'<td align="left" width="30%" style="padding-left:48px">'.$this->item->prevbutton.'</td>'
		.'<td align="center"></td>'
		.'<td align="center">'.str_replace("%onclickreload%", $this->t['detailwindowreload'], $this->item->reloadbutton).'</td>';
		if ($this->t['detailwindow'] == 4 || $this->t['detailwindow'] == 5 || $this->t['detailwindow'] == 7) {
		} else {
			echo '<td align="center">' . str_replace("%onclickclose%", $this->t['detailwindowclose'], $this->item->closebutton). '</td>';
		}
		echo '<td align="right" width="30%" style="padding-right:48px">'.$this->item->nextbutton.'</td>'
		.'</tr>';
	}
	echo '</table>';
	echo $this->loadTemplate('rating');
	if ($this->t['detailwindow'] == 7) {
        echo PhocaGalleryUtils::getExtInfo();
	}
	echo '</div>';
}
*/
?>
