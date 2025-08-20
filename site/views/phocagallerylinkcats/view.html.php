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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
jimport( 'joomla.application.component.view' );
phocagalleryimport('phocagallery.render.renderadminviews');
class phocaGalleryViewphocaGalleryLinkCats extends HtmlView
{

	protected $r;
	protected $t;
	protected $categoriesoutput;

	function display($tpl = null) {
		$app	= Factory::getApplication();
		$this->r = new PhocaGalleryRenderAdminViews();
		$this->t = PhocaGalleryUtils::setVars('link');

		//Frontend Changes
		$tUri = '';
		if (!$app->isClient('administrator')) {
			$tUri = Uri::base();
			phocagalleryimport('phocagallery.render.renderadmin');
		}


		$editor    = $app->getInput()->getCmd('editor', '');
		if (!empty($editor)) {
			$this->document->addScriptOptions('xtd-phocagallery', array('editor' => $editor));
		}


		HTMLHelper::_('jquery.framework', false);
		HTMLHelper::stylesheet( 'media/com_phocagallery/css/administrator/phocagallery.css' );
		HTMLHelper::stylesheet( 'media/plg_editors-xtd_phocagallery/css/phocagallery.css' );
		$eName				= $app->getInput()->getCmd('editor', '');

		$this->t['ename']		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
		$this->t['backlink']	= $tUri.'index.php?option=com_phocagallery&amp;view=phocagallerylinks&amp;tmpl=component&amp;editor='.$this->t['ename'];


		// Category Tree
		$db = Factory::getDBO();
		$query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
		. ' FROM #__phocagallery_categories AS a'
	//	. ' WHERE a.published = 1' You can hide not published and not authorized categories too
	//	. ' AND a.approved = 1'
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$categories = $db->loadObjectList();

		$tree = array();
		$text = '';
		$tree = PhocaGalleryCategoryhtml::CategoryTreeOption($categories, $tree, 0, $text, -1);
		//-----------------------------------------------------------------------

		// Multiple
		$ctrl	= 'hidecategories';
		$attribs	= ' ';
		$attribs	.= ' size="5"';
		//$attribs	.= 'class="'.$v.'"';
		$attribs	.= ' class="form-control"';
		$attribs	.= ' multiple="multiple"';
		$ctrl		.= '';
		//$value		= implode( '|', )

		$this->categoriesoutput = HTMLHelper::_('select.genericlist', $tree, $ctrl, $attribs, 'value', 'text', 0, 'hidecategories' );



		parent::display($tpl);
	}
}
?>
