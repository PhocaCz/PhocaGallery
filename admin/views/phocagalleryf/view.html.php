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
use Joomla\CMS\Object\CMSObject;
jimport( 'joomla.application.component.view');

class PhocaGalleryCpViewPhocagalleryF extends HtmlView
{
	protected $field;
	protected $fce;
	protected $t;
	protected $r;

	public function display($tpl = null) {

		$params = ComponentHelper::getParams( 'com_phocagallery' );
		$app 	= Factory::getApplication();
		$app->allowCache(false);

		$this->t	= PhocaGalleryUtils::setVars('f');
		$this->r	= new PhocaGalleryRenderAdminview();


		$document	= Factory::getDocument();


		$path 			= PhocaGalleryPath::getPath();

		$this->field	= Factory::getApplication()->getInput()->get('field');
		$this->fce 		= 'phocaSelectFolder_'.$this->field;

		/*$this->assignRef('session', JFactory::getSession());
		$this->assign('path_orig_rel', $path->image_rel);
		$this->assignRef('folders', $this->get('folders'));
		$this->assignRef('state', $this->get('state'));*/

		$this->t['session'] = Factory::getSession();
		$this->t['path_orig_rel'] = $path->image_rel;
		$this->t['folders'] = $this->get('folders');
		$this->t['state'] = $this->get('state');

		parent::display($tpl);
	}

	protected function setFolder($index = 0) {
		if (isset($this->t['folders'][$index])) {
			$this->_tmp_folder = $this->t['folders'][$index];
		} else {
			$this->_tmp_folder = new CMSObject;
		}
	}
}
?>
