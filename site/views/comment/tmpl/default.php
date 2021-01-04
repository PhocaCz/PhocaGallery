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
if ($this->t['backbutton'] != '' && $this->t['enable_multibox_iframe'] != 1) {
	echo $this->t['backbutton'];
}
echo '<div id="phocagallery-comments">';
if (($this->t['detailwindow'] == 7 || $this->t['display_comment_nopup'] == 1) && $this->t['enable_multibox_iframe'] != 1) {
	echo '<div id="image-box" style="text-align:center">'.$this->item->linkimage.'</div>';
}

if ($this->t['externalcommentsystem'] == 1) {
	if (JComponentHelper::isEnabled('com_jcomments', true)) {
		include_once(JPATH_BASE.'/components/com_jcomments/jcomments.php');
		echo JComments::showComments($this->item->id, 'com_phocagallery_images', JText::_('COM_PHOCAGALLERY_IMAGE') .' '. $this->item->title);
	}
} else if($this->t['externalcommentsystem'] == 2) {

	$uri 		= \Joomla\CMS\Uri\Uri::getInstance();
	$getParamsArray = explode(',', 'start,limitstart,template,fb_comment_id,tmpl');
	if (!empty($getParamsArray) ) {
		foreach($getParamsArray as $key => $value) {
			$uri->delVar($value);
		}
	}

	echo '<div style="margin:10px">';
	if ($this->t['fb_comment_app_id'] == '') {
		echo JText::_('COM_PHOCAGALLERY_ERROR_FB_APP_ID_EMPTY');
	} else {

		$cCount = '';
		if ((int)$this->t['fb_comment_count'] > 0) {
			$cCount = 'numposts="'.$this->t['fb_comment_count'].'"';
		}

?><fb:comments href="<?php echo $uri->toString(); ?>" simple="1" <?php echo $cCount;?> width="<?php echo (int)$this->t['fb_comment_width'] ?>"></fb:comments>
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
   FB.init({
     appId: '<?php echo $this->t['fb_comment_app_id'] ?>',
     status: true,
	 cookie: true,
     xfbml: true
   });
 };
  (function() {
    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.src = document.location.protocol + '//connect.facebook.net/<?php echo $this->t['fb_comment_lang']; ?>/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
   }());
</script>
<?php
	echo '</div>';
	}

} else {

	if (!empty($this->commentitem)){
		//$userImage	= Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/icon-user.png', '');
		$userImage	= PhocaGalleryRenderFront::renderIcon('user', 'media/com_phocagallery/images/icon-user.png', '');
		$smileys = PhocaGalleryComment::getSmileys();

		foreach ($this->commentitem as $itemValue) {
			$date		= Joomla\CMS\HTML\HTMLHelper::_('date',  $itemValue->date, JText::_('DATE_FORMAT_LC2') );
			$comment	= $itemValue->comment;
			$comment 	= PhocaGalleryComment::bbCodeReplace($comment);
			foreach ($smileys as $smileyKey => $smileyValue) {
				$comment = str_replace($smileyKey, Joomla\CMS\HTML\HTMLHelper::_( 'image', 'media/com_phocagallery/images/'.$smileyValue .'.png',''), $comment);
			}

			echo '<blockquote><h4>'.$userImage.'&nbsp;'.$itemValue->name.'</h4>'
				.'<p><strong>'.PhocaGalleryText::wordDelete($itemValue->title, 50, '...').'</strong></p>'
				.'<p style="overflow:auto;width:'.$this->t['commentwidth'].'px;">'.$comment.'</p>'
				.'<p style="text-align:right"><small>'.$date.'</small></p></blockquote>';
		}
	}

	echo '<h4>'.JText::_('COM_PHOCAGALLERY_ADD_COMMENT').'</h4>';

	if ($this->t['already_commented']) {
		echo '<p>'.JText::_('COM_PHOCAGALLERY_COMMENT_ALREADY_SUBMITTED').'</p>';
	} else if ($this->t['not_registered']) {
		echo '<p>'.JText::_('COM_PHOCAGALLERY_COMMENT_ONLY_REGISTERED_LOGGED_SUBMIT_COMMENT').'</p>';
	} else {
		echo '<form action="'.htmlspecialchars($this->t['action']).'" name="phocagallerycommentsform" id="phocagallery-comments-form" method="post" >'
			.'<table>'
			.'<tr>'
			.'<td>'.JText::_('COM_PHOCAGALLERY_NAME').':</td>'
			.'<td>'.$this->t['name'].'</td>'
			.'</tr>';

		echo '<tr>'
			.'<td>'.JText::_('COM_PHOCAGALLERY_TITLE').':</td>'
			.'<td><input type="text" name="phocagallerycommentstitle" id="phocagallery-comments-title" value="" maxlength="255" class="comment-input" /></td>'
			.'</tr>';

		echo '<tr>'
			.'<td>&nbsp;</td>'
			.'<td>'
			.'<a href="#" onclick="pasteTag(\'b\', true); return false;">'
			. PhocaGalleryRenderFront::renderIcon('bold', $this->t['icon_path'].'icon-b.png', JText::_('COM_PHOCAGALLERY_BOLD'))
			.'</a>&nbsp;'

			.'<a href="#" onclick="pasteTag(\'i\', true); return false;">'
			. PhocaGalleryRenderFront::renderIcon('italic', $this->t['icon_path'].'icon-i.png', JText::_('COM_PHOCAGALLERY_ITALIC'))
			.'</a>&nbsp;'

			.'<a href="#" onclick="pasteTag(\'u\', true); return false;">'
			. PhocaGalleryRenderFront::renderIcon('underline', $this->t['icon_path'].'icon-u.png', JText::_('COM_PHOCAGALLERY_UNDERLINE'))
			.'</a>&nbsp;&nbsp;'

			.'<a href="#" onclick="pasteSmiley(\':)\'); return false;">'
			. PhocaGalleryRenderFront::renderIcon('smile', $this->t['icon_path'].'icon-s-smile.png', JText::_('COM_PHOCAGALLERY_SMILE'))
			.'</a>&nbsp;'

			.'<a href="#" onclick="pasteSmiley(\':lol:\'); return false;">'
			. PhocaGalleryRenderFront::renderIcon('lol', $this->t['icon_path'].'icon-s-lol.png', JText::_('COM_PHOCAGALLERY_LOL'))
			.'</a>&nbsp;'

			.'<a href="#" onclick="pasteSmiley(\':(\'); return false;">'
			. PhocaGalleryRenderFront::renderIcon('sad', $this->t['icon_path'].'icon-s-sad.png', JText::_('COM_PHOCAGALLERY_SAD'))
			.'</a>&nbsp;'

			.'<a href="#" onclick="pasteSmiley(\':?\'); return false;">'
			. PhocaGalleryRenderFront::renderIcon('confused', $this->t['icon_path'].'icon-s-confused.png', JText::_('COM_PHOCAGALLERY_CONFUSED'))
			.'</a>&nbsp;'

			.'<a href="#" onclick="pasteSmiley(\':wink:\'); return false;">'
			. PhocaGalleryRenderFront::renderIcon('wink', $this->t['icon_path'].'icon-s-wink.png', JText::_('COM_PHOCAGALLERY_WINK'))
			.'</a>&nbsp;'
			.'</td>'
			.'</tr>';

			echo '<tr>'
				.'<td>&nbsp;</td>'
				.'<td>'
				.'<textarea name="phocagallerycommentseditor" id="phocagallery-comments-editor" cols="30" rows="10"  class= "comment-input" onkeyup="countChars();" ></textarea>'
				.'</td>'
				.'</tr>';

			echo '<tr>'
				.'<td>&nbsp;</td>'
				.'<td>'
				. JText::_('COM_PHOCAGALLERY_CHARACTERS_WRITTEN').' <input name="phocagallerycommentscountin" value="0" readonly="readonly" class="comment-input2" /> '
				. JText::_('COM_PHOCAGALLERY_AND_LEFT_FOR_COMMENT').' <input name="phocagallerycommentscountleft" value="'. $this->t['maxcommentchar'].'" readonly="readonly" class="comment-input2" />'
				.'</td>'
				.'</tr>';

			echo '<tr>'
				.'<td>&nbsp;</td>'
				.'<td align="right">'
				.'<input type="submit" class="btn" id="phocagallerycommentssubmit" onclick="return(checkCommentsForm());" value="'. JText::_('COM_PHOCAGALLERY_SUBMIT_COMMENT').'"/>'
				.'</td>'
				.'</tr>';

			echo '</table>';

			echo '<input type="hidden" name="task" value="comment" />';
			echo '<input type="hidden" name="view" value="comment" />';
			echo '<input type="hidden" name="controller" value="comment" />';
			echo '<input type="hidden" name="id" value="'. $this->t['id'].'" />';
			echo '<input type="hidden" name="catid" value="'. $this->t['catid'].'" />';
			echo '<input type="hidden" name="Itemid" value="'. $this->itemId .'" />';
			echo Joomla\CMS\HTML\HTMLHelper::_( 'form.token' );
			echo '</form>';
		}
}
echo '</div>';
if ($this->t['detailwindow'] == 7 || $this->t['display_comment_nopup'] == 1) {
    echo PhocaGalleryUtils::getExtInfo();
}
?>
