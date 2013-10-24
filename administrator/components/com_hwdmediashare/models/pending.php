<?php
/**
 * @version    SVN $Id: pending.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      14-Feb-2012 15:01:40
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelPending extends JModelLegacy {
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
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_media')."
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
	public function getAlbums($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_albums')."
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
	public function getGroups($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_groups')."
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
	public function getUsers($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_users')."
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
	public function getPlaylists($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_playlists')."
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
	public function getActivities($pk = null)
	{
                $db =& JFactory::getDBO();
                $query = "
                  SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_activities')."
                    WHERE ".$db->quoteName('status')." = ".$db->quote(2).";
                  ";
                $db->setQuery($query);
                return $db->loadResult();
	}
}
