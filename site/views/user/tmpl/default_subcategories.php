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

echo '<div id="phocagallery-subcategory-creating">'.$this->t['iepx'];

if ($this->t['displaysubcategory'] == 1) {
	if ($this->t['categorypublished'] == 0) {
		echo '<p>'.JText::_('COM_PHOCAGALLERY_YOUR_MAIN_CATEGORY_IS_UNPUBLISHED').'</p>';
	} else if ($this->t['task'] == 'editsubcat' && $this->t['categorysubcatedit']) {

?><h4><?php echo JText::_('COM_PHOCAGALLERY_EDIT'); ?></h4>
<form action="<?php echo htmlspecialchars($this->t['action']);?>" name="phocagallerycreatesubcatform" id="phocagallery-create-subcat-form" method="post" >
<table>
	<tr>
		<td><?php echo JText::_('COM_PHOCAGALLERY_SUBCATEGORY');?>:</td>
		<td><input type="text" id="subcategoryname" name="subcategoryname" maxlength="255" class="comment-input" value="<?php echo $this->t['categorysubcatedit']->title ?>" /></td>
	</tr>

	<tr>
		<td><?php echo JText::_( 'COM_PHOCAGALLERY_DESCRIPTION' ); ?>:</td>
		<td><textarea id="phocagallery-create-subcat-description" name="phocagallerycreatesubcatdescription" onkeyup="countCharsCreateSubCat();" cols="30" rows="10" class="comment-input"><?php echo $this->t['categorysubcatedit']->description; ?></textarea></td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td><?php echo JText::_('COM_PHOCAGALLERY_CHARACTERS_WRITTEN');?> <input name="phocagallerycreatesubcatcountin" value="0" readonly="readonly" class="comment-input2" /> <?php echo JText::_('COM_PHOCAGALLERY_AND_LEFT_FOR_DESCRIPTION');?> <input name="phocagallerycreatesubcatcountleft" value="<?php echo $this->t['maxcreatecatchar'];?>" readonly="readonly" class="comment-input2" />
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td align="right"><button class="btn" onclick="window.location='<?php echo JRoute::_($this->t['pp'].$this->t['ps']);?>'" id="phocagallerycreatesubcatcancel"><?php echo JText::_('COM_PHOCAGALLERY_CANCEL'); ?></button> <button class="btn" type="submit" onclick="return(checkCreateSubCatForm());" id="phocagallerycreatesubcatsubmit"><?php echo JText::_('COM_PHOCAGALLERY_EDIT'); ?></button></td>
	</tr>
</table>

<?php echo Joomla\CMS\HTML\HTMLHelper::_( 'form.token' ); ?>
<input type="hidden" name="task" value="editsubcategory"/>
<input type="hidden" name="controller" value="user" />
<input type="hidden" name="view" value="user"/>
<input type="hidden" name="tab" value="<?php echo $this->t['currenttab']['createsubcategory'];?>" />
<input type="hidden" name="limitstartsubcat" value="<?php echo $this->t['subcategorypagination']->limitstart;?>" />
<input type="hidden" name="limitstartimage" value="<?php echo $this->t['imagepagination']->limitstart;?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
<input type="hidden" name="id" value="<?php echo $this->t['categorysubcatedit']->id ?>"/>
<input type="hidden" name="parentcategoryid" value="<?php echo $this->t['parentcategoryid'] ?>"/>
<input type="hidden" name="filter_order_subcat" value="<?php echo $this->listssubcat['order']; ?>" />
<input type="hidden" name="filter_order_Dir_subcat" value="" />
</form>
<?php
	} else {



		?><div style="float:left" class="filter-search btn-group pull-left" ><h4><?php echo JText::_( 'COM_PHOCAGALLERY_SUBCATEGORIES' ); ?></h4>
		<form action="<?php echo htmlspecialchars($this->t['action']);?>" method="post" name="phocagallerysubcatform" id="phocagallerysubcatform">

		<?php /*
		echo JText::_( 'COM_PHOCAGALLERY_FILTER' ); ?>:
		 <input type="text" name="phocagallerysubcatsearch" id="phocagallerysubcatsearch" value="<?php echo $this->listssubcat['search'];?>" onchange="document.phocagallerysubcatform.submit();" class="filter-select hidden-phone" />
		<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'COM_PHOCAGALLERY_SEARCH' ); ?></button>
		<button class="btn" onclick="document.getElementById('phocagallerysubcatsearch').value='';document.phocagallerysubcatform.submit();"><?php echo JText::_( 'COM_PHOCAGALLERY_RESET' ); ?></button>
		*/ ?>


		<div class="filter-search btn-group pull-left">
		<label for="filter_search" class="element-invisible"><?php echo JText::_( 'COM_PHOCAGALLERY_FILTER' ); ?></label>
		<input type="text" name="phocagallerysubcatsearch" id="phocagallerysubcatsearch" placeholder="<?php echo JText::_( 'COM_PHOCAGALLERY_SEARCH' ); ?>" value="<?php echo $this->listssubcat['search'];?>" title="<?php echo JText::_( 'COM_PHOCAGALLERY_SEARCH' ); ?>" /></div>

		<div class="btn-group pull-left hidden-phone">
		<button class="btn tip hasTooltip" type="submit" onclick="this.form.submit();"  title="<?php echo JText::_( 'COM_PHOCAGALLERY_SEARCH' ); ?>"><?php echo PhocaGalleryRenderFront::renderIcon('search', $this->t['pi'].'icon-search.png', JText::_('COM_PHOCAGALLERY_SEARCH')) ?></button>
		<button class="btn tip hasTooltip" type="button" onclick="document.getElementById('phocagallerysubcatsearch').value='';document.phocagallerysubcatform.submit();" title="<?php echo JText::_( 'COM_PHOCAGALLERY_CLEAR' ); ?>"><?php echo PhocaGalleryRenderFront::renderIcon('remove', $this->t['pi'].'icon-remove.png', JText::_('COM_PHOCAGALLERY_CLEAR')) ?></button></div>



		</div><div style="float:right">
		<?php echo $this->listssubcat['catid'] ?>
		<br />
		<?php echo $this->listssubcat['state']; ?>
		</div>
<table class="adminlist">
<thead>
	<tr>
	<th width="5"><?php echo JText::_( 'COM_PHOCAGALLERY_NUM' ); ?></th>
	<th class="title" width="40%"><?php echo PhocaGalleryGrid::sort(  'COM_PHOCAGALLERY_TITLE', 'a.title', $this->listssubcat['order_Dir'], $this->listssubcat['order'], 'subcategory', 'asc', '', 'phocagallerysubcatform', '_subcat'); ?></th>
	<th width="5%" nowrap="nowrap"><?php echo PhocaGalleryGrid::sort(  'COM_PHOCAGALLERY_PUBLISHED', 'a.published', $this->listssubcat['order_Dir'], $this->listssubcat['order'], 'subcategory', 'asc', '', 'phocagallerysubcatform', '_subcat' ); ?></th>
	<th width="5%" nowrap="nowrap"><?php echo JText::_('COM_PHOCAGALLERY_DELETE'); ?></th>
	<th width="5%" nowrap="nowrap"><?php echo PhocaGalleryGrid::sort(  'COM_PHOCAGALLERY_APPROVED', 'a.approved', $this->listssubcat['order_Dir'], $this->listssubcat['order'], 'subcategory', 'asc', '', 'phocagallerysubcatform', '_subcat' ); ?></th>
	<th width="50" nowrap="nowrap" align="center">

	<?php echo PhocaGalleryGrid::sort('COM_PHOCAGALLERY_ORDER', 'a.ordering', $this->listssubcat['order_Dir'], $this->listssubcat['order'], 'subcategory', 'asc', '', 'phocagallerysubcatform', '_subcat' );
	//$image = '<img src="'.JURI::base(true).'/'. $this->t['pi'].'icon-filesave.png'.'" width="16" height="16" border="0" alt="'.JText::_( 'COM_PHOCAGALLERY_SAVE_ORDER' ).'" />';
	$image = PhocaGalleryRenderFront::renderIcon('save', $this->t['pi'].'icon-filesave.png', JText::_('COM_PHOCAGALLERY_SAVE_ORDER'));
	$task = 'saveordersubcat';
	$href = '<a href="javascript:saveordersubcat()" title="'.JText::_( 'COM_PHOCAGALLERY_SAVE_ORDER' ).'">'.$image.'</a>';
	echo $href;
	?></th>
	<th width="1%" nowrap="nowrap"><?php echo PhocaGalleryGrid::sort( 'COM_PHOCAGALLERY_ID', 'a.id', $this->listssubcat['order_Dir'], $this->listssubcat['order'] , 'subcategory', 'asc', '', 'phocagallerysubcatform', '_subcat'); ?></th>
	</tr>
</thead>

<tbody><?php
$k 		= 0;
$i 		= 0;
$n 		= count( $this->t['subcategoryitems'] );
$rows 	= &$this->t['subcategoryitems'];

if (is_array($rows)) {
	foreach ($rows as $row) {
		$linkEdit 	= JRoute::_( $this->t['pp'].'&task=editsubcat&id='. $row->slug.$this->t['ps'] );

	?><tr class="<?php echo "row$k"; ?>">
	<td>
		<input type="hidden" id="cb<?php echo $k ?>" name="cid[]" value="<?php echo $row->id ?>" />
		<?php
		echo $this->t['subcategorypagination']->getRowOffset( $i );?>
	</td>
	<td><a href="<?php echo $linkEdit; ?>" title="<?php echo JText::_( 'COM_PHOCAGALLERY_EDIT_CATEGORY' ); ?>"><?php echo $row->title; ?></a></td>
	<?php

	// Publish Unpublish
	echo '<td align="center">';
	if ($row->published == 1) {
		echo ' <a title="'.JText::_('COM_PHOCAGALLERY_UNPUBLISH').'" href="'. JRoute::_($this->t['pp'].'&id='.$row->slug.'&task=unpublishsubcat'. $this->t['ps']).'">';
		//echo Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_UNPUBLISH'))
		echo PhocaGalleryRenderFront::renderIcon('publish', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_UNPUBLISH'))
		.'</a>';
	}
	if ($row->published == 0) {
		echo ' <a title="'.JText::_('COM_PHOCAGALLERY_PUBLISH').'" href="'. JRoute::_($this->t['pp'].'&id='.$row->slug.'&task=publishsubcat'.$this->t['ps']).'">';
		//echo Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_PUBLISH'))
		echo PhocaGalleryRenderFront::renderIcon('unpublish', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_PUBLISH'))
		.'</a>';
	}
	echo '</td>';

	// Remove
	echo '<td align="center">';
	echo ' <a onclick="return confirm(\''.JText::_('COM_PHOCAGALLERY_WARNING_DELETE_ITEMS').'\')" title="'.JText::_('COM_PHOCAGALLERY_DELETE').'" href="'. JRoute::_($this->t['pp'].'&id='.$row->slug.'&task=removesubcat'.$this->t['ps'] ).'">';
	//echo Joomla\CMS\HTML\HTMLHelper::_('image',  $this->t['pi'].'icon-trash.png', JText::_('COM_PHOCAGALLERY_UNPUBLISH')).'</a>';
	echo PhocaGalleryRenderFront::renderIcon('trash', $this->t['pi'].'icon-trash.png', JText::_('COM_PHOCAGALLERY_DELETE'))
		.'</a>';
	echo '</td>';

	// Approved
	echo '<td align="center">';
	if ($row->approved == 1) {
		//echo Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_APPROVED'));
		echo PhocaGalleryRenderFront::renderIcon('publish', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_APPROVED'));
	} else {
		//echo Joomla\CMS\HTML\HTMLHelper::_('image', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_NOT_APPROVED'));
		echo PhocaGalleryRenderFront::renderIcon('unpublish', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_NOT_APPROVED'));
	}
	echo '</td>';

	$linkUp 	= JRoute::_($this->t['pp'].'&id='.$row->slug.'&task=orderupsubcat'.$this->t['ps']);
	$linkDown 	= JRoute::_($this->t['pp'].'&id='.$row->slug.'&task=orderdownsubcat'.$this->t['ps']);

	echo '<td class="order" align="right">'
	.'<span>'. $this->t['subcategorypagination']->orderUpIcon( $i, $row->orderup == 1, $linkUp, JText::_('COM_PHOCAGALLERY_MOVE_UP'), $this->t['subcategoryordering']).'</span> '
	.'<span>'. $this->t['subcategorypagination']->orderDownIcon( $i, $n, $row->orderdown == 1, $linkDown, JText::_('COM_PHOCAGALLERY_MOVE_DOWN'), $this->t['subcategoryordering'] ).'</span> ';

	$disabled = $this->t['subcategoryordering'] ?  '' : 'disabled="disabled"';
	echo '<input type="text" name="order[]" size="5" value="'. $row->ordering.'" '. $disabled.' class="inputbox input-mini" style="text-align: center" />';
	echo '</td>';

	echo '<td align="center">'. $row->id .'</td>'
	.'</tr>';

		$k = 1 - $k;
		$i++;
	}
}
?></tbody>
<tfoot>
	<tr>
	<td colspan="7" class="footer"><?php

$this->t['subcategorypagination']->setTab($this->t['currenttab']['createsubcategory']);
if (count($this->t['subcategoryitems'])) {
	echo '<div class="pg-center">';
	echo '<div class="pg-inline">'
		.JText::_('COM_PHOCAGALLERY_DISPLAY_NUM') .'&nbsp;'
		.$this->t['subcategorypagination']->getLimitBox()
		.'</div>';
	echo '<div style="margin:0 10px 0 10px;display:inline;" class="sectiontablefooter'.$this->params->get( 'pageclass_sfx' ).'" >'
		.$this->t['subcategorypagination']->getPagesLinks()
		.'</div>'
		.'<div style="margin:0 10px 0 10px;display:inline;" class="pagecounter">'
		.$this->t['subcategorypagination']->getPagesCounter()
		.'</div>';
	echo '</div>';
}

?></td>
	</tr>
</tfoot>
</table>

<?php echo Joomla\CMS\HTML\HTMLHelper::_( 'form.token' ); ?>
<input type="hidden" name="controller" value="user" />
<input type="hidden" name="task" value=""/>
<input type="hidden" name="view" value="user"/>
<input type="hidden" name="tab" value="<?php echo $this->t['currenttab']['createsubcategory'];?>" />
<input type="hidden" name="limitstartsubcat" value="<?php echo $this->t['subcategorypagination']->limitstart;?>" />
<input type="hidden" name="limitstartimage" value="<?php echo $this->t['imagepagination']->limitstart;?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
<input type="hidden" name="parentcategoryid" value="<?php echo $this->t['parentcategoryid'] ?>"/>
<input type="hidden" name="filter_order_subcat" value="<?php echo $this->listssubcat['order']; ?>" />
<input type="hidden" name="filter_order_Dir_subcat" value="" />

</form>


<h4><?php echo JText::_('COM_PHOCAGALLERY_CREATE'); ?></h4>
<form action="<?php echo htmlspecialchars($this->t['action']);?>" name="phocagallerycreatesubcatform" id="phocagallery-create-subcat-form" method="post" >
<table>
	<tr>
		<td><strong><?php echo JText::_('COM_PHOCAGALLERY_SUBCATEGORY');?>:</strong></td>
		<td><input type="text" id="subcategoryname" name="subcategoryname" maxlength="255" class="comment-input" value="" /></td>
	</tr>

	<tr>
		<td><strong><?php echo JText::_( 'COM_PHOCAGALLERY_DESCRIPTION' ); ?>:</strong></td>
		<td><textarea id="phocagallery-create-subcat-description" name="phocagallerycreatesubcatdescription" onkeyup="countCharsCreateSubCat();" cols="30" rows="10" class="comment-input"></textarea></td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td><?php echo JText::_('COM_PHOCAGALLERY_CHARACTERS_WRITTEN');?> <input name="phocagallerycreatesubcatcountin" value="0" readonly="readonly" class="comment-input2" /> <?php echo JText::_('COM_PHOCAGALLERY_AND_LEFT_FOR_DESCRIPTION');?> <input name="phocagallerycreatesubcatcountleft" value="<?php echo $this->t['maxcreatecatchar'];?>" readonly="readonly" class="comment-input2" />
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td align="right"><button class="btn" onclick="return(checkCreateSubCatForm());" id="phocagallerycreatesubcatsubmit"><?php echo JText::_('COM_PHOCAGALLERY_CREATE_SUBCATEGORY'); ?></button></td>
	</tr>
</table>

<?php echo Joomla\CMS\HTML\HTMLHelper::_( 'form.token' );

?>
<input type="hidden" name="task" value="createsubcategory"/>
<input type="hidden" name="controller" value="user" />
<input type="hidden" name="view" value="user"/>
<input type="hidden" name="tab" value="<?php echo $this->t['currenttab']['createsubcategory'];?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
<input type="hidden" name="parentcategoryid" value="<?php echo $this->t['parentcategoryid'] ?>"/>
</form>

<?php
	}
} else {
	echo '<p>'.JText::_('COM_PHOCAGALLERY_MAIN_CATEGORY_IS_NOT_CREATED').'</p>';
}
echo '</div>';
?>
