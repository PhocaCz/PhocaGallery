<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

JFactory::getDocument()->addScriptDeclaration(
'function importYtb(task) {
	if (document.getElementById("ytblink").value == "") {
		alert("'. Text::_("COM_PHOCAGALLERY_YTB_LINK_NOT_SET", true).'");
	} else {
		Joomla.submitform(task, document.getElementById("phocagalleryytb-form"));
	}
}

function pasteYtb() {
	var link 	= "";
	var title 	= "";
	var desc	= "";
	var filename= "";
	if (document.getElementById("ytblink").value != "") {
		link = document.getElementById("ytblink").value;
	}
	if (document.getElementById("ytbtitle").value != "") {
		title = document.getElementById("ytbtitle").value;
	}
	if (document.getElementById("ytbdesc").value != "") {
		desc = document.getElementById("ytbdesc").value;
	}
	if (document.getElementById("ytbfilename").value != "") {
		filename = document.getElementById("ytbfilename").value;
	}

	if (window.parent) {
		//window.parent.jInsertEditorText(desc, "jform_description");
		window.parent.Joomla.editors.instances["jform_description"].replaceSelection(desc);
		window.parent.phocaSelectYtb_'. PhocaGalleryText::filterValue($this->t["field"], "alphanumeric2") .'(link, title, desc, filename);
	}
}'
); ?>


<form action="<?php Route::_('index.php?option=com_phocagallery'); ?>" method="post" name="adminForm" id="phocagalleryytb-form" class="form-validate">
	<div>
		<fieldset class="adminform">
			<legend><?php echo Text::_('COM_PHOCAGALLERY_YTB_IMPORT'); ?></legend>
				<table>
<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo Text::_( 'COM_PHOCAGALLERY_YTB_LINK' ); ?>:</label></td>
	<td colspan="2"><input class="text_area form-control" type="text" name="ytb_link" id="ytblink" size="60" value="<?php echo PhocaGalleryText::filterValue($this->t['ytblink'], 'text');?>" /></td>
</tr>
<?php if ((int)$this->t['catid'] < 1) { ?>
	<tr>
		<td width="100" align="right" class="key"><label for="title"><?php echo Text::_( 'COM_PHOCAGALLERY_SELECT_CATEGORY' ); ?>:</label></td>
		<td colspan="2"><select name="catid" class="form-control">
				<option value=""><?php echo Text::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo HTMLHelper::_('select.options', PhocaGalleryCategoryhtml::options('com_phocagallery'), 'value', 'text', '');?>
			</select></td>
	</tr>
<?php }

if ($this->t['import'] == '1') {
	?>
<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo Text::_( 'COM_PHOCAGALLERY_TITLE' ); ?>:</label></td>
	<td colspan="2"><input class="text_area form-control" type="text" name="ytb_title" id="ytbtitle" size="60" value="<?php echo PhocaGalleryText::filterValue($this->t['ytbtitle'], 'text');?>" /></td>
</tr>
<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo Text::_( 'COM_PHOCAGALLERY_DESCRIPTION' ); ?>:</label></td>
	<td colspan="2"><textarea class="text_area form-control" type="text" name="ytb_desc" id="ytbdesc" cols="9" rows="3"><?php echo PhocaGalleryText::filterValue($this->t['ytbdesc'], 'text');?></textarea></td>
</tr>
<tr>
	<td width="100" align="right" class="key"><label for="title"><?php echo Text::_( 'COM_PHOCAGALLERY_FILENAME' ); ?>:</label></td>
	<td colspan="2"><input class="text_area form-control" type="text" name="ytb_filename" id="ytbfilename" size="60" value="<?php echo PhocaGalleryText::filterValue($this->t['ytbfilename'], 'filepath');?>" /></td>
</tr>
	<?php
}
echo '</table>';

if ($this->t['import'] == '1') {
	echo '<div style="float:right;"><a href="javascript:void(0)" onclick="pasteYtb()"><div class=" btn btn-primary">'.Text::_('COM_PHOCAGALLERY_YTB_IMPORT_PASTE').'</div></a></div>';
} else {
	echo '<div style="float:right;"><a href="javascript:void(0)" onclick="importYtb(\'phocagalleryytb.import\')"><div class=" btn btn-primary">'.Text::_('COM_PHOCAGALLERY_YTB_IMPORT_IMPORT').'</div></a></div>';
}
?>
		</fieldset>
	</div>
	<div class="clearfix"></div>
<input type="hidden" name="task" value="" />
<?php if ((int)$this->t['catid'] > 0) { ?>
<input type="hidden" name="catid" value="<?php echo (int)$this->t['catid'] ;?>" />
<?php } ?>
<input type="hidden" name="field" value="<?php echo PhocaGalleryText::filterValue($this->t['field'], 'alphanumeric2') ;?>" />
<?php echo HTMLHelper::_('form.token'); ?>
</form>




