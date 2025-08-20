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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

defined( '_JEXEC' ) or die( 'Restricted access' );

class PhocaGalleryUtils
{

	public static function getExtInfo() {

        PluginHelper::importPlugin('phocatools');
        $results = Factory::getApplication()->triggerEvent('onPhocatoolsOnDisplayInfo', array('NzI5NzY5NTcxMTc='));

        if (isset($results[0]) && $results[0] === true) {
            return '';
        }
	    return '<div style="display:block;color:#ccc;text-align:right;">Powered by <a href="https://www.phoca.cz/phocagallery">Phoca Gallery</a></div>';
    }

	public static function htmlToRgb($clr) {
		if ($clr[0] == '#') {
			$clr = substr($clr, 1);
		}

		if (strlen($clr) == 6) {
			list($r, $g, $b) = array($clr[0].$clr[1],$clr[2].$clr[3],$clr[4].$clr[5]);
		} else if (strlen($clr) == 3) {
			list($r, $g, $b) = array($clr[0].$clr[0], $clr[1].$clr[1], $clr[2].$clr[2]);
		} else {
			$r = $g = $b = 255;
		}

		$color[0] = hexdec($r);
		$color[1] = hexdec($g);
		$color[2] = hexdec($b);

		return $color;
	}

	/*
	 * Source: http://php.net/manual/en/function.ini-get.php
	 */
	public static function iniGetBool($a) {
		$b = ini_get($a);
		switch (strtolower($b)) {
			case 'on':
			case 'yes':
			case 'true':
			return 'assert.active' !== $a;

			case 'stdout':
			case 'stderr':
			return 'display_errors' === $a;

			Default:
			return (bool) (int) $b;
		}
	}

	public static function setQuestionmarkOrAmp($url) {
		$isThereQMR = false;
		$isThereQMR = preg_match("/\?/i", $url);
		if ($isThereQMR) {
			return '&amp;';
		} else {
			return '?';
		}
	}

	public static function toArray($value = FALSE) {
		if ($value == FALSE) {
			return array(0 => 0);
		} else if (empty($value)) {
			return array(0 => 0);
		} else if (is_array($value)) {
			return $value;
		} else {
			return array(0 => $value);
		}

	}

	public static function setMessage($new = '', $current = '') {

		$message = $current;
		if($new != '') {
			if ($current != '') {
				$message .= '<br />';
			}
			$message .= $new;
		}
		return $message;
	}




	public static function filterInput($string) {
		if (strpos($string, '"') !== false) {
			$string = str_replace(array('=', '<'), '', $string);
		}
		return $string;
	}

	public static function isURLAddress($url) {
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}

	public static function isEnabledMultiboxFeature($feature) {

		$app		= Factory::getApplication();
		$params		= $app->getParams();

		$enable_multibox				= $params->get( 'enable_multibox', 0);
		$display_multibox				= $params->get( 'display_multibox', array(1,2));

		if ($enable_multibox == 1 && in_array($feature,$display_multibox)) {
			return true;
		}
		return false;
	}

	public static function setVars( $task = '') {

		$a			= array();
		$app		= Factory::getApplication();
		$a['o'] 	= htmlspecialchars(strip_tags($app->getInput()->get('option')));
		$a['c'] 	= str_replace('com_', '', $a['o']);
		$a['n'] 	= 'Phoca' . ucfirst(str_replace('com_phoca', '', $a['o']));
		$a['l'] 	= strtoupper($a['o']);
		$a['i']		= 'media/'.$a['o'].'/images/administrator/';
		$a['ja']	= 'media/'.$a['o'].'/js/administrator/';
		$a['jf']	= 'media/'.$a['o'].'/js/';
		$a['s']		= 'media/'.$a['o'].'/css/administrator/'.$a['c'].'.css';
		$a['task']	= $a['c'] . htmlspecialchars(strip_tags($task));
		$a['tasks'] = $a['task']. 's';
		return $a;
	}

	public static function getIntFromString($string) {

		if (empty($string)) {
			return 0;
		}
		$int	= '';//$int = 0
		$parts 	= explode(':', $string);
		if (isset($parts[0])) {
			$int = (int)$parts[0];
		}
		return $int;
	}

	/*
	 public static function getIp() {
		$params 				= ComponentHelper::getParams('com_phocagallery');
		$store_ip				= $params->get( 'store_ip', 0 );

		if ($store_ip == 0) {
			return '';
		}

		$ip = false;
		if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR')) {
			$ip  = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip  = getenv('HTTP_X_FORWARDED_FOR');
		}
		if (!$ip) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}*/
}
?>
