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
use Joomla\CMS\Factory;
jimport( 'joomla.application.component.view' );
phocagalleryimport( 'phocagallery.facebook.fb' );
phocagalleryimport( 'phocagallery.facebook.fbsystem' );

class PhocaGalleryCpViewphocaGalleryFbA extends HtmlView
{
	protected $r;
	protected $t;

	function display($tpl = null) {
		$app		= Factory::getApplication();
		$document	= Factory::getDocument();
		$uri		= \Joomla\CMS\Uri\Uri::getInstance();

		$this->t	= PhocaGalleryUtils::setVars('fba');
		$this->r	= new PhocaGalleryRenderAdminview();


		$this->field	= Factory::getApplication()->getInput()->get('field');
		$this->fce 		= 'phocaSelectFbAlbum_'.$this->field;

		//$eName	= JFactory::getApplication()->getInput()->get('editor');
		//$eName	= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );



		$uid	= Factory::getApplication()->getInput()->get('uid', 0, '', 'int');

		$db = Factory::getDBO();
		$query = 'SELECT a.*'
		. ' FROM #__phocagallery_fb_users AS a'
		. ' WHERE a.published = 1'
		. ' AND a.id = '.(int)$uid
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$user = $db->loadObject();

		if(!isset($user->uid)) {
			$this->userInfo = 0;

		} else {

			$session 	= PhocaGalleryFbSystem::setSessionData($user);
			$albumN		= PhocaGalleryFb::getFbAlbums($user->appid, $user->fanpageid,  $user->appsid, $session);

			$albumR = array();
			$i = 0;
			if (!empty($albumN)) {
				foreach($albumN as $k => $v) {
					if (!empty($v)) {
						foreach($v as $k2 => $v2) {
							$albumR[$i]['id'] 	= $v2['id'];
							$albumR[$i]['name'] = $v2['name'];
							$i++;
						}
					}
				}
			}
			$this->albums = $albumR;

			$this->userInfo = 1;
		}

		//$this->assignRef('tmpl',	$t);
		parent::display($tpl);

	}
}
?>
