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
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

if (!Factory::getUser()->authorise('core.manage', 'com_phocagallery')) {
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
}
if (! class_exists('PhocaGalleryLoader')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocagallery/libraries/loader.php');
}

require_once JPATH_ADMINISTRATOR . '/components/com_phocagallery/libraries/autoloadPhoca.php';
require_once( JPATH_COMPONENT.'/controller.php' );
phocagalleryimport('phocagallery.utils.settings');
phocagalleryimport('phocagallery.utils.utils');
phocagalleryimport('phocagallery.utils.exception');
phocagalleryimport('phocagallery.category.category');
phocagalleryimport('phocagallery.path.path');
phocagalleryimport('phocagallery.file.file');
phocagalleryimport('phocagallery.file.filefolder');
phocagalleryimport('phocagallery.file.filethumbnail');
phocagalleryimport('phocagallery.file.fileupload');
phocagalleryimport('phocagallery.render.renderadmin');
phocagalleryimport('phocagallery.render.renderadminview');
phocagalleryimport('phocagallery.render.renderadminviews');
phocagalleryimport('phocagallery.text.text');
phocagalleryimport('phocagallery.render.renderprocess');
//phocagalleryimport('phocagallery.html.grid');
phocagalleryimport('phocagallery.html.categoryhtml');
phocagalleryimport('phocagallery.html.jgrid');
phocagalleryimport('phocagallery.html.category');
phocagalleryimport('phocagallery.html.batch');
phocagalleryimport('phocagallery.image.image');
phocagalleryimport('phocagallery.access.access');

jimport('joomla.application.component.controller');

$controller	= BaseController::getInstance('PhocaGalleryCp');

$controller->execute(Factory::getApplication()->input->get('task'));

$controller->redirect();


?>
