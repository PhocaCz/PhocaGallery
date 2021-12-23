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

/*
jimport('joomla.html.grid');
jimport('joomla.html.html.grid');
jimport('joomla.html.html.jgrid');
*/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\Helpers\JGrid;
use Joomla\CMS\HTML\HTMLHelper;
if (! class_exists('HTMLHelperJGrid')) {
	require_once( JPATH_SITE.'/libraries/src/HTML/Helpers/JGrid.php' );
}

class PhocaGalleryJGrid extends JGrid
{

	public static function approved($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
	{
		if (is_array($prefix)) {
			$options	= $prefix;
			$enabled	= array_key_exists('enabled',	$options) ? $options['enabled']		: $enabled;
			$checkbox	= array_key_exists('checkbox',	$options) ? $options['checkbox']	: $checkbox;
			$prefix		= array_key_exists('prefix',	$options) ? $options['prefix']		: '';
		}
		$states	= array(
			1	=> array('disapprove',	'COM_PHOCAGALLERY_APPROVED',	'COM_PHOCAGALLERY_NOT_APPROVE_ITEM',	'COM_PHOCAGALLERY_APPROVED',	false,	'publish',		'publish'),
			0	=> array('approve',		'COM_PHOCAGALLERY_NOT_APPROVED',	'COM_PHOCAGALLERY_APPROVE_ITEM',	'COM_PHOCAGALLERY_NOT_APPROVED',	false,	'unpublish',	'unpublish')
		);
		return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
	}

}
?>
