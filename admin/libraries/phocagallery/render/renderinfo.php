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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');

class PhocaGalleryRenderInfo
{
    public static function getPhocaVersion() {
        $folder = JPATH_ADMINISTRATOR . '/' . 'components/com_phocagallery';
        if (JFolder::exists($folder)) {
            $xmlFilesInDir = JFolder::files($folder, '.xml$');
        } else {
            $folder = JPATH_SITE . '/components/com_phocagallery';
            if (JFolder::exists($folder)) {
                $xmlFilesInDir = JFolder::files($folder, '.xml$');
            } else {
                $xmlFilesInDir = null;
            }
        }
        $xml_items = array();
        if (!empty($xmlFilesInDir)) {
            foreach ($xmlFilesInDir as $xmlfile) {
                if ($data = \JInstaller::parseXMLInstallFile($folder . '/' . $xmlfile)) {
                    foreach ($data as $key => $value) {
                        $xml_items[$key] = $value;
                    }
                }
            }
        }
        if (isset($xml_items['version']) && $xml_items['version'] != '') {
            return $xml_items['version'];
        } else {
            return '';
        }
    }
}
?>
