<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareFavourites extends JObject
{
    	/**
	 * Library variabled.
	 * @var int
	 */
	public $elementType = 1;
        
    	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
	}

	/**
	 * Returns the hwdMediaShareFavourites object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareFavourites A hwdMediaShareFavourites object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareFavourites';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to add an item to a user's favourites
         * @return	void
	 */
	public function addFavourite($pks)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);
                
                if (empty($user->id))
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }
                
		foreach ($pks as $i => $pk)
		{
                        if (empty($pk))
                        {
                                $this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
                                return false;
                        }

                        $query = $db->getQuery(true)
                                ->select('COUNT(*)')
                                ->from('#__hwdms_favourites')
                                ->where('element_type = ' . $db->quote($this->elementType))
                                ->where('element_id = ' . $db->quote($pk))
                                ->where('user_id = ' . $db->quote($user->id))
                                ->group('element_id');
                        try
                        {                
                                $db->setQuery($query);
                                $db->query(); 
                                $favourite = $db->getResult();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }

                        if(!$favourite)
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table = JTable::getInstance('Favourite', 'hwdMediaShareTable');    

                                // Create an object to bind to the database
                                $object = new StdClass;
                                $object->element_type = $this->elementType;
                                $object->element_id = $pk;
                                $object->user_id = $user->id;
                                $object->created = $date->toSql();

                                // Attempt to save the details to the database.
                                if (!$table->save($object))
                                {
                                        $this->setError($table->getError());
                                        return false;
                                }
                        }
		}

		return true;
	}
        
	/**
	 * Method to remove an item to a user's favourites
         * @return	void
	 */
	public function removeFavourite($pks)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                $db = JFactory::getDBO();

		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

                if (empty($user->id))
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }
                
		foreach ($pks as $i => $pk)
		{
                        if (empty($pk))
                        {
                                $this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
                                return false;
                        }
                        
                        $query = $db->getQuery(true);

                        $conditions = array(
                            $db->quoteName('element_type') . ' = ' . $db->quote($this->elementType), 
                            $db->quoteName('element_id') . ' = ' . $db->quote($pk), 
                            $db->quoteName('user_id') . ' = ' . $db->quote($user->id), 
                        );

                        $query->delete($db->quoteName('#__hwdms_favourites'));
                        $query->where($conditions);
                        try
                        {
                                $db->setQuery($query);
                                $result = $db->query();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }
		}

		return $result;            
	}
        
 	/**
	 * Method to check if an item is in a user's favourites
         * @return	void
	 */
	public function isFavourite($pk)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                $db = JFactory::getDBO();
                $pk = (int) $pk;
                
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_favourites')
                        ->where('element_type = ' . $db->quote($this->elementType))
                        ->where('element_id = ' . $db->quote($pk))
                        ->where('user_id = ' . $db->quote($user->id));
                try
                {                
                        $db->setQuery($query);
                        $db->query(); 
                        $favourite = $db->loadResult();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }

                if (!$favourite)
                {
                        return false;
                }
                
                return true;
	}
} 
