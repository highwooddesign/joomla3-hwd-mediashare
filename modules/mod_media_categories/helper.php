<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_categories
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modMediaCategoriesHelper extends JViewLegacy 
{
	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   array  $module  The module object.
	 * @param   array  $params  The module parameters.
         * @return  void
	 */       
	public function __construct($module, $params)
	{
                // Load HWD assets.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');

                // Include JHtml helpers.
                JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/helpers/html');
                JHtml::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/helpers/html');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');

                // Load HWD config (and force reset).
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig(null, true);
                
                // Merge with module parameters.
                $config->merge($params);

                // Load lite CSS.
                $config->set('load_lite_css', 1);
                
                // Load the HWD language file.
                $lang = JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag());
                
                // Get data.
                $this->module = $module;                
                $this->params = $config;                
                $this->items = $this->getItems();
                $this->utilities = hwdMediaShareUtilities::getInstance();
                $this->columns = $params->get('list_columns', 3);
                $this->return = base64_encode(JFactory::getURI()->toString());
                $this->moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
                $this->startLevel = reset($this->items)->getParent()->level;

                // Add assets to the head tag.
                JHtml::_('hwdhead.core', $config);
	}

	/**
	 * Method to get a list of items.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */           
	public function getItems()
	{
		$options               = array();
		$options['countItems'] = $this->params->get('numitems', 1);
		$options['module']     = true; // Dummy option array to load a complete category node tree.

		$categories = JCategories::getInstance('hwdMediaShare', $options);
		$category   = $categories->get($this->params->get('parent', 'root'));

		if ($category != null)
		{
			$items = $category->getChildren();

			if ($this->params->get('count', 0) > 0 && count($items) > $this->params->get('count', 0))
			{
				$items = array_slice($items, 0, $this->params->get('count', 0));
			}

			return $items;
		}
	}
}
