<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareModelCategory extends JModelList
{
	/**
	 * Model context string.
         * 
         * @access      public
	 * @var         string
	 */  
	public $context = 'com_hwdmediashare.category';

	/**
	 * The category data.
         * 
         * @access      protected
	 * @var         object
	 */  
	protected $_category;
        
	/**
	 * The category items.
         * 
         * @access      protected
	 * @var         object
	 */
	protected $_items;
        
	/**
	 * The category subcategories.
         * 
         * @access      protected
	 * @var         object
	 */
	protected $_subcategories;
        
	/**
	 * The category featured item.
         * 
         * @access      protected
	 * @var         object
	 */        
	protected $_feature;

        /**
	 * The media model used for obtaining category items.
         * 
         * @access      protected
	 * @var         object
	 */        
	protected $_model;

	/**
	 * The number of media in the category.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_numMedia = 0;
        
	/**
	 * Class constructor. Defines a white list of column filters.
	 *
	 * @access	public
	 * @param       array       $config     An optional associative array of configuration settings.
         * @return      void
	 */ 
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'title', 'a.title',
				'viewed', 'a.viewed',                            
				'likes', 'a.likes',
				'dislikes', 'a.dislikes',
				'ordering', 'a.ordering', 'map.ordering', 'pmap.ordering',
				'created_user_id', 'a.created_user_id', 'created_user_id_alias', 'a.created_user_id_alias', 'author',
                                'created', 'a.created',
				'modified', 'a.modified',
				'hits', 'a.hits',
                                'random', 'random',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get a table object, and load it if necessary.
	 *
	 * @access  public
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Category', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Method to get a single category.
	 *
         * @access  public
	 * @param   integer     $pk     The id of the primary key.
	 * @return  mixed       Object on success, false on failure.
	 */
	public function getCategory($pk = null)
	{
		// Initialise variables.
		$pk = (int) (!empty($pk)) ? $pk : $this->getState('filter.category_id');

                if ($pk > 0)
                {   
                        $categories = JCategories::getInstance('hwdMediaShare');
                        if ($this->_category = $categories->get($pk))
                        {
                                // Convert params field to registry.
                                if (property_exists($this->_category, 'params'))
                                {
                                        $registry = new JRegistry;
                                        $registry->loadString($this->_category->params);
                                        $this->_category->params = $registry;  

                                        // Check if this category has a custom ordering.
                                        if ($ordering = $this->_category->params->get('list_order_media')) 
                                        {
                                                // Force this new ordering.
                                                $orderingParts = explode(' ', $ordering); 
                                                $app = JFactory::getApplication();
                                                $list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array');
                                                $list['fullordering'] = $ordering;
                                                $app->setUserState($this->context . '.list', $list);
                                                $this->setState('list.ordering', $orderingParts[0]);
                                                $this->setState('list.direction', $orderingParts[1]);   
                                        }  
                                }    
                                
                                // Add the number of media.
                                $this->_category->nummedia = $this->_numMedia;
                                
                                // Add the tags.
                                $this->_category->tags = new JHelperTags;
                                $this->_category->tags->getItemTags('com_hwdmediashare.category', $this->_category->id);
                        
                                return $this->_category;
                        }
                        else
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_DOES_NOT_EXIST'));
                                return false;
                        }
                }
                else
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_ITEM_DOES_NOT_EXIST'));
			return false;
                }
	}

        /**
	 * Method to get a list of subcategories.
	 *
         * @access  public
	 * @param   integer	The id of the primary key.
	 * @return  mixed	Object on success, false on failure.
	 */
	public function getSubcategories($pk = null)
	{
		if(!$this->_subcategories)
		{
                        $parent = $this->getCategory($pk);
			if(is_object($parent))
			{
				$this->_subcategories = $parent->getChildren();
			}
		}

		return $this->_subcategories;
	}
        
        /**
	 * Method to get the feature item.
         *
         * @access  public
	 * @return  mixed   Object on success, false on failure.
	 */
	public function getFeature()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                if ($this->_category->params->get('feature') == 1)
                {
                        $this->_model = JModelLegacy::getInstance('MediaItem', 'hwdMediaShareModel', array('ignore_request' => true));
                        $this->_model->populateState();
                        $this->_model->setState('media.id', (int) $this->_category->params->get('featuremedia', 0));

                        if ($this->_feature = $this->_model->getItem()) return $this->_feature; 
                }
                elseif ($this->_category->params->get('feature') > 1)
                {
                        $this->_model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                        $this->_model->populateState();
                        $this->_model->setState('filter.category_id', $this->getState('filter.category_id'));
                        switch ($this->_category->params->get('feature')) 
                        {
                            case 2:
                                $this->_model->setState('list.ordering', 'a.created');
                                $this->_model->setState('list.direction', 'desc');
                                break;
                            case 3:
                                $this->_model->setState('list.ordering', 'a.created');
                                $this->_model->setState('list.direction', 'desc');
                                $this->_model->setState('filter.featured', 'only');
                                break;
                        }
                        
                        if ($this->_feature = $this->_model->getItem()) return $this->_feature;                        
                }
                
                return false;
	}        
        
	/**
	 * Method to get a list of items.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->context = 'com_hwdmediashare.category';
                $this->_model->populateState();
                $this->_model->setState('list.ordering', $this->getState('list.ordering'));
                $this->_model->setState('list.direction', $this->getState('list.direction'));
                $this->_model->setState('filter.category_id', $this->getState('filter.category_id'));
                $this->_model->setState('filter.featured', $this->getState('category.show_featured'));

                if ($this->_items = $this->_model->getItems())
                {
                        $this->_numMedia = $this->_model->getTotal();
                }

                return $this->_items; 
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
         * @access  public
	 * @return  JPagination  A JPagination object for the data set.
	 */
	public function getPagination()
	{
                return $this->_model->getPagination(); 
	}

	/**
	 * Method to number of media in the category.
	 *
         * @access  public
	 * @return  integer The number of media.
	 */
	public function getNumMedia()
	{
                return (int) $this->_numMedia; 
	}
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @access  protected
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $user = JFactory::getUser();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('filter.category_id', $id);

                // Only set these states when in the com_hwdmediashare.category context.
                if ($this->context == 'com_hwdmediashare.category')
                {              
                        // Load the display state.
                        $display = $this->getUserStateFromRequest('media.display', 'display', $config->get('list_default_display', 'details'), 'word', false);
                        if (!in_array(strtolower($display), array('details', 'gallery', 'list'))) $display = 'details';
                        $this->setState('media.display', $display);

                        // Load the featured state.
                        $featured = $this->getUserStateFromRequest('category.show_featured', 'show_featured', $config->get('show_featured', 'show'), 'word', false);
                        if (!in_array(strtolower($featured), array('show', 'hide', 'only'))) $display = 'show';
                        $this->setState('category.show_featured', $featured);

                        // Check for list inputs and set default values if none exist
                        // This is required as the fullordering input will not take default value unless set
                        $orderingFull = $config->get('list_order_media', 'a.created DESC');
                        $orderingParts = explode(' ', $orderingFull); 
                        $ordering = $orderingParts[0];
                        $direction = $orderingParts[1];                        
                        if (!$list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
                        {
                                $list['fullordering'] = $orderingFull;
                                $list['limit'] = $config->get('list_limit', 6);
                                $app->setUserState($this->context . '.list', $list);
                        }                        
                }
                
                // List state information.
                parent::populateState($ordering, $direction);           
	}
        
	/**
	 * Increment the hit counter for the record.
	 *
         * @access  public
	 * @param   integer  $pk  Optional primary key of the record to increment.
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.category_id');

			$table = $this->getTable();
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}

	/**
	 * Increment the like counter for the record.
	 *
         * @access  public
	 * @param   integer  $pk     Optional primary key of the record to increment.
	 * @param   integer  $value  The value of the property to increment.
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function like($pk = 0, $value = 1)
	{            
                $user = JFactory::getUser();
                if (!$user->authorise('hwdmediashare.like', 'com_hwdmediashare'))
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }
                
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.category_id');

                $table = $this->getTable();
                $table->load($pk);
                $table->like($pk, $value);

                return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
         * @access  public
	 * @param   array    $pks    A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 * @return  boolean  True on success.
	 */
	public function publish($pks, $value = 0)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Access checks.
		foreach ($pks as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.category.'. (int) $id))
			{
				// Prune items that the user can't change.
				unset($pks[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}
                
		if (empty($pks))
		{
			$this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
			return false;
		}

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__categories'))
                                    ->set('published = ' . $db->quote((int) $value))
                                    ->where('id IN (' . implode(',', $pks) . ')');
                        $db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Clear the component's cache.
		$this->cleanCache();

		return true;
	}
}