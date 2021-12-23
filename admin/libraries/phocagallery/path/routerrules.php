<?php



defined('_JEXEC') or die();
use Joomla\CMS\Component\Router\Rules\MenuRules;


use Joomla\Registry\Registry;

class PhocaGalleryRouterrules extends MenuRules
{
	public function preprocess(&$query)
	{

		parent::preprocess($query);

	}

	protected function buildLookup($language = '*')
	{
		parent::buildLookup($language);

	}

}
