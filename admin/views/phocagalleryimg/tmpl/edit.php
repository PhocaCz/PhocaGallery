<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Uri\Uri;

$task		= 'phocagalleryimg';

//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');
//JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

$r 			= $this->r;
$app		= Factory::getApplication();
$option 	= $app->input->get('option');
$OPT		= strtoupper($option);

/*
<script type="text/javascript">
Joomla.submitbutton = function(task){
	if (task != 'phocagalleryimg.cancel' && document.getElementById('jform_catid').value == '') {
		alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')) . ' - '. $this->escape(Text::_('COM_PHOCAGALLERY_CATEGORY_NOT_SELECTED'));?>');
	} else if (task == 'phocagalleryimg.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
		<?php //echo $this->form->getField('description')->save(); ?>
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
	else {
		<?php /* Joomla.renderMessages({"error": ["<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true);?>"]});
		 //alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true);?>'); *//* ?>

		// special case for modal popups validation response
		jQuery('#adminForm .modal-value.invalid').each(function(){

			var field = jQuery(this),
				idReversed = field.attr('id').split('').reverse().join(''),
				separatorLocation = idReversed.indexOf('_'),
				nameId = '#' + idReversed.substr(separatorLocation).split('').reverse().join('') + 'name';
			alert(nameId);
			jQuery(nameId).addClass('invalid');
		});
		return false;

	}
}
</script>
*/


Factory::getDocument()->addScriptDeclaration(

'Joomla.submitbutton = function(task) { 
	if (task != "'. $this->t['task'].'.cancel" && document.getElementById("jform_catid").value == "") {
		alert("'. Text::_('JGLOBAL_VALIDATION_FORM_FAILED', true) . ' - '. Text::_('COM_PHOCAGALLERY_CATEGORY_NOT_SELECTED', true).'");
	} else if (task == "'. $this->t['task'].'.cancel" || document.formvalidator.isValid(document.getElementById("adminForm"))) {
		Joomla.submitform(task, document.getElementById("adminForm"));
	} else {

	    // special case for modal popups validation response
		jQuery("#adminForm .modal-value.invalid").each(function(){

			var field = jQuery(this),
				idReversed = field.attr("id").split("").reverse().join(""),
				separatorLocation = idReversed.indexOf("_"),
				nameId = "#" + idReversed.substr(separatorLocation).split("").reverse().join("") + "name";
			jQuery(nameId).addClass("invalid");
		});
		return false;

	}


}'

);

echo $r->startHeader();
echo $r->startForm($option, $task, $this->item->id, 'adminForm', 'adminForm');
// First Column
//echo '<div class="span12 form-horizontal">';
echo '<div>';
$tabs = array (
'general' 		=> Text::_($OPT.'_GENERAL_OPTIONS'),
'publishing' 	=> Text::_($OPT.'_PUBLISHING_OPTIONS'),
'geo' 			=> Text::_($OPT.'_GEO_OPTIONS'),
'external'		=> Text::_($OPT.'_EXTERNAL_LINK_OPTIONS'),
'metadata'		=> Text::_($OPT.'_METADATA_OPTIONS'));
echo $r->navigation($tabs);

// Header
// - - - - - - - - - -
// Image

$fileOriginal = PhocaGalleryFile::getFileOriginal($this->item->filename);
if (!File::exists($fileOriginal)) {
	$this->item->fileoriginalexist = 0;
} else {
	$fileThumb 		= PhocaGalleryFileThumbnail::getOrCreateThumbnail($this->item->filename, '', 0, 0, 0);
	$this->item->linkthumbnailpath 	= $fileThumb['thumb_name_m_no_rel'];
	$this->item->fileoriginalexist = 1;
}

$image = '';
if (isset($this->item->extid) && $this->item->extid !='') {

	$resW				= explode(',', $this->item->extw);
	$resH				= explode(',', $this->item->exth);
	$correctImageRes 	= PhocaGalleryImage::correctSizeWithRate($resW[2], $resH[2], 100, 100);
	$imgLink			= $this->item->extl;

	$image = '<img class="img-polaroid" src="'.$this->item->exts.'" width="'.$correctImageRes['width'].'" height="'.$correctImageRes['height'].'" alt="" />';

} else if (isset ($this->item->fileoriginalexist) && $this->item->fileoriginalexist == 1) {

	$imageRes			= PhocaGalleryImage::getRealImageSize($this->item->filename, 'medium');
	//$correctImageRes 	= PhocaGalleryImage::correctSizeWithRate($imageRes['w'], $imageRes['h'], 100, 100);
	$imgLink			= PhocaGalleryFileThumbnail::getThumbnailName($this->item->filename, 'large');
	// TO DO check the image

	$image = '<img class="img-polaroid" style="max-width:100px;" src="'.Uri::root().$this->item->linkthumbnailpath.'?imagesid='.md5(uniqid(time())).'" alt="" />'
	.'</a>';
}
$formArray = array ('title', 'alias');
echo $r->groupHeader($this->form, $formArray, $image);

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');





/*
echo '<div class="ph-float-right ph-admin-additional-box">';
// PICASA
if (isset($this->item->extid) && $this->item->extid !='') {

	$resW				= explode(',', $this->item->extw);
	$resH				= explode(',', $this->item->exth);
	$correctImageRes 	= PhocaGalleryImage::correctSizeWithRate($resW[2], $resH[2], 100, 100);
	$imgLink			= $this->item->extl;

	echo '<img class="img-polaroid" src="'.$this->item->exts.'" width="'.$correctImageRes['width'].'" height="'.$correctImageRes['height'].'" alt="" />';

} else if (isset ($this->item->fileoriginalexist) && $this->item->fileoriginalexist == 1) {

	$imageRes			= PhocaGalleryImage::getRealImageSize($this->item->filename, 'medium');
	//$correctImageRes 	= PhocaGalleryImage::correctSizeWithRate($imageRes['w'], $imageRes['h'], 100, 100);
	$imgLink			= PhocaGalleryFileThumbnail::getThumbnailName($this->item->filename, 'large');
	// TO DO check the image

	echo '<img class="img-polaroid" style="max-width:100px;" src="'.Uri::root().$this->item->linkthumbnailpath.'?imagesid='.md5(uniqid(time())).'" alt="" />'
	.'</a>';
} else {

}
echo '</div>';
*/




$formArray = array ('catid', 'ordering', 'filename', 'videocode', 'pcproductid');
echo $r->group($this->form, $formArray);

echo $this->form->getInput('extid');

$formArray = array('description');
echo $r->group($this->form, $formArray, 1);




echo $r->endTab();

echo $r->startTab('publishing', $tabs['publishing']);
foreach($this->form->getFieldset('publish') as $field) {
	echo '<div class="control-group">';
	if (!$field->hidden) {
		echo '<div class="control-label">'.$field->label.'</div>';
	}
	echo '<div class="controls">';
	echo $field->input;
	echo '</div></div>';
}
echo $r->endTab();

echo $r->startTab('geo', $tabs['geo']);
$formArray = array ('latitude', 'longitude', 'zoom', 'geotitle');
echo $r->group($this->form, $formArray);
echo $r->endTab();

echo $r->startTab('external', $tabs['external']);
echo '<div class="clearfix"></div>'. "\n";
echo '<h3>'.Text::_('COM_PHOCAGALLERY_EXTERNAL_LINKS1').'</h3>'."\n";
$formArray = array ('extlink1link', 'extlink1title', 'extlink1target', 'extlink1icon');
echo $r->group($this->form, $formArray);

echo '<div class="clearfix"></div>'. "\n";
echo '<h3>'.Text::_('COM_PHOCAGALLERY_EXTERNAL_LINKS2').'</h3>'."\n";
$formArray = array ('extlink2link', 'extlink2title', 'extlink2target', 'extlink2icon');
echo $r->group($this->form, $formArray);
echo $r->endTab();


echo $r->startTab('metadata', $tabs['metadata']);
echo $this->loadTemplate('metadata');
echo $r->endTab();


//echo '</div>';//end span10
// Second Column
//echo '<div class="span2">';




echo $r->endTabs();
echo '</div>';

echo $r->formInputs($this->t['task']);
echo $r->endForm();
?>
