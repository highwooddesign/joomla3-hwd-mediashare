<?php
/**
 * @version    SVN $Id: tags.php 1351 2013-04-09 13:04:04Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework tags class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
abstract class hwdMediaShareTags
{
	/**
	 * Method to save tags
         *
	 * @since   0.1
	 */
        function save($params)
        {
                $db =& JFactory::getDBO();
                $app=& JFactory::getApplication();
                $config =& JFactory::getConfig();

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $tagMapRow =& JTable::getInstance('tagmap', 'hwdMediaShareTable');
                $tagRow =& JTable::getInstance('tag', 'hwdMediaShareTable');

                $tagArray = hwdMediaShareTags::tagArray($params);

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_tag_map')."
                        WHERE ".$db->quoteName('element_id')." = ".$db->quote($params->elementId)."
                        AND ".$db->quoteName('element_type')." = ".$db->quote($this->elementType)."
                      ";

                $db->setQuery($query);
                if (!$db->query() && $config->getValue( 'debug' ))
                {
                        $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                }

                // Loop over categories assigned to elementid
                for ($i=0, $n=count($tagArray); $i < $n; $i++)
                {
                        unset($tagId);
                        $tagMapRow =& JTable::getInstance('tagmap', 'hwdMediaShareTable');
                        $tagRow =& JTable::getInstance('tag', 'hwdMediaShareTable');

                        // Create an object to bind to the database
                        $objectTag = new StdClass;
                        $objectTag->tag = $tagArray[$i];

                        $query = "
                              SELECT id
                                FROM ".$db->quoteName('#__hwdms_tags')."
                                WHERE ".$db->quoteName('tag')." = ".$db->quote($objectTag->tag).";
                              ";
                        $db->setQuery($query);
                        $tagId = $db->loadResult();

                        if (!$tagId)
                        {
                                if (!$tagRow->bind($objectTag))
                                {
                                        return JError::raiseWarning( 500, $tagRow->getError() );
                                }

                                if (!$tagRow->store())
                                {
                                        JError::raiseError(500, $tagRow->getError() );
                                }                               
                                $tagId = $tagRow->id;
                        }

                        // Create an object to bind to the database
                        $objectTagMap = new StdClass;
                        $objectTagMap->element_id = $params->elementId;
                        $objectTagMap->element_type = $this->elementType;
                        $objectTagMap->tag_id = $tagId;

                        if (!$tagMapRow->bind($objectTagMap))
                        {
                                return JError::raiseWarning( 500, $tagMapRow->getError() );
                        }

                        if (!$tagMapRow->store())
                        {
                                JError::raiseError(500, $tagMapRow->getError() );
                        }
                }
        }
        
	/**
	 * Method to get tags
         *
	 * @since   0.1
	 */
        function get($item)
        {
                 // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

                $array = array();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'tags.tag'
			)
		);

                // From the albums table
                $query->from('#__hwdms_tags AS tags');

                // Join over the categories.
                $query->join('LEFT', '#__hwdms_tag_map AS map ON tags.id = map.tag_id');

                $query->where($db->quoteName('map.element_id').' = '.$db->quote($item->id));
                $query->where($db->quoteName('map.element_type').' = '.$db->quote($this->elementType));

                $db->setQuery($query);
                $rows = $db->loadObjectList();

                //$tags = JArrayHelper::toObject($rows);
                $tags = $rows;
                return $tags;
        }

	/**
	 * Method to get tags for input format
         *
	 * @since   0.1
	 */
        function getInput($item)
        {
                // Get category array
                $rows = hwdMediaShareTags::get($item);

                $return = '';
                for ($i=0, $n=count($rows); $i < $n; $i++)
                {
                        $row = $rows[$i];
                        $return.= "\"$row->tag\" ";
                }
                return $return;
        }

	/**
	 * Method to get an array of tags from a string
         *
	 * @since   0.1
	 */
        function tagArray($params)
        {
                $array = array();

                // We will apply the most strict filter to the variable
                jimport( 'joomla.filter.filterinput' );
                $filter = JFilterInput::getInstance();
                
                // This match pulls out any phrases in double quotes
                // Forward slashes are the start and end delimeters
                // Third parameter is the array we want to fill with matches
                if (preg_match_all('/"([^"]+)"/', $params->tags, $m))
                {
                        for ($i=0, $n=count($m[1]); $i < $n; $i++)
                        {
                                $array[] = $filter->clean($m[1][$i]);
                                $params->tags = str_replace($m[0][$i], "", $params->tags);
                        }
                } 
                else
                {
                        // The preg_match returns the number of matches found, 
                        // so if here didn't match pattern
                }

                // Split by spaces and commas
                $splitSpaces = explode(" ", $params->tags);
                foreach ($splitSpaces as $splitSpace)
                {
                        if (!empty($splitSpace))
                        {
                                $splitCommas = explode(",", $splitSpace);
                                foreach ($splitCommas as $splitComma)
                                {
                                        if (!empty($splitComma))
                                        {
                                                $array[] = $filter->clean($splitComma);
                                        }
                                }
                        }
                }
                
                return $array;
        }
        
	/**
	 * Method to delete tags
         *
	 * @since   0.1
	 */
        function delete($pk)
        {
                $db =& JFactory::getDBO();
                $app=& JFactory::getApplication();
                $config =& JFactory::getConfig();
                $pk = intval ($pk);
                
                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_tags')."
                        WHERE ".$db->quoteName('id')." = ".$db->quote($pk)."
                      ";

                $db->setQuery($query);
                if (!$db->query() && $config->getValue( 'debug' ))
                {
                        $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                        return false;
                }

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_tag_map')."
                        WHERE ".$db->quoteName('tag_id')." = ".$db->quote($pk)."
                       ";

                $db->setQuery($query);
                if (!$db->query() && $config->getValue( 'debug' ))
                {
                        $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
                        return false;
                }

                return true;
        }
}
