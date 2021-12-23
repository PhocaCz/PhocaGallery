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


use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access'); ?>


<div class="ph-item-box">
		<div class="ph-item-image"><a title="<?php echo $this->_tmp_folder->name ?>" href="index.php?option=com_phocagallery&amp;view=phocagalleryf&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_with_name_relative_no; ?>&amp;field=<?php echo $this->field; ?>"><span class="ph-cp-item"><i class="phi duotone phi-fs-l phi-fc-brd icon-folder-close"></i></span></a></div>

	    <div class="ph-item-name"><a  title="<?php echo $this->_tmp_folder->name ?>" href="index.php?option=com_phocagallery&amp;view=phocagalleryf&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_with_name_relative_no; ?>&amp;field=<?php echo $this->field; ?>"><span><?php echo PhocagalleryText::WordDelete($this->_tmp_folder->name, 15); ?></span></a></div>

        <div class="ph-item-action-box">
			<a href="#" onclick="if (window.parent) window.parent.<?php echo $this->fce; ?>('<?php echo $this->_tmp_folder->path_with_name_relative_no; ?>');" title="<?php echo Text::_('COM_PHOCAGALLERY_INSERT_FOLDER') ?>"><span class="ph-cp-item"><i class="phi duotone phi-fs-m phi-fc-gd icon-download"></i></span></a></div>
</div>
