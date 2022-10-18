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
//JHtml::_('behavior.tooltip');

$js = '
function insertPGLink() {

        if (!Joomla.getOptions(\'xtd-phocagallery\')) {
            return false;
        }

        var _Joomla$getOptions = Joomla.getOptions(\'xtd-phocagallery\'), editor = _Joomla$getOptions.editor;


	var imagecategories = document.getElementById("imagecategories").value;
	if (imagecategories != \'\') {
		imagecategories = "|imagecategories="+imagecategories;
	}
	var imagecategoriessize = document.getElementById("imagecategoriessize").value;
	if (imagecategoriessize != \'\') {
		imagecategoriessize = "|imagecategoriessize="+imagecategoriessize;
	}

	var hideCategoriesOutput = \'\';
	hidecategories = getSelectedData();

	if (hidecategories != \'\') {
		hideCategoriesOutput = "|hidecategories="+hidecategories;
	}

	var tag = "{phocagallery view=categories"+imagecategories+imagecategoriessize+hideCategoriesOutput+"}";
	window.parent.Joomla.editors.instances[editor].replaceSelection(tag);

          if (window.parent.Joomla.Modal) {
            window.parent.Joomla.Modal.getCurrent().close();
          }

        return false;
}

function getSelectedData(array) {
	var selected = new Array();
	var dataSelect = document.forms["adminFormLink"].elements["hidecategories"];

    if (dataSelect === undefined ) {
        return \'\';
    }
	for(j = 0; j < dataSelect.options.length; j++){
		if (dataSelect.options[j].selected) {
			selected.push(dataSelect.options[j].value); }
	}
	if (array != \'true\') {
		return selected.toString();
	} else {
		return selected;
	}
}';

Factory::getDocument()->addScriptDeclaration($js);
?>
<div id="phocagallery-links">
    <fieldset class="adminform options-menu options-form">
<legend><?php echo Text::_( 'COM_PHOCAGALLERY_CATEGORIES' ); ?></legend>
<form name="adminFormLink" id="adminFormLink">
            <div class="control-group">
                <div class="control-label">
			<label for="imagecategories">
				<?php echo Text::_( 'COM_PHOCAGALLERY_DISPLAY_IMAGES' ); ?>
			</label>
                </div>
                <div class="controls">
			<select name="imagecategories" id="imagecategories"  class="form-select">
			<option value="0" ><?php echo Text::_( 'COM_PHOCAGALLERY_NO' ); ?></option>
			<option value="1" selected="selected"><?php echo Text::_( 'COM_PHOCAGALLERY_YES' ); ?></option>
			</select>
		</div>
	</div>
	<div class="control-group">
        <div class="control-label">
			<label for="imagecategoriessize">
				<?php echo Text::_( 'COM_PHOCAGALLERY_IMAGE_SIZE' ); ?>
			</label>
		</div>
		<div class="controls">
			<select name="imagecategoriessize" id="imagecategoriessize" class="form-select">
			<option value="0"><?php echo Text::_( 'COM_PHOCAGALLERY_SMALL' ); ?></option>
			<option value="1" selected="selected"><?php echo Text::_( 'COM_PHOCAGALLERY_MEDIUM' ); ?></option>
			</select>
		</div>
	</div>


	<div class="control-group">
		<div class="control-label">
			<label for="hidecategories">
				<?php echo Text::_( 'COM_PHOCAGALLERY_HIDE_CATEGORIES' ); ?>
			</label>
		</div>
		<div class="controls">
		<?php echo $this->categoriesoutput;?>
		</div>
	</div>

	<div class="btn-box-submit">
                    <button class="btn btn-primary plg-button-insert " onclick="insertPGLink();"><span class="icon-ok"></span> <?php echo Text::_('COM_PHOCAGALLERY_INSERT_CODE'); ?></button>
                </div>
        </form>

</fieldset>
    <div class="btn-box-back"><a class="btn btn-light" href="<?php echo $this->t['backlink']; ?>"><span class="icon-arrow-left"></span> <?php echo Text::_('COM_PHOCAGALLERY_BACK') ?></a></div>
</div>
