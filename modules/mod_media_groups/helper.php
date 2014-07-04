<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_groups
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Base this helper on the component view model.
require_once JPATH_SITE.'/components/com_hwdmediashare/views/groups/view.html.php';

class modMediaGroupsHelper extends hwdMediaShareViewGroups
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
		// Initialise variables.
		$app = JFactory::getApplication();
                $doc = JFactory::getDocument();
                $cache = JFactory::getCache();
                
                // Force method caching.
                $cache->setCaching(1);
                
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Groups', 'hwdMediaShareModel', array('ignore_request' => true));               

                // Populate state (and set the context).
                $this->_model->context = 'mod_media_groups';
		$this->_model->populateState();

		// Set the start and limit states.
		$this->_model->setState('list.start', 0);
		$this->_model->setState('list.limit', (int) $this->params->get('count', 0));

		// Set the ordering states.
                $ordering = $this->params->get('list_order_group', 'a.created DESC');
                $orderingParts = explode(' ', $ordering); 
                $this->_model->setState('list.ordering', $orderingParts[0]);
                $this->_model->setState('list.direction', $orderingParts[1]);
                
                // Apply author filter.
                $authors = $this->params->get('created_by');                                        
                if (is_array($authors)) $authors = array_filter($authors);
                if ($authors)
                {    
                        $this->_model->setState('filter.author_id', $this->params->get('created_by', ''));
                        $this->_model->setState('filter.author_id.include', $this->params->get('author_filtering_type', 1));
                }      

                // Apply date filter.
                $date_filtering = $this->params->get('date_filtering', 'off');
                if ($date_filtering !== 'off') 
                {
                        $this->_model->setState('filter.date_filtering', $date_filtering);
                        $this->_model->setState('filter.date_field', $this->params->get('date_field', 'a.created'));
                        $this->_model->setState('filter.start_date_range', $this->params->get('start_date_range', '1000-01-01 00:00:00'));
                        $this->_model->setState('filter.end_date_range', $this->params->get('end_date_range', '9999-12-31 23:59:59'));
                        $this->_model->setState('filter.relative_date', $this->params->get('relative_date', 30));
                }
                                
		// Additional filters.
                $this->_model->setState('filter.media_type', $this->params->get('list_default_media_type', ''));                
		$this->_model->setState('filter.featured', $this->params->get('show_featured', 'show'));

                $this->items = $this->_model->getItems();

                return $this->items;                
	}
}
