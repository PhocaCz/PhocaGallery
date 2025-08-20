<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
jimport( 'joomla.application.component.view');
phocagalleryimport( 'phocagallery.rate.rateimage');

class PhocaGalleryViewRatingImgA extends HtmlView
{

	function display($tpl = null){

		if (!Session::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => Text::_('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		$app	= Factory::getApplication();
		$params			= $app->getParams();


		$ratingVote 	= $app->getInput()->get( 'ratingVote', 0,  'int'  );
		$ratingId 		= $app->getInput()->get( 'ratingId', 0,  'int'  );// ID of File
		$format 		= $app->getInput()->get( 'format', '', 'string'  );
		$task 			= $app->getInput()->get( 'task', '',  'string'  );
		$view 			= $app->getInput()->get( 'view', '',  'string'  );
		$small			= $app->getInput()->get( 'small', 1,  'string'  );//small or large rating icons


		$paramsC 		= ComponentHelper::getParams('com_phocagallery');
		$param['display_rating_img'] = $paramsC->get( 'display_rating_img', 0 );

		// Check if rating is enabled - if not then user should not be able to rate or to see updated reating



		if ($task == 'refreshrate' && (int)$param['display_rating_img'] == 2) {
			$ratingOutput 		= PhocaGalleryRateImage::renderRateImg((int)$ratingId, $param['display_rating_img'], $small, true);// ID of
			$response = array(
					'status' => '1',
					'message' => $ratingOutput );
				echo json_encode($response);
				return;
			//return $ratingOutput;

		} else if ($task == 'rate') {

			$user 		= Factory::getUser();
			//$view 		= J Request::get Var( 'view', '', 'get', '', J REQUEST_NOTRIM  );
			//$Itemid		= J Request::get Var( 'Itemid', 0, '', 'int');

			$neededAccessLevels		= PhocaGalleryAccess::getNeededAccessLevels();
			$access					= PhocaGalleryAccess::isAccess($user->getAuthorisedViewLevels(), $neededAccessLevels);


			$post['imgid'] 	= (int)$ratingId;
			$post['userid']		= $user->id;
			$post['rating']		= (int)$ratingVote;


			if ($format != 'json') {
				$msg = Text::_('COM_PHOCAGALLERY_ERROR_WRONG_RATING') ;
				$response = array(
					'status' => '0',
					'error' => $msg);
				echo json_encode($response);
				return;
			}

			if ((int)$post['imgid'] < 1) {
				$msg = Text::_('COM_PHOCAGALLERY_ERROR_IMAGE_NOT_EXISTS');
				$response = array(
					'status' => '0',
					'error' => $msg);
				echo json_encode($response);
				return;
			}

			$model = $this->getModel();

			$checkUserVote	= PhocaGalleryRateImage::checkUserVote( $post['imgid'], $post['userid'] );

			// User has already rated this category
			if ($checkUserVote) {
				$msg = Text::_('COM_PHOCAGALLERY_ALREADY_RATE_IMG');
				$response = array(
					'status' => '0',
					'error' => '',
					'message' => $msg);
				echo json_encode($response);
				return;
			} else {
				if ((int)$post['rating']  < 1 || (int)$post['rating'] > 5) {

					$msg = Text::_('COM_PHOCAGALLERY_ERROR_WRONG_RATING');
					$response = array(
					'status' => '0',
					'error' => $msg);
					echo json_encode($response);
					return;
				}

				if ($access > 0 && $user->id > 0) {
					if(!$model->rate($post)) {
						$msg = Text::_('COM_PHOCAGALLERY_ERROR_RATING_IMG');
						$response = array(
						'status' => '0',
						'error' => $msg);
						echo json_encode($response);
						return;
					} else {
						$msg = Text::_('COM_PHOCAGALLERY_SUCCESS_RATING_IMAGE');
						$msg = '';// No changing of the box, no message, only change the rating
						$response = array(
						'status' => '1',
						'error' => '',
						'message' => $msg);
						echo json_encode($response);
						return;
					}
				} else {
					$msg = Text::_('COM_PHOCAGALLERY_NOT_AUTHORISED_ACTION');
						$response = array(
						'status' => '0',
						'error' => $msg);
						echo json_encode($response);
						return;
				}
			}
		} else {
			$msg = Text::_('COM_PHOCAGALLERY_NOT_AUTHORISED_ACTION');
			$response = array(
			'status' => '0',
			'error' => $msg);
			echo json_encode($response);
			return;
		}
	}
}
?>
