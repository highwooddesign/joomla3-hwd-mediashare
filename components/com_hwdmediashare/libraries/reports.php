<?php
/**
 * @version    SVN $Id: reports.php 503 2012-09-05 13:13:26Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      08-Jan-2012 10:55:27
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework reports class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareReports
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
	 * Returns the hwdMediaShareReports object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareReports A hwdMediaShareReports object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareReports';
                        $instance = new $c;
		}

		return $instance;
	}

	/**
	 * Method to add a report
         * 
	 * @since   0.1
	 **/        
	public function add($params)
	{
                $date =& JFactory::getDate();
                $db =& JFactory::getDBO();
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('Report', 'hwdMediaShareTable');

                // Create an object to bind to the database
                $object = new StdClass;
                $object->element_type = $params->elementType;
                $object->element_id = $params->elementId;
                $object->user_id = $params->userId;
                $object->report_id = $params->reportId;
                $object->description = $params->description;
                $object->created = $date->format('Y-m-d H:i:s');
                
                if (!$row->bind($object))
                {
                        return JError::raiseWarning( 500, $row->getError() );
                }

                if (!$row->store())
                {
                        JError::raiseError(500, $row->getError() );
                }
	}
}
