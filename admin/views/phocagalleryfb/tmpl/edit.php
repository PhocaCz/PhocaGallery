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

$task		= 'phocagalleryimg';

//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');
HtmlHelper::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

$r 			= $this->r;
$app		= Factory::getApplication();
$option 	= $app->input->get('option');
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
'application' 		=> Text::_($OPT.'_FB_SETTINGS'),
'publishing' 	=> Text::_($OPT.'_PUBLISHING_OPTIONS')
);
echo $r->navigation($tabs);

echo $r->startTabs();


echo $r->startTab('application', $tabs['application'], 'active');

echo '<h4>'. Text::_($OPT.'_FB_APPLICATION').'</h4>';

$formArray = array ('appid', 'appsid', 'ordering');
echo $r->group($this->form, $formArray);

echo '<div class="clearfix"></div>'
.'<div>'.Text::_('COM_PHOCAGALLERY_FB_INSTR1') .'</div>'
.'<div style="text-align:right"><a style="text-decoration:underline;font-weight:bold;" href="https://developers.facebook.com/setup/" target="_blank" >'.  Text::_('COM_PHOCAGALLERY_FB_CREATE_APP').'</a></div>'
.'<div class="clearfix"></div>';






































if (isset($this->item->appid) && $this->item->appid != ''
	&& isset($this->item->appsid) && $this->item->appsid != '') {

	echo '<h4>'.Text::_('COM_PHOCAGALLERY_FB_USER_SETTINGS'). '</h4>';

	$status	= PhocaGalleryFb::getFbStatus($this->item->appid, $this->item->appsid);

	echo $status['html'];

	if ($status['session']['uid'] != ''
	/*&& $status['session']['base_domain'] != ''*/
	&& $status['session']['secret'] != ''
	//&& $status['session']['session_key'] != ''
	&& $status['session']['access_token'] != ''
	//&& $status['session']['sig'] != ''
	&& $status['u']['name'] != '') {
		/*$this->form->setValue('uid', '', $status['session']['uid']);
		$this->form->setValue('base_domain', '', $status['session']['base_domain']);
		$this->form->setValue('secret', '', $status['session']['secret']);
		$this->form->setValue('session_key', '', $status['session']['session_key']);
		$this->form->setValue('access_token', '', $status['session']['access_token']);
		$this->form->setValue('sig', '', $status['session']['sig']);
		if ($status['u']['name'] != '') {
			$this->form->setValue('name', '', $status['u']['name']);
		}
		*/

		$div	= array();
		$script = array();
		$fields = array( 'uid', 'secret', 'access_token');

		$script[] = 'function pasteFbFields() {';
		foreach ($fields as $field) {
			if (!isset($status['session'][$field])) {
				$status['session'][$field] = '';
			}
			$script[] = ' document.getElementById(\'jform_'.$field.'\').value = document.getElementById(\'div_'.$field.'\').value;';
			$div[]	  = '<input type="hidden" id="div_'.$field.'" value="'.$status['session'][$field].'" />';

		}
		$script[] 	= ' document.getElementById(\'jform_name\').value = document.getElementById(\'div_name\').value;';
		$div[]		= '<input type="hidden" id="div_name" value="'.$status['u']['name'].'" />';
		$script[] = '}';

		echo '<div style="display:none">';
		$n = "\n";
		echo implode($n, $div);
		echo '</div>';

		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		echo '<div style="float:right;"><a href="javascript:void(0)" onclick="pasteFbFields()"><div class="btn btn-primary">'.Text::_('COM_PHOCAGALLERY_FB_PASTE_LOADED_DATA').'</div></a></div>';
	}

	echo '<div class="clearfix"></div>';
	//$formArray = array ('name', 'uid', 'base_domain', 'secret', 'session_key', 'access_token', 'sig', 'fanpageid');
	$formArray = array ('name', 'uid', 'secret', 'access_token', 'fanpageid');
	echo $r->group($this->form, $formArray);
	echo '<input name="jform[expires]" id="jform_expires" value="0" readonly="readonly" type="hidden" />'. "\n";
	echo '<div class="clearfix"></div>';

	echo '<h4>'. Text::_('COM_PHOCAGALLERY_FB_COMMENTS_SETTINGS') .'</h4>';
	foreach($this->form->getFieldset('comments') as $field) {
		echo '<div class="control-group">';
		if (!$field->hidden) {
			echo '<div class="control-label">'.$field->label.'</div>';
		}
		echo '<div class="controls">';
		echo $field->input;
		echo '</div></div>';
	}

}
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

echo '</div>';//end span10
// Second Column
//echo '<div class="span2"></div>';//end span2
echo $r->formInputs($this->t['task']);
echo $r->endForm();


