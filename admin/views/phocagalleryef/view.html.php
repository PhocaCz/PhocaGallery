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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
jimport('joomla.application.component.view');


class PhocaGalleryCpViewPhocaGalleryEf extends HtmlView
{
	protected $item;
	protected $form;
	protected $state;
	protected $t;
	protected $r;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{


		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->t['ftp']		= ClientHelper::setCredentialsFromRequest('ftp');
		$this->t		= new StdClass;
		$model 			= $this->getModel();

		$this->t	= PhocaGalleryUtils::setVars('ef');
		$this->r	= new PhocaGalleryRenderAdminview();

		// Set CSS for codemirror
		Factory::getApplication()->setUserState('editor.source.syntax', 'css');


		// New or edit
		if (!$this->form->getValue('id') || $this->form->getValue('id') == 0) {
			$this->form->setValue('source', null, '');
			$this->form->setValue('type', null, 2);
			$this->t['suffixtype'] = Text::_('COM_PHOCAGALERY_WILL_BE_CREATED_FROM_TITLE');

		} else {
			$this->source	= $model->getSource($this->form->getValue('id'), $this->form->getValue('filename'), $this->form->getValue('type'));
			$this->form->setValue('source', null, $this->source->source);
			$this->t['suffixtype'] = '';
		}

		// Only help input form field - to display Main instead of 1 and Custom instead of 2
		if ($this->form->getValue('type') == 1) {
			$this->form->setValue('typeoutput', null, Text::_('COM_PHOCAGALLERY_MAIN_CSS'));
		} else {
			$this->form->setValue('typeoutput', null, Text::_('COM_PHOCAGALLERY_CUSTOM_CSS'));
		}



		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagalleryefs.php';
		Factory::getApplication()->getInput()->set('hidemainmenu', true);
		$bar 		= Toolbar::getInstance('toolbar');
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaGalleryEfsHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		$paramsC 	= ComponentHelper::getParams('com_phocagallery');

		$text = $isNew ? Text::_( 'COM_PHOCAGALLERY_NEW' ) : Text::_('COM_PHOCAGALLERY_EDIT');
		ToolbarHelper::title(   Text::_( 'COM_PHOCAGALLERY_STYLE' ).': <small><small>[ ' . $text.' ]</small></small>' , 'eye');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply('phocagalleryef.apply', 'JToolbar_APPLY');
			ToolbarHelper::save('phocagalleryef.save', 'JToolbar_SAVE');
		}

		ToolbarHelper::cancel('phocagalleryef.cancel', 'JToolbar_CLOSE');
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}

}
?>
