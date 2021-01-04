<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');


$r = $this->r;
echo $r->startCp();

echo '<div class="ph-box-cp">';
echo '<div class="ph-left-cp">';

echo '<div class="ph-cp-item-box">';
$link	= 'index.php?option='.$this->t['o'].'&view=';
foreach ($this->views as $k => $v) {
	$linkV	= $link . $this->t['c'] . $k;
	echo $r->quickIconButton( $linkV, JText::_($v[0]), $v[1], $v[2]);
}
echo '</div>';
echo '</div>';

echo '<civ class="ph-right-cp">';

echo '<div class="ph-extension-info-box">';
echo '<div class="ph-cpanel-logo">'.Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['i'] . 'logo-'.str_replace('phoca', 'phoca-', $this->t['c']).'.png', 'Phoca.cz') . '</div>';
echo '<div style="float:right;margin:10px;">'. Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['i'] . 'logo-phoca.png', 'Phoca.cz' ).'</div>';

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

?>
<p>&nbsp;</p>
<p><strong><?php echo JText::_('COM_PHOCAGALLERY_SHADOWBOX_LICENCE_HEAD');?></strong></p>
<p class="license"><?php echo JText::_('COM_PHOCAGALLERY_SHADOWBOX_LICENCE');?></p>
<p><a href="http://www.shadowbox-js.com/" target="_blank">Shadowbox.js</a> by <a target="_blank" href="http://www.shadowbox-js.com/">Michael J. I. Jackson</a><br />
<a target="_blank" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution-Noncommercial-Share Alike</a></p>

<p><strong><?php echo JText::_('COM_PHOCAGALLERY_HIGHSLIDE_LICENCE_HEAD');?></strong></p>
<p class="license"><?php echo JText::_('COM_PHOCAGALLERY_HIGHSLIDE_LICENCE');?></p>
<p><a href="http://highslide.com/" target="_blank">Highslide JS</a> by <a target="_blank" href="http://highslide.com/">Torstein Hønsi</a><br />
<a target="_blank" href="http://creativecommons.org/licenses/by-nc/2.5/">Creative Commons Attribution-NonCommercial 2.5  License</a></p>

<p><strong><?php echo JText::_('COM_PHOCAGALLERY_BOXPLUS_LICENCE_HEAD');?></strong></p>
<p class="license"><?php echo JText::_('COM_PHOCAGALLERY_BOXPLUS_LICENCE');?></p>
<p><a href="http://hunyadi.info.hu/en/projects/boxplus" target="_blank">boxplus</a> by <a target="_blank" href="http://hunyadi.info.hu/">Levente Hunyadi</a><br />
<a target="_blank" href="http://www.gnu.org/licenses/gpl.html">GPL</a></p>

<p>Google™, Google Maps™, Google Picasa™, Google+™, Google Photos™ and YouTube Broadcast Yourself™ are registered trademarks of Google Inc.</p>

<?php

echo '<div style="border-top:1px solid #c2c2c2"></div><p>&nbsp;</p>'
.'<div class="btn-group"><a class="btn btn-large btn-primary" href="https://www.phoca.cz/version/index.php?'.$this->t['c'].'='.  $this->t['version'] .'" target="_blank"><i class="icon-loop icon-white"></i>&nbsp;&nbsp;'.  JText::_($this->t['l'] . '_CHECK_FOR_UPDATE') .'</a></div>'
.'<div style="float:right; margin: 0 10px"><a href="https://www.phoca.cz/" target="_blank">'.Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['i'] . 'logo.png', 'Phoca.cz' ).'</a></div>';

echo '</div>';

echo '<div class="ph-extension-links-box">';
echo $r->getLinks();
echo '</div>';

echo '</div>';

echo '</div>';
echo $r->endCp();

?>
