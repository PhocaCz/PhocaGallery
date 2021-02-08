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

$r = $this->r;
echo '<div class="ph-item-list-box ph-item-list-box-admin">';
echo $this->loadTemplate('up');
if (count($this->images) > 0 || count($this->folders) > 0) {
    for ($i=0,$n=count($this->folders); $i<$n; $i++) {
        $this->setFolder($i);
        echo $this->loadTemplate('folder');
    }
    for ($i=0,$n=count($this->images); $i<$n; $i++) {
        $this->setImage($i);
        echo $this->loadTemplate('image');
	}
} else {
    echo '<div class="ph-item-list-box-head">'.JText::_( 'COM_PHOCAGALLERY_THERE_IS_NO_IMAGE' ).'</div>';
}

echo '</div>';

echo '<div class="ph-item-list-box-hr"></div>';


if ($this->t['displaytabs'] > 0) {

	/*echo '<ul class="nav nav-tabs" id="configTabs">';

	$label = Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-upload.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_UPLOAD');
	echo '<li><a href="#upload" data-toggle="tab">'.$label.'</a></li>';

	if((int)$this->t['enablemultiple']  >= 0) {
		$label = Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-upload-multiple.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_MULTIPLE_UPLOAD');
		echo '<li><a href="#multipleupload" data-toggle="tab">'.$label.'</a></li>';
	}

	if($this->t['enablejava'] >= 0) {

		$label = Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-upload-java.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_JAVA_UPLOAD');
		echo '<li><a href="#javaupload" data-toggle="tab">'.$label.'</a></li>';
	}
	$label = Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-folder.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_CREATE_FOLDER');
	echo '<li><a href="#createfolder" data-toggle="tab">'.$label.'</a></li>';

	echo '</ul>';*/

	$activeTab = '';
	if (isset($this->t['tab']) && $this->t['tab'] != '') {
	    $activeTab = $this->t['tab'];
    } else  {
		$activeTab = 'multipleupload';
	}

	echo $r->startTabs($activeTab);

	$tabs = array();
	$tabs['multipleupload'] = '<span class="ph-cp-item"><i class="phi phi-fs-s phi-fc-bl duotone icon-upload"></i></span>' . '&nbsp;'.JText::_('COM_PHOCAGALLERY_MULTIPLE_UPLOAD');
	$tabs['upload'] = '<span class="ph-cp-item"><i class="phi phi-fs-s phi-fc-bd duotone icon-upload"></i></span>' . '&nbsp;'.JText::_('COM_PHOCAGALLERY_UPLOAD');

	if (!empty($this->t['javaupload'])) {
	    $tabs['javaupload'] = '<span class="ph-cp-item"><i class="phi phi-fs-s phi-fc-rl duotone icon-upload"></i></span>' . '&nbsp;'.JText::_('COM_PHOCAGALLERY_JAVA_UPLOAD');
    }

	$tabs['createfolder'] = '<span class="ph-cp-item"><i class="phi phi-fs-s phi-fc-brd duotone icon-folder"></i></span>' . '&nbsp;'.JText::_('COM_PHOCAGALLERY_CREATE_FOLDER');



	echo $r->navigation($tabs, $activeTab);

	echo $r->startTab('multipleupload', $tabs['multipleupload'], $activeTab == 'multipleupload' ? 'active' : '');
	echo $this->loadTemplate('multipleupload');
	echo $r->endTab();

	echo $r->startTab('upload', $tabs['upload'], $activeTab == 'upload' ? 'active' : '');
	echo $this->loadTemplate('upload');
	echo $r->endTab();

	if (!empty($this->t['javaupload'])) {
        echo $r->startTab('javaupload', $tabs['javaupload'], $activeTab == 'javaupload' ? 'active' : '');
        echo $this->loadTemplate('javaupload');
        echo $r->endTab();
    }

	echo $r->startTab('createfolder', $tabs['createfolder'], $activeTab == 'createfolder' ? 'active' : '');
	echo '<div id="phocagallery-multipleupload" class="ph-in">';
	echo PhocaGalleryFileUpload::renderCreateFolder($this->session->getName(), $this->session->getId(), $this->currentFolder, 'phocagalleryi', 'tab=createfolder&amp;field='.PhocaGalleryText::filterValue($this->field, 'alphanumeric2'));
	echo '</div>';
	echo $r->endTab();

	echo $r->endTabs();
}
?>



<?php
/*
if ($this->t['displaytabs'] > 0) {
	echo '<div id="phocagallery-pane">';
	//$pane =& J Pane::getInstance('Tabs', array('startOffset'=> $this->t['tab']));
	echo Joomla\CMS\HTML\HTMLHelper::_('tabs.start', 'config-tabs-com_phocagallery-i', array('useCookie'=>1, 'startOffset'=> $this->t['tab']));
	//echo $pane->startPane( 'pane' );

	//echo $pane->startPanel( Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-upload.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_UPLOAD'), 'upload' );
	echo Joomla\CMS\HTML\HTMLHelper::_('tabs.panel', Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-upload.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_UPLOAD'), 'upload' );
	echo $this->loadTemplate('upload');
	//echo $pane->endPanel();

	if((int)$this->t['enablemultiple']  >= 0) {
		//echo $pane->startPanel( Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-upload-multiple.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_MULTIPLE_UPLOAD'), 'multipleupload' );
		echo Joomla\CMS\HTML\HTMLHelper::_('tabs.panel', Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-upload-multiple.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_MULTIPLE_UPLOAD'), 'multipleupload' );
		echo $this->loadTemplate('multipleupload');
		//echo $pane->endPanel();
	}

	if($this->t['enablejava'] >= 0) {
		//echo $pane->startPanel( Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-upload-java.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_JAVA_UPLOAD'), 'javaupload' );
		echo Joomla\CMS\HTML\HTMLHelper::_('tabs.panel', Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/administrator/icon-16-upload-java.png','') . '&nbsp;'.JText::_('COM_PHOCAGALLERY_JAVA_UPLOAD'), 'javaupload' );
		echo $this->loadTemplate('javaupload');
		//echo $pane->endPanel();
	}

	//echo $pane->endPane();
	echo Joomla\CMS\HTML\HTMLHelper::_('tabs.end');
	echo '</div>';// end phocagallery-pane
}
*/

//TEMP
//$this->t['tab'] = 'multipleupload';
/*if ($this->t['tab'] != '') {$jsCt = 'a[href=#'.PhocaGalleryText::filterValue($this->t['tab'], 'alphanumeric2') .']';} else {$jsCt = 'a:first';}
echo '<script type="text/javascript">';
echo '   jQuery(\'#configTabs '.$jsCt.'\').tab(\'show\');'; // Select first tab
echo '</script>';*/
?>
