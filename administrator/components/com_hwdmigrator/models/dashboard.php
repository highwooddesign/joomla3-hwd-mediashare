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

class hwdMigratorModelDashboard extends JModelAdmin
{
	public function getVideoItems($pk = null)
	{
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdvidsvideos')
                        {
                                $query = "
                                    SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdvidsvideos')."
                                ";
                                $db->setQuery($query);
                                $retval->count = $db->loadResult();
                        }
                }

                $query = "
                    SELECT count(*)
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('1')."
                    AND ".$db->quoteName('status')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $retval->migrated = $db->loadResult();

                return $retval;
	}

	public function getVideoCategories($pk = null)
	{
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdvidscategories')
                        {
                                $query = "
                                    SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdvidscategories')."
                                ";
                                $db->setQuery($query);
                                $retval->count = $db->loadResult();
                        }
                }

                $query = "
                    SELECT count(*)
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('2')."
                    AND ".$db->quoteName('status')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $retval->migrated = $db->loadResult();

                return $retval;
	}

	public function getVideoGroups($pk = null)
	{
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdvidsgroups')
                        {
                                $query = "
                                    SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdvidsgroups')."
                                ";
                                $db->setQuery($query);
                                $retval->count = $db->loadResult();
                        }
                }

                $query = "
                    SELECT count(*)
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('3')."
                    AND ".$db->quoteName('status')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $retval->migrated = $db->loadResult();

                return $retval;
	}

	public function getVideoPlaylists($pk = null)
	{
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdvidsplaylists')
                        {
                                $query = "
                                    SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdvidsplaylists')."
                                ";
                                $db->setQuery($query);
                                $retval->count = $db->loadResult();
                        }
                }

                $query = "
                    SELECT count(*)
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('4')."
                    AND ".$db->quoteName('status')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $retval->migrated = $db->loadResult();

                return $retval;
	}

	public function getPhotoItems($pk = null)
	{
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdpsphotos')
                        {
                                $query = "
                                    SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdpsphotos')."
                                ";
                                $db->setQuery($query);
                                $retval->count = $db->loadResult();
                        }
                }

                $query = "
                    SELECT count(*)
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('5')."
                    AND ".$db->quoteName('status')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $retval->migrated = $db->loadResult();

                return $retval;
	}

	public function getPhotoCategories($pk = null)
	{
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdpscategories')
                        {
                                $query = "
                                    SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdpscategories')."
                                ";
                                $db->setQuery($query);
                                $retval->count = $db->loadResult();
                        }
                }

                $query = "
                    SELECT count(*)
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('6')."
                    AND ".$db->quoteName('status')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $retval->migrated = $db->loadResult();

                return $retval;
	}

	public function getPhotoGroups($pk = null)
	{
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdpsgroups')
                        {
                                $query = "
                                    SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdpsgroups')."
                                ";
                                $db->setQuery($query);
                                $retval->count = $db->loadResult();
                        }
                }

                $query = "
                    SELECT count(*)
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('7')."
                    AND ".$db->quoteName('status')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $retval->migrated = $db->loadResult();

                return $retval;
	}

	public function getPhotoAlbums($pk = null)
	{
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                $retval = new stdClass;
                $retval->count = "0";
                $retval->migrated = "0";

                $db->setQuery( 'SHOW TABLES' );
                $tables = $db->loadColumn();

                foreach ($tables as $table)
                {
                        if ($table == $app->getCfg( 'dbprefix' ).'hwdpsalbums')
                        {
                                $query = "
                                    SELECT count(*)
                                    FROM ".$db->quoteName('#__hwdpsalbums')."
                                ";
                                $db->setQuery($query);
                                $retval->count = $db->loadResult();
                        }
                }

                $query = "
                    SELECT count(*)
                    FROM ".$db->quoteName('#__hwdms_migrator')."
                    WHERE ".$db->quoteName('element_type')." = ".$db->quote('8')."
                    AND ".$db->quoteName('status')." = ".$db->quote('1')."
                ";
                $db->setQuery($query);
                $retval->migrated = $db->loadResult();

                return $retval;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		parent::getForm($data, $loadData);
	}
}
