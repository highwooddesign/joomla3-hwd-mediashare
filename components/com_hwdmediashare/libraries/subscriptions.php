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

class hwdMediaShareSubscriptions extends JObject
{
	/**
	 * The element type to use with this library.
         * 
         * @access      public
	 * @var         string
	 */
	public $elementType = 5;

	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed  $properties  Either and associative array or another
	 *                              object to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareSubscriptions object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareSubscriptions Object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareSubscriptions';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to subscribe a user to an item.
         * 
         * @access  public
         * @param   array   $pks    An array of record primary keys.
         * @return  boolean True on success.
	 */
	public function subscribe($pks)
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
                
                if (!$user->authorise('hwdmediashare.subscribe', 'com_hwdmediashare'))
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
                                ->from('#__hwdms_subscriptions')
                                ->where('element_type = ' . $db->quote($this->elementType))
                                ->where('element_id = ' . $db->quote($pk))
                                ->where('user_id = ' . $db->quote($user->id));
                        try
                        {                
                                $db->setQuery($query);
                                $subscribed = $db->loadResult();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }

                        if(!$subscribed)
                        {
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table = JTable::getInstance('Subscription', 'hwdMediaShareTable');    

                                // Create an object to bind to the database.
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
	 * Method to unsubscribe a user to an item.
         * 
         * @access  public
         * @param   array   $pks    An array of record primary keys.
         * @return  boolean True on success.
	 */
	public function unsubscribe($pks)
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

                if (!$user->authorise('hwdmediashare.subscribe', 'com_hwdmediashare'))
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

                        $query->delete($db->quoteName('#__hwdms_subscriptions'));
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
	 * Method to check if a user is subscribed to an item.
         * 
         * @access  public
         * @param   integer $pk    The primary key to check.
         * @return  boolean True if favourite.
	 */
	public function isSubscribed($pk)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                $db = JFactory::getDBO();
                $pk = (int) $pk;
                
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_subscriptions')
                        ->where('element_type = ' . $db->quote($this->elementType))
                        ->where('element_id = ' . $db->quote($pk))
                        ->where('user_id = ' . $db->quote($user->id));
                try
                {                
                        $db->setQuery($query);
                        $subscribed = $db->loadResult();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }

                if (!$subscribed)
                {
                        return false;
                }
                
                return true;
	}
}
