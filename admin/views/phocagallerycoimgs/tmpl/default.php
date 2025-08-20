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

$task		= 'phocagallerycoimg';

$r 			= $this->r;
$app		= Factory::getApplication();
$option 	= $app->getInput()->get('option');
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
/*
echo $r->startFilterBar();
echo $r->inputFilterSearch($OPT.'_FILTER_SEARCH_LABEL', $OPT.'_FILTER_SEARCH_DESC',
							$this->escape($this->state->get('filter.search')));
echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
echo $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder);

echo $r->startFilterBar(2);
echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.published'));
//echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
echo $r->selectFilterCategory(PhocaGalleryCategory::options($option), 'JOPTION_SELECT_CATEGORY', $this->state->get('filter.category_id'));
echo $r->endFilterBar();

echo $r->endFilterBar();*/
echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));

echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);


echo '<th class="ph-user">'.HTMLHelper::_('searchtools.sort',  		$OPT.'_USER', 'ua.username', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-title">'.HTMLHelper::_('searchtools.sort',  	$OPT.'_TITLE', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-date">'.HTMLHelper::_('searchtools.sort',  		$OPT.'_DATE', 'a.date', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-published">'.HTMLHelper::_('searchtools.sort',  $OPT.'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-imagetitle">'.HTMLHelper::_('searchtools.sort', $OPT.'_IMAGE', 'image_title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-parentcattitle">'.HTMLHelper::_('searchtools.sort', $OPT.'_CATEGORY', 'category_title', $listDirn, $listOrder ).'</th>'."\n";
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
$orderkey   	= array_search($item->id, $this->ordering[$item->image_id]);
$ordering		= ($listOrder == 'a.ordering');
$canCreate		= $user->authorise('core.create', $option);
$canEdit		= $user->authorise('core.edit', $option);
$canCheckin		= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange		= $user->authorise('core.edit.state', $option) && $canCheckin;
$linkEdit 		= Route::_( $urlEdit. $item->id );
$linkCat		= Route::_( 'index.php?option=com_phocagallery&task=phocagalleryc.edit&id='.(int) $item->category_id );
$canEditCat		= $user->authorise('core.edit', $option);
$linkImg		= Route::_( 'index.php?option=com_phocagallery&task=phocagalleryimg.edit&id='.(int) $item->image_id );
$canEditImg		= $user->authorise('core.edit', 'com_phocagallery');

echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);
echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);

$usrO = $item->commentname;
if ($item->commentusername) {$usrO = $usrO . ' ('.$item->commentusername.')';}
echo $r->td($usrO, "small");
$checkO = '';
if ($item->checked_out) {
	$checkO .= HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $tasks.'.', $canCheckin);
}

if ($item->title == '') {
	$item->title = Text::_('COM_PHOCAGALLERY_NO_TITLE');
}

if ($canCreate || $canEdit) {
	$checkO .= '<a href="'. Route::_($linkEdit).'">'. $this->escape($item->title);


	$checkO .= '</a>';
	if ($item->comment != '') {
		$checkO .='<br> <small>('.$this->escape($item->comment).')</small>';
	}
} else {
	$checkO .= $this->escape($item->title);
	if ($item->comment != '') {
		$checkO .=' <small>('.$this->escape($item->comment).')</small>';
	}
}

echo $r->td($checkO, "small");
echo $r->td($item->date, "small");

echo $r->td(HTMLHelper::_('jgrid.published', $item->published, $i, $tasks.'.', $canChange), "small");

if ($canEditImg) {
	$imgO = '<a href="'. Route::_($linkImg).'">'. $this->escape($item->image_title).'</a>';
} else {
	$imgO = $this->escape($item->image_title);
}
echo $r->td($imgO, "small");
if ($canEditCat) {
	$catO = '<a href="'. Route::_($linkCat).'">'. $this->escape($item->category_title).'</a>';
} else {
	$catO = $this->escape($item->category_title);
}

echo $r->td($catO, "small");
//echo $r->td($item->id . " - " . $item->ordering." - ", "small");

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
