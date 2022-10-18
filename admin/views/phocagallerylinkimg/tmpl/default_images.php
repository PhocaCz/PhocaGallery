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
$user 	= Factory::getUser();

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'a.ordering');

$view = 'category';
if($this->t['type'] == 5) {
    $view = 'category-masonry';
}

//JHtml::_('behavior.tooltip');
?>
<script type="text/javascript">
//<![CDATA[
function insertLink() {

    if (!Joomla.getOptions('xtd-phocagallery')) {
       return false;
    }

       var _Joomla$getOptions = Joomla.getOptions('xtd-phocagallery'), editor = _Joomla$getOptions.editor;

	<?php
	/*$items = array('imageshadow', 'fontcolor', 'bgcolor', 'bgcolorhover', 'imagebgcolor', 'bordercolor', 'bordercolorhover', 'detail','displayname', 'displaydetail', 'displaydownload', 'displaybuttons', 'displaydescription', 'descriptionheight' ,'namefontsize', 'namenumchar', 'enableswitch', 'overlib', 'piclens','float', 'boxspace', 'displayimgrating', 'pluginlink', 'type', 'imageordering', 'minboxwidth' );
	$itemsArrayOutput = '';
	foreach ($items as $key => $value) {

		echo 'var '.$value.' = document.getElementById("'.$value.'").value;'."\n"
			.'if ('.$value.' != \'\') {'. "\n"
			.''.$value.' = "|'.$value.'="+'.$value.';'."\n"
			.'}';
		$itemsArrayOutput .= '+'.$value;
	}*/
	?>

	/* LimitStart*/
	var limitStartOutput = '';
	var limitstart = document.getElementById("limitstartparam").value;
	if (limitstart != '') {
		limitStartOutput = "|limitstart="+limitstart;
	}
	/* LimitCount*/
	var limitCountOutput = '';
	var limitcount = document.getElementById("limitcountparam").value;
	if (limitcount != '') {
		limitCountOutput = "|limitcount="+limitcount;
	}

    /* max*/
	var maxOutput = '';
	var max = document.getElementById("maxparam").value;
	if (max != '') {
		maxOutput = "|max="+max;
	}

    /* ImageOrdering*/
    var imageOrderingOutput = '';
	var imageordering = document.getElementById("imageordering").value;
	if (imageordering != '') {
		imageOrderingOutput = "|imageordering="+imageordering;
	}

	/* Category */
	var categoryid = document.getElementById("filter_catid").value;
	var categoryIdOutput = '';
	if (categoryid != '') {
		categoryIdOutput = "|categoryid="+categoryid;
	}

	if (limitStartOutput != '') {
		/*return false;*/
	} else {
		alert("<?php echo Text::_( 'COM_PHOCAGALLERY_PLEASE_SELECT_LIMIT_START', true ); ?>");
		return false;
	}

	if (limitCountOutput != '') {
		/*return false;*/
	} else {
		alert("<?php echo Text::_( 'COM_PHOCAGALLERY_PLEASE_SELECT_LIMIT_COUNT', true ); ?>");
		return false;
	}


	if (categoryIdOutput != '' &&  parseInt(categoryid) > 0) {
		/*return false;*/
	} else {
		alert("<?php echo Text::_( 'COM_PHOCAGALLERY_PLEASE_SELECT_CATEGORY', true ); ?>");
		return false;
	}

	var tag = "{phocagallery view=<?php echo $view ?>"+categoryIdOutput+limitStartOutput+limitCountOutput+maxOutput+imageOrderingOutput<?php /*echo $itemsArrayOutput*/ ?>+"}";

    window.parent.Joomla.editors.instances[editor].replaceSelection(tag);

  if (window.parent.Joomla.Modal) {
    window.parent.Joomla.Modal.getCurrent().close();
  }

    return false;

  <?php /*window.parent.jInsertEditorText(tag, '<?php echo $this->t['ename']; ?>');
	window.parent.SqueezeBox.close(); */ ?>
}
//]]>
</script>
<div id="phocagallery-links">
<fieldset class="adminform options-menu options-form">
<legend><?php echo Text::_('COM_PHOCAGALLERY_IMAGES'); ?></legend>


<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm"  id="adminForm">

<div class="control-group">
    <div class="control-label"><label for="title" ><?php echo Text::_( 'COM_PHOCAGALLERY_CATEGORY' ); ?></label></div>
    <div class="controls"><?php echo $this->lists['catid']; ?></div>
</div>

<div class="control-group">
    <div class="control-label"><label for="imagecategories"><?php echo Text::_( 'COM_PHOCAGALLERY_LIMIT_START' ); ?></label></div>
    <div class="controls"><?php echo $this->lists['limitstartparam'];?></div>
</div>

<div class="control-group">
    <div class="control-label"><label for="imagecategories"><?php echo Text::_( 'COM_PHOCAGALLERY_LIMIT_COUNT' ); ?></label></div>
    <div class="controls"><?php echo $this->lists['limitcountparam'];?></div>
</div>

<div class="control-group">
    <div class="control-label"><label for="imagemax"><?php echo Text::_( 'COM_PHOCAGALLERY_MAX_NUMBER_IMAGES' ); ?></label></div>
    <div class="controls"><input type="text" name="maxparam" id="maxparam" value="" class="form-control"></div>
</div>



<input type="hidden" name="controller" value="phocagallerylinkimg" />
<input type="hidden" name="type" value="<?php echo (int)$this->t['type']; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<input type="hidden" name="e_name" value="<?php echo $this->t['ename']?>" />
</form>



<form name="adminFormLink" id="adminFormLink">
<div class="control-group">
                    <div class="control-label">
                        <label for="imageordering"><?php echo Text::_( 'COM_PHOCAGALLERY_FIELD_IMAGE_ORDERING_LABEL' ); ?></label>
                        </div>
		<div class="controls"><select name="imageordering" id="imageordering" class="form-select">
			<option value="" selected="selected"><?php echo Text::_('COM_PHOCAGALLERY_DEFAULT')?></option>
			<option value="1"><?php echo Text::_('COM_PHOCAGALLERY_ORDERING_ASC')?></option>
			<option value="2"><?php echo Text::_('COM_PHOCAGALLERY_ORDERING_DESC')?></option>
			<option value="3"><?php echo Text::_('COM_PHOCAGALLERY_TITLE_ASC')?></option>
			<option value="4"><?php echo Text::_('COM_PHOCAGALLERY_TITLE_DESC')?></option>
			<option value="5"><?php echo Text::_('COM_PHOCAGALLERY_DATE_ASC')?></option>
			<option value="6"><?php echo Text::_('COM_PHOCAGALLERY_DATE_DESC')?></option>
			<option value="7"><?php echo Text::_('COM_PHOCAGALLERY_ID_ASC')?></option>
			<option value="8"><?php echo Text::_('COM_PHOCAGALLERY_ID_DESC')?></option>
			<option value="9"><?php echo Text::_('COM_PHOCAGALLERY_RANDOM')?></option>
            </select>
		</div>
</div>

	<?php /*
	// Colors
	$itemsColor = array ('fontcolor' => 'COM_PHOCAGALLERY_FIELD_FONT_COLOR_LABEL', 'bgcolor' => 'COM_PHOCAGALLERY_FIELD_BACKGROUND_COLOR_LABEL', 'bgcolorhover' => 'COM_PHOCAGALLERY_FIELD_BACKGROUND_COLOR_HOVER_LABEL', 'imagebgcolor' => 'COM_PHOCAGALLERY_FIELD_IMAGE_BACKGROUND_COLOR_LABEL', 'bordercolor' => 'COM_PHOCAGALLERY_FIELD_BORDER_COLOR_LABEL', 'bordercolorhover' => 'COM_PHOCAGALLERY_FIELD_BORDER_COLOR_HOVER_LABEL');

	foreach ($itemsColor as $key => $value) {
		echo '<tr>'
		.'<td class="key" align="right" width="30%"><label for="'.$key.'">'.Text::_($value).'</label></td>'
		.'<td nowrap="nowrap"><input type="text" name="'.$key.'" id="'.$key.'" value="" class="text_area" /><span style="margin-left:10px" onclick="openPicker(\''.$key.'\')"  class="picker_buttons">'. Text::_('COM_PHOCAGALLERY_PICK_COLOR').'</span></td>'
		.'</tr>';
	}
	?>

	<tr>
		<td class="key" align="right" width="30%"><label for="detail"><?php echo Text::_( 'COM_PHOCAGALLERY_DETAIL_WINDOW' ); ?></label></td>
		<td width="70%">
		<select name="detail" id="detail" class="form-control">
		<option value=""  selected="selected"><?php echo Text::_( 'COM_PHOCAGALLERY_DEFAULT' )?></option>
		<option value="1" ><?php echo Text::_( 'COM_PHOCAGALLERY_STANDARD_POPUP_WINDOW' ); ?></option>
		<option value="0" ><?php echo Text::_( 'COM_PHOCAGALLERY_MODAL_POPUP_BOX' ); ?></option>
		<option value="2" ><?php echo Text::_( 'COM_PHOCAGALLERY_MODAL_POPUP_BOX_IMAGE_ONLY' ); ?></option>
		<option value="3" ><?php echo Text::_( 'COM_PHOCAGALLERY_SHADOWBOX' ); ?></option>
		<option value="4" ><?php echo Text::_( 'COM_PHOCAGALLERY_HIGHSLIDE' ); ?></option>
		<option value="5" ><?php echo Text::_( 'COM_PHOCAGALLERY_HIGHSLIDE_IMAGE_ONLY' ); ?></option>
		<option value="6" ><?php echo Text::_( 'COM_PHOCAGALLERY_JAK_LIGHTBOX' ); ?></option>
		<option value="8" ><?php echo Text::_( 'COM_PHOCAGALLERY_SLIMBOX' ); ?></option>
		<?php /*<option value="7" >No Popup</option>*//* ?>
		</select></td>
	</tr>

	<?php
		echo '<tr>'
		.'<td class="key" align="right" width="30%"><label for="pluginlink">'.Text::_('COM_PHOCAGALLERY_PLUGIN_LINK').'</label></td>'
		.'<td nowrap><select name="pluginlink" id="pluginlink" class="form-control">'
		.'<option value=""  selected="selected">'. Text::_( 'COM_PHOCAGALLERY_DEFAULT' ).'</option>'
		.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_LINK_TO_DETAIL_IMAGE' ).'</option>'
		.'<option value="1" >'.Text::_( 'COM_PHOCAGALLERY_LINK_TO_CATEGORY' ).'</option>'
		.'<option value="2" >'.Text::_( 'COM_PHOCAGALLERY_LINK_TO_CATEGORIES' ).'</option>';

		echo '<tr>'
		.'<td class="key" align="right" width="30%"><label for="type">'.Text::_('COM_PHOCAGALLERY_PLUGIN_TYPE').'</label></td>'
		.'<td nowrap><select name="type" id="type" class="form-control">'
		.'<option value=""  selected="selected">'. Text::_( 'COM_PHOCAGALLERY_DEFAULT' ).'</option>'
		.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_LINK_TO_DETAIL_IMAGE' ).'</option>'
		.'<option value="1" >'.Text::_( 'COM_PHOCAGALLERY_MOSAIC' ).'</option>'
		.'<option value="2" >'.Text::_( 'COM_PHOCAGALLERY_LARGE_IMAGE' ).'</option>';

	// yes/no
	$itemsYesNo = array ('displayname' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_NAME_LABEL', 'displaydetail' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_DETAIL_ICON_LABEL', 'displaydownload' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_DOWNLOAD_ICON_LABEL', 'displaybuttons' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_BUTTONS_LABEL', 'displaydescription' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_DESCRIPTION_DETAIL_LABEL', 'displayimgrating' => 'COM_PHOCAGALLERY_DISPLAY_IMAGE_RATING' );
	foreach ($itemsYesNo as $key => $value) {
		echo '<tr>'
		.'<td class="key" align="right" width="30%"><label for="'.$key.'">'.Text::_($value).'</label></td>'
		.'<td nowrap><select name="'.$key.'" id="'.$key.'" class="form-control">'
		.'<option value=""  selected="selected">'. Text::_( 'COM_PHOCAGALLERY_DEFAULT' ).'</option>';

		if ($key == 'displaydownload') {
			echo '<option value="1" >'. Text::_( 'COM_PHOCAGALLERY_SHOW' ).'</option>'
			.'<option value="2" >'.Text::_( 'COM_PHOCAGALLERY_SHOW_DIRECT_DOWNLOAD' ).'</option>'
			.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_HIDE' ).'</option>';
		} else {
			echo '<option value="1" >'. Text::_( 'COM_PHOCAGALLERY_SHOW' ).'</option>'
			.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_HIDE' ).'</option>';
		}
		echo '</select></td>'
		.'</tr>';
	}


	// Number
	$itemsNumber = array ('descriptionheight' => 'COM_PHOCAGALLERY_FIELD_DESCRIPTION_DETAIL_HEIGHT_LABEL','namefontsize' => 'COM_PHOCAGALLERY_FIELD_FONT_SIZE_NAME_LABEL', 'namenumchar' => 'COM_PHOCAGALLERY_FIELD_CHAR_LENGTH_NAME_LABEL', 'boxspace' => 'COM_PHOCAGALLERY_FIELD_CATEGORY_BOX_SPACE_LABEL','minboxwidth' => 'COM_PHOCAGALLERY_MIN_BOX_WIDTH');
	foreach ($itemsNumber as $key => $value) {
		echo '<tr>'
		.'<td class="key" align="right" width="30%"><label for="'.$key.'">'.Text::_($value).'</label></td>'
		.'<td nowrap="nowrap"><input type="text" name="'.$key.'" id="'.$key.'" value="" class="text_area" /></td>'
		.'</tr>';
	}

	echo '<tr>'
		.'<td class="key" align="right" width="30%"><label for="enableswitch">'.Text::_('COM_PHOCAGALLERY_SWITCH_IMAGE').'</label></td>'
		.'<td nowrap><select name="enableswitch" id="enableswitch" class="form-control">'
		.'<option value=""  selected="selected">'. Text::_( 'COM_PHOCAGALLERY_DEFAULT' ).'</option>'
		.'<option value="1" >'.Text::_( 'COM_PHOCAGALLERY_ENABLE' ).'</option>'
		.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_DISABLE' ).'</option>';

	echo '<tr>'
		.'<td class="key" align="right" width="30%"><label for="overlib">'.Text::_('COM_PHOCAGALLERY_FIELD_OVERLIB_EFFECT_LABEL').'</label></td>'
		.'<td nowrap><select name="overlib" id="overlib" class="form-control">'
		.'<option value=""  selected="selected">'. Text::_( 'COM_PHOCAGALLERY_DEFAULT' ).'</option>'
		.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_NONE' ).'</option>'
		.'<option value="1" >'.Text::_( 'COM_PHOCAGALLERY_ONLY_IMAGE' ).'</option>'
		.'<option value="2" >'.Text::_( 'COM_PHOCAGALLERY_ONLY_DESCRIPTION' ).'</option>'
		.'<option value="3" >'.Text::_( 'COM_PHOCAGALLERY_IMAGE_AND_DESCRIPTION' ).'</option>';

	echo '<tr>'
		.'<td class="key" align="right" width="30%"><label for="piclens">'.Text::_('COM_PHOCAGALLERY_ENABLE_COOLIRIS').'</label></td>'
		.'<td nowrap><select name="piclens" id="piclens" class="form-control">'
		.'<option value=""  selected="selected">'. Text::_( 'COM_PHOCAGALLERY_DEFAULT' ).'</option>'
		.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_NO' ).'</option>'
		.'<option value="1" >'.Text::_( 'COM_PHOCAGALLERY_YES' ).'</option>'
		.'<option value="2" >'.Text::_( 'COM_PHOCAGALLERY_FIELD_YES_START_COOLIRIS' ).'</option>';
	?>


	<tr>
		<td class="key" align="right" width="30%"><label for="float"><?php echo Text::_( 'COM_PHOCAGALLERY_FLOAT_IMAGE' ); ?></label></td>
		<td width="70%">
			<select name="float" id="float">
			<option value=""  selected="selected"><?php echo Text::_( 'COM_PHOCAGALLERY_DEFAULT' )?></option>
			<option value="left" ><?php echo Text::_( 'COM_PHOCAGALLERY_LEFT' ); ?></option>
			<option value="right" ><?php echo Text::_( 'COM_PHOCAGALLERY_RIGHT' ); ?></option>
			</select>
		</td>
	</tr>
*/
?>
	<div class="btn-box-submit">
                    <button class="btn btn-primary plg-button-insert " onclick="insertLink();return false;"><span class="icon-ok"></span> <?php echo Text::_('COM_PHOCAGALLERY_INSERT_CODE'); ?></button>
                </div>
            </form>

</fieldset>
<div class="btn-box-back"><a class="btn btn-light" href="<?php echo $this->t['backlink']; ?>"><span class="icon-arrow-left"></span> <?php echo Text::_('COM_PHOCAGALLERY_BACK') ?></a></div>
</div>
