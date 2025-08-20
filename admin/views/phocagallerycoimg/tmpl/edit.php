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

$task		= 'phocagallerycoimg';

//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');
HtmlHelper::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

$r 			= $this->r;
$app		= Factory::getApplication();
$option 	= $app->getInput()->get('option');
$OPT		= strtoupper($option);
Factory::getDocument()->addScriptDeclaration(

'Joomla.submitbutton = function(task) {
	if (task == "'. $this->t['task'].'.cancel" || document.formvalidator.isValid(document.getElementById("adminForm"))) {
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
'publishing' 	=> Text::_($OPT.'_PUBLISHING_OPTIONS'));
echo $r->navigation($tabs);


$formArray = array ('title');
echo $r->groupHeader($this->form, $formArray);

echo $r->startTabs();

echo $r->startTab('general', $tabs['general'], 'active');
$formArray = array ('usertitle', 'cattitle', 'imagetitle', 'ordering');
echo $r->group($this->form, $formArray);
$formArray = array('comment');
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

echo $r->endTabs();
echo '</div>';

// Second Column
//echo '<div class="span2"></div>';//end span2
echo $r->formInputs($this->t['task']);
echo $r->endForm();
?>

