<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

/*
jimport('joomla.html.grid');
jimport('joomla.html.html.grid');
jimport('joomla.html.html.jgrid');
*/
if (! class_exists('JHtmlGrid')) {
	require_once( JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'grid.php' );
}



class PhocaGalleryGrid extends JHtmlGrid
{	
	public static function id($rowNum, $recId, $checkedOut = false, $name = 'cid')
	{
		if ($checkedOut)
		{
			return '';
		}
		else
		{
			return '<input type="checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId
				. '" onclick="Joomla.isChecked(this.checked, \'undefined\');" title="' . JText::sprintf('JGRID_CHECKBOX_ROW_N', ($rowNum + 1)) . '" />';
			//return '<input type="checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId
			//	. '"  title="' . JText::sprintf('JGRID_CHECKBOX_ROW_N', ($rowNum + 1)) . '" />';
		}
	}
	
	/**
	 * Method to sort a column in a grid
	 *
	 * @param   string  $title          The link title
	 * @param   string  $order          The order field for the column
	 * @param   string  $direction      The current direction
	 * @param   string  $selected       The selected ordering
	 * @param   string  $task           An optional task override
	 * @param   string  $new_direction  An optional direction for the new column
	 * @param   string  $tip            An optional text shown as tooltip title instead of $title
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	 
	 
	 /*
	 * GRID in frontend must be customized
	 * because Joomla! takes "adminForm" as the only one name of form ??????????????????????????????????????????
	 *
	 */
	
	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc', $tip = '', $form = '', $suffix = '')
	{
		JHtml::_('behavior.core');
		JHtml::_('bootstrap.tooltip');

		$direction = strtolower($direction);
		$icon = array('arrow-up-3', 'arrow-down-3');
		$index = (int) ($direction == 'desc');

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$html = '<a href="#" onclick="Joomla.tableOrderingPhoca(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\',\'' . $form . '\',\'' . $suffix . '\');return false;"'
			. ' class="hasTooltip" title="' . JHtml::tooltipText(($tip ? $tip : $title), 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN') . '">';

		if (isset($title['0']) && $title['0'] == '<')
		{
			$html .= $title;
		}
		else
		{
			$html .= JText::_($title);
		}

		if ($order == $selected)
		{
			$html .= ' <i class="icon-' . $icon[$index] . '"></i>';
		}

		$html .= '</a>';

		return $html;
	}
	
	public static function renderSortJs() {
	

$o = '';	
$o .= '<script type="text/javascript">'."\n";
$o .= ''."\n";
$o .= 'Joomla.tableOrderingPhoca = function(order, dir, task, form, suffix) {'."\n";
$o .= ''."\n";
$o .= '   if (typeof(form) === "undefined") {'."\n";
$o .= '      form = document.getElementById("adminForm");'."\n";
$o .= '   }'."\n";
$o .= ''."\n";
$o .= ''."\n";
$o .= '   if (typeof form == "string" || form instanceof String) {'."\n";
$o .= '      form = document.getElementById(form);'."\n";
$o .= '   }'."\n";
$o .= ''."\n";    
$o .= '   var orderS 		= "filter_order" + suffix;'."\n";
$o .= '   var orderSDir 	= "filter_order_Dir" + suffix;'."\n";
$o .= ''."\n";
$o .= '   form[orderS].value = order;'."\n";
$o .= '   form[orderSDir].value = dir;'."\n";
$o .= '   Joomla.submitform(task, form);'."\n";
$o .= ''."\n";	
$o .= '}'."\n";
$o .= '</script>'."\n";


	
		$document = JFactory::getDocument();
		$document->addCustomTag($o);
	}
}
