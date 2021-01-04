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

if ($this->params->get('show_ordering_categories') || $this->params->get('show_pagination_limit_categories') || $this->params->get('show_pagination_categories')) {
	echo '<form action="'.htmlspecialchars($this->t['action']).'" method="post" name="adminForm">'. "\n";
	if (count($this->categories)) {
		echo '<div class="pagination pagination-centered">';
		if ($this->params->get('show_ordering_categories')) {
			echo JText::_('COM_PHOCAGALLERY_ORDER_FRONT') .'&nbsp;'.$this->t['ordering'];
		}
		if ($this->params->get('show_pagination_limit_categories')) {
			echo JText::_('COM_PHOCAGALLERY_DISPLAY_NUM') .'&nbsp;'.$this->t['pagination']->getLimitBox();
		}
		if ($this->params->get('show_pagination_categories')) {
		
			echo '<div class="counter pull-right">'.$this->t['pagination']->getPagesCounter().'</div>'
				.'<div class="pagination pagination-centered">'.$this->t['pagination']->getPagesLinks().'</div>';
		}
		echo '</div>'. "\n";

	}
	echo '<input type="hidden" name="controller" value="categories" />';
	echo Joomla\CMS\HTML\HTMLHelper::_( 'form.token' );
	echo '</form>';
	
	echo '<div class="ph-cb pg-csv-paginaton">&nbsp;</div>';
} else {
	echo '<div class="ph-cb pg-csv-paginaton">&nbsp;</div>';
}
?>
