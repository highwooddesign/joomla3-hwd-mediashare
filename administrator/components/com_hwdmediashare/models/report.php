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

class hwdMediaShareModelReport extends JModelAdmin
{
	/**
	 * Method to get a table object, and load it if necessary.
	 *
	 * @access  public
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Report', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @access  public
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed    A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		return false;
	}    
        
	/**
	 * Method to remove content that has been reported.
	 *
         * @access  public
	 * @param   array    $pks  An array of record primary keys.
	 * @return  boolean  True on success.
	 */
	public function remove($pks)
	{
		// Initialise variables.
                $user = JFactory::getUser();
                
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

                // Access check.
                if (!$user->authorise('core.edit.state', 'com_hwdmediashare'))
                {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			return false;
                }
                
		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		// Access checks.
		foreach ($pks as $i => $id)
		{
                        // Get a table instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Report', 'hwdMediaShareTable');
                        
                        // Attempt to load the table row.
                        $return = $table->load($id);

                        // Check for a table object error.
                        if ($return === false && $table->getError())
                        {
                                $this->setError($table->getError());
                                return false;
                        }
                
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');                      
                        
                        switch ($item->element_type)
                        {
                                case 1: // Media
                                        $query = $db->getQuery(true)->update($db->quoteName('#__hwdms_media'));
                                break;
                                case 2: // Album
                                        $query = $db->getQuery(true)->update($db->quoteName('#__hwdms_albums'));
                                break;
                                case 3: // Group
                                        $query = $db->getQuery(true)->update($db->quoteName('#__hwdms_groups'));
                                break;
                                case 4: // Playlist
                                        $query = $db->getQuery(true)->update($db->quoteName('#__hwdms_playlists'));
                                break;
                                case 5: // Channel
                                        $query = $db->getQuery(true)->update($db->quoteName('#__hwdms_users'));
                                break;
                        }
                        
                        try
                        {
                                // Complete the query to trash the content.
                                $query->set('published = ' . $db->quote(-2))
                                      ->where('id = ' . $db->quote($item->element_id));                            
                                
                                $db->setQuery($query);
                                $db->execute();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }
                        
                        // Now we dismiss the report also.
                        if (!parent::delete($pks))
                        {
                                return false;
                        }
		}

		// Clear the component's cache.
		$this->cleanCache();

		return true;
	}        
}
