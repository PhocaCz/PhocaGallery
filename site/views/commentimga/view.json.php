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
use Joomla\Filesystem\File;
use Joomla\CMS\Uri\Uri;
jimport( 'joomla.application.component.view');
phocagalleryimport('phocagallery.comment.comment');
phocagalleryimport('phocagallery.comment.commentimage');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

class PhocaGalleryViewCommentImgA extends HtmlView
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
		$params	= $app->getParams();


		$commentValue	= $app->getInput()->get( 'commentValue', '',  'string'  );
		$commentId 		= $app->getInput()->get( 'commentId', 0,  'int'  );// ID of File
		$format 		= $app->getInput()->get( 'format', '',  'string'  );
		$task 			= $app->getInput()->get( 'task', '',  'string'  );
		$view 			= $app->getInput()->get( 'view', '',  'string'  );


		$paramsC 		= ComponentHelper::getParams('com_phocagallery');
		$param['display_comment_img'] = $paramsC->get( 'display_comment_img', 0 );


		if ($task == 'refreshcomment' && ((int)$param['display_comment_img'] == 2 || (int)$param['display_comment_img'] == 3)) {

			$user 		= Factory::getUser();
			//$view 		= J Request::get Var( 'view', '', 'get', '', J REQUEST_NOTRIM  );
			//$Itemid		= J Request::get Var( 'Itemid', 0, '', 'int');

			$neededAccessLevels	= PhocaGalleryAccess::getNeededAccessLevels();
			$access				= PhocaGalleryAccess::isAccess($user->getAuthorisedViewLevels(), $neededAccessLevels);


			$post['imgid'] 		= (int)$commentId;
			$post['userid']		= $user->id;
			$post['comment']	= strip_tags($commentValue);


			if ($format != 'json') {
				$msg = Text::_('COM_PHOCAGALLERY_ERROR_WRONG_COMMENT') ;
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


			$checkUserComment	= PhocaGalleryCommentImage::checkUserComment( $post['imgid'], $post['userid'] );

			// User has already commented this category
			if ($checkUserComment) {
				$msg = Text::_('COM_PHOCAGALLERY_COMMENT_ALREADY_SUBMITTED');
				$response = array(
					'status' => '0',
					'error' => '',
					'message' => $msg);
				echo json_encode($response);
				return;
			} else {

				if ($access > 0 && $user->id > 0) {
					if(!$model->comment($post)) {
						$msg = Text::_('COM_PHOCAGALLERY_ERROR_COMMENTING_IMAGE');
						$response = array(
						'status' => '0',
						'error' => $msg);
						echo json_encode($response);
						return;
					} else {

						$o = '<div class="pg-cv-comment-img-box-item">';
						$o .= '<div class="pg-cv-comment-img-box-avatar">';
						$avatar 			= PhocaGalleryCommentImage::getUserAvatar($user->id);
						$this->t['path'] = PhocaGalleryPath::getPath();
						$img = '<div style="width: 20px; height: 20px;">&nbsp;</div>';
						if (isset($avatar->avatar) && $avatar->avatar != '') {
							$pathAvatarAbs	= $this->t['path']->avatar_abs  .'thumbs/phoca_thumb_s_'. $avatar->avatar;
							$pathAvatarRel	= $this->t['path']->avatar_rel . 'thumbs/phoca_thumb_s_'. $avatar->avatar;
							if (PhocaGalleryFile::exists($pathAvatarAbs)){
								$avSize = getimagesize($pathAvatarAbs);
								$avRatio = $avSize[0]/$avSize[1];
								$avHeight = 20;
								$avWidth = 20 * $avRatio;
								$img = '<img src="'.Uri::base().'/'.$pathAvatarRel.'" width="'.$avWidth.'" height="'.$avHeight.'" alt="" />';
							}
						}
						$o .= $img;
						$o .= '</div>';
						$o .= '<div class="pg-cv-comment-img-box-comment">'.$user->name.': '.$post['comment'].'</div>';
						$o .= '<div style="clear:both"></div>';
						$o .= '</div>';


						$msg = $o . '<br />' . Text::_('COM_PHOCAGALLERY_SUCCESS_COMMENT_SUBMIT');
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
