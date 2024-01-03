<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.application.component.view' );
phocagalleryimport( 'phocagallery.render.renderinfo' );

class PhocaGalleryCpViewPhocaGallerycp extends HtmlView
{
	protected $t;
	protected $r;

	public function display($tpl = null) {

		$this->t	= PhocaGalleryUtils::setVars('cp');
		$this->r	= new PhocaGalleryRenderAdminview();

		$i = ' icon-';
		$d = 'duotone ';


		$this->views= array(
		'imgs'		=> array($this->t['l'] . '_IMAGES', $d.$i.'pictures', '#dd5500'),
		'cs'		=> array($this->t['l'] . '_CATEGORIES', $d.$i.'folder-open', '#da7400'),
		't'			=> array($this->t['l'] . '_THEMES', $d.$i.'modules', '#cd76cc'),
		'ra'		=> array($this->t['l'] . '_CATEGORY_RATING', $i.'star-empty', '#ffd460'),
		'raimg'		=> array($this->t['l'] . '_IMAGE_RATING', $i.'star-empty', '#f5b300'),
		'cos'		=> array($this->t['l'] . '_CATEGORY_COMMENTS', $d.$i.'comment', '#399ed0'),
		'coimgs'	=> array($this->t['l'] . '_IMAGE_COMMENTS', $d.$i.'comment', '#1e6080'),
		'users'		=> array($this->t['l'] . '_USERS', $d.$i.'users', '#7faa7f'),
		///'fbs'		=> $this->t['l'] . '_FB',
		'tags'		=> array($this->t['l'] . '_TAGS', $d.$i.'tag-double', '#CC0033'),
		'efs'		=> array($this->t['l'] . '_STYLES', $i.'styles', '#9900CC'),
		'in'		=> array($this->t['l'] . '_INFO', $d.$i.'info-circle', '#3378cc')
		);



		HTMLHelper::stylesheet( $this->t['s'] );
		////JHtml::_('behavior.tooltip');
		$this->t['version'] = PhocaGalleryRenderInfo::getPhocaVersion();

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/phocagallerycp.php';

		$state	= $this->get('State');
		$canDo	= PhocaGalleryCpHelper::getActions();
		ToolbarHelper::title( Text::_( 'COM_PHOCAGALLERY_PG_CONTROL_PANEL' ), 'home-2 cpanel' );

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocagallery" class="btn btn-primary btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAGALLERY_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAGALLERY_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_phocagallery');
			ToolbarHelper::divider();
		}

		ToolbarHelper::help( 'screen.phocagallery', true );
	}
}
?>
