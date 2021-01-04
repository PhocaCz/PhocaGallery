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
jimport( 'joomla.application.component.view');


class PhocaGalleryCpViewPhocagalleryYtb extends JViewLegacy
{
	protected $field;
	protected $fce;
	protected $context 	= 'com_phocagallery.phocagalleryytjjb';
	protected $r;
	protected $t;

	public function display($tpl = null) {

		$params = JComponentHelper::getParams( 'com_phocagallery' );
		$app 	= JFactory::getApplication();
		$app->allowCache(false);

		$this->t	= PhocaGalleryUtils::setVars('ytb');
		$this->r	= new PhocaGalleryRenderAdminview();


		$document	= JFactory::getDocument();


		$this->t['catid']		= JFactory::getApplication()->input->get( 'catid', 0, 'int' );
		$this->t['field']		= JFactory::getApplication()->input->get( 'field', '', 'string');
		$this->t['import']		= JFactory::getApplication()->input->get( 'import', 0, 'int' );



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
