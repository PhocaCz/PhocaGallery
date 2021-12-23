<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_newsfeeds
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Component\Router\Rules\RulesInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;



class PhocagalleryRouterRulesLegacy implements RulesInterface
{


	public function __construct($router)
	{
		$this->router = $router;
	}

	public function preprocess(&$query)
	{
	}


	public function build(&$query, &$segments)
	{


		// Get a menu item based on Itemid or currently active
		$params = ComponentHelper::getParams('com_phocagallery');
		$advanced = $params->get('sef_advanced_link', 0);

		if (empty($query['Itemid']))
		{
			$menuItem = $this->router->menu->getActive();
		}
		else
		{
			$menuItem = $this->router->menu->getItem($query['Itemid']);
		}

		$mView = empty($menuItem->query['view']) ? null : $menuItem->query['view'];
		$mId   = empty($menuItem->query['id']) ? null : $menuItem->query['id'];



		if (isset($query['view']))
		{
			$view = $query['view'];

			if (empty($menuItem) || $menuItem->component !== 'com_phocagallery' || empty($query['Itemid']))
			{
				$segments[] = $query['view'];
			}

			unset($query['view']);
		}

		// Are we dealing with a view that is attached to a menu item?
		if (isset($query['view'], $query['id']) && $mView == $query['view'] && $mId == (int) $query['id'])
		{
			unset($query['view'], $query['catid'], $query['id']);

			return;
		}



		if (isset($view) && ($view === 'category' || $view === 'detail'))
		{
			if ($mId != (int) $query['id'] || $mView != $view)
			{
				if ($view === 'detail' && isset($query['catid']))
				{
					$catid = $query['catid'];
				}
				elseif (isset($query['id']))
				{
					$catid = $query['id'];
				}

				$menuCatid = $mId;

				$category = PhocaGalleryCategory::getCategoryById($catid);

				if (isset($category->id)) {
                    $path = PhocaGalleryCategory::getPath(array(), (int)$category->id, $category->parent_id, $category->title, $category->alias);

                }
				if (!empty($path)) {


				    $path = array_reverse($path, true);

					$array = array();

					foreach ($path as $id)
					{
						if ((int) $id === (int) $menuCatid)
						{
							break;
						}

						if ($advanced)
						{
							list($tmp, $id) = explode(':', $id, 2);
						}

						$array[] = $id;
					}


					$segments = array_merge($segments, $array);
                }


				/*$categories = JCategories::getInstance('Newsfeeds');
				$category = $categories->get($catid);*/

				//$path = PhocaGalleryCategory::getPath(array(), (int)$catid);



				/*if ($category)
				{
					$path = $category->getPath();
					$path = array_reverse($path);

					$array = array();

					foreach ($path as $id)
					{
						if ((int) $id === (int) $menuCatid)
						{
							break;
						}

						if ($advanced)
						{
							list($tmp, $id) = explode(':', $id, 2);
						}

						$array[] = $id;
					}

					$segments = array_merge($segments, array_reverse($array));
				}*/

			/*	if ($view === 'category')
				{
					if ($advanced)
					{
						list($tmp, $id) = explode(':', $query['id'], 2);
					}
					else
					{
						$id = $query['id'];
					}

					$segments[] = $id;
				}*/

				if ($view === 'detail')
				{
					if ($advanced)
					{
						list($tmp, $id) = explode(':', $query['id'], 2);
					}
					else
					{
						$id = $query['id'];
					}

					$segments[] = $id;
				}
			}

			unset($query['id'], $query['catid']);
		}

		if (isset($query['layout']))
		{
			if (!empty($query['Itemid']) && isset($menuItem->query['layout']))
			{
				if ($query['layout'] == $menuItem->query['layout'])
				{
					unset($query['layout']);
				}
			}
			else
			{
				if ($query['layout'] === 'default')
				{
					unset($query['layout']);
				}
			}
		}

		$total = count($segments);

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}



	}


	public function parse(&$segments, &$vars)
	{
		$total = count($segments);


		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		// Get the active menu item.
		$item	= $this->router->menu->getActive();
		$params = ComponentHelper::getParams('com_phocagallery');
		$advanced = $params->get('sef_advanced_link', 0);

		// Count route segments
		$count = count($segments);



		// Standard routing for newsfeeds.
		if (!isset($item))
		{
			$vars['view'] = $segments[0];
			$vars['id']   = $segments[$count - 1];

			return;
		}

		// From the categories view, we can only jump to a category.
		$id = (isset($item->query['id']) && $item->query['id'] > 1) ? $item->query['id'] : '';
		//$categories = JCategories::getInstance('Newsfeeds')->get($id)->getChildren();
		$vars['catid'] = $id;
		$vars['id'] = $id;
		$found = 0;






		$categories = [];
		$categories[0] = new stdClass();
		$categories[0]->slug = '1:architecture';
		$categories[0]->alias = 'architecture';
		$categories[0]->id = '1';




		$newCategories = [];
		$newCategories[0] = new stdClass();
		$newCategories[0]->slug = '3:tri';
		$newCategories[0]->alias = 'tri';
		$newCategories[0]->id = '3';

		$newCategories[1] = new stdClass();
		$newCategories[1]->slug = '2:autumn';
		$newCategories[1]->alias = 'autumn';
		$newCategories[1]->id = '2';

		/*$newCategories[1] = new stdClass();
		$newCategories[1]->slug = '4:ctyri';
		$newCategories[1]->alias = 'ctyri';
		$newCategories[1]->id = '4';*/


		foreach ($segments as $segment) {
			foreach ($categories as $category) {
				if (($category->slug == $segment) || ($advanced && $category->alias == str_replace(':', '-', $segment))) {
					$vars['id'] = $category->id;
					$vars['view'] = 'calendar';
					$categories = $newCategories;
					$found = 1;


					break;
				}

			}

			if ($found == 0)
			{

			   echo  "not found";
            }

		}

		exit;

		foreach ($segments as $segment)
		{
			$segment = $advanced ? str_replace(':', '-', $segment) : $segment;

			/*foreach ($categories as $category)
			{
				if ($category->slug == $segment || $category->alias == $segment)
				{
					$vars['id'] = $category->id;
					$vars['catid'] = $category->id;
					$vars['view'] = 'category';
					$categories = $category->getChildren();
					$found = 1;
					break;
				}
			}*/

			if ($found == 0)
			{
				if ($advanced)
				{
					$db = Factory::getDbo();
					$query = $db->getQuery(true)
						->select($db->quoteName('id'))
						->from('#__newsfeeds')
						->where($db->quoteName('catid') . ' = ' . (int) $vars['catid'])
						->where($db->quoteName('alias') . ' = ' . $db->quote($segment));
					$db->setQuery($query);
					$nid = $db->loadResult();
				}
				else
				{
					$nid = $segment;
				}

				$vars['id'] = $nid;
				$vars['view'] = 'category';
			}

			$found = 0;
		}

		unset($segments[0]);
		//unset($vars['catid']);

	}
}
