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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

$task		= 'phocagalleryef';

$r 			= $this->r;
$app		= Factory::getApplication();
$option 	= $app->input->get('option');
$tasks		= $task . 's';
$OPT		= strtoupper($option);
$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', $option);
$saveOrder	= $listOrder == 'a.ordering';
$saveOrderingUrl = '';
if ($saveOrder && !empty($this->items)) {
	$saveOrderingUrl = $r->saveOrder($this->t, $listDirn);
}
$sortFields = $this->getSortFields();

echo $r->startHeader();
echo $r->jsJorderTable($listOrder);

echo $r->startForm($option, $tasks, 'adminForm');
//echo $r->startFilter();
//echo $r->endFilter();

echo $r->startMainContainer();
/*echo $r->startFilterBar();
echo $r->inputFilterSearch($OPT.'_FILTER_SEARCH_LABEL', $OPT.'_FILTER_SEARCH_DESC',
							$this->escape($this->state->get('filter.search')));
echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
echo $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder);

echo $r->startFilterBar(2);
echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.published'));
echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
echo $r->selectFilterCategory(PhocaGalleryCategory::options(1), 'COM_PHOCAGALLERY_FILTER_SELECT_TYPE', $this->state->get('filter.category_id'));
echo $r->endFilterBar();

//echo $r->endFilterBar();*/
echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));

echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);


echo '<th class="ph-title-short">'.HTMLHelper::_('searchtools.sort',  	$OPT.'_TITLE', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-filename-long">'.HTMLHelper::_('searchtools.sort',  	$OPT.'_FILENAME', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-published">'.HTMLHelper::_('searchtools.sort',  $OPT.'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-parentcattitle">'.HTMLHelper::_('searchtools.sort', $OPT.'_TYPE', 'a.type', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-language">'.HTMLHelper::_('searchtools.sort',  	'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-id">'.HTMLHelper::_('searchtools.sort',  		$OPT.'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

echo $r->endTblHeader();
echo $r->startTblBody($saveOrder, $saveOrderingUrl, $listDirn);

$originalOrders = array();
$j 				= 0;

if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {
		//if ($i >= (int)$this->pagination->limitstart && $j < (int)$this->pagination->limit) {
			$j++;

$urlEdit		= 'index.php?option='.$option.'&task='.$task.'.edit&id=';
$urlTask		= 'index.php?option='.$option.'&task='.$task;
$orderkey   	= array_search($item->id, $this->ordering[$item->type]);
$ordering		= ($listOrder == 'a.ordering');
$canCreate		= $user->authorise('core.create', $option);
$canEdit		= $user->authorise('core.edit', $option);
$canCheckin		= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange		= $user->authorise('core.edit.state', $option) && $canCheckin;
$linkEdit 		= Route::_( $urlEdit. $item->id );

echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);
echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);


$checkO = '';
if ($item->checked_out) {
	$checkO .= HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $tasks.'.', $canCheckin);
}
if ($canCreate || $canEdit) {
	$checkO .= '<a href="'. Route::_($linkEdit).'">'. $this->escape($item->title).'</a>';
} else {
	$checkO .= $this->escape($item->title);
}

echo $r->td($checkO, "small");

$filename 	= PhocaGalleryFile::existsCss($item->filename, $item->type);
$main		= '';
if ((int)$item->type == 1) {
	$main = ' <span class="label label-warning badge bg-warning">'.Text::_('COM_PHOCAGALLERY_MAIN').'</span>';
}
if ($filename) {
	echo $r->td($item->filename . $main .' <span class="label label-success badge bg-success">'.Text::_('COM_PHOCAGALLERY_FILE_EXISTS').'</span>', "small");
} else {
	echo $r->td($item->filename  . $main .' <span class="label label-important label-danger badge bg-danger">'.Text::_('COM_PHOCAGALLERY_FILE_DOES_NOT_EXIST').'</span>', "small");
}

echo $r->td(HTMLHelper::_('jgrid.published', $item->published, $i, $tasks.'.', $canChange), "small");

switch($item->type) {
	case 2:
		echo $r->td(Text::_('COM_PHOCAGALLERY_CUSTOM_CSS'), "small");
	break;
	case 1:
	default:
		echo $r->td(Text::_('COM_PHOCAGALLERY_MAIN_CSS'), "small");
	break;
}
echo $r->tdLanguage($item->language, $item->language_title, $this->escape($item->language_title));
echo $r->td($item->id, "small");

echo $r->endTr();

		//}
	}
}
echo $r->endTblBody();

echo $r->tblFoot($this->pagination->getListFooter(), 15);
echo $r->endTable();


echo $r->formInputsXML($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();
?>
