<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmigrator
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMigratorModelDashboard extends JModelLegacy
{
        /**
	 * Method to count the video items for migration.
         * 
         * @access  public
	 * @return  mixed   The integer count on success, false on failure.
	 */
	public function getVideoItems()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery('SHOW TABLES');
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg('dbprefix').'hwdvidsvideos')
                        {
                                $query = $db->getQuery(true)
                                        ->select('COUNT(*)')
                                        ->from('#__hwdvidsvideos');
                                try
                                {
                                        $db->setQuery($query);
                                        $retval->count = $db->loadResult();
                                }
                                catch (RuntimeException $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }
                        }
                }

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_migrator')
                        ->where($db->quoteName('element_type') . ' = ' . $db->quote(1))
                        ->where($db->quoteName('status') . ' = ' . $db->quote(1));
                try
                {
                        $db->setQuery($query);
                        $retval->migrated = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $retval;
	}

        /**
	 * Method to count the video categories for migration.
         * 
         * @access  public
	 * @return  mixed   The integer count on success, false on failure.
	 */
	public function getVideoCategories()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery('SHOW TABLES');
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg('dbprefix').'hwdvidscategories')
                        {
                                $query = $db->getQuery(true)
                                        ->select('COUNT(*)')
                                        ->from('#__hwdvidscategories');
                                try
                                {
                                        $db->setQuery($query);
                                        $retval->count = $db->loadResult();
                                }
                                catch (RuntimeException $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }
                        }
                }

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_migrator')
                        ->where($db->quoteName('element_type') . ' = ' . $db->quote(2))
                        ->where($db->quoteName('status') . ' = ' . $db->quote(1));
                try
                {
                        $db->setQuery($query);
                        $retval->migrated = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $retval;
	}

        /**
	 * Method to count the video groups for migration.
         * 
         * @access  public
	 * @return  mixed   The integer count on success, false on failure.
	 */
	public function getVideoGroups()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery('SHOW TABLES');
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg('dbprefix').'hwdvidsgroups')
                        {
                                $query = $db->getQuery(true)
                                        ->select('COUNT(*)')
                                        ->from('#__hwdvidsgroups');
                                try
                                {
                                        $db->setQuery($query);
                                        $retval->count = $db->loadResult();
                                }
                                catch (RuntimeException $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }
                        }
                }

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_migrator')
                        ->where($db->quoteName('element_type') . ' = ' . $db->quote(3))
                        ->where($db->quoteName('status') . ' = ' . $db->quote(1));
                try
                {
                        $db->setQuery($query);
                        $retval->migrated = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $retval;
	}

        /**
	 * Method to count the video playlists for migration.
         * 
         * @access  public
	 * @return  mixed   The integer count on success, false on failure.
	 */
	public function getVideoPlaylists()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery('SHOW TABLES');
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg('dbprefix').'hwdvidsplaylists')
                        {
                                $query = $db->getQuery(true)
                                        ->select('COUNT(*)')
                                        ->from('#__hwdvidsplaylists');
                                try
                                {
                                        $db->setQuery($query);
                                        $retval->count = $db->loadResult();
                                }
                                catch (RuntimeException $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }
                        }
                }

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_migrator')
                        ->where($db->quoteName('element_type') . ' = ' . $db->quote(4))
                        ->where($db->quoteName('status') . ' = ' . $db->quote(1));
                try
                {
                        $db->setQuery($query);
                        $retval->migrated = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $retval;
	}

        /**
	 * Method to count the photo items for migration.
         * 
         * @access  public
	 * @return  mixed   The integer count on success, false on failure.
	 */
	public function getPhotoItems()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery('SHOW TABLES');
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg('dbprefix').'hwdpsphotos')
                        {
                                $query = $db->getQuery(true)
                                        ->select('COUNT(*)')
                                        ->from('#__hwdpsphotos');
                                try
                                {
                                        $db->setQuery($query);
                                        $retval->count = $db->loadResult();
                                }
                                catch (RuntimeException $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }
                        }
                }

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_migrator')
                        ->where($db->quoteName('element_type') . ' = ' . $db->quote(5))
                        ->where($db->quoteName('status') . ' = ' . $db->quote(1));
                try
                {
                        $db->setQuery($query);
                        $retval->migrated = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $retval;
	}

        /**
	 * Method to count the photo categories for migration.
         * 
         * @access  public
	 * @return  mixed   The integer count on success, false on failure.
	 */
	public function getPhotoCategories()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery('SHOW TABLES');
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg('dbprefix').'hwdpscategories')
                        {
                                $query = $db->getQuery(true)
                                        ->select('COUNT(*)')
                                        ->from('#__hwdpscategories');
                                try
                                {
                                        $db->setQuery($query);
                                        $retval->count = $db->loadResult();
                                }
                                catch (RuntimeException $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }
                        }
                }

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_migrator')
                        ->where($db->quoteName('element_type') . ' = ' . $db->quote(6))
                        ->where($db->quoteName('status') . ' = ' . $db->quote(1));
                try
                {
                        $db->setQuery($query);
                        $retval->migrated = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $retval;
	}

        /**
	 * Method to count the photo groups for migration.
         * 
         * @access  public
	 * @return  mixed   The integer count on success, false on failure.
	 */
	public function getPhotoGroups()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery('SHOW TABLES');
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg('dbprefix').'hwdpsgroups')
                        {
                                $query = $db->getQuery(true)
                                        ->select('COUNT(*)')
                                        ->from('#__hwdpsgroups');
                                try
                                {
                                        $db->setQuery($query);
                                        $retval->count = $db->loadResult();
                                }
                                catch (RuntimeException $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }
                        }
                }

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_migrator')
                        ->where($db->quoteName('element_type') . ' = ' . $db->quote(7))
                        ->where($db->quoteName('status') . ' = ' . $db->quote(1));
                try
                {
                        $db->setQuery($query);
                        $retval->migrated = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $retval;
	}

        /**
	 * Method to count the photo albums for migration.
         * 
         * @access  public
	 * @return  mixed   The integer count on success, false on failure.
	 */
	public function getPhotoAlbums()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery('SHOW TABLES');
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg('dbprefix').'hwdpsalbums')
                        {
                                $query = $db->getQuery(true)
                                        ->select('COUNT(*)')
                                        ->from('#__hwdpsalbums');
                                try
                                {
                                        $db->setQuery($query);
                                        $retval->count = $db->loadResult();
                                }
                                catch (RuntimeException $e)
                                {
                                        $this->setError($e->getMessage());
                                        return false;                            
                                }
                        }
                }

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_migrator')
                        ->where($db->quoteName('element_type') . ' = ' . $db->quote(8))
                        ->where($db->quoteName('status') . ' = ' . $db->quote(1));
                try
                {
                        $db->setQuery($query);
                        $retval->migrated = $db->loadResult();
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                
                return $retval;
	}
}

