<?php
/**
 * @version    SVN $Id: categories.php 647 2012-10-19 13:27:38Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-Oct-2011 11:48:43
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modellist');
jimport( 'joomla.application.categories' );

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelCategories extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.categories';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_hwdmediashare';

	private $_parent = null;

	private $_items = null;

        /**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	0.1
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$this->setState('filter.extension', $this->_extension);

		// Get the parent id if defined.
		$parentId = JRequest::getInt('id');
		$this->setState('filter.parentId', $parentId);

		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		$this->setState('filter.published',	1);
		$this->setState('filter.access',	true);

		// Load the display state.
		$display = $this->getUserStateFromRequest('media.category-display', 'display', $config->get('category_list_default_display', 'tree'));
                if (!in_array(strtolower($display), array('details', 'tree'))) $display = 'tree';
		$this->setState('media.category-display', $display);
                
		// List state information.
		parent::populateState();
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	/**
	 * redefine the function an add some properties to make the styling more easy
	 *
	 * @return mixed An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if(!count($this->_items))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry();
			if($active)
			{
				$params->loadString($active->params);
			}
			$options = array();
			$options['countItems'] = $params->get('show_cat_num_links', 1) || !$params->get('show_empty_categories_cat', 0);
                        $options['countItems'] = 1;
                        $categories = JCategories::getInstance('hwdMediaShare', $options);
			$this->_parent = $categories->get($this->getState('filter.parentId', 'root'));
			if(is_object($this->_parent))
			{
				$this->_items = $this->_parent->getChildren();
			}
                        else
                        {
				$this->_items = false;
			}
		}

		return $this->_items;
	}

	public function getParent()
	{
		if(!is_object($this->_parent))
		{
			$this->getItems();
		}
		return $this->_parent;
	}

        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getMedia($pk = null)
	{
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                  
                if ($config->get('category_list_media_tooltip') == 'hide') return;
                
                // Get an instance of the generic articles model
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set list filter based on parameters and views
		$model->setState('list.start', 0);
		$model->setState('list.limit', 30);
                
                // Set other filters
		$id = JRequest::getInt('category_id');
		$model->setState('filter.category_id', $id);
                
		// Ordering
		$model->setState('com_hwdmediashare.media.list.ordering', $config->get('category_list_media_tooltip', 'a.created'));
		$model->setState('com_hwdmediashare.media.list.direction', 'DESC');

                $user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) &&  (!$user->authorise('core.edit', 'com_hwdmediashare')))
                {
			// Limit to published for people who can't edit or edit.state.
			$model->setState('filter.published',	1);
			$model->setState('filter.status',	1);

			// Filter by start and end dates.
			$model->setState('filter.publish_date', true);
		}
                else
                {
			// Limit to published for people who can't edit or edit.state.
			$model->setState('filter.published',	array(0,1));
			$model->setState('filter.status',	1);
                }
                
		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());
 
                if ($items = $model->getItems())
                {
                }

		return $items;   
	}
}
