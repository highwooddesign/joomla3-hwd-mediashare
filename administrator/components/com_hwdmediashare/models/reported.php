<?php
/**
 * @version    SVN $Id: reported.php 1239 2013-03-08 14:03:34Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      20-Feb-2012 20:19:14
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelReported extends JModelList
{
        /**
	 * Method to count media media.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getMedia($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('1');
                $query->from('#__hwdms_reports AS a');
                $query->where('a.element_type = 1');
                $query->group('a.element_id');
                $db->setQuery($query);
                $db->query();                
                return $db->getNumRows();
	}
        
        /**
	 * Method to count media media.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getAlbums($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('1');
                $query->from('#__hwdms_reports AS a');
                $query->where('a.element_type = 2');
                $query->group('a.element_id');
                $db->setQuery($query);
                $db->query();                
                return $db->getNumRows();
	}
        
        /**
	 * Method to count media media.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getGroups($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('1');
                $query->from('#__hwdms_reports AS a');
                $query->where('a.element_type = 3');
                $query->group('a.element_id');
                $db->setQuery($query);
                $db->query();                
                return $db->getNumRows();
	}
        
        /**
	 * Method to count media media.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getUsers($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('1');
                $query->from('#__hwdms_reports AS a');
                $query->where('a.element_type = 5');
                $query->group('a.element_id');
                $db->setQuery($query);
                $db->query();                
                return $db->getNumRows();
	}  
        
        /**
	 * Method to count media media.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getPlaylists($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('1');
                $query->from('#__hwdms_reports AS a');
                $query->where('a.element_type = 4');
                $query->group('a.element_id');
                $db->setQuery($query);
                $db->query();                
                return $db->getNumRows();
	}
        
        /**
	 * Method to count media media.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getActivities($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('1');
                $query->from('#__hwdms_reports AS a');
                $query->where('a.element_type = 7');
                $query->group('a.element_id');
                $db->setQuery($query);
                $db->query();                
                return $db->getNumRows();
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItems($pk = null)
	{                
                if ($items = parent::getItems($pk))
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                        }
                }
		return $items;
	}
        
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        protected function getListQuery()
        {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.user_id, a.report_id, a.description, a.created'
			)
		);
                // From the hello table
                $query->from('#__hwdms_reports AS a');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.description LIKE '.$search);
			}
		}
                
                // Additional filters
		$elementType = $this->getState('filter.element_type');
                $query->where('a.element_type = ' . $elementType);
		$elementId = $this->getState('filter.element_id');
                $query->where('a.element_id = ' . $elementId);

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
        }
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

                $listOrder = $this->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', 'a.created');
                $this->setState('list.ordering', $listOrder);

                $listDirn  = $this->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', 'DESC');
                $this->setState('list.direction', $listDirn);

                $layout = JRequest::getWord('layout');
                switch ($layout) {
                    case 'media':
                        $this->setState('filter.element_type', 1);
                        $this->setState('filter.element_id', JRequest::getInt('id'));
                        break;
                }
                
		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($listOrder, $listDirn);
	}    
        
	/**
	 * Method to delete one or more records.
         * The delete() method was added to the JModelList class, instead of created a second model file. 
         * If more actions are added to this feature we can create the second model. 
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   11.1
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$pks = (array) $pks;
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Report', 'hwdMediaShareTable');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
                                if (!$table->delete($pk))
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
