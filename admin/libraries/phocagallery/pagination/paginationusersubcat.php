<?php
/**
 * @package   Phoca Gallery
 * @author    Jan Pavelka - https://www.phoca.cz
 * @copyright Copyright (C) Jan Pavelka https://www.phoca.cz
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 and later
 * @cms       Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\PaginationObject;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

jimport('joomla.html.pagination');
class PhocaGalleryPaginationUserSubCat extends Pagination
{
	var $_tabId;

	public function setTab($tabId) {
		$this->_tabId = (string)$tabId;
	}

	public function _buildDataObject()
	{
		$tabLink = '';
		if ((string)$this->_tabId > 0) {
			$tabLink = '&tab='.(string)$this->_tabId;
		}

		// Initialize variables
		$data = new stdClass();

		$data->all	= new JPaginationObject(Text::_('COM_PHOCAGALLERY_VIEW_ALL'));
		if (!$this->viewall) {
			$data->all->base	= '0';
			$data->all->link	= Route::_($tabLink."&limitstartsubcat=");
		}

		// Set the start and previous data objects
		$data->start	= new JPaginationObject(Text::_('COM_PHOCAGALLERY_PAG_START'));
		$data->previous	= new JPaginationObject(Text::_('COM_PHOCAGALLERY_PAG_PREV'));

		if ($this->pagesCurrent > 1)
		{
			$page = ($this->pagesCurrent -2) * $this->limit;

			$page = $page == 0 ? '' : $page; //set the empty for removal from route

			$data->start->base	= '0';
			$data->start->link	= Route::_($tabLink."&limitstartsubcat=");
			$data->previous->base	= $page;
			$data->previous->link	= Route::_($tabLink."&limitstartsubcat=".$page);
		}

		// Set the next and end data objects
		$data->next	= new JPaginationObject(Text::_('COM_PHOCAGALLERY_PAG_NEXT'));
		$data->end	= new JPaginationObject(Text::_('COM_PHOCAGALLERY_PAG_END'));

		if ($this->pagesCurrent < $this->pagesTotal)
		{
			$next = $this->pagesCurrent * $this->limit;
			$end  = ($this->pagesTotal -1) * $this->limit;

			$data->next->base	= $next;
			$data->next->link	= Route::_($tabLink."&limitstartsubcat=".$next);
			$data->end->base	= $end;
			$data->end->link	= Route::_($tabLink."&limitstartsubcat=".$end);
		}

		$data->pages = array();
		$stop = $this->pagesStop;
		for ($i = $this->pagesStart; $i <= $stop; $i ++)
		{
			$offset = ($i -1) * $this->limit;

			$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route

			$data->pages[$i] = new PaginationObject($i);
			if ($i != $this->pagesCurrent || $this->viewall)
			{
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->link	= Route::_($tabLink."&limitstartsubcat=".$offset);
			}
		}
		return $data;
	}

	public function getLimitBox()
	{
		$app	= Factory::getApplication();

		// Initialize variables
		$limits = array ();

		// Make the option list
		for ($i = 5; $i <= 30; $i += 5) {
			$limits[] = HTMLHelper::_('select.option', "$i");
		}
		$limits[] = HTMLHelper::_('select.option', '50');
		$limits[] = HTMLHelper::_('select.option', '100');
		$limits[] = HTMLHelper::_('select.option', '0', Text::_('COM_PHOCAGALLERY_ALL'));

		$selected = $this->viewall ? 0 : $this->limit;

		// Build the select list
		if ($app->isClient('administrator')) {
			$html = HTMLHelper::_('select.genericlist',  $limits, 'limitsubcat', 'class="form-control input-mini" size="1" onchange="Joomla.submitform();"', 'value', 'text', $selected);
		} else {
			$html = HTMLHelper::_('select.genericlist',  $limits, 'limitsubcat', 'class="form-control input-mini" size="1"  onchange="this.form.submit()"', 'value', 'text', $selected);
		}
		return $html;
	}


	public function orderUpIcon($i, $condition = true, $task = '#', $alt = 'COM_PHOCAGALLERY_MOVE_UP', $enabled = true, $checkbox = 'cb') {


		$alt = Text::_($alt);


		$html = '&nbsp;';
		if (($i > 0 || ($i + $this->limitstart > 0)) && $condition)
		{
			if($enabled) {
				$html	= '<a href="'.$task.'" title="'.$alt.'">';
				$html	.= '   <img src="'.Uri::base(true).'/media/com_phocagallery/images/icon-uparrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
				$html	.= '</a>';
			} else {
				$html	= '<img src="'.Uri::base(true).'/media/com_phocagallery/images/icon-uparrow0.png" width="16" height="16" border="0" alt="'.$alt.'" />';
			}
		}

		return $html;
	}


	public function orderDownIcon($i, $n, $condition = true, $task = '#', $alt = 'COM_PHOCAGALLERY_MOVE_DOWN', $enabled = true, $checkbox = 'cb'){

		$alt = Text::_($alt);

		$html = '&nbsp;';
		if (($i < $n -1 || $i + $this->limitstart < $this->total - 1) && $condition)
		{
			if($enabled) {
				$html	= '<a href="'.$task.'" title="'.$alt.'">';
				$html	.= '  <img src="'.Uri::base(true).'/media/com_phocagallery/images/icon-downarrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
				$html	.= '</a>';
			} else {
				$html	= '<img src="'.Uri::base(true).'/media/com_phocagallery/images/icon-downarrow0.png" width="16" height="16" border="0" alt="'.$alt.'" />';
			}
		}

		return $html;
	}
}
?>
