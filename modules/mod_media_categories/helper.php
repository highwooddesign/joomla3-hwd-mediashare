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
	public function __construct($module, $params)
	{
                // Load HWD assets.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_ROOT.'/components/com_hwdmediashare/helpers/dropdown.php');
                
                // Load and register libraries.
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('utilities');

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // We need to reset this varaible to avoid issues where other modules set this value in earlier position.
                $config->set('list_default_media_type', '');

                // Merge with module parameters.
                $config->merge($params);

                // Load the HWD language file.
                $lang = JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);
                
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
                $this->addHead();
	}

	public function addHead()
	{
                JHtml::_('bootstrap.framework');
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
                if ($this->params->get('load_joomla_css') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/aspect.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $doc->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/aspect.js');
	}

	public function getItems()
	{
		// Get an instance with a dummary option array to load a complete cateogry node tree.
                $categories = JCategories::getInstance('hwdMediaShare', array('module' => true));
		$category = $categories->get($this->params->get('parent', 'root'));

		if($category != null)
		{
			$this->items = $category->getChildren();
			if($this->params->get('count', 0) > 0 && count($items) > $this->params->get('count', 0))
			{
				$this->items = array_slice($items, 0, $this->params->get('count', 0));
			}
                        
			return $this->items;
		}
	}
}
