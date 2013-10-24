<?php
/**
 * @version    SVN $Id: category.php 1352 2013-04-09 13:05:49Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework categories class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareCategory
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
	 * Returns the hwdMediaShareCategory object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareCategory A hwdMediaShareCategory object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareCategory';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to save categories
         * 
         * @since   0.1
	 **/
        function save($params)
        {
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $date =& JFactory::getDate();

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('CategoryMap', 'hwdMediaShareTable');

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_category_map')."
                        WHERE ".$db->quoteName('element_id')." = ".$db->quote($params->elementId)."
                        AND ".$db->quoteName('element_type')." = ".$db->quote($this->elementType)."
                      ";

                $db->setQuery($query);
                if (!$db->query() && $config->getValue( 'debug' ))
                {
                        $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                }

                // Loop over categories assigned to elementid
                for ($i=0, $n=count($params->categoryId); $i < $n; $i++)
                {
                        $row =& JTable::getInstance('CategoryMap', 'hwdMediaShareTable');

                        // Create an object to bind to the database
                        $object = new StdClass;
                        $object->id = null;
                        $object->element_type = (int) $this->elementType;
                        $object->element_id = (int) $params->elementId;
                        $object->category_id = (int) $params->categoryId[$i];
                        $object->created_user_id = (int) $user->id;
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
                return true;
        }
	/**
	 * Method to save an individual category to an item
         * 
         * @since   0.1
	 **/
        function saveIndividual($params)
        {
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $date =& JFactory::getDate();
                
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('CategoryMap', 'hwdMediaShareTable');

                $query = "
                      SELECT COUNT(*)
                        FROM ".$db->quoteName('#__hwdms_category_map')."
                        WHERE ".$db->quoteName('element_id')." = ".$db->quote($params->elementId)."
                        AND ".$db->quoteName('element_type')." = ".$db->quote($params->elementType)."
                        AND ".$db->quoteName('category_id')." = ".$db->quote($params->categoryId)."
                      ";

                $db->setQuery($query);
                $result = $db->loadResult();

                // Loop over categories assigned to elementid
                if($result == 0)
                {
                        $row =& JTable::getInstance('CategoryMap', 'hwdMediaShareTable');

                        // Create an object to bind to the database
                        $object = new StdClass;
                        $object->id = null;
                        $object->element_type = $params->elementType;
                        $object->element_id = $params->elementId;
                        $object->category_id = $params->categoryId;
                        $object->created_user_id = $user->id;
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
                return true;
        }
        
	/**
	 * Method to get categories of an item
         * 
         * @since   0.1
	 **/
        function get($item)
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $array = array();

		// Select the required fields from the table.
		$query->select('c.id, c.title');

                // From the albums table
                $query->from('#__hwdms_category_map AS cmap');

		// Join over the categories.
                $query->join('LEFT', '#__categories AS c ON c.id = cmap.category_id');

                $query->where($db->quoteName('cmap.element_id').' = '.$db->quote($item->id));
                $query->where($db->quoteName('cmap.element_type').' = '.$db->quote($this->elementType));
                // Make sure we don't get any invalid categories
                $query->where($db->quoteName('c.id').' > 0');

                // Restrict based on access
		if ($config->get('entice_mode') == 0)
                {
                        $query->where('c.access IN ('.$groups.')');
                }

                //Filter by published category
		if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) && (!$user->authorise('core.edit', 'com_hwdmediashare')))
                {
			// Limit to published for people who can't edit or edit.state.
			$published = 1;
		}
                else
                {
			// Limit to published for people who can't edit or edit.state.
			$published = array(0,1);
                }
		// Filter by state
		if (is_array($published)) 
                {
			JArrayHelper::toInteger($published);
			$published = implode(',', $published);
			if ($published) 
                        {
                                $query->where('c.published IN ('.$published.')');
			}
		}
                else if (is_numeric($published))
                {
			$query->where('c.published = '.(int) $published);
		}
                        
                $db->setQuery($query);
                $rows = $db->loadObjectList();
                
                $categories = JArrayHelper::toObject($rows);
                $categories = $rows;
                return $categories;
        }
        
	/**
	 * Method to get categories of an item (in format for a form input)
         * 
         * @since   0.1
	 **/
        function getInput($item)
        {
                // Get category array
                $rows = hwdMediaShareCategory::get($item);

                $return = array();

                // Loop over categories assigned to elementid
                for ($i=0, $n=count($rows); $i < $n; $i++)
                {
                        if (!in_array(intval($rows[$i]->id), $return))
                        {
                                $return[] = intval($rows[$i]->id);
                        }
                }

                return $return;
        }
}
