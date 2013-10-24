<?php
/**
 * @version    SVN $Id: process.php 459 2012-08-13 12:58:37Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      09-Nov-2011 09:18:53
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelProcess extends JModelAdmin
{
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	0.1
	 */
	public function getTable($type = 'Process', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
	}
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        public function getItems()
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $processId = JRequest::getInt('id');

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.input, a.output, a.status, a.created'
			)
		);

                // From the albums table
                $query->from('#__hwdms_process_log AS a');

		$query->where('a.process_id = '.(int) $processId);
                $query->order('created DESC');

		//echo nl2br(str_replace('#__','jos_',$query));

                $db->setQuery($query);
                return $db->loadObjectList();
        }
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        public function getProcess()
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $processId = JRequest::getInt('id');

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.process_type, a.attempts'
			)
		);

                // From the albums table
                $query->from('#__hwdms_processes AS a');
		$query->where('a.id = ' . $processId);

                // Join over the media
		$query->select('m.title');
		$query->join('LEFT', '`#__hwdms_media` AS m ON m.id = a.media_id');

                // Join over the media
		$query->select('ext.media_type');
		$query->join('LEFT', '`#__hwdms_ext` AS ext ON ext.id = m.ext_id');

                $db->setQuery($query);
                return $db->loadObject();
        }
	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function reset($pks, $all)
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
        
        /**
	 * Method to assign user to a single record
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function delete(&$pks)
	{            
		if (!parent::delete($pks))
                {
			return false;
		}

		$db =& JFactory::getDBO();
                $pks = (array) $pks;
                $query = array();
                
		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
                        $queries = array();
                        
                        // Delete records from process log
                        $queries[] = "
                            DELETE
                                FROM ".$db->quoteName('#__hwdms_process_log')."
                                WHERE ".$db->quoteName('process_id')." = ".$db->quote($pk)."
                            ";

                        // Iterate the queries to execute each one.
                        foreach ($queries as $query)
                        {
                                $db->setQuery($query);
                                if (!$db->query())
                                {
                                        $this->setError(nl2br($db->getErrorMsg()));
                                        return false;
                                }
                        }                        
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
