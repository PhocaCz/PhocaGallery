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
use Joomla\CMS\Language\Text;
echo '<div id="phocagallery-upload" class="ph-in">';
echo '<div id="upload-noflash" class="actions">';
echo '<form action="'. $this->t['su_url'] .'" id="uploadFormU" method="post" enctype="multipart/form-data">';
if ($this->t['ftp']) { echo PhocaGalleryFileUpload::renderFTPaccess();}  
echo '<div class="control-label ph-head-form">'. Text::_( 'COM_PHOCAGALLERY_UPLOAD_FILE' ).' [ ';

if ($this->t['uploadmaxsizeread'] != '0 B') {
		echo Text::_( 'COM_PHOCAGALLERY_MAX_SIZE' ).':&nbsp;'.$this->t['uploadmaxsizeread'].','
	.' ';
}
echo Text::_('COM_PHOCAGALLERY_MAX_RESOLUTION').':&nbsp;'. $this->t['uploadmaxreswidth'].' x '.$this->t['uploadmaxresheight'].' px ]</div>';
echo $this->t['su_output'];
echo '</form>';
echo '</div>';
echo '</div>';
?>
