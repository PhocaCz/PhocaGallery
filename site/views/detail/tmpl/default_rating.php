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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
$app = Factory::getApplication();
$amp = PhocaGalleryUtils::setQuestionmarkOrAmp($this->t['action']);

//if ((int)$this->t['display_rating_img'] == 1 || $this->t['mb_rating']) {
if ((int)$this->t['display_rating_img'] == 1) {
	// Leave message for already voted images
	$vote = $app->getInput()->get('vote', 0, 'int');;
	if ($vote == 1) {
		$voteMsg = Text::_('COM_PHOCAGALLERY_ALREADY_RATED_IMG_THANKS');
	} else {
		$voteMsg = Text::_('COM_PHOCAGALLERY_ALREADY_RATE_IMG');
	}

	echo '<table style="text-align:left" border="0">'
		.'<tr>'
		.'<td><strong>' . Text::_('COM_PHOCAGALLERY_RATING'). '</strong>: ' . $this->t['votesaverageimg'] .' / '.$this->t['votescountimg'] . ' ' . Text::_($this->t['votestextimg']). '&nbsp;&nbsp;</td>';

	if ($this->t['alreay_ratedimg']) {
		echo '<td style="text-align:left"><ul class="star-rating">'
			.'<li class="current-rating" style="width:'.$this->t['voteswidthimg'].'px"></li>'
			.'<li><span class="star1"></span></li>';

		for ($i = 2;$i < 6;$i++) {
			echo '<li><span class="stars'.$i.'"></span></li>';
		}
		echo '</ul></td>';

		if ($this->t['enable_multibox'] == 1) {
			echo '<td></td></tr>';
			echo '<tr><td style="text-align:left" colspan="4" class="pg-rating-msg">'.$voteMsg.'</td></tr>';
		} else {
			echo '<td style="text-align:left" colspan="4" class="pg-rating-msg">&nbsp;&nbsp;'.$voteMsg.'</td></tr>';
		}


	} else if ($this->t['not_registered_img']) {

		echo '<td style="text-align:left"><ul class="star-rating">'
			.'<li class="current-rating" style="width:'.$this->t['voteswidthimg'].'px"></li>'
			.'<li><span class="star1"></span></li>';

		for ($i = 2;$i < 6;$i++) {
			echo '<li><span class="stars'.$i.'"></span></li>';
		}
		echo '</ul></td>';

		if ($this->t['enable_multibox'] == 1) {
			echo '<td></td></tr>';
			echo '<tr><td style="text-align:left" colspan="4" class="pg-rating-msg">'.Text::_('COM_PHOCAGALLERY_COMMENT_ONLY_REGISTERED_LOGGED_RATE_IMAGE').'</td></tr>';
		} else {
			echo '<td style="text-align:left" colspan="4" class="pg-rating-msg">&nbsp;&nbsp;' . Text::_('COM_PHOCAGALLERY_COMMENT_ONLY_REGISTERED_LOGGED_RATE_IMAGE').'</td></tr>';
		}


	} else {

		echo '<td style="text-align:left"><ul class="star-rating">'
			.'<li class="current-rating" style="width:'.$this->t['voteswidthimg'].'px"></li>'
			//.'<li><a href="'.$this->t['action'].$amp.'controller=detail&task=rate&rating=1" title="1 '. JText::_('COM_PHOCAGALLERY_STAR_OUT_OF').' 5" class="star1">1</a></li>';

			.'<li><a href="'.htmlspecialchars($this->t['action']).$amp.'controller=detail&task=rate&rating=1" title="'. Text::sprintf('COM_PHOCAGALLERY_STAR_OUT_OF', 1, 5). '" class="star1">1</a></li>';

		for ($i = 2;$i < 6;$i++) {
			//echo '<li><a href="'.$this->t['action'].$amp.'controller=detail&task=rate&rating='.$i.'" title="'.$i.' '. JText::_('COM_PHOCAGALLERY_STARS_OUT_OF').' 5" class="stars'.$i.'">'.$i.'</a></li>';

			echo '<li><a href="'.htmlspecialchars($this->t['action']).$amp.'controller=detail&task=rate&rating='.$i.'" title="'.Text::sprintf('COM_PHOCAGALLERY_STARS_OUT_OF', $i, 5). '" class="stars'.$i.'">'.$i.'</a></li>';
		}
		echo '</ul></td></tr>';
	}
	echo '</table>';
}
?>
