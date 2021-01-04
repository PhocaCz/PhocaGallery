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

$title			= $this->item->filename;
$imgLink		= Joomla\CMS\HTML\HTMLHelper::_( 'image', 'images/phocagallery/'. $this->item->filenameno, '');

$extImage = PhocaGalleryImage::isExtImage($this->item->extid);
if ($extImage) {
	$title		= $this->item->title;
	$imgLink	= Joomla\CMS\HTML\HTMLHelper::_( 'image', $this->item->exto, '');
}

if ($this->t['backbutton'] != '') {
	echo $this->t['backbutton'];

	echo '<div id="download-box"><div style="overflow:scroll;width:'.$this->t['boxlargewidth'].'px;height:'.$this->t['boxlargeheight'].'px;margin:0px;padding:0px;">' . $imgLink . '</div>';
	echo '<div id="download-msg-nopopup"><div>'
		.'<table width="360">'
		.'<tr><td align="left">' . JText::_('COM_PHOCAGALLERY_IMAGE_NAME') . ': </td><td>'.$title.'</td></tr>'
		.'<tr><td align="left">' . JText::_('COM_PHOCAGALLERY_IMAGE_FORMAT') . ': </td><td>'.$this->item->imagesize.'</td></tr>'
		.'<tr><td align="left">' . JText::_('COM_PHOCAGALLERY_IMAGE_SIZE') . ': </td><td>'.$this->item->filesize.'</td></tr>';

	echo '<tr><td align="left"><a title="'. JText::_('COM_PHOCAGALLERY_IMAGE_DOWNLOAD').'" href="'. JRoute::_('index.php?option=com_phocagallery&view=detail&catid='.$this->item->catslug.'&id='.$this->item->slug.'&phocadownload=2'.'&Itemid='. $this->itemId ).'">'.JText::_('COM_PHOCAGALLERY_IMAGE_DOWNLOAD').'</a></td><td>&nbsp;</td>';

	echo '</table>';
	echo '</div></div></div>';


} else {

	echo '<div id="download-box"><div style="overflow:scroll;width:'.$this->t['boxlargewidth'].'px;height:'.$this->t['boxlargeheight'].'px;margin:0px;padding:0px;">' . $imgLink. '</div>';
	echo '<div id="download-msg"><div>'
		.'<table width="360">'
		.'<tr><td align="left">' . JText::_('COM_PHOCAGALLERY_IMAGE_NAME') . ': </td><td>'.$title.'</td></tr>'
		.'<tr><td align="left">' . JText::_('COM_PHOCAGALLERY_IMAGE_FORMAT') . ': </td><td>'.$this->item->imagesize.'</td></tr>'
		.'<tr><td align="left">' . JText::_('COM_PHOCAGALLERY_IMAGE_SIZE') . ': </td><td>'.$this->item->filesize.'</td></tr>'
		.'<tr><td colspan="2" align="left"><small>' . JText::_('COM_PHOCAGALLERY_DOWNLOAD_IMAGE') . '</small></td></tr>';

		switch($this->t['detailwindow']) {
			case 3:
			case 4:
			case 5:
			case 7:
			case 9:
			case 10:

			break;

			default:
				echo '<tr><td>&nbsp;</td><td align="right">'.str_replace("%onclickclose%", $this->t['detailwindowclose'], $this->item->closetext).'</td></tr>';
			break;
		}

	echo '</table>';
	echo '</div></div></div>';
}
echo '<div id="phocaGallerySlideshowC" style="display:none"></div>';//because of loaded slideshow js
if ($this->t['detailwindow'] == 7) {
    echo PhocaGalleryUtils::getExtInfo();
}
?>
