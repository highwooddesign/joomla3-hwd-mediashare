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

class hwdMediaShareModelPending extends JModelLegacy 
{
        /**
	 * Method to count pending media.
         * 
         * @return	mixed	Object on success, false on failure.
	 */
	public function getMedia()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_media')
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
	 * Method to count pending albums.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getAlbums()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_albums')
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
	 * Method to count pending groups.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getGroups()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_groups')
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
	 * Method to count pending users.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getUsers()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_users')
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
	 * Method to count pending playlists.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getPlaylists()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_playlists')
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
}
