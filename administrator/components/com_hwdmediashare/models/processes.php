<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareModelProcesses extends JModelList
{
    	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'process_type', 'a.process_type',
				'media_id', 'a.media_id',
				'status', 'a.status',   
				'attempts', 'a.attempts',  
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'created_user_id', 'a.created_user_id', 'author',
				'created', 'a.created',
				'modified_user_id', 'a.modified_user_id',
				'modified', 'a.modified',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

                for ($x = 0, $count = count($items); $x < $count; $x++)
                {
                }
                
		return $items;
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @return  JDatabaseQuery  database query
	 */
        protected function getListQuery()
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.process_type, a.media_id, a.status, a.attempts,' .
                                'a.checked_out, a.checked_out_time,' .
				'a.created_user_id,' .
				'a.status, a.created'
			)
		);

                // From the processes table.
                $query->from('#__hwdms_processes AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the media for the media title.
		$query->select('m.title');
		$query->join('LEFT', '#__hwdms_media AS m ON m.id=a.media_id');

                // Filter by status state
		$status = $this->getState('filter.status');
		if (is_numeric($status)) 
                {
                        $query->where('a.status = '.(int) $status);
		}

                // Filter by status state
		$process_type = $this->getState('filter.process_type');
                if (is_numeric($process_type)) 
                {
                        $query->where('a.process_type = '.(int) $process_type);
		}
                
		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');

		$query->order($db->escape($listOrder.' '.$listDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
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
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
                $status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '', 'string');
		$this->setState('filter.status', $status);

                $process_type = $this->getUserStateFromRequest($this->context.'.filter.process_type', 'filter_process_type', '', 'string');
		$this->setState('filter.process_type', $process_type);
                
		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.created', 'desc');
	}
        
        /**
	 * Method to count the number of successful processes.
	 * @return  void
	 */
	public function getSuccessful()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_processes')
                        ->where('status = ' . $db->quote(2));
                $db->setQuery($query);
                try
                {
                        $count = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}

        /**
	 * Method to count the number of unnecessary processes.
	 * @return  void
	 */
	public function getUnnecessary()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_processes')
                        ->where('status = ' . $db->quote(4));
                $db->setQuery($query);
                try
                {
                        $count = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $count;
	}      
}
