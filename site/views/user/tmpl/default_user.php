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
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;
echo '<div id="phocagallery-user">'.$this->t['iepx'];
?><h4><?php echo Text::_( 'COM_PHOCAGALLERY_USER' ); ?></h4>
<table>
	<tr>
		<td><strong><?php echo Text::_('COM_PHOCAGALLERY_USER');?>:</strong></td>
		<td><?php echo $this->t['user']?></td>
	</tr>
	<tr>
		<td><strong><?php echo Text::_('COM_PHOCAGALLERY_USERNAME');?>:</strong></td>
		<td><?php echo $this->t['username']?></td>
	</tr>
	<tr>
		<td><strong><?php echo Text::_('COM_PHOCAGALLERY_MAIN_CATEGORY');?>:</strong></td>
		<td><?php echo $this->t['usermaincategory']?></td>
	</tr>
	<tr>
		<td><strong><?php echo Text::_('COM_PHOCAGALLERY_NUMBER_OF_SUBCATEGORIES');?>:</strong></td>
		<td><?php echo $this->t['usersubcategory'] . ' ('.Text::_('COM_PHOCAGALLERY_MAX').': '.$this->t['usersubcatcount'].', '.Text::_('COM_PHOCAGALLERY_SPACE_LEFT').': '.$this->t['usersubcategoryleft'].')'; ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Text::_('COM_PHOCAGALLERY_NUMBER_OF_IMAGES');?>:</strong></td>
		<td><?php echo $this->t['userimages']; ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Text::_('COM_PHOCAGALLERY_USED_SPACE');?>:</strong></td>
		<td><?php echo $this->t['userimagesspace']. ' ('.Text::_('COM_PHOCAGALLERY_MAX').': '.$this->t['userimagesmaxspace'].', '.Text::_('COM_PHOCAGALLERY_SPACE_LEFT').': '.$this->t['userimagesspaceleft'].')'; ?></td>
	</tr>
</table><?php

if ($this->t['enableuploadavatar'] == 1) {
	?><p>&nbsp;</p>
<h4><?php
	echo Text::_( 'COM_PHOCAGALLERY_UPLOAD_AVATAR' ).' [ '. Text::_( 'COM_PHOCAGALLERY_MAX_SIZE' ).':&nbsp;'.$this->t['uploadmaxsizeread'].','
	.' '.Text::_('COM_PHOCAGALLERY_MAX_RESOLUTION').':&nbsp;'. $this->t['uploadmaxreswidth'].' x '.$this->t['uploadmaxresheight'].' px ]';
?></h4>

<form onsubmit="return OnUploadSubmitUserPG();" action="<?php echo htmlspecialchars($this->t['actionamp']) . $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo Session::getFormToken();?>=1&amp;viewback=user" id="uploadForm" method="post" enctype="multipart/form-data">

	<table>
		<tr>
			<td><strong><?php echo Text::_('COM_PHOCAGALLERY_AVATAR');?>:</strong></td>
			<td><?php echo $this->t['useravatarimg']?></td>
		</tr>
		<tr>
			<td><strong><?php echo Text::_('COM_PHOCAGALLERY_APPROVED');?>:</strong></td>
			<td><?php
			if ($this->t['useravatarapproved'] == 1) {
				//echo JHtml::_('image', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_APPROVED'));
				//echo PhocaGalleryRenderFront::renderIcon('publish', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_APPROVED'));
				echo '<svg class="ph-si ph-si-enabled"><title>'.Text::_('COM_PHOCAGALLERY_APPROVED').'</title><use xlink:href="#ph-si-enabled"></use></svg>';
			} else {
				//echo JHtml::_('image', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_NOT_APPROVED'));
				//echo PhocaGalleryRenderFront::renderIcon('unpublish', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_NOT_APPROVED'));
				echo '<svg class="ph-si ph-si-disabled"><title>'.Text::_('COM_PHOCAGALLERY_NOT_APPROVED').'</title><use xlink:href="#ph-si-disabled"></use></svg>';
			}
		?></td>
		</tr>
		<tr>
			<td><strong><?php echo Text::_('COM_PHOCAGALLERY_FILENAME');?>:</strong></td>
			<td>
			<input type="file" id="file-upload" name="Filedata" />
			<button class="btn btn-primary" id="file-upload-submit"><i class="icon-upload icon-white"></i> <?php echo Text::_('COM_PHOCAGALLERY_START_UPLOAD') ?></button>
			<span id="upload-clear"></span>
			</td>
		</tr>
	</table>

	<ul class="upload-queue" id="upload-queue">
		<li style="display: none" ></li>
	</ul>

	<?php echo HTMLHelper::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="uploadavatar"/>
	<input type="hidden" name="tab" value="<?php echo $this->t['currenttab']['user'];?>" />
	<input type="hidden" name="controller" value="user" />
	<input type="hidden" name="viewback" value="user" />
	<input type="hidden" name="view" value="user"/>
	<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
</form>
<?php /*<div id="loading-label-user" style="text-align:center"><?php echo JHtml::_('image', $this->t['pi'].'icon-switch.gif', '') . '  '. JText::_('COM_PHOCAGALLERY_LOADING'); ?></div>*/
    echo '<div id="loading-label-user" class="ph-loading-text ph-loading-hidden"><div class="ph-lds-ellipsis"><div></div><div></div><div></div><div></div></div><div>'. Text::_('COM_PHOCAGALLERY_LOADING') . '</div></div>';
}
echo '</div>';
?>
