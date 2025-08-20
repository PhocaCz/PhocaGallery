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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$task		= 'phocagalleryc';

//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');
HtmlHelper::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

$r 			=  $this->r;
$app		= Factory::getApplication();
$option 	= $app->getInput()->get('option');
$OPT		= strtoupper($option);

Factory::getDocument()->addScriptDeclaration(

'Joomla.submitbutton = function(task) {

	if (task == "'. $this->t['task'].'.cancel" || document.formvalidator.isValid(document.getElementById("adminForm"))) {
		
		if (task == "phocagalleryc.loadextimgp") {
			document.getElementById("phOverlayDiv").style.display="block";
		}
		if (task == "phocagalleryc.loadextimgf") {
			document.getElementById("phOverlayDiv").style.display="block";
		}
		if (task == "phocagalleryc.uploadextimgf") {
			document.getElementById("phOverlayDiv").style.display="block";
		}
        if (task == "phocagalleryc.loadextimgi") {
            document.getElementById("phOverlayDiv").style.display="block";
        }

		Joomla.submitform(task, document.getElementById("adminForm"));
	} else {
		return false;
	}
}'

);

echo $r->startHeader();
echo $r->startForm($option, $task, $this->item->id, 'adminForm', 'adminForm');
// First Column
echo '<div class="span12 form-horizontal">';
$tabs = array (
'general' 		=> Text::_($OPT.'_GENERAL_OPTIONS'),
'publishing' 	=> Text::_($OPT.'_PUBLISHING_OPTIONS'),
'metadata'		=> Text::_($OPT.'_METADATA_OPTIONS'),
/*'picasa'		=> Text::_($OPT.'_PICASA_SETTINGS'),*/
'imgur'		    => Text::_($OPT.'_IMGUR_SETTINGS')/*,
'facebook'		=> Text::_($OPT.'_FB_SETTINGS')*/);
echo $r->navigation($tabs);


$formArray = array ('title', 'alias');
echo $r->groupHeader($this->form, $formArray);

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');
$formArray = array ('parent_id', 'image_id', 'ordering', 'access', 'accessuserid', 'uploaduserid', 'deleteuserid', 'owner_id', 'userfolder', 'latitude', 'longitude', 'zoom', 'geotitle');
echo $r->group($this->form, $formArray);
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

echo $r->startTab('metadata', $tabs['metadata']);
echo $this->loadTemplate('metadata');
echo $r->endTab();
/*
if ($this->t['enablepicasaloading'] == 1) {
	echo $r->startTab('picasa', $tabs['picasa']);
	$formArray = array ('extu', 'exta', 'extauth');
	echo $r->group($this->form, $formArray);
	echo $r->endTab();
}
*/
echo $r->startTab('imgur', $tabs['imgur']);
$formArray = array ('imgurclient', 'imguralbum');
echo $r->group($this->form, $formArray);
echo $r->endTab();
///
/*
echo '<div class="tab-pane" id="facebook">'. "\n";
// Extid is hidden - only for info if this is an external image (the filename field will be not required)
$formArray = array ('extfbuid', 'extfbcatid');
echo $r->group($this->form, $formArray);
echo '</div>';
*/
echo $r->endTabs();
echo '</div>';//end span10
// Second Column
//echo '<div class="span2"></div>';//end span2
echo $r->formInputs($this->t['task']);
echo $r->endForm();

echo '<div id="phOverlayDiv"><div id="phLoaderText">'.Text::_('COM_PHOCAGALLERY_IMGUR_LOADING_DATA').'</div><div id="phLoaderFull"></div></div>';
/*
?>
<div id="loading-ext-imgp"><div class="loading"><div><div class="ph-lds-ellipsis"><div></div><div></div><div></div><div></div></div><div>&nbsp;</div><div><?php echo Text::_('COM_PHOCAGALLERY_PICASA_LOADING_DATA'); ?></div></div></div>
<div id="loading-ext-imgf"><div class="loading"><div><div class="ph-lds-ellipsis"><div></div><div></div><div></div><div></div></div><div>&nbsp;</div><div><?php echo Text::_('COM_PHOCAGALLERY_FACEBOOK_LOADING_DATA'); ?></div></div></div>
<div id="uploading-ext-imgf"><div class="loading"><div><div class="ph-lds-ellipsis"><div></div><div></div><div></div><div></div></div><div>&nbsp;</div><div><?php echo Text::_('COM_PHOCAGALLERY_FB_UPLOADING_DATA'); ?></div></div></div>
<div id="loading-ext-imgi"><div class="loading"><div><div class="ph-lds-ellipsis"><div></div><div></div><div></div><div></div></div><div>&nbsp;</div><div><?php echo Text::_('COM_PHOCAGALLERY_IMGUR_LOADING_DATA'); ?></div></div></div>
*/ ?>
