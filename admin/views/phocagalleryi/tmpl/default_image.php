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
use Joomla\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
jimport( 'joomla.filesystem.file' );
$image['width'] = $image['height'] = 100;

if (PhocaGalleryFile::exists( $this->_tmp_img->linkthumbnailpathabs )) {
	list($width, $height) = GetImageSize( $this->_tmp_img->linkthumbnailpathabs );
	$image = PhocaGalleryImage::correctSizeWithRate($width, $height);
}
/*
?><div class="phocagallery-box-file-i">
	<center>
		<div class="phocagallery-box-file-first-i">
			<div class="phocagallery-box-file-second">
				<div class="phocagallery-box-file-third">
					<center>
					<a href="#" onclick="if (window.parent) window.parent.<?php echo $this->fce; ?>('<?php echo $this->_tmp_img->nameno; ?>');">
	<?php

	$imageRes	= PhocaGalleryImage::getRealImageSize($this->_tmp_img->nameno, 'medium');
	$correctImageRes = PhocaGalleryImage::correctSizeWithRate($imageRes['w'], $imageRes['h'], 100, 100);
	echo HTMLHelper::_( 'image', $this->_tmp_img->linkthumbnailpath, '', array('width' => $image['width'], 'height' => $image['height']), '', null); ?></a>
					</center>
				</div>
			</div>
		</div>
	</center>

	<div class="name"><?php echo $this->_tmp_img->name; ?></div>
		<div class="detail" style="text-align:right">
			<a href="#" onclick="if (window.parent) window.parent.<?php echo $this->fce; ?>('<?php echo $this->_tmp_img->nameno; ?>');"><?php echo HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-insert.gif', Text::_('COM_PHOCAGALLERY_INSERT_IMAGE'), array('title' => Text::_('COM_PHOCAGALLERY_INSERT_IMAGE'))); ?></a>
		</div>
	<div style="clear:both"></div>
</div>
*/

?><div class="ph-item-box">
    <div class="ph-item-image"><a title="<?php echo $this->_tmp_img->name ?>" href="#" onclick="if (window.parent) window.parent.<?php echo $this->fce; ?>('<?php echo $this->_tmp_img->nameno; ?>');"><?php

            $imageRes	= PhocaGalleryImage::getRealImageSize($this->_tmp_img->nameno, 'medium');
	        $correctImageRes = PhocaGalleryImage::correctSizeWithRate($imageRes['w'], $imageRes['h'], 100, 100);
	        echo HTMLHelper::_( 'image', $this->_tmp_img->linkthumbnailpath, '', array('width' => $image['width'], 'height' => $image['height']), '', null);

            ?></a></div>

	    <div class="ph-item-name" title="<?php echo $this->_tmp_img->name ?>"><?php echo PhocagalleryText::WordDelete($this->_tmp_img->name, 15); ?></div>

        <div class="ph-item-action-box">
			<a href="#" onclick="if (window.parent) window.parent.<?php echo $this->fce; ?>('<?php echo $this->_tmp_img->nameno; ?>');" title="<?php echo Text::_('COM_PHOCAGALLERY_INSERT_IMAGE') ?>"><span class="ph-cp-item"><i class="phi duotone phi-fs-m phi-fc-gd icon-download"></i></span></a></div>
</div>
