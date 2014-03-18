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

class hwdMediaShareModelPlaylistMediaItem extends JModelAdmin
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'LinkedPlaylists', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
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
	 * Method to unlink one or more media items with a playlist.
	 *
	 * @param   array    $pks         A list of the primary keys to change.
	 * @param   integer  $playlistId  The value of the playlist key to associate with.
	 *
	 * @return  boolean  True on success.
	 */
        public function unlink($pks, $playlistId = null)
        {
                $db = JFactory::getDbo();
		$pks = (array) $pks;
		$table = $this->getTable();            
            
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_playlist_map')
                                ->where('playlist_id = ' . $db->quote($playlistId))
                                ->where('media_id = ' . $db->quote($pk));

                        $db->setQuery($query);
                        try
                        {
                                $rows = $db->loadColumn();
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());

                                return false;                            
                        }

                        // Iterate the items to delete each one.
                        foreach ($rows as $x => $row)
                        {
                                if ($table->load($row))
                                {
                                        if ($utilities->authorisePlaylistAction('unlink', $playlistId, $pk))
                                        {
                                                if (!$table->delete($row))
                                                {
                                                        $this->setError($table->getError());

                                                        return false;
                                                }
                                        }
                                        else
                                        {
                                                // Prune items that you can't change.
                                                unset($rows[$x]);
                                                $error = $this->getError();

                                                if ($error)
                                                {
                                                        JLog::add($error, JLog::WARNING, 'jerror');

                                                        return false;
                                                }
                                                else
                                                {
                                                        JLog::add(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), JLog::WARNING, 'jerror');

                                                        return false;
                                                }
                                        }
                                }
                                else
                                {
                                        $this->setError($table->getError());

                                        return false;
                                }
                        }
                        
                        // Reorder this playlist
                        $table->reorder(' playlist_id = '.$pk.' ');
		}

		// Clear the component's cache
		$this->cleanCache();
             
		return true;
        }

	/**
	 * Method to link one or more media items with a playlist.
	 *
	 * @param   array    $pks         A list of the primary keys to change.
	 * @param   integer  $playlistId  The value of the playlist key to associate with.
	 *
	 * @return  boolean  True on success.
	 */
	public function link($pks, $playlistId = null)
	{
		$user = JFactory::getUser();
                $date = JFactory::getDate();
		$table = $this->getTable();
		$pks = (array) $pks;

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                                
		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

                        if (!$utilities->authorisePlaylistAction('link', $playlistId, $pk))
                        {
                                // Prune items that you can't change.
                                unset($pks[$i]);
                                JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');

                                return false;
                        }
                        
                        // Create an object to bind to the database
                        $object = new StdClass;
                        $object->playlist_id = (int) $playlistId;
                        $object->media_id = (int) $pk;
                        $object->created_user_id = (int) $user->id;
                        $object->created = $date->format('Y-m-d H:i:s');

                        // Attempt to change the state of the records.
                        if (!$table->save($object))
                        {
                                $this->setError($table->getError());
                                return false;
                        }

                        // Reorder this playlist
                        $table->reorder(' playlist_id = '.$playlistId.' ');                        
		}

		// Clear the component's cache
		$this->cleanCache();

                return true;
	}
       
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'playlist_id = '.(int) $table->playlist_id;
                return $condition;
	}      
}
