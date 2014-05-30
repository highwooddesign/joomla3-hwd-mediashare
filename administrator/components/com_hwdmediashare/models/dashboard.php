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
	 * @return  void
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
	 * @return  void
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
	 * @return  void
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
	 * @return  void
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
	 * @return  void
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
	 * @return  void
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
	 * Method to count the number of user channels in the gallery.
	 * @return  void
	 */
	public function getUserCount()
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
	 * Method to count the number of categories in the gallery.
	 * @return  void
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
        
        /**
	 * Method to get the version number of HWD.
	 * @return  void
	 */
	public function getVersion()
	{
                jimport('joomla.application.component.helper');
                $params = JComponentHelper::getComponent('com_hwdmediashare');
                JTable::addIncludePath(JPATH_LIBRARIES.'/joomla/database/table');
                $table = JTable::getInstance('Extension');
                $table->load($params->id);
                $cache = new JRegistry($table->manifest_cache);
                return $cache->get('version');
                
                // Get the SVN revision (this XML parser is now deprecated, but isn't used anyway)
                $xml = JFactory::getXMLParser('Simple');
                $xmlfile= JPATH_SITE.'/administrator/components/com_hwdmediashare/com_hwdmediashare.xml';
                $xml->loadFile($xmlfile);               
                return $xml->document->getElementByPath('svn')->data();
	}
}
