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
use Joomla\CMS\HTML\HTMLHelper;

echo '<div id="phocagallery-category-creating">'.$this->t['iepx'];

if ($this->t['categorypublished'] == 0) {
	echo '<p>'.Text::_('COM_PHOCAGALLERY_YOUR_CATEGORY_IS_UNPUBLISHED').'</p>';
} else {

if ($this->t['categorytitle'] != '') {
	?><h4><?php echo Text::_('COM_PHOCAGALLERY_MAIN_CATEGORY'); ?></h4>
	<table>
		<tr>
			<td><strong><?php echo Text::_('COM_PHOCAGALLERY_CATEGORY');?>:</strong></td>
			<td><?php echo $this->t['categorytitle'] ;?></td>
		</tr>
		<tr>
			<td><strong><?php echo Text::_('COM_PHOCAGALLERY_DESCRIPTION');?>:</strong></td>
			<td><?php echo $this->t['categorydescription'] ;?></td>
		</tr>
		<tr>
			<td><strong><?php echo Text::_('COM_PHOCAGALLERY_APPROVED');?>:</strong></td>
			<td><?php


			if ($this->t['categoryapproved'] == 1) {
				//echo PhocaGalleryRenderFront::renderIcon('publish', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_APPROVED'));
				echo '<svg class="ph-si ph-si-enabled"><title>'.Text::_('COM_PHOCAGALLERY_APPROVED').'</title><use xlink:href="#ph-si-enabled"></use></svg>';
			} else {
				//echo PhocaGalleryRenderFront::renderIcon('unpublish', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_NOT_APPROVED'));
				echo '<svg class="ph-si ph-si-disabled"><title>'.Text::_('COM_PHOCAGALLERY_NOT_APPROVED').'</title><use xlink:href="#ph-si-disabled"></use></svg>';
			}

		?></td>
		</tr>
	</table><?php
}
?><h4><?php echo $this->t['categorycreateoredit']; ?></h4>
	<form action="<?php echo htmlspecialchars($this->t['action']);?>" name="phocagallerycreatecatform" id="phocagallery-create-cat-form" method="post" >
	<table>
		<tr>
			<td><strong><?php echo Text::_('COM_PHOCAGALLERY_CATEGORY');?>:</strong></td>
			<td><input type="text" id="categoryname" name="categoryname" maxlength="255" class="form-control comment-input" value="<?php echo $this->t['categorytitle'] ;?>" /></td>
		</tr>

		<tr>
			<td><strong><?php echo Text::_( 'COM_PHOCAGALLERY_DESCRIPTION' ); ?>:</strong></td>
			<td><textarea id="phocagallery-create-cat-description" name="phocagallerycreatecatdescription" onkeyup="countCharsCreateCat();" cols="30" rows="10" class="form-control comment-input"><?php echo $this->t['categorydescription'] ;?></textarea></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><?php echo Text::_('COM_PHOCAGALLERY_CHARACTERS_WRITTEN');?> <input name="phocagallerycreatecatcountin" value="0" readonly="readonly" class="form-control comment-input2" /> <?php echo Text::_('COM_PHOCAGALLERY_AND_LEFT_FOR_DESCRIPTION');?> <input name="phocagallerycreatecatcountleft" value="<?php echo $this->t['maxcreatecatchar'];?>" readonly="readonly" class="form-control comment-input2" />
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td align="right"><button class="btn btn-primary" onclick="return(checkCreateCatForm());" id="phocagallerycreatecatsubmit" ><?php echo $this->t['categorycreateoredit']; ?></button>
			</td>
		</tr>
	</table>

	<?php echo HTMLHelper::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="createcategory"/>
	<input type="hidden" name="controller" value="user"/>

	<input type="hidden" name="view" value="user"/>
	<input type="hidden" name="tab" value="<?php echo $this->t['currenttab']['createcategory'];?>" />
	<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
	</form><?php
}
echo '</div>';
?>
