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
use Joomla\CMS\Language\Text;
// SEF problem
$isThereQMR = false;
$isThereQMR = preg_match("/\?/i", $this->t['action']);
if ($isThereQMR) {$amp = '&amp;';} else {$amp = '?';}

echo '<div id="phocagallery-votes">'. "\n";
echo '<h4>'. Text::_('COM_PHOCAGALLERY_RATE_THIS_CATEGORY'). '</h4>'. "\n";
echo '<p><strong>' . Text::_('COM_PHOCAGALLERY_RATING'). '</strong>: ' . $this->t['votesaverage'] .' / '.$this->t['votescount'] . ' ' . Text::_($this->t['votestext']). '</p>'. "\n";

if ($this->t['alreay_rated']) {

	echo '<ul class="star-rating">'
		.'<li class="current-rating" style="width:'.$this->t['voteswidth'].'px"></li>'
		.'<li><span class="star1"></span></li>';

	for ($i = 2;$i < 6;$i++) {
		echo '<li><span class="stars'.$i.'"></span></li>';
	}
	echo '</ul>'
		.'<p>'.Text::_('COM_PHOCAGALLERY_RATING_ALREADY_RATED').'</p>'. "\n";

} else if ($this->t['not_registered']) {

	echo '<ul class="star-rating">'
		.'<li class="current-rating" style="width:'.$this->t['voteswidth'].'px"></li>'
		.'<li><span class="star1"></span></li>';

	for ($i = 2;$i < 6;$i++) {
		echo '<li><span class="stars'.$i.'"></span></li>';
	}
	echo '</ul>'
		.'<p>'.Text::_('COM_PHOCAGALLERY_COMMENT_ONLY_REGISTERED_LOGGED_RATE_CATEGORY').'</p>'. "\n";

} else {

	echo '<ul class="star-rating">'
		.'<li class="current-rating" style="width:'.$this->t['voteswidth'].'px"></li>'
		.'<li><a href="'.htmlspecialchars($this->t['action']).$amp.'controller=category&task=rate&rating=1&tab='.$this->t['currenttab']['rating'].$this->t['limitstarturl'].'" title="'. Text::sprintf('COM_PHOCAGALLERY_STAR_OUT_OF', 1, 5). '" class="star1">1</a></li>';

	for ($i = 2;$i < 6;$i++) {

		echo '<li><a href="'.htmlspecialchars($this->t['action']).$amp.'controller=category&task=rate&rating='.$i.'&tab='.$this->t['currenttab']['rating'].$this->t['limitstarturl'].'" title="'. Text::sprintf('COM_PHOCAGALLERY_STARS_OUT_OF', $i, 5) .'" class="stars'.$i.'">'.$i.'</a></li>';
	}
	echo '</ul>';
}

echo '</div>'. "\n";
?>
