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
echo '<div id="phocagallery" class="pg-detail-view'.$this->params->get( 'pageclass_sfx' ).'">';
if ($this->t['backbutton'] != '') {
	echo $this->t['backbutton'];
}

/*if($this->t['responsive'] == 1) {
	$iW = '100%';
	$iH = '100%';
	$iH = $this->t['largeheight']. 'px';
	$iW = $this->t['largewidth']. 'px';
} else {*/
	$iW = $this->t['largewidth']. 'px';
	$iH = $this->t['largeheight']. 'px';
//}

//echo '<div id="phocaGallerySlideshowC" style="width:'. $iW.';height:'.$iH .';padding:0;margin: auto"></div>';

echo '<div class="ph-mc">'
.'<table border="0" class="ph-mc" cellpadding="0" cellspacing="0">'
.'<tr>'
.'<td colspan="6"  valign="middle"'
.' style="height:'.$iH.';width: '.$iW.';" >';

echo '<div id="phocaGallerySlideshowC" style="max-width:'. $iW.';max-height:'.$iH .';padding:0;margin: auto;">';

//.'<a href="#" onclick="'.$this->t['detailwindowclose'].'">'.$this->item->linkimage.'</a>';
/*.'<script type="text/javascript" style="padding:0;margin:0;">';
if ( $this->t['slideshowrandom'] == 1 ) {
	echo 'new fadeshow(fadeimages, '.$this->t['largewidth'] .', '. $this->t['largeheight'] .', 0, '. $this->t['slideshowdelay'] .', '. $this->t['slideshowpause'] .', \'R\')';
} else {
	echo 'new fadeshow(fadeimages, '.$this->t['largewidth'] .', '. $this->t['largeheight'] .', 0, '. $this->t['slideshowdelay'] .', '. $this->t['slideshowpause'] .')';
}
echo '</script>';*/

echo '</div>';
echo '</td>'
.'</tr>';

echo '<tr><td colspan="6"><div style="padding:0;margin:0;height:3px;font-size:0px;">&nbsp;</div></td></tr>';

// Standard Description (to get the same height as by not slideshow
if ($this->t['displaydescriptiondetail'] == 1) {
	echo '<tr><td colspan="6" align="left" valign="top"><div></div></td></tr>';
}

echo '<tr>'
.'<td align="left" width="30%" style="padding-left:48px">'. $this->item->prevbutton .'</td>'
.'<td align="center">'. $this->item->slideshowbutton .'</td>'
.'<td align="center">'. str_replace("%onclickreload%", $this->t['detailwindowreload'], $this->item->reloadbutton).'</td>';
if ($this->t['detailwindow'] == 4 || $this->t['detailwindow'] == 5 || $this->t['detailwindow'] == 7) {
} else {
	echo '<td align="center">'. str_replace("%onclickclose%", $this->t['detailwindowclose'], $this->item->closebutton).'</td>';
}
echo '<td align="right" width="30%" style="padding-right:48px">'. $this->item->nextbutton .'</td>'
.'</tr>'
.'</table>'
.'</div>';

if ($this->t['detailwindow'] == 7) {
    echo PhocaGalleryUtils::getExtInfo();
}
echo '</div>';
