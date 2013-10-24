<?php
/**
 * @version    SVN $Id: processes.php 1404 2013-04-30 09:31:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelProcesses extends JModelList
{
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
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

                // From the albums table
                $query->from('#__hwdms_processes AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

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
	 * @since	0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

                $status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '', 'string');
		$this->setState('filter.status', $status);

                $process_type = $this->getUserStateFromRequest($this->context.'.filter.process_type', 'filter_process_type', '', 'string');
		$this->setState('filter.process_type', $process_type);
                
                // Passing additional parameters to prevent resetting the page
                $listOrder = $this->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', 'a.created', null, false);
                $this->setState('list.ordering', $listOrder);

                // Passing additional parameters to prevent resetting the page
                $listDirn  = $this->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', 'DESC', null, false);
                $this->setState('list.direction', $listDirn);
                
		// Load the parameters.
		$params = JComponentHelper::getParams('com_hwdmediashare');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($listOrder, $listDirn);
	}
        
        /**
	 * Method to count media media.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getSuccessful($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_processes')."
                    WHERE ".$db->quoteName('status')." = ".$db->quote(2).";
                  ";
                $db->setQuery($query);
                return $db->loadResult();
	}
        
        /**
	 * Method to count media media.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getUnnecessary($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_processes')."
                    WHERE ".$db->quoteName('status')." = ".$db->quote(4).";
                  ";
                $db->setQuery($query);
                return $db->loadResult();
	}
        
	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function deletesuccessful()
	{

            
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

                $db =& JFactory::getDBO();
                $query = "
                  UPDATE ".$db->quoteName('#__hwdms_processes')."
                    SET ".$db->quoteName('attempts')." = ".$db->quote(0).", ".$db->quoteName('status')." = ".$db->quote(1)."
                    WHERE ".$db->quoteName('id')." = ".implode(" OR ".$db->quoteName('id')." = ", $pks)."
                  ";

                if (!$all)
                {
                        $query.= "
                            AND ".$db->quoteName('status')." IN (3)
                        ";
                }
                                
                $db->setQuery($query);

                // Check for a database error.
		if (!$db->query())
                {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

                // Clear the component's cache
		$this->cleanCache();

		return true;
        }        
}
