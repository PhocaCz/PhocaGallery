<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
phocagalleryimport('phocagallery.access.access');
phocagalleryimport('phocagallery.rate.rateimage');
class PhocaGalleryControllerDetail extends PhocaGalleryController
{

	function display($cachable = false, $urlparams = false) {
		if ( ! Factory::getApplication()->input->get('view') ) {
			Factory::getApplication()->input->set('view', 'detail' );
		}

		parent::display($cachable, $urlparams);
    }

	function rate() {
		$app	= Factory::getApplication();
		$params			= $app->getParams();
		$detailWindow	= $params->get( 'detail_window', 0 );

		$user 		= Factory::getUser();
		$view 		= $this->input->get( 'view', '', 'string'  );
		$imgid 		= $this->input->get( 'id', '', 'string'  );
		$catid 		= $this->input->get( 'catid', '', 'string'  );
		$rating		= $this->input->get( 'rating', '', 'string' );
		$Itemid		= $this->input->get( 'Itemid', 0, 'int');

		$neededAccessLevels	= PhocaGalleryAccess::getNeededAccessLevels();
		$access				= PhocaGalleryAccess::isAccess($user->getAuthorisedViewLevels(), $neededAccessLevels);

		if ($detailWindow == 7) {
			$tCom = '';
		} else {
			$tCom = '&tmpl=component';
		}

		$post['imgid'] 		= (int)$imgid;
		$post['userid']		= $user->id;
		$post['rating']		= (int)$rating;

		$imgIdAlias 	= $imgid;
		$catIdAlias 	= $catid;		//Itemid
		if ($view != 'detail') {
			$this->setRedirect( Route::_('index.php?option=com_phocagallery', false) );
		}

		$model = $this->getModel('detail');

		$checkUserVote	= PhocaGalleryRateImage::checkUserVote( $post['imgid'], $post['userid'] );

		// User has already rated this category

		if ($checkUserVote) {
			$msg = Text::_('COM_PHOCAGALLERY_RATING_IMAGE_ALREADY_RATED');
		} else {
			if ((int)$post['rating']  < 1 || (int)$post['rating'] > 5) {

				$app->redirect( Route::_('index.php?option=com_phocagallery', false)  );
				exit;
			}

			if ($access > 0 && $user->id > 0) {
				if(!$model->rate($post)) {
				$msg = Text::_('COM_PHOCAGALLERY_ERROR_RATING_IMAGE');
				} else {
				$msg = Text::_('COM_PHOCAGALLERY_SUCCESS_RATING_IMAGE');
				// Features added by Bernard Gilly - alphaplug.com
				// load external plugins
				//$dispatcher = JDispatcher::getInstance();
				PluginHelper::importPlugin('phocagallery');
				$results = Factory::getApplication()->triggerEvent('onVoteImage', array($imgid, $rating, $user->id ) );
				}
			} else {
				$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_NOT_AUTHORISED_ACTION'), 'error');
				$app->redirect(Route::_('index.php?option=com_users&view=login', false));
				exit;
			}
		}
		// Do not display System Message in Detail Window as there are no scrollbars, so other items will be not displayed
		// we send infor about already rated via get and this get will be worked in view (detail - default.php) - vote=1
		$msg = '';

		//$this->setRedirect( JRoute::_('index.php?option=com_phocagallery&view=detail&catid='.$catIdAlias.'&id='.$imgIdAlias.$tCom.'&vote=1&Itemid='. $Itemid, false), $msg );
		$this->setRedirect( Route::_('index.php?option=com_phocagallery&view=detail&catid='.$catIdAlias.'&id='.$imgIdAlias.$tCom.'&vote=1&Itemid='. $Itemid, false) );
	}

	function comment() {

		Session::checkToken() or jexit( 'Invalid Token' );
		phocagalleryimport('phocagallery.comment.comment');
		phocagalleryimport('phocagallery.comment.commentimage');
		$app				= Factory::getApplication();
		$user 				= Factory::getUser();
		$view 				= $this->input->get('view', '', 'string');
		$catid 				= $this->input->get('catid', '', 'string');
		$id 				= $this->input->get('id', '', 'string' );
		$post['title']		= $this->input->get('phocagallerycommentstitle', '', 'string');
		$post['comment']	= $this->input->get('phocagallerycommentseditor', '', 'string');
		$Itemid				= $this->input->get('Itemid', 0,  'int');
		$limitStart			= $this->input->get('limitstart', 0,  'int');
		$tab				= $this->input->get('tab', 0,  'int' );
		$neededAccessLevels	= PhocaGalleryAccess::getNeededAccessLevels();
		$access				= PhocaGalleryAccess::isAccess($user->getAuthorisedViewLevels(), $neededAccessLevels);
		$params				= $app->getParams();
		$detailWindow		= $params->get( 'detail_window', 0 );
		$maxCommentChar		= $params->get( 'max_comment_char', 1000 );
		$displayCommentNoPopup	= $params->get( 'display_comment_nopup', 0);
		// Maximum of character, they will be saved in database
		$post['comment']	= substr($post['comment'], 0, (int)$maxCommentChar);

		if ($detailWindow == 7 || $displayCommentNoPopup == 1) {
			$tCom = '';
		} else {
			$tCom = '&tmpl=component';
		}

		// Close Tags
		$post['comment'] = PhocaGalleryComment::closeTags($post['comment'], '[u]', '[/u]');
		$post['comment'] = PhocaGalleryComment::closeTags($post['comment'], '[i]', '[/i]');
		$post['comment'] = PhocaGalleryComment::closeTags($post['comment'], '[b]', '[/b]');



		$post['imgid'] 	= (int)$id;
		$post['userid']	= $user->id;

		$catidAlias 	= $catid;
		$imgidAlias 	= $id;
		if ($view != 'comment') {
			$this->setRedirect( Route::_('index.php?option=com_phocagallery', false) );
		}

		$model = $this->getModel('detail');

		$checkUserComment	= PhocaGalleryCommentImage::checkUserComment( $post['imgid'], $post['userid'] );

		// User has already submitted a comment
		if ($checkUserComment) {
			$msg = Text::_('COM_PHOCAGALLERY_COMMENT_ALREADY_SUBMITTED');
		} else {
			// If javascript will not protect the empty form
			$msg 		= '';
			$emptyForm	= 0;
			if ($post['title'] == '') {
				$msg .= Text::_('COM_PHOCAGALLERY_ERROR_COMMENT_TITLE') . ' ';
				$emtyForm = 1;
			}
			if ($post['comment'] == '') {
				$msg .= Text::_('COM_PHOCAGALLERY_ERROR_COMMENT_COMMENT');
				$emtyForm = 1;
			}
			if ($emptyForm == 0) {
				if ($access > 0 && $user->id > 0) {
					if(!$model->comment($post)) {
					$msg = Text::_('COM_PHOCAGALLERY_ERROR_COMMENT_SUBMITTING');
					} else {
					$msg = Text::_('COM_PHOCAGALLERY_SUCCESS_COMMENT_SUBMIT');
					// Features by Bernard Gilly - alphaplug.com
					// load external plugins
					//$dispatcher = JDispatcher::getInstance();
					PluginHelper::importPlugin('phocagallery');
					$results = Factory::getApplication()->triggerEvent('onCommentImage', array($id, $catid, $post['title'], $post['comment'], $user->id ) );
					}
				} else {
					$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_NOT_AUTHORISED_ACTION'));
					$app->redirect(Route::_('index.php?option=com_users&view=login', false));
					exit;
				}
			}
		}
		$app->enqueueMessage($msg);
		$this->setRedirect( Route::_('index.php?option=com_phocagallery&view=detail&catid='.$catidAlias.'&id='.$imgidAlias.$tCom.'&Itemid='. $Itemid, false));
	}
}
?>
