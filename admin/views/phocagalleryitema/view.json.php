<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
jimport( 'joomla.application.component.view');

class PhocaGalleryCpViewPhocaGalleryItemA extends HtmlView
{
	function display($tpl = null){

		if (!Session::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => '<span class="ph-result-txt ph-error-txt">' . Text::_('JINVALID_TOKEN') . '</span>');
			echo json_encode($response);
			return;
		}

		$app		= Factory::getApplication();
		$q			= $app->input->get( 'q', '', 'string'  );
		$id			= $app->input->get( 'item_id', '', 'int'  );

		if (isset($q) && $q != '') {
			$db		= Factory::getDbo();
			$query	= $db->getQuery(true);


			$query->select('a.id as id, a.title as title, a.filename as filename, a.exts as exts');
			$query->from('`#__phocagallery` AS a');
			$query->select('c.title AS category_title, c.id AS category_id');
			$query->join('LEFT', '#__phocagallery_categories AS c ON c.id = a.catid');


			$search = $db->Quote('%'.$db->escape($q, true).'%');
			if ((int)$id > 0) {
				// It is not related function so we don't limit ID here, used when we ask in product edit other products
				//	$query->where('( a.id <> '.(int)$id.')');
			}
			$query->where('( a.title LIKE '.$search.')');
			//$query->group($db->escape('a.id'));
            $query->group($db->escape('a.id, a.title, a.filename, a.exts, c.id, c.title'));
			$query->order($db->escape('a.ordering'));

			$db->setQuery($query);



			/*if (!$db->query()) {
				$response = array(
				'status' => '0',
				'error' => '<span class="ph-result-txt ph-error-txt">Database Error - Getting Selected Images</span>');
				echo json_encode($response);
				return;
			}*/

			try {
				$items 	= $db->loadObjectList();
			} catch (\RuntimeException $e) {
				$response = array(
					'status' => '0',
					'error' => '<span class="ph-result-txt ph-error-txt">Database Error - Getting Selected Images</span>');
				echo json_encode($response);
				return;
			}

			$itemsA	= array();
			if (!empty($items)) {
				foreach ($items as $k => $v) {
					$itemsA[$k]['id'] 		= $v->id;
					$itemsA[$k]['title'] 	= $v->title . ' ('.$v->category_title.')';

					if ($v->exts != '') {
						$itemsA[$k]['exts']= $v->exts;
					} else if ($v->filename != '') {
						$thumb = PhocaGalleryFileThumbnail::getOrCreateThumbnail($v->filename, '', 0, 0, 0, 0);
						if ($thumb['thumb_name_s_no_rel'] != '') {
							$itemsA[$k]['image']= $thumb['thumb_name_s_no_rel'];
						}
					}
				}
			}

			$response = array(
			'status'	=> '1',
			'items'		=> $itemsA);
			echo json_encode($response);
			return;
		}

		$response = array(
		'status'	=> '1',
		'items'		=> array());
		echo json_encode($response);
		return;
	}
}
?>
