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

$currentFolder = '';
if (isset($this->t['state']->folder) && $this->t['state']->folder != '') {
 $currentFolder = $this->t['state']->folder;
}

echo '<div class="ph-item-list-box ph-item-list-box-admin">';
echo $this->loadTemplate('up');
if (count($this->t['folders']) > 0) {
    for ($i=0,$n=count($this->t['folders']); $i<$n; $i++) {
        $this->setFolder($i);
        echo $this->loadTemplate('folder');
    }
} else {
    echo '<div class="ph-item-list-box-head">'.Text::_( 'COM_PHOCAGALLERY_THERE_IS_NO_FOLDER' ).'</div>';
}
echo '</div>';

echo '<div style="clear:both"></div>';
echo PhocaGalleryFileUpload::renderCreateFolder($this->t['session']->getName(), $this->t['session']->getId(), $currentFolder, 'phocagalleryf', 'field='.$this->field);
?>




