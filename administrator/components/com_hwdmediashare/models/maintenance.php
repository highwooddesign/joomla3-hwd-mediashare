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

class hwdMediaShareModelMaintenance extends JModelLegacy
{
        /**
	 * Method to clean the category map.
         * 
         * @access  public
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function cleanCategoryMap()
	{
                $db = JFactory::getDbo();

                $query = $db->getQuery(true);

                $conditions = array(
                    $db->quoteName('element_type') . ' = ' . $db->quote(0),
                    $db->quoteName('element_id') . ' = ' . $db->quote(0),
                    $db->quoteName('category_id') . ' = ' . $db->quote(0)
                );

                $query->delete($db->quoteName('#__hwdms_category_map'));
                $query->where($conditions, 'OR');

                try
                {
                        $db->setQuery($query);
                        $result = $db->execute();                 
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $result;
        }
                
        /**
	 * Method to empty old upload tokens
         * 
         * @access  public
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function emptyUploadTokens()
	{
                $db = JFactory::getDbo();

                $query = $db->getQuery(true);

                $conditions = array(
                    $db->quoteName('datetime') . ' < (NOW() - INTERVAL 10 MINUTE)'
                );

                $query->delete($db->quoteName('#__hwdms_upload_tokens'));
                $query->where($conditions);

                try
                {
                        $db->setQuery($query);
                        $result = $db->execute();                 
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $result;
        }    
        
        /**
	 * Method to purge old processes
         * 
         * @access  public
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function purgeOldProcesses()
	{
                $db = JFactory::getDbo();

                $query = $db->getQuery(true);

                $conditions = array(
                    $db->quoteName('created') . ' < (NOW() - INTERVAL 90 DAY)'
                );

                $query->delete($db->quoteName('#__hwdms_process_log'));
                $query->where($conditions);

                try
                {
                        $db->setQuery($query);
                        $result = $db->execute();                 
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $result;
        }   
        
        /**
	 * Method to purge old processes
         * 
         * @access  public
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function uninstallOldExtensions()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDbo();
                $installer = JInstaller::getInstance();
                
                // Find player_hwdjwplayer.
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('type' => 'plugin', 'folder' => 'hwdmediashare', 'element' => 'player_hwdjwplayer'));
                if ($eid)
		{
                        $result = $installer->uninstall('plugin', $eid);               
                        if ($result === false)
                        {
                                $this->setError($installer->getError());
                                return false;                               
                        }
		}
                
                // Find player_flowplayerreloaded.
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('type' => 'plugin', 'folder' => 'hwdmediashare', 'element' => 'player_flowplayerreloaded'));
                if ($eid)
		{
                        $result = $installer->uninstall('plugin', $eid);               
                        if ($result === false)
                        {
                                $this->setError($installer->getError());
                                return false;                               
                        }
		}
                
                // Find player_hwdflowplayer.
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('type' => 'plugin', 'folder' => 'hwdmediashare', 'element' => 'player_hwdflowplayer'));
                if ($eid)
		{
                        $result = $installer->uninstall('plugin', $eid);               
                        if ($result === false)
                        {
                                $this->setError($installer->getError());
                                return false;                               
                        }
		}
                
                // Find player_bo_videojs.
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('type' => 'plugin', 'folder' => 'hwdmediashare', 'element' => 'player_bo_videojs'));
                if ($eid)
		{
                        $result = $installer->uninstall('plugin', $eid);               
                        if ($result === false)
                        {
                                $this->setError($installer->getError());
                                return false;                               
                        }
		}
                
                // Find remote_videogooglecom.
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('type' => 'plugin', 'folder' => 'hwdmediashare', 'element' => 'remote_videogooglecom'));
                if ($eid)
		{
                        $result = $installer->uninstall('plugin', $eid);               
                        if ($result === false)
                        {
                                $this->setError($installer->getError());
                                return false;                               
                        }
		}
                
                // Find remote_ukextremecom.
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('type' => 'plugin', 'folder' => 'hwdmediashare', 'element' => 'remote_ukextremecom'));
                if ($eid)
		{
                        $result = $installer->uninstall('plugin', $eid);               
                        if ($result === false)
                        {
                                $this->setError($installer->getError());
                                return false;                               
                        }
		}
                
                // Find mod_media_tags.
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('type' => 'module', 'element' => 'mod_media_tags'));
                if ($eid)
		{
                        $result = $installer->uninstall('module', $eid);               
                        if ($result === false)
                        {
                                $this->setError($installer->getError());
                                return false;                               
                        }
		}
                
                // Find legacy "hwdMediaShare Addons" package.
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('type' => 'package', 'element' => 'pkg_hwdmediashare'));
                if ($eid)
		{
                        $query = $db->getQuery(true);

                        $conditions = array(
                            $db->quoteName('type') . ' = ' . $db->quote('package'), 
                            $db->quoteName('element') . ' = ' . $db->quote('pkg_hwdmediashare')
                        );

                        $query->delete($db->quoteName('#__extensions'));
                        $query->where($conditions);

                        try
                        {
                                $db->setQuery($query);
                                $result = $db->execute();                 
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());
                                return false;                            
                        }

                        if ($result)
                        {
                                $file = JPATH_SITE.'/administrator/manifests/packages/pkg_hwdmediashare.xml';
                                if (!JFile::delete($file))
                                {
                                        $this->setError(JText::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file));
                                }
                        }
		}

                return true;
        }   
        
        /**
	 * Method to optimise database indexes
         * 
         * @access  public
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function databaseIndexOptimisation()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDbo();

                // Fulltext idx_searches #__hwdms_media
                $query = 'SHOW INDEX FROM ' . $db->quoteName('#__hwdms_media') . ' WHERE ' . $db->quoteName('Key_name') . ' = ' . $db->quote('idx_searches');
                try
                {
                        $db->setQuery($query);
                        $result = $db->loadAssoc();                        
                }
                catch (RuntimeException $e)
                {
                        $app->enqueueMessage($e->getMessage());
                        return false;                         
                }

                if (!isset($result['Key_name']) || $result['Key_name'] != 'idx_searches')
                {
                        $query = 'ALTER TABLE ' . $db->quoteName('#__hwdms_media') . ' ADD FULLTEXT idx_searches (' . $db->quoteName('title') . ', ' . $db->quoteName('description') . ')';
                        try
                        {
                                $db->setQuery($query);
                                $result = $db->execute();                        
                        }
                        catch (RuntimeException $e)
                        {
                                $app->enqueueMessage($e->getMessage());
                                return false;                         
                        }
                }

                // Index idx_element_id #__hwdms_category_map
                $query = 'SHOW INDEX FROM ' . $db->quoteName('#__hwdms_category_map') . ' WHERE ' . $db->quoteName('Key_name') . ' = ' . $db->quote('idx_element_id');
                try
                {
                        $db->setQuery($query);
                        $result = $db->loadAssoc();                        
                }
                catch (RuntimeException $e)
                {
                        $app->enqueueMessage($e->getMessage());
                        return false;                         
                }

                if (!isset($result['Key_name']) || $result['Key_name'] != 'idx_element_id')
                {
                        $query = 'ALTER TABLE ' . $db->quoteName('#__hwdms_category_map') . ' ADD KEY idx_element_id  (' . $db->quoteName('element_id') . ')';
                        try
                        {
                                $db->setQuery($query);
                                $result = $db->execute();                        
                        }
                        catch (RuntimeException $e)
                        {
                                $app->enqueueMessage($e->getMessage());
                                return false;                         
                        }
                }
                
                // Index idx_element_type #__hwdms_category_map
                $query = 'SHOW INDEX FROM ' . $db->quoteName('#__hwdms_category_map') . ' WHERE ' . $db->quoteName('Key_name') . ' = ' . $db->quote('idx_element_type');
                try
                {
                        $db->setQuery($query);
                        $result = $db->loadAssoc();                        
                }
                catch (RuntimeException $e)
                {
                        $app->enqueueMessage($e->getMessage());
                        return false;                         
                }

                if (!isset($result['Key_name']) || $result['Key_name'] != 'idx_element_type')
                {
                        $query = 'ALTER TABLE ' . $db->quoteName('#__hwdms_category_map') . ' ADD KEY idx_element_type  (' . $db->quoteName('element_type') . ')';
                        try
                        {
                                $db->setQuery($query);
                                $result = $db->execute();                        
                        }
                        catch (RuntimeException $e)
                        {
                                $app->enqueueMessage($e->getMessage());
                                return false;                         
                        }
                }
                
                return true;                
        }          
}
