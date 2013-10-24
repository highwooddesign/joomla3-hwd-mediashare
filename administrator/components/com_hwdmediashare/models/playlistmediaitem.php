<?php
/**
 * @version    SVN $Id: playlistmediaitem.php 846 2013-01-07 10:01:03Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      08-Dec-2011 17:21:21
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelPlaylistMediaItem extends JModelAdmin
{
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	0.1
	 */
	public function getTable($type = 'LinkedPlaylists', $prefix = 'hwdMediaShareTable', $config = array())
	{
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
		// Get the form.
		$form = $this->loadForm('com_hwdmediashare.playlist', 'playlist', array('control' => 'jform', 'load_data' => $loadData));
                if (empty($form))
		{
			return false;
		}
		return $form;
	}
       /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        public function unlink($id, $params)
        {
                $table =& JTable::getInstance('LinkedPlaylists', 'hwdMediaShareTable');

                $db =& JFactory::getDBO();
                $query = "
                  SELECT id
                    FROM ".$db->quoteName('#__hwdms_playlist_map')."
                    WHERE ".$db->quoteName('media_id')." = ".$db->quote($id)." AND ".$db->quoteName('playlist_id')." = ".$db->quote($params->playlistId).";
                  ";
                $db->setQuery($query);
                $rows = $db->loadObjectList();

		for( $i = 0; $i < count($rows); $i++ )
		{
			$row = $rows[$i];

                        if( !$table->delete( $row->id ) )
			{
				$errors	= true;
			}
		}
		if( $errors )
		{
			$message = JText::_('COM_HWDMS_ERROR');
		}

                return true;
        }
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return      string  An SQL query
         */
        public function link($id, $params)
        {
                $table = $this->getTable();

                // Create an object to bind to the database
                $object = new StdClass;
                $object->media_id = $id;
                $object->playlist_id = $params->playlistId;
                $object->ordering = 1000;

                if (!$table->bind($object))
                {
                        return JError::raiseWarning( 500, $table->getError() );
                }

                if (!$table->store())
                {
                        JError::raiseError(500, $table->getError() );
                }
                
                // Reorder this playlist in integer increments
                $where = ' playlist_id = '.$params->playlistId.' ';
                $table->reorder($where);
                
                return true;
        }
                
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'playlist_id = '.(int) $table->playlist_id;
		return $condition;
	}        
}






