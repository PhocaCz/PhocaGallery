<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;

$r = $this->r;
echo $r->startCp();

echo '<div class="ph-box-info">';

echo '<div style="float:right;margin:10px;">' . Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['i'] . 'logo-phoca.png', 'Phoca.cz' ) .'</div>'
	. '<div class="ph-cpanel-logo">'.Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['i'] . 'logo-'.str_replace('phoca', 'phoca-', $this->t['c']).'.png', 'Phoca.cz') . '</div>'
	.'<h3>'.JText::_($this->t['component_head']).' - '. JText::_($this->t['l'].'_INFORMATION').'</h3>'
	.'<div style="clear:both;"></div>';


echo '<p>'. JText::_('COM_PHOCAGALLERY_RECOMMENDED_SETTINGS').'</p>'
	.'<div style="clear:both;"></div>';

echo '<table cellpadding="5" cellspacing="1">'
	.'<tr><td></td>'
	.'<td align="center">'.JText::_('COM_PHOCAGALLERY_RECOMMENDED').'</td>'
	.'<td align="center">'.JText::_('COM_PHOCAGALLERY_CURRENT').'</td></tr>';

if ($this->t['enablethumbcreation'] == 1) {
	$bgStyle = 'class="alert alert-error"';
} else {
	$bgStyle = 'class="alert alert-success"';
}


echo '<tr '.$bgStyle.'>'
	.'<td>'. JText::_('COM_PHOCAGALLERY_ENABLE_THUMBNAIL_GENERATION').'</td>'
	//.'<td align="center">'.Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-false.png', JText::_('COM_PHOCAGALLERY_DISABLED') ) .'</td>'
	.'<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-minus-circle" title="'. JText::_('COM_PHOCAGALLERY_DISABLED') .'"></i></td>'
	.'<td align="center">'.$this->t['enablethumbcreationstatus'].'</td>'
	.'</tr>'
	.'<tr>'
	.'<td colspan="3">'.JText::_('COM_PHOCAGALLERY_ENABLE_THUMBNAIL_GENERATION_INFO_DESC').'</td></tr>';


if ($this->t['paginationthumbnailcreation'] == 1) {
	$bgStyle 	= 'class="alert alert-success"';
	$icon		= 'success';
	$iconText	= JText::_('COM_PHOCAGALLERY_ENABLED');
} else {
	$bgStyle 	= 'class="alert alert-error"';
	$icon		= 'minus-circle';
	$iconText	= JText::_('COM_PHOCAGALLERY_DISABLED');
}

echo '<tr '.$bgStyle.'>'
	.'<td>'. JText::_('COM_PHOCAGALLERY_PAGINATION_THUMBNAIL_GENERATION').'</td>'
	//.'<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ) .'</td>'
	//.'<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-'.$icon.'.png', JText::_($iconText) ) .'</td>'

	.'<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-success" title="'. JText::_('COM_PHOCAGALLERY_ENABLED') .'"></i></td>'
	.'<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  JText::_($iconText) .'"></i></td></tr>'

	.'</tr>'
	.'<tr><td colspan="3">'. JText::_('COM_PHOCAGALLERY_PAGINATION_THUMBNAIL_GENERATION_INFO_DESC').'</td></tr>';

if ($this->t['cleanthumbnails'] == 0) {
	$bgStyle = 'class="alert alert-success"';
	$icon		= 'minus-circle';
	$iconText	= JText::_('COM_PHOCAGALLERY_DISABLED');


} else {
	$bgStyle = 'class="alert alert-error"';
	$icon		= 'success';
	$iconText	= JText::_('COM_PHOCAGALLERY_ENABLED');
}
echo '<tr '.  $bgStyle.'>'
	.'<td>'. JText::_('COM_PHOCAGALLERY_CLEAN_THUMBNAILS').'</td>'
	//.'<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-false.png' , JText::_('COM_PHOCAGALLERY_DISABLED') ) .'</td>'
	//.'<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images//administrator/icon-16-'.$icon.'.png', JText::_($iconText) ) .'</td>'
	.'<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-minus-circle" title="'. JText::_('COM_PHOCAGALLERY_DISABLED') .'"></i></td>'
	.'<td align="center" class="ph-info-item ph-cp-item"><i class="phi duotone icon-'.$icon.'" title="'.  JText::_($iconText) .'"></i></td></tr>'
	.'</tr>'
	.'<tr><td colspan="3">'. JText::_('COM_PHOCAGALLERY_CLEAN_THUMBNAILS_INFO_DESC').'</td></tr>';

echo $this->foutput;
echo '</table>';


echo '<h3>'.  JText::_($this->t['l'].'_HELP').'</h3>';

echo '<div>';
if (!empty($this->t['component_links'])) {
	foreach ($this->t['component_links'] as $k => $v) {
	    echo '<div><a href="'.$v[1].'" target="_blank">'.$v[0].'</a></div>';
	}
}
echo '</div>';

echo '<h3>'.  JText::_($this->t['l'] . '_VERSION').'</h3>'
.'<p>'.  $this->t['version'] .'</p>';

echo '<h3>'.  JText::_($this->t['l'] . '_COPYRIGHT').'</h3>'
.'<p>© 2007 - '.  date("Y"). ' Jan Pavelka</p>'
.'<p><a href="https://www.phoca.cz/" target="_blank">www.phoca.cz</a></p>';

echo '<h3>'.  JText::_($this->t['l'] . '_LICENSE').'</h3>'
.'<p><a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></p>';

echo '<h3>'.  JText::_($this->t['l'] . '_TRANSLATION').': '. JText::_($this->t['l'] . '_TRANSLATION_LANGUAGE_TAG').'</h3>'
        .'<p>© 2007 - '.  date("Y"). ' '. JText::_($this->t['l'] . '_TRANSLATER'). '</p>'
        .'<p>'.JText::_($this->t['l'] . '_TRANSLATION_SUPPORT_URL').'</p>';

echo '<input type="hidden" name="task" value="" />'
.'<input type="hidden" name="option" value="'.$this->t['o'].'" />'
.'<input type="hidden" name="controller" value="'.$this->t['c'].'info" />';

echo Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['i'] . 'logo.png', 'Phoca.cz');

echo '<p>&nbsp;</p>';

echo '<div style="border-top:1px solid #eee"></div><p>&nbsp;</p>'.'<div class="btn-group">
<a class="btn btn-large btn-primary" href="https://www.phoca.cz/version/index.php?'.$this->t['c'].'='.  $this->t['version'] .'" target="_blank"><i class="icon-loop icon-white"></i>&nbsp;&nbsp;'.  JText::_($this->t['l'].'_CHECK_FOR_UPDATE') .'</a></div>';

echo '<div style="margin-top:30px;height:39px;background: url(\''.JURI::root(true).'/media/com_'.$this->t['c'].'/images/administrator/line.png\') 100% 0 no-repeat;">&nbsp;</div>';

echo '</div>';


echo '</div>';
echo $r->endCp();

?>


<?php /*
defined('_JEXEC') or die;
//Joomla\CMS\HTML\HTMLHelper::_('behavior.tooltip');

echo '<form action="index.php" method="post" name="adminForm" id="phocagalleryin-form">';
echo '<div id="j-sidebar-container" class="span2">'.$this->sidebar.'</div>';
echo '<div id="j-main-container" class="span10">'

	.'<div style="float:right;margin:10px;">'
	. Joomla\CMS\HTML\HTMLHelper::_('image', 'media/com_phocagallery/images/administrator/logo-phoca.png', 'Phoca.cz' )
	.'</div>';


echo '<div class="ph-cpanel-logo">'.Joomla\CMS\HTML\HTMLHelper::_('image', 'media/com_phocagallery/images/administrator/logo-phoca-gallery.png', 'Phoca.cz') . '</div>';
//echo '<div style="clear:both;"></div>';
echo '<h3>'.JText::_('COM_PHOCAGALLERY_PHOCA_GALLERY').' - '. JText::_('COM_PHOCAGALLERY_INFORMATION').'</h3>'
	.'<p>'. JText::_('COM_PHOCAGALLERY_RECOMMENDED_SETTINGS').'</p>'
	.'<div style="clear:both;"></div>';

echo '<table cellpadding="5" cellspacing="1">'
	.'<tr><td></td>'
	.'<td align="center">'.JText::_('COM_PHOCAGALLERY_RECOMMENDED').'</td>'
	.'<td align="center">'.JText::_('COM_PHOCAGALLERY_CURRENT').'</td></tr>';

if ($this->t['enablethumbcreation'] == 1) {
	$bgStyle = 'class="alert alert-error"';
} else {
	$bgStyle = 'class="alert alert-success"';
}


echo '<tr '.$bgStyle.'>'
	.'<td>'. JText::_('COM_PHOCAGALLERY_ENABLE_THUMBNAIL_GENERATION').'</td>'
	.'<td align="center">'.Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-false.png', JText::_('COM_PHOCAGALLERY_DISABLED') ) .'</td>'
	.'<td align="center">'.$this->t['enablethumbcreationstatus'].'</td>'
	.'</tr>'
	.'<tr>'
	.'<td colspan="3">'.JText::_('COM_PHOCAGALLERY_ENABLE_THUMBNAIL_GENERATION_INFO_DESC').'</td></tr>';


if ($this->t['paginationthumbnailcreation'] == 1) {
	$bgStyle 	= 'class="alert alert-success"';
	$icon		= 'true';
	$iconText	= JText::_('COM_PHOCAGALLERY_ENABLED');
} else {
	$bgStyle 	= 'class="alert alert-error"';
	$icon		= 'false';
	$iconText	= JText::_('COM_PHOCAGALLERY_DISABLED');
}

echo '<tr '.$bgStyle.'>'
	.'<td>'. JText::_('COM_PHOCAGALLERY_PAGINATION_THUMBNAIL_GENERATION').'</td>'
	.'<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-true.png', JText::_('COM_PHOCAGALLERY_ENABLED') ) .'</td>'
	.'<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-'.$icon.'.png', JText::_($iconText) ) .'</td>'
	.'</tr>'
	.'<tr><td colspan="3">'. JText::_('COM_PHOCAGALLERY_PAGINATION_THUMBNAIL_GENERATION_INFO_DESC').'</td></tr>';

if ($this->t['cleanthumbnails'] == 0) {
	$bgStyle = 'class="alert alert-success"';
	$icon		= 'false';
	$iconText	= JText::_('COM_PHOCAGALLERY_DISABLED');


} else {
	$bgStyle = 'class="alert alert-error"';
	$icon		= 'true';
	$iconText	= JText::_('COM_PHOCAGALLERY_ENABLED');
}
echo '<tr '.  $bgStyle.'>'
	.'<td>'. JText::_('COM_PHOCAGALLERY_CLEAN_THUMBNAILS').'</td>'
	.'<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images/administrator/icon-16-false.png' , JText::_('COM_PHOCAGALLERY_DISABLED') ) .'</td>'
	.'<td align="center">'. Joomla\CMS\HTML\HTMLHelper::_('image','media/com_phocagallery/images//administrator/icon-16-'.$icon.'.png', JText::_($iconText) ) .'</td>'
	.'</tr>'
	.'<tr><td colspan="3">'. JText::_('COM_PHOCAGALLERY_CLEAN_THUMBNAILS_INFO_DESC').'</td></tr>';

echo $this->foutput;
echo '</table>';

echo '<h3>'.  JText::_('COM_PHOCAGALLERY_HELP').'</h3>';

echo '<p>'
.'<a href="https://www.phoca.cz/phocagallery/" target="_blank">Phoca Gallery Main Site</a><br />'
.'<a href="https://www.phoca.cz/documentation/" target="_blank">Phoca Gallery User Manual</a><br />'
.'<a href="https://www.phoca.cz/forum/" target="_blank">Phoca Gallery Forum</a><br />'
.'</p>';

echo '<h3>'.  JText::_('COM_PHOCAGALLERY_VERSION').'</h3>'
.'<p>'.  $this->t['version'] .'</p>';

echo '<h3>'.  JText::_('COM_PHOCAGALLERY_COPYRIGHT').'</h3>'
.'<p>© 2007 - '.  date("Y"). ' Jan Pavelka</p>'
.'<p><a href="https://www.phoca.cz/" target="_blank">www.phoca.cz</a></p>';

echo '<h3>'.  JText::_('COM_PHOCAGALLERY_LICENCE').'</h3>'
.'<p><a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></p>';

echo '<h3>'.  JText::_('COM_PHOCAGALLERY_TRANSLATION').': '. JText::_('COM_PHOCAGALLERY_TRANSLATION_LANGUAGE_TAG').'</h3>'
        .'<p>© 2007 - '.  date("Y"). ' '. JText::_('COM_PHOCAGALLERY_TRANSLATER'). '</p>'
        .'<p>'.JText::_('COM_PHOCAGALLERY_TRANSLATION_SUPPORT_URL').'</p>';

echo '<input type="hidden" name="task" value="" />'
.'<input type="hidden" name="option" value="com_phocagallery" />'
.'<input type="hidden" name="controller" value="phocagalleryin" />';

echo  Joomla\CMS\HTML\HTMLHelper::_('image', 'media/com_phocagallery/images/administrator/logo.png', 'Phoca.cz');
echo '<p>&nbsp;</p>';

echo '<div style="border-top:1px solid #eee"></div><p>&nbsp;</p>'
.'<div class="btn-group">
<a class="btn btn-large btn-primary" href="https://www.phoca.cz/version/index.php?phocagallery='.  $this->t['version'] .'" target="_blank"><i class="icon-loop icon-white"></i>&nbsp;&nbsp;'.  JText::_('COM_PHOCAGALLERY_CHECK_FOR_UPDATE') .'</a></div>';


echo '<div style="margin-top:30px;height:39px;background: url(\''.JURI::root(true).'/media/com_phocagallery/images/administrator/line.png\') 100% 0 no-repeat;">&nbsp;</div>';


echo '</div>';
//echo '<div class="span1"></div>';
echo '</form>';
*/
