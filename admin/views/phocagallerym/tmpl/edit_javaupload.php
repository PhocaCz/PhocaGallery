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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
if (!empty($this->t['ju_output'])) {
	echo '<div id="phocagallery-javaupload" class="ph-in">';
	echo '<form action="'. Uri::base().'index.php?option=com_phocagallery" >';
	if ($this->t['ftp']) {echo PhocaGalleryFileUpload::renderFTPaccess();}
	echo '<div class="control-label ph-head-form">' . Text::_( 'COM_PHOCAGALLERY_UPLOAD_FILE' ).' [ '. Text::_( 'COM_PHOCAGALLERY_MAX_SIZE' ).':&nbsp;'.$this->t['uploadmaxsizeread'].','
		.' '.Text::_('COM_PHOCAGALLERY_MAX_RESOLUTION').':&nbsp;'. $this->t['uploadmaxreswidth'].' x '.$this->t['uploadmaxresheight'].' px ]</div>';
	echo $this->t['ju_output'];
	echo '</form>';
	echo '</div>';
}
?>
