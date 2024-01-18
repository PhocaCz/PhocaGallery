<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );
phocagalleryimport( 'phocagallery.render.renderinfo' );
phocagalleryimport( 'phocagallery.utils.utils' );

class PhocaGalleryCpViewPhocaGalleryFe extends HtmlView
{
	protected $t;
	protected $r;
	public function display($tpl = null) {


		$params 	= ComponentHelper::getParams('com_phocagallery');

		$this->t	= PhocaGalleryUtils::setVars('fe');
		$this->r	= new PhocaGalleryRenderAdminview();

		$this->sidebar = Sidebar::render();

		HTMLHelper::stylesheet( 'media/com_phocagallery/css/administrator/phocagallery.css' );
		$app		= Factory::getApplication();

		$this->t['error'] = $app->input->get('error');
		switch ($this->t['error']) {
			case 1:
				$this->t['errormessage'] = Text::_('COM_PHOCAGALLERY_ERROR_1_MEMORY');
			break;

			default:
				$this->t['errormessage'] = Text::_('COM_PHOCAGALLERY_ERROR_1_MEMORY');//TO DO
			break;
		}
		$this->addToolbar();
		parent::display($tpl);
	}


	protected function addToolBar(){
		require_once JPATH_COMPONENT.'/helpers/phocagallerycp.php';
		$canDo = PhocaGalleryCpHelper::getActions(NULL);
        ToolbarHelper::title(Text::_('COM_PHOCAGALLERY_PG_ERROR'), 'warning');

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocagallery" class="btn btn-primary btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAGALLERY_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAGALLERY_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_phocagallery');
		}
	    ToolbarHelper::help( 'screen.phocagallery', true );
    }
}
?>
