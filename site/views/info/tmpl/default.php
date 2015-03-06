<?php defined('_JEXEC') or die('Restricted access');
if ($this->tmpl['backbutton'] != '') {
	echo $this->tmpl['backbutton'];
}
echo '<div id="phoca-exif" class="pg-info-view'.$this->params->get( 'pageclass_sfx' ).'">'
.'<h1 class="phocaexif">'.JText::_('COM_PHOCAGALLERY_EXIF_INFO').':</h1>'
.'<table style="width:90%">'
.$this->infooutput
.'</table>'
.'</div>';
if ($this->tmpl['detailwindow'] == 7) {
	echo '<div style="text-align:right;color:#ccc;display:block">Powered by <a href="http://www.phoca.cz/phocagallery">Phoca Gallery</a></div>';
}
