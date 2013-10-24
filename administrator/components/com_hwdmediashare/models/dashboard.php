<?php
/**
 * @version    SVN $Id: dashboard.php 1136 2013-02-21 11:05:01Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelDashboard extends JModelAdmin
{
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getMedia($pk = null)
	{
                // Create a new query object.
                $db =& JFactory::getDBO();

                $query = "
                    SELECT 
                        created,
                        DATE_FORMAT(created, '%d') AS day,
                        COUNT(*) AS total 
                    FROM 
                        ".$db->quoteName('#__hwdms_media')."
                    WHERE 
                        ".$db->quoteName('created')." > (NOW() - INTERVAL 30 DAY)
                    GROUP BY 
                        DATE_FORMAT(created, '%d')
                    ORDER BY 
                        created ASC
                ";
                $db->setQuery($query);
                $rows = $db->loadObjectList();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $rows;
                }
	}
          
        /**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	0.1
	 */
	public function getTable($type = 'Media', $prefix = 'hwdMediaShareTable', $config = array())
	{
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                return JTable::getInstance($type, $prefix, $config);
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
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getCountMedia($pk = null)
	{
                // Create a new query object.
                $db =& JFactory::getDBO();

                $query = "SELECT COUNT(*) FROM `#__hwdms_media`";
                $db->setQuery($query);
                $count = $db->loadResult();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $count;
                }
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getCountCategories($pk = null)
	{
                // Create a new query object.
                $db =& JFactory::getDBO();

                $query = "SELECT COUNT(*) FROM `#__categories` WHERE `extension` = 'com_hwdmediashare'";
                $db->setQuery($query);
                $count = $db->loadResult();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $count;
                }
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getCountAlbums($pk = null)
	{
                // Create a new query object.
                $db =& JFactory::getDBO();

                $query = "SELECT COUNT(*) FROM `#__hwdms_albums`";
                $db->setQuery($query);
                $count = $db->loadResult();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $count;
                }
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getCountGroups($pk = null)
	{
                // Create a new query object.
                $db =& JFactory::getDBO();

                $query = "SELECT COUNT(*) FROM `#__hwdms_groups`";
                $db->setQuery($query);
                $count = $db->loadResult();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $count;
                }
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getCountChannels($pk = null)
	{
                // Create a new query object.
                $db =& JFactory::getDBO();

                $query = "SELECT COUNT(*) FROM `#__hwdms_users`";
                $db->setQuery($query);
                $count = $db->loadResult();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $count;
                }
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getCountPlaylists($pk = null)
	{
                // Create a new query object.
                $db =& JFactory::getDBO();

                $query = "SELECT COUNT(*) FROM `#__hwdms_playlists`";
                $db->setQuery($query);
                $count = $db->loadResult();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $count;
                }
	}  
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getVersion($pk = null)
	{
                jimport( 'joomla.application.component.helper' );
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
