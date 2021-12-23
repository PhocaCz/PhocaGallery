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
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.html.pagination');
class PhocaGalleryPaginationCategory extends Pagination
{
	public function getLimitBox() {
		$app	= Factory::getApplication();

		$paramsC 			= ComponentHelper::getParams('com_phocagallery') ;
		$pagination 		= $paramsC->get( 'pagination_category', '5,10,15,20,50' );
		$paginationArray	= explode( ',', $pagination );

		// Initialize variables
		$limits = array ();

		foreach ($paginationArray as $paginationValue) {
			$limits[] = HTMLHelper::_('select.option', $paginationValue);
		}
		$limits[] = HTMLHelper::_('select.option', '0', Text::_('COM_PHOCAGALLERY_ALL'));

		$selected = $this->viewall ? 0 : $this->limit;

		// Build the select list
		if ($app->isClient('administrator')) {
			$html = HTMLHelper::_('select.genericlist',  $limits, 'limit', 'class="form-select" size="1" onchange="Joomla.submitform();"', 'value', 'text', $selected);
		} else {
			$html = HTMLHelper::_('select.genericlist',  $limits, 'limit', 'class="form-select" size="1" onchange="this.form.submit()"', 'value', 'text', $selected);
		}
		return $html;
	}
}
?>
