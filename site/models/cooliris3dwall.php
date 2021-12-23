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
defined('_JEXEC') or die();
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
jimport('joomla.application.component.model');

class PhocagalleryModelCooliris3DWall extends BaseDatabaseModel
{

	function __construct() {
		parent::__construct();
	}

	function getCategory($id) {

		$app	= Factory::getApplication();
		if ($id > 0) {

			$query = 'SELECT c.*,' .
				' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug '.
				' FROM #__phocagallery_categories AS c' .
				' WHERE c.id = '. (int) $id;
			$this->_db->setQuery($query, 0, 1);
			$category = $this->_db->loadObject();

			$user = Factory::getUser();
			// USER RIGHT - ACCESS - - - - - -
			$rightDisplay	= 1;//default is set to 1 (all users can see the category)
			if (!empty($category)) {
				$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $category->accessuserid, $category->access, $user->getAuthorisedViewLevels(), $user->get('id', 0), 0);
			}

			if ($rightDisplay == 0) {
				$uri 			= \Joomla\CMS\Uri\Uri::getInstance();
				$t['pl']		= 'index.php?option=com_users&view=login&return='.base64_encode($uri->toString());
				$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_NOT_AUTHORISED_ACTION'));
				$app->redirect(Route::_($t['pl'], false));
				exit;
			}
			// - - - - - - - - - - - - - - - -
			return $category;
		}
		return false;
	}
}
?>
