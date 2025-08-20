<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root(true).'/media/com_phocagallery/js/photoswipe/css/photoswipe.css');
$document->addStyleSheet(Uri::root(true).'/media/com_phocagallery/js/photoswipe/css/default-skin/default-skin.css');
$document->addStyleSheet(Uri::root(true).'/media/com_phocagallery/js/photoswipe/css/photoswipe-style.css');

$task		= 'phocagalleryimg';

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

echo '<div class="phoca-thumb-status">' . $this->t['enablethumbcreationstatus'] .'</div>';
//echo '<div class="clearfix"></div>';

echo $r->startForm($option, $tasks, 'adminForm');
//echo $r->startFilter();
//echo $r->endFilter();

echo $r->startMainContainer();
if (isset($this->t['notapproved']->count) && (int)$this->t['notapproved']->count > 0 ) {

	echo '<div class="alert alert-error alert-dismissible fade show" role="alert">'. Text::_('COM_PHOCAGALLERY_NOT_APPROVED_IMAGE_IN_GALLERY').': '
	.(int)$this->t['notapproved']->count.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="'.Text::_('COM_PHOCAGALLERY_CLOSE').'"></button></div>';
}
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
echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
echo $r->selectFilterCategory(PhocaGalleryCategory::options($option, 1), 'JOPTION_SELECT_CATEGORY', $this->state->get('filter.category_id'));
//echo $r->endFilterBar();

echo $r->endFilterBar();*/

echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));

echo '<div  id="pg-msnr-container" class="pg-photoswipe pg-msnr-container pg-category-items-box" itemscope itemtype="http://schema.org/ImageGallery">';

echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);

echo '<th class="ph-image">'.Text::_( $OPT. '_IMAGE' ).'</th>'."\n";
echo '<th class="ph-title">'.HTMLHelper::_('searchtools.sort',  	$OPT.'_TITLE', 'a.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-filename">'.HTMLHelper::_('searchtools.sort',  	$OPT.'_FILENAME', 'a.filename', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-functions">'.Text::_( $OPT. '_FUNCTIONS' ).'</th>'."\n";
echo '<th class="ph-published">'.HTMLHelper::_('searchtools.sort',  $OPT.'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-approved">'.HTMLHelper::_('searchtools.sort',  	$OPT.'_APPROVED', 'a.approved', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-parentcattitle">'.HTMLHelper::_('searchtools.sort', $OPT.'_CATEGORY', 'category_id', $listDirn, $listOrder ).'</th>'."\n";
//echo '<th class="ph-access">'.JText::_($OPT.'_ACCESS').'</th>'."\n";
//echo '<th class="ph-owner">'.HTMLHelper::_('searchtools.sort',  	$OPT.'_OWNER', 'category_owner_id', $listDirn, $listOrder ).'</th>'."\n";
//echo '<th class="ph-uploaduser">'.HTMLHelper::_('searchtools.sort', $OPT.'_UPLOADED_BY', 'uploadusername', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-rating">'.HTMLHelper::_('searchtools.sort',  	$OPT.'_RATING', 'ratingavg', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-hits">'.HTMLHelper::_('searchtools.sort',  		$OPT.'_HITS', 'a.hits', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-language">'.HTMLHelper::_('searchtools.sort',  	'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-id">'.HTMLHelper::_('searchtools.sort',  		$OPT.'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

echo $r->endTblHeader();
echo $r->startTblBody($saveOrder, $saveOrderingUrl, $listDirn);

$originalOrders = array();
$parentsStr 	= "";
$j 				= 0;

if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {

		//if ($i >= (int)$this->pagination->limitstart && $j < (int)$this->pagination->limit) {
			$j++;

$urlEdit		= 'index.php?option='.$option.'&task='.$task.'.edit&id=';
$urlTask		= 'index.php?option='.$option.'&task='.$task;
$orderkey   	= array_search($item->id, $this->ordering[$item->catid]);
$ordering		= ($listOrder == 'a.ordering');
$canCreate		= $user->authorise('core.create', $option);
$canEdit		= $user->authorise('core.edit', $option);
$canCheckin		= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange		= $user->authorise('core.edit.state', $option) && $canCheckin;
$linkEdit 		= Route::_( $urlEdit. $item->id );
$linkRotate90 	= Route::_( $urlTask.'.rotate&angle=90&id='. $item->id );
$linkRotate270 	= Route::_( $urlTask.'.rotate&angle=270&id='. $item->id );
$linkDeleteThumbs= Route::_( $urlTask.'.recreate&cid[]='. (int)$item->id );

$linkCat	= Route::_( 'index.php?option=com_phocagallery&task=phocagalleryc.edit&id='.(int) $item->category_id );
$canEditCat	= $user->authorise('core.edit', $option);


echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);
echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->tdImage($item, 'pg-photoswipe-button', 'COM_PHOCAGALLERY_ENLARGE_IMAGE');
$checkO = '';
if ($item->checked_out) {
	$checkO .= HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $tasks.'.', $canCheckin);
}
if ($canCreate || $canEdit) {
	$checkO .= '<a href="'. Route::_($linkEdit).'">'. $this->escape($item->title).'</a>';
} else {
	$checkO .= $this->escape($item->title);
}
$checkO .= ' <span class="smallsub">(<span>'.Text::_($OPT.'_FIELD_ALIAS_LABEL').':</span>'. $this->escape($item->alias).')</span>';
echo $r->td($checkO, "small");

if (isset($item->extid) && $item->extid !='') {
    if (isset($item->exttype) && $item->exttype == 2) {
        echo $r->td(Text::_('COM_PHOCAGALLERY_IMGUR_STORED_FILE'));
        echo $r->td('');
    } else if (isset($item->exttype) && $item->exttype == 1) {
		echo $r->td(Text::_('COM_PHOCAGALLERY_FACEBOOK_STORED_FILE'));
		echo $r->td('');
	} else {
		echo $r->td(Text::_('COM_PHOCAGALLERY_PICASA_STORED_FILE'));
		echo $r->td('');
	}
} else {
	echo $r->td(str_replace('/', ' / ', $item->filename), "small ph-list-filename");
	echo '<td align="center">';

	echo '<div class="pha-toolbox">';
	echo '<a class="pha-no-underline" href="'. $linkRotate90 .'" title="'. Text::_( 'COM_PHOCAGALLERY_ROTATE_LEFT' ).'">'
		. '<span class="ph-cp-item"><i class="phi phi-mirror duotone icon-unblock phi-fs-m phi-fc-od" title="'. Text::_( 'COM_PHOCAGALLERY_ROTATE_LEFT' ).'"></i></span>'.'</a> '
		.'<a class="pha-no-underline" href="'. $linkRotate270 .'" title="'. Text::_( 'COM_PHOCAGALLERY_ROTATE_RIGHT' ).'">'
		. '<span class="ph-cp-item"><i class="phi duotone icon-unblock phi-fs-m phi-fc-od" title="'. Text::_( 'COM_PHOCAGALLERY_ROTATE_RIGHT' ).'"></i></span>'.'</a> '
		.'<a class="pha-no-underline" href="'. $linkDeleteThumbs.'" title="'. Text::_( 'COM_PHOCAGALLERY_RECREATE_THUMBS' ).'">'. '<span class="ph-cp-item"><i class="phi duotone icon-plus-circle phi-fs-m phi-fc-gd" title="'. Text::_( 'COM_PHOCAGALLERY_RECREATE_THUMBS' ).'"></i></span>'.'</a> '
		.'<a class="pha-no-underline" href="#" onclick="window.location.reload(true);" title="'. Text::_( 'COM_PHOCAGALLERY_RELOAD_SITE' ).'">'. '<span class="ph-cp-item"><i class="phi duotone icon-loop phi-fs-m phi-fc-bl " title="'. Text::_( 'COM_PHOCAGALLERY_RELOAD_SITE' ).'"></i></span>'.'</a>';

	echo '</div>';
	echo '</td>';
}

echo $r->td(HTMLHelper::_('jgrid.published', $item->published, $i, $tasks.'.', $canChange), "small");
echo $r->td(PhocaGalleryJGrid::approved( $item->approved, $i, $tasks.'.', $canChange), "small");

if ($canEditCat) {
	$catO = '<a href="'. Route::_($linkCat).'">'. $this->escape($item->category_title).'</a>';
} else {
	$catO = $this->escape($item->category_title);
}
echo $r->td($catO, "small");
//echo $r->td($this->escape($item->access_level), "small");
/*
$usrO = $item->usernameno;
if ($item->username) {$usrO = $usrO . ' ('.$item->username.')';}
echo $r->td($usrO, "small");

$usrU = $item->uploadname;
if ($item->uploadusername) {$usrU = $usrU . ' ('.$item->uploadusername.')';}
echo $r->td($usrU, "small");
*/
echo $r->tdRating($item->ratingavg);
echo $r->td($item->hits, "small");
echo $r->tdLanguage($item->language, $item->language_title, $this->escape($item->language_title));
echo $r->td($item->id, "small");

echo $r->endTr();

		//}
	}

}
echo $r->endTblBody();

echo $r->tblFoot($this->pagination->getListFooter(), 15);
echo $r->endTable();
echo "</div>";

echo $this->loadTemplate('batch');

echo $r->formInputsXML($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();

// Modal window for images
echo PhocaGalleryRenderDetailWindow::loadPhotoswipeBottom();
?>
