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
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
jimport( 'joomla.application.component.view' );

class phocaGalleryCpViewphocaGalleryLinks extends HtmlView
{

	protected $r;
	protected $t;

	function display($tpl = null) {

		$this->r = new PhocaGalleryRenderAdminViews();
		$this->t = PhocaGalleryUtils::setVars('link');

		$app	= Factory::getApplication();


		//Frontend Changes
		$tUri = '';
		if (!$app->isClient('administrator')) {
			$tUri = Uri::base();
		}

		$editor    = $app->getInput()->getCmd('editor', '');
		if (!empty($editor)) {
			$this->document->addScriptOptions('xtd-phocagallery', array('editor' => $editor));
		}

		$eName	= Factory::getApplication()->getInput()->get('editor');
		$eName	= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );

		HTMLHelper::_('jquery.framework', false);
		HTMLHelper::stylesheet( 'media/com_phocagallery/css/administrator/phocagallery.css' );
		HTMLHelper::stylesheet( 'media/plg_editors-xtd_phocagallery/css/phocagallery.css' );

		$this->t['categories']		= $tUri.'index.php?option=com_phocagallery&amp;view=phocagallerylinkcats&amp;tmpl=component&amp;editor='.$eName;
		//$this->t['COM_PHOCAGALLERY_CATEGORY']		= 'index.php?option=com_phocagallery&amp;view=phocagallerylinkcat&amp;tmpl=component&amp;editor='.$eName;
		$this->t['images']			= $tUri.'index.php?option=com_phocagallery&amp;view=phocagallerylinkimg&amp;type=2&amp;tmpl=component&amp;editor='.$eName;
		$this->t['image']			= $tUri.'index.php?option=com_phocagallery&amp;view=phocagallerylinkimg&amp;type=1&amp;tmpl=component&amp;editor='.$eName;
		$this->t['imagesmasonry']	= $tUri.'index.php?option=com_phocagallery&amp;view=phocagallerylinkimg&amp;type=5&amp;tmpl=component&amp;editor='.$eName;
		//$this->t['switchimage']	= $tUri.'index.php?option=com_phocagallery&amp;view=phocagallerylinkimg&amp;type=3&amp;tmpl=component&amp;editor='.$eName;
		//$this->t['slideshow']		= $tUri.'index.php?option=com_phocagallery&amp;view=phocagallerylinkimg&amp;type=4&amp;tmpl=component&amp;editor='.$eName;


		parent::display($tpl);
	}
}
?>
