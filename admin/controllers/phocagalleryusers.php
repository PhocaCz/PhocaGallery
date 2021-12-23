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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
jimport('joomla.application.component.controlleradmin');


class PhocaGalleryCpControllerPhocaGalleryUsers extends AdminController
{
	protected	$option 		= 'com_phocagallery';

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('disapprove',	'approve');

	}

	public function &getModel($name = 'PhocaGalleryUser', $prefix = 'PhocaGalleryCpModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	function approve()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid	= Factory::getApplication()->input->get('cid', array(), '', 'array');
		$data	= array('approve' => 1, 'disapprove' => 0);
		$task 	= $this->getTask();
		$value	= \Joomla\Utilities\ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid)) {
			throw new Exception(Text::_($this->text_prefix.'_NO_ITEM_SELECTED'), 500);
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			\Joomla\Utilities\ArrayHelper::toInteger($cid);

			// Publish the items.

			if (!$model->approve($cid, $value)) {
				throw new Exception($model->getError(), 500);
			} else {
				if ($value == 1) {
					$ntext = $this->text_prefix.'_N_ITEMS_APPROVED';
				} else if ($value == 0) {
					$ntext = $this->text_prefix.'_N_ITEMS_DISAPPROVED';
				}
				$this->setMessage(Text::plural($ntext, count($cid)));
			}
		}

		$this->setRedirect(Route::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}
	/* TO DO - get the same rules as approve has */
	function approveall() {

		$model = $this->getModel('phocagalleryuser');
		if(!$model->approveall()) {
			$msg = Text::_( 'COM_PHOCAGALLERY_ERROR_APPROVE_ALL' );
		} else {
			$msg = Text::_( 'COM_PHOCAGALLERY_SUCCESS_APPROVE_ALL' );
		}

		$this->setRedirect( 'index.php?option=com_phocagallery&view=phocagalleryusers' , $msg);
	}

	public function saveOrderAjax() {
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
		\Joomla\Utilities\ArrayHelper::toInteger($pks);
		\Joomla\Utilities\ArrayHelper::toInteger($order);
		$model = $this->getModel();
		$return = $model->saveorder($pks, $order);
		if ($return) { echo "1";}
		Factory::getApplication()->close();
	}
}
?>
