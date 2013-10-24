<?php
/**
 * @version    SVN $Id: favourites.php 503 2012-09-05 13:13:26Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Jan-2012 10:31:44
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework favourites class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareFavourites extends JObject
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
	 * Returns the hwdMediaShareFavourites object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareFavourites A hwdMediaShareFavourites object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareFavourites';
                        $instance = new $c;
		}

		return $instance;
	}
        
        /**
	 * Method to favour an item
         * 
	 * @since   0.1
	 **/
	public function favour($params)
	{
                $db =& JFactory::getDBO();
                $user = JFactory::getUser();
                $date =& JFactory::getDate();
                                
                if (!$user->id)
                {
                        if (JRequest::getVar('task') == 'mediaitem.favour')
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        }
                        else
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_LOGIN'));
                        }
                        return false;
                }
                
                $query = "
                      SELECT COUNT(*)
                        FROM ".$db->quoteName('#__hwdms_favourites')."
                        WHERE ".$db->quoteName('element_id')." = ".$db->quote($params->elementId)."
                        AND ".$db->quoteName('element_type')." = ".$db->quote($params->elementType)."
                        AND ".$db->quoteName('user_id')." = ".$db->quote($user->id)."
                      ";

                $db->setQuery($query);
                $result = $db->loadResult();

                // Loop over categories assigned to elementid
                if($result == 0)
                {
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $row =& JTable::getInstance('Favourite', 'hwdMediaShareTable');

                        // Create an object to bind to the database
                        $object = new StdClass;
                        $object->id = null;
                        $object->element_type = $params->elementType;
                        $object->element_id = $params->elementId;
                        $object->user_id = $user->id;
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
                }
                return true;
	}
        
        /**
	 * Method to unfavour an item
         * 
	 * @since   0.1
	 **/
	public function unfavour($params)
	{
                $db =& JFactory::getDBO();
                $user = JFactory::getUser();
                
                if (!$user->id)
                {
                        if (JRequest::getVar('task') == 'mediaitem.unfavour')
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        }
                        else
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_LOGIN'));
                        }
                        return false;
                }
                
                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_favourites')."
                        WHERE ".$db->quoteName('element_id')." = ".$db->quote($params->elementId)."
                        AND ".$db->quoteName('element_type')." = ".$db->quote($params->elementType)."
                        AND ".$db->quoteName('user_id')." = ".$db->quote($user->id)."
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
	 * Method to check the favourited status of an item
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
                $query->from('#__hwdms_favourites AS a');

                $query->where($db->quoteName('a.element_type').' = '.$params->elementType);
                $query->where($db->quoteName('a.element_id').' = '.$params->elementId);
                $query->where($db->quoteName('a.user_id').' = '.$user->id);

                $db->setQuery($query);
                $favoured = $db->loadResult();

                if ($favoured)
                {
                        return true;
                }
                else
                {
                        return false;
                }
	}
} 
