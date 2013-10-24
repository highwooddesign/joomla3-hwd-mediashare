<?php
/**
 * @version    SVN $Id: subscriptions.php 503 2012-09-05 13:13:26Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Nov-2011 14:10:03
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework subscriptions class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareSubscriptions extends JObject
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A configuration array including optional elements.
	 *
	 * @since   0.1
	 */
	public function __construct($config = array())
	{
	}

	/**
	 * Returns the hwdMediaShareSubscriptions object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareSubscriptions A hwdMediaShareSubscriptions object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareSubscriptions';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to subscribe
         * 
	 * @since   0.1
	 **/
	public function subscribe($params)
	{
                $db =& JFactory::getDBO();
                $date =& JFactory::getDate();

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('Subscription', 'hwdMediaShareTable');

                // Create an object to bind to the database
                $object = new StdClass;
                $object->element_type = $params->elementType;
                $object->element_id = $params->elementId;
                $object->user_id = $params->userId;
                $object->created = $date->format('Y-m-d H:i:s');

                if (!$row->bind($object))
                {
                        $this->setError(JError::raiseWarning(500, $row->getError()));
                        return false;
                }

                if (!$row->store())
                {
                        $this->setError(JError::raiseError(500, $row->getError()));
                        return false;
                }
                
                return true;
	}

        /**
	 * Method to unsubscribe
         * 
	 * @since   0.1
	 **/
	public function unsubscribe($params)
	{
                $db =& JFactory::getDBO();
                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_subscriptions')."
                        WHERE ".$db->quoteName('element_id')." = ".$db->quote($params->elementId)."
                        AND ".$db->quoteName('element_type')." = ".$db->quote($params->elementType)."
                        AND ".$db->quoteName('user_id')." = ".$db->quote($params->userId)."
                      ";

                $db->setQuery($query);
               
                if (!$db->query())
                {
                        $this->setError(JError::raiseError(500, $db->getErrorMsg()));
                        return false;
                }
                
                return true;
	}

	/**
	 * Method to get subscription state
         * 
	 * @since   0.1
	 **/
	public function get($params)
	{
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

                $user = JFactory::getUser();
                $array = array();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'COUNT(*)'
			)
		);

                // From the albums table
                $query->from('#__hwdms_subscriptions AS a');

                $query->where($db->quoteName('element_type').' = '.$params->elementType);
                $query->where($db->quoteName('element_id').' = '.$params->elementId);
                $query->where($db->quoteName('user_id').' = '.$user->id);

                $db->setQuery($query);
                $subscribed = $db->loadResult();

                if ($subscribed)
                {
                        return true;
                }
                else
                {
                        return false;
                }
	}
}
