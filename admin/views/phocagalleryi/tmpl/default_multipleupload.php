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
echo '<div id="phocagallery-multipleupload" class="ph-in">';
echo $this->t['mu_response_msg'] ;
echo '<form action="'. JURI::base().'index.php?option=com_phocagallery" >';
if ($this->t['ftp']) {echo PhocaGalleryFileUpload::renderFTPaccess();}
echo '<div class="control-label ph-head-form-small">' . JText::_( 'COM_PHOCAGALLERY_UPLOAD_FILE' ).' [ '. JText::_( 'COM_PHOCAGALLERY_MAX_SIZE' ).':&nbsp;'.$this->t['uploadmaxsizeread'].','
	.' '.JText::_('COM_PHOCAGALLERY_MAX_RESOLUTION').':&nbsp;'. $this->t['uploadmaxreswidth'].' x '.$this->t['uploadmaxresheight'].' px ]</div>';
echo '<small>'.JText::_('COM_PHOCAGALLERY_SELECT_IMAGES').'. '.JText::_('COM_PHOCAGALLERY_ADD_IMAGES_TO_UPLOAD_QUEUE_AND_CLICK_START_BUTTON').'</small>';
echo $this->t['mu_output'];
echo '</form>';
echo '</div>';
?>
