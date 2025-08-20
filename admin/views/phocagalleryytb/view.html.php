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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
jimport( 'joomla.application.component.view');


class PhocaGalleryCpViewPhocagalleryYtb extends HtmlView
{
	protected $field;
	protected $fce;
	protected $context 	= 'com_phocagallery.phocagalleryytjjb';
	protected $r;
	protected $t;

	public function display($tpl = null) {

		$params = ComponentHelper::getParams( 'com_phocagallery' );
		$app 	= Factory::getApplication();
		$app->allowCache(false);

		$this->t	= PhocaGalleryUtils::setVars('ytb');
		$this->r	= new PhocaGalleryRenderAdminview();


		$document	= Factory::getDocument();


		$this->t['catid']		= Factory::getApplication()->getInput()->get( 'catid', 0, 'int' );
		$this->t['field']		= Factory::getApplication()->getInput()->get( 'field', '', 'string');
		$this->t['import']		= Factory::getApplication()->getInput()->get( 'import', 0, 'int' );



		$this->t['ytblink'] 		= '';
		$this->t['ytbtitle'] 	= '';
		$this->t['ytbdesc'] 		= '';
		$this->t['ytbfilename'] 	= '';

		if ($this->t['import'] == '1') {
			$this->t['ytblink'] = $app->getUserStateFromRequest( $this->context.'.ytb_link', 'ytb_link', $this->t['ytblink'], 'string' );
			$this->t['ytbtitle'] = $app->getUserStateFromRequest( $this->context.'.ytb_title', 'ytb_titel', $this->t['ytbtitle'], 'string' );
			$this->t['ytbdesc'] = $app->getUserStateFromRequest( $this->context.'.ytb_desc', 'ytb_desc', $this->t['ytbdesc'], 'string' );
			$this->t['ytbfilename'] = $app->getUserStateFromRequest( $this->context.'.ytb_filename', 'ytb_filename', $this->t['ytbfilename'], 'string' );
		}

		parent::display($tpl);
	}

}
?>
