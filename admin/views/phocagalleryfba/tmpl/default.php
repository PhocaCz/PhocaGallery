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
//JHtml::_('behavior.tooltip');

echo '<div id="phocagallery-fba">'. "\n"
.'<h4>'.Text::_( 'COM_PHOCAGALLERY_FB_SELECT_ALBUM' ).'</h4>';

if ($this->userInfo == 1 ){

	echo '<ul>';
	if(!empty($this->albums)) {
		foreach ($this->albums as $key => $album) {
    //.'<a href="#" onclick="if (window.parent) window.parent.'.  $this->fce .' (\''. $album['aid'].'\');">'.$album['name'].'</a>'
	echo '<li class="icon-16-edb-categories">'
	.'<a href="#" onclick="if (window.parent) window.parent.'.  $this->fce .' (\''. $album['id'].'\');">'.$album['name'].'</a>'
	.'</li>' . "\n";
		}
	}

	echo '</ul>'. "\n";
} else {
	echo '<div>'.Text::_('COM_PHOCAGALLERY_FB_SELECT_USER').'</div>';
	echo '<p>&nbsp;</p>';
	echo '<div><a style="text-decoration:underline" href="#" onclick="window.parent.closeModal();">'.Text::_('COM_PHOCAGALLERY_CLOSE_WINDOW').'</a></div>';

}

echo '</div>'. "\n";

?>
