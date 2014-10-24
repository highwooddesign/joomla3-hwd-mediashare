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

class modMediaGroupsHelper extends JViewLegacy
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

                // Load HWD config, merge with module parameters (and force reset).
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig($params, true);

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
		// Initialise variables.
		$app = JFactory::getApplication();
                
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('Groups', 'hwdMediaShareModel', array('ignore_request' => true));               

                // Populate state (and set the context).
                $model->context = 'mod_media_groups';
		$model->populateState();

		// Set the start and limit states.
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $this->params->get('count', 0));

		// Set the ordering states.
                $ordering = $this->params->get('list_order_group', 'a.created DESC');
                $orderingParts = explode(' ', $ordering); 
                $model->setState('list.ordering', $orderingParts[0]);
                $model->setState('list.direction', $orderingParts[1]);
                
                // Apply author filter.
                $authors = $this->params->get('created_by');                                        
                if (is_array($authors)) $authors = array_filter($authors);
                if ($authors)
                {    
                        $model->setState('filter.author_id', $this->params->get('created_by', ''));
                        $model->setState('filter.author_id.include', $this->params->get('author_filtering_type', 1));
                }      

                // Apply date filter.
                $date_filtering = $this->params->get('date_filtering', 'off');
                if ($date_filtering !== 'off') 
                {
                        $model->setState('filter.date_filtering', $date_filtering);
                        $model->setState('filter.date_field', $this->params->get('date_field', 'a.created'));
                        $model->setState('filter.start_date_range', $this->params->get('start_date_range', '1000-01-01 00:00:00'));
                        $model->setState('filter.end_date_range', $this->params->get('end_date_range', '9999-12-31 23:59:59'));
                        $model->setState('filter.relative_date', $this->params->get('relative_date', 30));
                }
                                
		// Additional filters.
		$model->setState('filter.featured', $this->params->get('show_featured', 'show'));

                $this->items = $model->getItems();

                return $this->items;                 
	}
}
