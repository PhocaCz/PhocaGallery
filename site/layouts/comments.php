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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');
$d      = $displayData;
$t      = $d['t'];

echo '<div id="phocagallery-comments">'. "\n";

if (!empty($t['commentitem'])){

	$smileys = PhocaGalleryComment::getSmileys();

	foreach ($t['commentitem'] as $v) {

	    $date		= HTMLHelper::_('date',  $v->date, Text::_('DATE_FORMAT_LC2') );
		$comment	= $v->comment;
		$comment 	= PhocaGalleryComment::bbCodeReplace($comment);

		foreach ($smileys as $smileyKey => $smileyValue) {
			$comment = str_replace($smileyKey, $smileyValue, $comment);
		}

		echo '<blockquote>'
			.'<h4><svg class="ph-si ph-si-user"><use xlink:href="#ph-si-user"></use></svg>'.$v->name.'</h4>'
			.'<p><strong>'.PhocaGalleryText::wordDelete($v->title, 50, '...').'</strong></p>'
			.'<p>'.$comment.'</p>'
			.'<p style="text-align:right"><small>'.$date.'</small></p>'
			.'</blockquote>';
	}
}

echo '<h4>'.Text::_('COM_PHOCAGALLERY_ADD_COMMENT').'</h4>';

if ($t['already_commented']) {
	echo '<p>'.Text::_('COM_PHOCAGALLERY_COMMENT_ALREADY_SUBMITTED').'</p>';
} else if ($t['not_registered']) {
	echo '<p>'.Text::_('COM_PHOCAGALLERY_COMMENT_ONLY_REGISTERED_LOGGED_SUBMIT_COMMENT').'</p>';

} else {

	?>
	<form action="<?php echo htmlspecialchars($t['action']);?>" name="phocagallerycommentsform" id="phocagallery-comments-form" method="post" >

	<table>
		<tr>
			<td><?php echo Text::_('COM_PHOCAGALLERY_NAME');?>:</td>
			<td><?php echo $t['name']; ?></td>
		</tr>

		<tr>
			<td><?php echo Text::_('COM_PHOCAGALLERY_TITLE');?>:</td>
			<td><input type="text" name="phocagallerycommentstitle" id="phocagallery-comments-title" value="" maxlength="255" class="form-control comment-input" /></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td>
            <a class="pg-comment-btn pg-b" href="#" onclick="pgPasteTag('b', true); return false;" title="<?php echo Text::_('COM_PHOCAGALLERY_BOLD') ?>">B</a>
			<a class="pg-comment-btn pg-i" href="#" onclick="pgPasteTag('i', true); return false;" title="<?php echo Text::_('COM_PHOCAGALLERY_ITALIC') ?>">I</a>
			<a class="pg-comment-btn pg-u" href="#" onclick="pgPasteTag('u', true); return false;" title="<?php echo Text::_('COM_PHOCAGALLERY_UNDERLINE') ?>">U</a>

			<a class="pg-comment-btn" href="#" onclick="pgPasteSmiley(':)'); return false;" title="<?php echo Text::_('COM_PHOCAGALLERY_SMILE') ?>">&#x1F642</a>
			<a class="pg-comment-btn" href="#" onclick="pgPasteSmiley(':lol:'); return false;" title="<?php echo Text::_('COM_PHOCAGALLERY_LOL') ?>">&#x1F604</a>
			<a class="pg-comment-btn" href="#" onclick="pgPasteSmiley(':('); return false;" title="<?php echo Text::_('COM_PHOCAGALLERY_SAD') ?>">&#x2639</a>
			<a class="pg-comment-btn" href="#" onclick="pgPasteSmiley(':?'); return false;" title="<?php echo Text::_('COM_PHOCAGALLERY_CONFUSED') ?>">&#x1F615</a>
			<a class="pg-comment-btn" href="#" onclick="pgPasteSmiley(':wink:'); return false;" title="<?php echo Text::_('COM_PHOCAGALLERY_WINK') ?>">&#x1F609</a>


			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td>
				<textarea name="phocagallerycommentseditor" id="phocagallery-comments-editor" cols="30" rows="10"  class= "form-control comment-input" onkeyup="pgCountChars(<?php echo $t['max_comment_char'];?>);" ></textarea>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><?php echo Text::_('COM_PHOCAGALLERY_CHARACTERS_WRITTEN');?> <input name="phocagallerycommentscountin" value="0" readonly="readonly" class="form-control comment-input2" /> <?php echo Text::_('COM_PHOCAGALLERY_AND_LEFT_FOR_COMMENT');?> <input name="phocagallerycommentscountleft" value="<?php echo $t['max_comment_char'];?>" readonly="readonly" class="form-control comment-input2" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align="right">
				<input class="btn btn-primary" type="submit" id="phocagallerycommentssubmit" onclick="return(pgCheckCommentsForm());" value="<?php echo Text::_('COM_PHOCAGALLERY_SUBMIT_COMMENT'); ?>"/>
			</td>
		</tr>

	</table>

        <?php

        echo '<input type="hidden" name="task" value="'.$d['form']['task'].'" />';
        echo '<input type="hidden" name="view" value="'.$d['form']['view'].'" />';
        echo '<input type="hidden" name="controller" value="'.$d['form']['controller'].'" />';
        echo '<input type="hidden" name="tab" value="'.$d['form']['tab'].'" />';
        echo '<input type="hidden" name="id" value="'. $d['form']['id'].'" />';
        echo '<input type="hidden" name="catid" value="'. $d['form']['catid'].'" />';
        echo '<input type="hidden" name="Itemid" value="'. $d['form']['itemid'] .'" />';
        echo HTMLHelper::_( 'form.token' );
	    echo '</form>';

}
echo '</div>'. "\n";
?>
