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

class hwdMediaShareModelDashboard extends JModelLegacy
{
        /**
	 * Method to count the number of media added each day for the past 30 days.
         * 
         * @access  public
	 * @return  mixed   An array of data items on success, false on failure.
	 */
	public function getMedia()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('created, DATE_FORMAT(created, ' . $db->quote('%d') . ') AS day, COUNT(*) AS total')
                        ->from('#__hwdms_media')
                        ->where('created > (NOW() - INTERVAL 30 DAY)')
                        ->group('DATE_FORMAT(created, ' . $db->quote('%d') . ')')
                        ->order('created ASC');
                $db->setQuery($query);
                try
                {
                        $rows = $db->loadObjectList();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }              
                return $rows;
	}
          
        /**
	 * Method to get the recent gallery activity.
         * 
         * @access  public
	 * @return  mixed   An array of data items on success, false on failure.
	 */
	public function getActivity()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/models');
                $this->model = JModelLegacy::getInstance('Activities', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->model->populateState();
                $this->model->setState('list.start', 0);
                $this->model->setState('list.limit', 8);
                $this->model->setState('list.ordering', 'a.created');
                $this->model->setState('list.direction', 'desc');
                
                return $this->model->getItems(); 
	}        
        
        /**
	 * Method to count the number of media in the gallery.
         * 
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getMediaCount()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_media')
                        ->where('published IN (0, 1)');
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
	 * Method to count the number of categories in the gallery.
         * 
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getCategoryCount()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__categories')
                        ->where('extension = ' . $db->quote('com_hwdmediashare'));
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
	 * Method to count the number of albums in the gallery.
         * 
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getAlbumCount()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_albums')
                        ->where('published IN (0, 1)');
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
	 * Method to count the number of groups in the gallery.
         * 
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getGroupCount()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_groups')
                        ->where('published IN (0, 1)');
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
	 * Method to count the number of channels in the gallery.
         * 
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getChannelCount()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_users')
                        ->where('published IN (0, 1)');
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
	 * Method to count the number of playlists in the gallery.
         * 
         * @access  public
	 * @return  mixed   An integer on success, false on failure.
	 */
	public function getPlaylistCount($pk = null)
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_playlists')
                        ->where('published IN (0, 1)');
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
