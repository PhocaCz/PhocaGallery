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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root(true).'/media/com_phocagallery/js/photoswipe/css/photoswipe.css');
$document->addStyleSheet(Uri::root(true).'/media/com_phocagallery/js/photoswipe/css/default-skin/default-skin.css');
$document->addStyleSheet(Uri::root(true).'/media/com_phocagallery/js/photoswipe/css/photoswipe-style.css');

$task		= 'phocagalleryuser';

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
echo $r->endFilterBar();

echo $r->endFilterBar();*/
echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));

echo '<div  id="pg-msnr-container" class="pg-photoswipe pg-msnr-container pg-category-items-box" itemscope itemtype="http://schema.org/ImageGallery">';

echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);


echo '<th class="ph-image">'.Text::_('COM_PHOCAGALLERY_AVATAR').'</th>'."\n";
echo '<th class="ph-user">'.HTMLHelper::_('searchtools.sort',  	$OPT.'_USER', 'ua.username', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-user">'.Text::_('COM_PHOCAGALLERY_CATEGORY_COUNT').'</th>'."\n";
echo '<th class="ph-user">'.Text::_('COM_PHOCAGALLERY_IMAGE_COUNT').'</th>'."\n";
echo '<th class="ph-published">'.HTMLHelper::_('searchtools.sort',  $OPT.'_PUBLISHED_AVATAR', 'a.published', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-approved">'.HTMLHelper::_('searchtools.sort',  $OPT.'_APPROVED_AVATAR', 'a.approved', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-id">'.HTMLHelper::_('searchtools.sort',  		$OPT.'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

echo $r->endTblHeader();
echo $r->startTblBody($saveOrder, $saveOrderingUrl, $listDirn);
$originalOrders = array();
$j 				= 0;

if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {
		//if ($i >= (int)$this->pagination->limitstart && $j < (int)$this->pagination->limit) {
			$j++;

$orderkey   	= array_search($item->id, $this->ordering[0]);
$ordering		= ($listOrder == 'a.ordering');
$canCheckin		= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange		= $user->authorise('core.edit.state', $option) && $canCheckin;

echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);
echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->tdImage($item, 'pg-photoswipe-button', 'COM_PHOCAGALLERY_ENLARGE_IMAGE', '', $this->t['avatarpathabs'], $this->t['avatarpathrel']);


$usrO = $item->username;
if ($item->usernameno) {$usrO = $usrO . ' ('.$item->usernameno.')';}
echo $r->td($usrO, "small");

if ($item->countcid) {$countCid = $item->countcid;} else {$countCid = '0';}
echo $r->td($countCid, "small");

if ($item->countiid) {$countIid = $item->countiid;} else {$countIid = '0';}
echo $r->td($countIid, "small");

echo $r->td(HTMLHelper::_('jgrid.published', $item->published, $i, $tasks.'.', $canChange), "small");
echo $r->td(PhocaGalleryJGrid::approved( $item->approved, $i, $tasks.'.', $canChange), "small");

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

// Modal window for images
echo PhocaGalleryRenderDetailWindow::loadPhotoswipeBottom();
?>
