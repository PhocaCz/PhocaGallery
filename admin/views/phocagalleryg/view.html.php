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
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
jimport( 'joomla.application.component.view');
phocagalleryimport('phocagallery.render.rendermaposm');

class PhocaGalleryCpViewPhocagalleryG extends HtmlView
{
	protected $latitude;
	protected $longitude;
	protected $zoom;
	protected $map_type;
	protected $type;
	protected $r;
	protected $t;

	public function display($tpl = null) {

		$app = Factory::getApplication();

		$this->t	= PhocaGalleryUtils::setVars('g');
		$this->r	= new PhocaGalleryRenderAdminview();

		$params	 			= ComponentHelper::getParams( 'com_phocagallery' );
		$this->latitude		= $app->input->get( 'lat', '50.079623358200884', 'get', 'string' );
		$this->longitude	= $app->input->get( 'lng', '14.429919719696045', 'get', 'string' );
		$this->zoom			= $app->input->get( 'zoom', '2', 'get', 'string' );
		$this->map_type		= $params->get( 'map_type', 2 );

		$this->type 		= 'map';

		$document	= Factory::getDocument();
		$document->addCustomTag( "<style type=\"text/css\"> \n"
			." html,body, .contentpane{overflow:hidden;background:#ffffff;} \n"
			." </style> \n");




		if ($this->map_type == 2) {
			parent::display('osm');
		} else {
			parent::display($tpl);
		}
	}
}
?>
