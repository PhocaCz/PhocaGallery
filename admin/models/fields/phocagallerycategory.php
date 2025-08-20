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
defined('_JEXEC') or die();
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

if (! class_exists('PhocaGalleryLoader')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocagallery/libraries/loader.php');
}
phocagalleryimport('phocagallery.render.renderadmin');
phocagalleryimport('phocagallery.html.categoryhtml');

Factory::getApplication()->getLanguage()->load('com_phocagallery');

class JFormFieldPhocaGalleryCategory extends FormField
{
	protected $type 		= 'PhocaGalleryCategory';
	protected $layout   = 'phocagallery.form.field.category';

	protected function getRenderer($layoutId = 'default')
	{
		// Make field usable outside of Phoca Cart component
		$renderer = parent::getRenderer($layoutId);
		$renderer->addIncludePath(JPATH_ADMINISTRATOR . '/components/com_phocagallery/layouts');
		return $renderer;
	}

	private function buildCategoryTree(array &$options, array $categories, string $treeTitle, array $typeFilter, array $langFilter, array $omitIds): void {
    foreach ($categories as $category) {
      if ($typeFilter && !in_array($category->type, $typeFilter)) continue;
      if ($langFilter && !in_array($category->language, $langFilter)) continue;
      if ($omitIds && in_array($category->id, $omitIds)) continue;

      $title = ($treeTitle ? $treeTitle . ' - ' : '') . $category->title;
      $options[] = (object)[
        'text' => $title . ($category->language === '*' ? '' : ' (' . $category->language . ')'),
        'value' => $category->id,
      ];
      if ($category->children)
        $this->buildCategoryTree($options, $category->children, $title, $typeFilter, $langFilter, $omitIds);
    }
  }

	protected function getInput() {

		$db 			= Factory::getDBO();
		$multiple		= (string)$this->element['multiple'] == 'true';
		$typeMethod		= $this->element['typemethod'];

       	switch($this->element['categorytype']) {
		  case 1:
			$typeFilter = [0, 1];
			break;
		  case 2:
			$typeFilter = [0, 2];
			break;
		  case 0:
		  default:
			$typeFilter = [];
			break;
		}

		if ($this->element['language']) {
		  $langFilter = explode(',', $this->element['language']);
		} elseif ($this->form->getValue('language', 'filter')) {
		  $langFilter = [$this->form->getValue('language', 'filter')];
		} else {
		  $langFilter = [];
		}

		 // TO DO - check for other views than category edit
		$omitIds = [];
		switch (Factory::getApplication()->getInput()->get('view')) {
		  case 'phocagallerycategory':
			if ($this->form->getValue('id') > 0)
			  $omitIds[] = $this->form->getValue('id');
			break;
		}

		$db->setQuery('SELECT a.*, null AS children FROM #__phocagallery_categories AS a ORDER BY a.ordering, a.id');
		$categories = $db->loadObjectList('id') ?? [];

		array_walk($categories, function ($category) use ($categories) {
			if ($category->parent_id) {
				if ($categories[$category->parent_id]->children === null) {
					$categories[$category->parent_id]->children = [];
				}
				$categories[$category->parent_id]->children[] = $category;
			}
		});

		$rootCategories = array_filter($categories, function($category) {
		  return !$category->parent_id;
		});

		$options = [];
		if ($multiple) {
		  if ($typeMethod == 'allnone') {
			$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_PHOCAGALLERY_NONE'), 'value', 'text');
			$options[] = HTMLHelper::_('select.option', '-1', Text::_('COM_PHOCAGALLERY_ALL'), 'value', 'text');
		  }
		} else {
		  // in filter we need zero value for canceling the filter
		  if ($typeMethod == 'filter') {
			$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_PHOCAGALLERY_SELECT_CATEGORY') . ' -', 'value', 'text');
		  } else {
			$options[] = HTMLHelper::_('select.option', '0', '- '.Text::_('COM_PHOCAGALLERY_SELECT_CATEGORY').' -', 'value', 'text');
		  }
		}

		$this->buildCategoryTree($options, $rootCategories, '', $typeFilter, $langFilter, $omitIds);

		$data = $this->getLayoutData();
		$data['options'] = $options;

		//if (!empty($activeCats)) {
		//	$data['value'] = $activeCats;
		//} else {
			$data['value'] = $this->value;
		//}

		$data['refreshPage']    = (bool)$this->element['refresh-enabled'];
		$data['refreshCatId']   = (string)$this->element['refresh-cat-id'];
		$data['refreshSection'] = (string)$this->element['refresh-section'];
		$data['hasCustomFields']= !empty(FieldsHelper::getFields('com_phocagallery.phocagalleryitem'));



		$document					= Factory::getDocument();
		$document->addCustomTag('<script type="text/javascript">
function changeCatid() {
	var catid = document.getElementById(\'jform_catid\').value;
	var href = document.getElementById(\'pgselectytb\').getAttribute(\'data-url\');
    href = href ? href.substring(0, href.lastIndexOf("&")) : \'\';
    href += \'&catid=\' + catid;
    document.getElementById(\'pgselectytb\').setAttribute(\'data-url\', href);
}
</script>');


		return $this->getRenderer($this->layout)->render($data);

	}
}
?>
