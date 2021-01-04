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
defined('_JEXEC') or die('Restricted access'); ?>


<fieldset>
<legend><?php echo JText::_('COM_PHOCAGALLERY_BACKGROUND_IMAGE'). ' - '. JText::_('COM_PHOCAGALLERY_SMALL_THUMBNAIL');?></legend>

<div style="float:right;position:relative;margin-top:15px;margin-right:10px">
<?php echo Joomla\CMS\HTML\HTMLHelper::_('image', 'media/com_phocagallery/images/administrator/image-bg-image.png', '' ); ?>
</div>

<form  action="index.php" method="post" name="adminFormBackgroundImageSmall">
<table class="adminform">
<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_SMALL_IMAGE_WIDTH' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="siw" id="siw" size="12" maxlength="8" value="<?php echo $this->t['siw'];?>" /></td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_SMALL_IMAGE_HEIGHT' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="sih" id="sih" size="12" maxlength="8" value="<?php echo $this->t['sih'];?>" /></td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_SITE_BACKGROUND_COLOR' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="ssbgc" id="ssbgc" size="12" maxlength="8" value="<?php echo $this->t['ssbgc'];?>" /> <span style="margin-left:10px" onclick="openPicker('ssbgc')"  class="picker_buttons"><?php echo  JText::_('COM_PHOCAGALLERY_PICK_COLOR'); ?></span> (1)</td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_IMAGE_BACKGROUND_COLOR' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="sibgc" id="sibgc" size="12" maxlength="8" value="<?php echo $this->t['sibgc'];?>" /> <span style="margin-left:10px" onclick="openPicker('sibgc')"  class="picker_buttons"><?php echo  JText::_('COM_PHOCAGALLERY_PICK_COLOR'); ?></span> (2)</td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_IMAGE_BORDER_COLOR' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="sibrdc" id="sibrdc" size="12" maxlength="8" value="<?php echo $this->t['sibrdc'];?>" /> <span style="margin-left:10px" onclick="openPicker('sibrdc')"  class="picker_buttons"><?php echo  JText::_('COM_PHOCAGALLERY_PICK_COLOR'); ?></span> (3)</td>
</tr>

<tr>
	<td valign="middle" align="right" class="key"><?php echo JText::_( 'COM_PHOCAGALLERY_IMAGE_EFFECT' ); ?>:</td>
	<td valign="middle"><select id="sie" name="sie">
		<option value="0" <?php if ($this->t['sie'] == 0) {echo 'selected="selected"';} ?> ><?php echo JText::_('COM_PHOCAGALLERY_NONE')?></option>
		<option value="1" <?php if ($this->t['sie'] == 1) {echo 'selected="selected"';} ?> ><?php echo JText::_('COM_PHOCAGALLERY_DROP_SHADOW')?></option>
		<option value="2" <?php if ($this->t['sie'] == 2) {echo 'selected="selected"';} ?> ><?php echo JText::_('COM_PHOCAGALLERY_DROP_SHADOW_TR')?></option>
		<option value="3" <?php if ($this->t['sie'] == 3) {echo 'selected="selected"';} ?> ><?php echo JText::_('COM_PHOCAGALLERY_OUTER_GLOW')?></option>
	</select></td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_IMAGE_EFFECT_COLOR' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="siec" id="siec" size="12" maxlength="8" value="<?php echo $this->t['siec'];?>" /> <span style="margin-left:10px" onclick="openPicker('siec')"  class="picker_buttons"><?php echo  JText::_('COM_PHOCAGALLERY_PICK_COLOR'); ?></span> (4)</td>
</tr>


<tr>
	<td valign="middle" align="right" class="key"><?php echo JText::_( 'COM_PHOCAGALLERY_CURRENT_IMAGE' ); ?>:</td>
	<td valign="middle">
	<?php echo '(shadow3.png)';?>
	<br />
	<?php echo '<img src="'.JURI::root().'/media/com_phocagallery/images/shadow3.png' .'?imagesid='.md5(uniqid(time())) . '" alt="" />';
	?>
	</td>
</tr>
</table>
<div style="text-align:right"><input type="submit" name="sisubmit" value="<?php echo JText::_('COM_PHOCAGALLERY_CREATE_BG_IMAGE');?>" /></div>

<input type="hidden" name="type" value="" />
<input type="hidden" name="task" value="phocagalleryt.bgimagesmall" />
<input type="hidden" name="option" value="com_phocagallery" />
<?php echo Joomla\CMS\HTML\HTMLHelper::_( 'form.token' ); ?>
</form>

</fieldset>



<fieldset>
<legend><?php echo JText::_('COM_PHOCAGALLERY_BACKGROUND_IMAGE'). ' - '. JText::_('COM_PHOCAGALLERY_MEDIUM_THUMBNAIL');?></legend>

<div style="float:right;position:relative;margin-top:15px;margin-right:10px">
<?php echo Joomla\CMS\HTML\HTMLHelper::_('image', 'media/com_phocagallery/images/administrator/image-bg-image.png', '' ); ?>
</div>

<form  action="index.php" method="post" name="adminFormBackgroundImageMedium">
<table class="adminform">
<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_MEDIUM_IMAGE_WIDTH' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="miw" id="miw" size="12" maxlength="8" value="<?php echo $this->t['miw'];?>" /></td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_MEDIUM_IMAGE_HEIGHT' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="mih" id="mih" size="12" maxlength="8" value="<?php echo $this->t['mih'];?>" /></td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_SITE_BACKGROUND_COLOR' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="msbgc" id="msbgc" size="12" maxlength="8" value="<?php echo $this->t['msbgc'];?>" /> <span style="margin-left:10px" onclick="openPicker('msbgc')"  class="picker_buttons"><?php echo  JText::_('COM_PHOCAGALLERY_PICK_COLOR'); ?></span> (1)</td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_IMAGE_BACKGROUND_COLOR' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="mibgc" id="mibgc" size="12" maxlength="8" value="<?php echo $this->t['mibgc'];?>" /> <span style="margin-left:10px" onclick="openPicker('mibgc')"  class="picker_buttons"><?php echo  JText::_('COM_PHOCAGALLERY_PICK_COLOR'); ?></span> (2)</td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_IMAGE_BORDER_COLOR' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="mibrdc" id="mibrdc" size="12" maxlength="8" value="<?php echo $this->t['mibrdc'];?>" /> <span style="margin-left:10px" onclick="openPicker('mibrdc')"  class="picker_buttons"><?php echo  JText::_('COM_PHOCAGALLERY_PICK_COLOR'); ?></span> (3)</td>
</tr>

<tr>
	<td valign="middle" align="right" class="key"><?php echo JText::_( 'COM_PHOCAGALLERY_IMAGE_EFFECT' ); ?>:</td>
	<td valign="middle"><select id="mie" name="mie">
		<option value="0" <?php if ($this->t['mie'] == 0) {echo 'selected="selected"';} ?> ><?php echo JText::_('COM_PHOCAGALLERY_NONE')?></option>
		<option value="1" <?php if ($this->t['mie'] == 1) {echo 'selected="selected"';} ?> ><?php echo JText::_('COM_PHOCAGALLERY_DROP_SHADOW')?></option>
		<option value="2" <?php if ($this->t['mie'] == 2) {echo 'selected="selected"';} ?> ><?php echo JText::_('COM_PHOCAGALLERY_DROP_SHADOW_TR')?></option>
		<option value="3" <?php if ($this->t['mie'] == 3) {echo 'selected="selected"';} ?> ><?php echo JText::_('COM_PHOCAGALLERY_OUTER_GLOW')?></option>
	</select></td>
</tr>

<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo JText::_( 'COM_PHOCAGALLERY_IMAGE_EFFECT_COLOR' ); ?>:</label></td>
	<td colspan="2"><input class="text_area" type="text" name="miec" id="miec" size="12" maxlength="8" value="<?php echo $this->t['miec'];?>" /> <span style="margin-left:10px" onclick="openPicker('miec')"  class="picker_buttons"><?php echo  JText::_('COM_PHOCAGALLERY_PICK_COLOR'); ?></span> (4)</td>
</tr>


<tr>
	<td valign="middle" align="right" class="key"><?php echo JText::_( 'COM_PHOCAGALLERY_CURRENT_IMAGE' ); ?>:</td>
	<td valign="middle">
	<?php echo '(shadow1.png)';?>
	<br />
	<?php echo '<img src="'.JURI::root().'/media/com_phocagallery/images/shadow1.png' .'?imagesid='.md5(uniqid(time())) . '" alt="" />';
	//echo Joomla\CMS\HTML\HTMLHelper::_('image', 'media/com_phocagallery/images/shadow1.'.$this->t['formaticon'] .'?imagesid='.md5(uniqid(time())),'');
	?>
	</td>
</tr>
</table>
<div style="text-align:right"><input type="submit" name="misubmit" value="<?php echo JText::_('COM_PHOCAGALLERY_CREATE_BG_IMAGE');?>" /></div>

<input type="hidden" name="type" value="" />
<input type="hidden" name="task" value="phocagalleryt.bgimagemedium" />
<input type="hidden" name="option" value="com_phocagallery" />
<?php echo Joomla\CMS\HTML\HTMLHelper::_( 'form.token' ); ?>
</form>

</fieldset>


