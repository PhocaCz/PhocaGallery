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
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;

if (! class_exists('PhocaGalleryLoader')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocagallery/libraries/loader.php');
}

require_once JPATH_ADMINISTRATOR . '/components/com_phocagallery/libraries/autoloadPhoca.php';
// Require the base controller
require_once( JPATH_COMPONENT.'/controller.php' );

phocagalleryimport('phocagallery.category.category');
phocagalleryimport('phocagallery.utils.settings');
phocagalleryimport('phocagallery.path.path');
phocagalleryimport('phocagallery.path.route');
phocagalleryimport('phocagallery.pagination.paginationcategories');
phocagalleryimport('phocagallery.pagination.paginationcategory');
phocagalleryimport('phocagallery.library.library');
phocagalleryimport('phocagallery.text.text');
phocagalleryimport('phocagallery.access.access');
phocagalleryimport('phocagallery.file.file');
phocagalleryimport('phocagallery.image.image');
phocagalleryimport('phocagallery.image.imagefront');
phocagalleryimport('phocagallery.render.renderdetailwindow');
phocagalleryimport('phocagallery.render.renderinfo');
phocagalleryimport('phocagallery.render.renderfront');
phocagalleryimport('phocagallery.utils.utils');
phocagalleryimport('phocagallery.utils.extension');
phocagalleryimport('phocagallery.tag.tag');
phocagalleryimport('phocagallery.html.categoryhtml');
phocagalleryimport('phocagallery.html.grid');
//phocagalleryimport('phocagallery.utils.utils');
//PhocaGalleryRenderFront::correctRender();


// Require specific controller if requested

if($controller = Factory::getApplication()->input->get( 'controller')) {
    $path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
// Create the controller
$app	= Factory::getApplication();
$classname    = 'PhocaGalleryController'.ucfirst((string)$controller);
$controller   = new $classname( );

// Perform the Request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
?>
