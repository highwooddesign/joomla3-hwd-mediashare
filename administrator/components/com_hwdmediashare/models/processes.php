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
	 * Class constructor. Defines a white list of column filters.
	 *
	 * @access  public
	 * @param   array   $config  An optional associative array of configuration settings.
         * @return  void
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
                                /** Filter fields for additional joins **/
				'title', 'm.title',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @access  protected
	 * @return  JDatabaseQuery  The database query.
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
				'a.id, a.process_type, a.media_id, a.status, a.attempts, a.checked_out, a.checked_out_time,' . 
				'a.created_user_id, a.created'
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

   		// Group over the key to prevent duplicates.
                $query->group('a.id');
                
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
        }
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @access  protected
	 * @param   string     $ordering   An optional ordering field.
	 * @param   string     $direction  An optional direction (asc|desc).
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.created', 'desc');
	}
        
        /**
	 * Method to count the number of successful processes.
         * 
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getSuccessful()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_processes')
                        ->where('status = ' . $db->quote(2));
                try
                {
                        $db->setQuery($query);
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
         * 
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getUnnecessary()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_processes')
                        ->where('status = ' . $db->quote(4));
                try
                {
                        $db->setQuery($query);
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
