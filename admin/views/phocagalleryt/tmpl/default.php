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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
Factory::getDocument()->addScriptDeclaration(

'Joomla.submitbutton = function(task) {
	if (task == "'. $this->t['task'].'.cancel" || document.formvalidator.isValid(document.getElementById("phocagalleryt-form"))) {
		Joomla.submitform(task, document.getElementById("phocagalleryt-form"));
	} else {
		return false;
	}
}'

); ?>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="phocagalleryt-form" class="form-validate">

<?php
if ($this->require_ftp) {
	echo PhocaGalleryFileUpload::renderFTPaccess();
}
?>
<table class="adminform" border="0">
<?php if ($this->theme_name != '') { ?>
	<tr>
		<td colspan="3"><?php echo Text::_( 'COM_PHOCAGALLERY_CURRENT_THEME' ); ?> : <?php echo $this->theme_name; ?></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
<?php
}
?>
	<?php /*
	<tr>
		<td width="5"><input type="checkbox" name="theme_component" value=""  /></td>
		<td colspan="2"><?php echo Text::_( 'COM_PHOCAGALLERY_APPLY_COMPONENT' ); ?></td>
	</tr>

	<tr>
		<td width="5"><input type="checkbox" name="theme_categories" value="" /></td>
		<td colspan="2"><?php echo Text::_( 'COM_PHOCAGALLERY_APPLY_CATEGORIES' ); ?></td>
	</tr>
	<tr>
		<td width="5"><input type="checkbox" name="theme_category" value="" /></td>
		<td colspan="2"><?php echo Text::_( 'COM_PHOCAGALLERY_APPLY_CATEGORY' ); ?></td>
	</tr>
	*/ ?>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="2"><b><?php echo Text::_( 'COM_PHOCAGALLERY_UPLOAD_THEME_PACKAGE_FILE' ); ?></b></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td width="120">
			<label for="install_package"><?php echo Text::_( 'COM_PHOCAGALLERY_UPLOAD_FILE' ); ?>:</label>
		</td>
		<td>

			<input type="file" id="sfile-upload" class="input" name="Filedata" size="57" />
			<?php /* <input class="button" type="button" value="<?php echo JText::_( 'COM_PHOCAGALLERY_UPLOAD_FILE' ); ?> &amp; <?php echo JText::_( 'COM_PHOCAGALLERY_INSTALL' ); ?>" onclick="Joomla.submitbutton()" /> */ ?>

			<button onclick="Joomla.submitbutton()" class="btn btn-primary" id="upload-submit"><i class="icon-upload icon-white"></i> <?php echo Text::_( 'COM_PHOCAGALLERY_UPLOAD_FILE' ); ?> &amp; <?php echo Text::_( 'COM_PHOCAGALLERY_INSTALL' ); ?></button>

		</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	</table>


<input type="hidden" name="type" value="" />
<input type="hidden" name="option" value="com_phocagallery" />
<input type="hidden" name="task" value="phocagalleryt.themeinstall" />
<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>

<?php
echo '<h4>'.Text::_('COM_PHOCAGALLERY_EDIT_CSS_FILES'). '</h4>';
echo '<ul class="nav nav-tabs nav-stacked"><li><a href="index.php?option=com_phocagallery&view=phocagalleryefs"><i class="icon-edit"></i> '.Text::_('COM_PHOCAGALLERY_EDIT_CSS_FILES'). '</a></li></ul>';
?>

<p>&nbsp;</p><div class="btn-group"><a class="btn btn-large btn-primary" href="https://www.phoca.cz/themes/" target="_blank"><i class="icon-grid-view-2 icon-white"></i>&nbsp;&nbsp;<?php echo Text::_('COM_PHOCAGALLERY_NEW_THEME_DOWNLOAD'); ?></a></div>

