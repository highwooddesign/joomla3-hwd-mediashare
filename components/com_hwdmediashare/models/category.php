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
	 * @var string
	 */
	public $context = 'com_hwdmediashare.category';

	/**
	 * Model data
	 * @var array
	 */
	protected $_category = null;
	protected $_items = null;
	protected $_subcategories = null;        
	protected $_feature = null;        
	protected $_model = null;
        protected $_numMedia = null;
        
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Category', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Method to get a single category.
	 *
	 * @param   integer	The id of the primary key.
         * 
	 * @return  mixed  Object on success, false on failure.
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
                                                // Force this new ordering
                                                $orderingParts = explode(' ', $ordering); 
                                                $app = JFactory::getApplication();
                                                $list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array');
                                                $list['fullordering'] = $ordering;
                                                $app->setUserState($this->context . '.list', $list);
                                                $this->setState('list.ordering', $orderingParts[0]);
                                                $this->setState('list.direction', $orderingParts[1]);   
                                        }  
                                }    
                                
                                // Add the number of media
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
	 * Method to get a list of subcategories
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
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
	 * Method to get the feature record
         *
	 * @return	mixed	Object on success, false on failure.
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
                else if ($this->_category->params->get('feature') > 1)
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
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('list.ordering', $this->getState('list.ordering'));
                $this->_model->setState('list.direction', $this->getState('list.direction'));
                $this->_model->setState('filter.category_id', $this->getState('filter.category_id'));

                if ($this->_items = $this->_model->getItems())
                {
                        $this->_numMedia = $this->_model->getTotal();
                }

                return $this->_items; 
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 */
	public function getPagination()
	{
                return $this->_model->getPagination(); 
	}

	/**
	 * Method to number of media in the category.
	 *
	 * @return  JPagination  A JPagination object for the data set.
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
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
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

		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));

		$this->setState('layout', $app->input->getString('layout'));                
                
		// Load the display state.
		$display = $this->getUserStateFromRequest('media.display', 'display', $config->get('list_default_display', 'details' ), 'word', false);
                if (!in_array(strtolower($display), array('details', 'gallery', 'list'))) $display = 'details';
		$this->setState('media.display', $display);

                // Check for list inputs and set default values if none exist
                // This is required as the fullordering input will not take default value unless set
                $ordering = $config->get('list_order_media', 'a.created DESC');
                $orderingParts = explode(' ', $ordering); 
                if (!$list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
                {
                        $list['fullordering'] = $ordering;
                        $list['limit'] = $config->get('list_limit', 6);
                        $app->setUserState($this->context . '.list', $list);
                }

		// List state information.
		parent::populateState($orderingParts[0], $orderingParts[1]);          
	}
        
	/**
	 * Increment the hit counter for the record.
	 *
	 * @param   integer  $pk  Optional primary key of the record to increment.
	 *
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
	 * @param   integer  $pk     Optional primary key of the record to increment.
	 * @param   integer  $value  The value of the property to increment.
         * 
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
	 * @param   array    $pks    A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
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

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to report an object
	 * @return  void
	 */
	public function report()
	{
		// Initialiase variables.
		$user = JFactory::getUser();
                $date = JFactory::getDate();                
		$input = JFactory::getApplication()->input;

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		$table = $this->getTable('Report', 'hwdMediaShareTable');    

                if ($user->authorise('hwdmediashare.report', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;                    
                }
                                        
                // Create an object to bind to the database.
                $object = new StdClass;
                $object->element_type = 6;
                $object->element_id = $input->get('id', 0, 'int');
                $object->user_id = $user->id;
                $object->report_id = $input->get('report_id', 0, 'int');
                $object->description = $input->get('description', '', 'string');
                $object->created = $date->toSql();
                
                // Attempt to save the report details to the database.
                if (!$table->save($object))
                {
                        $this->setError($table->getError());
                        return false;
                }

		return true;
	}
}