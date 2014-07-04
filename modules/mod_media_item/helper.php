<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_item
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Base this helper on the component view model.
require_once JPATH_SITE.'/components/com_hwdmediashare/views/mediaitem/view.html.php';

class modMediaItemHelper extends hwdMediaShareViewMediaItem
{
	public function __construct($module, $params)
	{
                // Load HWD assets.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_ROOT.'/components/com_hwdmediashare/helpers/dropdown.php');
                
                JLoader::register('hwdMediaShareHelperNavigation', JPATH_ROOT.'/components/com_hwdmediashare/helpers/navigation.php');
                
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
                $this->item = $this->getItem();
                $this->utilities = hwdMediaShareUtilities::getInstance();
                $this->return = base64_encode(JFactory::getURI()->toString());

                // Add assets to the head tag.
                $this->addHead();
	}

	public function addHead()
	{
                JHtml::_('bootstrap.framework');
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
                if ($this->params->get('load_joomla_css') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
                if ($this->params->get('load_joomla_css') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/strapped.2.hwd.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/aspect.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $doc->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/aspect.js');
	}

	public function getItem()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $doc = JFactory::getDocument();
                $cache = JFactory::getCache();
                
                // Force method caching.
                $cache->setCaching(1);
                
                if ($this->params->get('display') == "selected")
                {
                        if (!$media_id = (int) $this->params->get('media_id')) return;

                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $this->_model = JModelLegacy::getInstance('MediaItem', 'hwdMediaShareModel', array('ignore_request' => true));               

                        // Populate state (and set the context).
                        $this->_model->context = 'mod_media_item';
                        $this->_model->populateState();

                        // Set the media ID state.
                        $this->_model->setState('media.id', $media_id);

                        $this->item = $this->_model->getItem();

                        return $this->item;
                }
                else
                {    
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $this->_model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));               

                        // Populate state (and set the context).
                        $this->_model->context = 'mod_media_item';
                        $this->_model->populateState();

                        // Set the start and limit states.
                        $this->_model->setState('list.start', 0);
                        $this->_model->setState('list.limit', (int) $this->params->get('count', 0));

                        // Set the ordering states.
                        $ordering = $this->params->get('list_order_media', 'a.created DESC');
                        $orderingParts = explode(' ', $ordering); 
                        $this->_model->setState('list.ordering', $orderingParts[0]);
                        $this->_model->setState('list.direction', $orderingParts[1]);
                        
                        // Apply category filter.
                        $catids = $this->params->get('catid');                                        
                        if (is_array($catids)) $catids = array_filter($catids);
                        if ($catids)
                        {
                                $this->_model->setState('filter.category_id.include', (bool) $this->params->get('category_filtering_type', 1));
                            
                                if ($this->params->get('show_child_category_articles', 0) && (int) $this->params->get('levels', 0) > 0) 
                                {
                                        // Get an instance of the categories model.
                                        $categories = JModelLegacy::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true));
                                        $categories->setState('params', $appParams);
                                        $levels = $this->params->get('levels', 1) ? $this->params->get('levels', 1) : 9999;
                                        $categories->setState('filter.get_children', $levels);
                                        $categories->setState('filter.published', 1);
                                        $additional_catids = array();

                                        foreach($catids as $catid)
                                        {
                                                $categories->setState('filter.parentId', $catid);
                                                $recursive = true;
                                                $items = $categories->getItems($recursive);

                                                if ($items)
                                                {
                                                        foreach($items as $category)
                                                        {
                                                                $condition = (($category->level - $categories->getParent()->level) <= $levels);
                                                                if ($condition) {
                                                                        $additional_catids[] = $category->id;
                                                                }
                                                        }
                                                }
                                        }

                                        $catids = array_unique(array_merge($catids, $additional_catids));
                                }

                                $this->_model->setState('filter.category_id', $catids);
                        }
                        
                        // Apply author filter.
                        $authors = $this->params->get('created_by');                                        
                        if (is_array($authors)) $authors = array_filter($authors);
                        if ($authors)
                        {    
                                $this->_model->setState('filter.author_id', $this->params->get('created_by', ''));
                                $this->_model->setState('filter.author_id.include', $this->params->get('author_filtering_type', 1));
                        }                    

                        // Additional filters.
                        $this->_model->setState('filter.media_type', $this->params->get('list_default_media_type', ''));                
                        $this->_model->setState('filter.featured', $this->params->get('show_featured', 'show'));

                        $this->item = $this->_model->getItem();

                        return $this->item;  
                }
	}
}
