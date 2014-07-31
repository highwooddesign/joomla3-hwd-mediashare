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

class hwdMediaShareCategory extends JObject
{
	/**
	 * The element type to use with this library.
         * 
         * @access      public
	 * @var         string
	 */
	public $elementType = 1;
        
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
	 * Returns the hwdMediaShareCategory object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareCategory Object.
	 */ 
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareCategory';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to save categories.
	 *
	 * @access  public
	 * @param   array    $pks    A list of the category keys to assign.
	 * @param   integer  $value  The primary key of the element.
	 * @return  hwdMediaShareCategory Object.
	 */ 
        public function save($pks, $value = 0)
	{
		// Initialiase variables.
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $date = JFactory::getDate();
                
		// Sanitize the ids.
		$value = (int) $value;
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);
                
                if (!$value)
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }

                // Remove the existing category map.
                $query = $db->getQuery(true);

                $conditions = array(
                    $db->quoteName('element_type') . ' = ' . $db->quote($this->elementType), 
                    $db->quoteName('element_id') . ' = ' . $db->quote($value), 
                );
                
                $query->delete($db->quoteName('#__hwdms_category_map'));
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
   
		foreach ($pks as $i => $pk)
		{
                        if (empty($pk))
                        {
                                $this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
                                return false;
                        }

                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('CategoryMap', 'hwdMediaShareTable');    

                        // Create an object to bind to the database.
                        $object = new StdClass;
                        $object->element_type = $this->elementType;
                        $object->element_id = $value;
                        $object->category_id = $pk;
                        $object->created_user_id = $user->id;
                        $object->created = $date->toSql();

                        // Attempt to save the details to the database.
                        if (!$table->save($object))
                        {
                                $this->setError($table->getError());
                                return false;
                        }

		}

		return true;
        }
	/**
	 * Method to save an individual category to an item.
	 *
	 * @access  public
	 * @param   integer  $pk     The primary category key to assign.
	 * @param   integer  $value  The primary key of the element.
	 * @return  hwdMediaShareCategory Object.
	 */ 
        public function saveIndividual($pk, $value = 0)
        {
		// Initialiase variables.
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $date = JFactory::getDate();
                
		// Sanitize the ids.
		$value = (int) $value;
		$pk = (int) $pk;               

                if (!$value)
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }

                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_category_map')
                        ->where('element_type = ' . $db->quote($this->elementType))
                        ->where('element_id = ' . $db->quote($value))
                        ->where('category_id = ' . $db->quote($pk));
                try
                {                
                        $db->setQuery($query);
                        $result = $db->loadResult();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }

                if(!$result)
                {
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('CategoryMap', 'hwdMediaShareTable');    

                        // Create an object to bind to the database.
                        $object = new StdClass;
                        $object->element_type = $this->elementType;
                        $object->element_id = $value;
                        $object->category_id = $pk;
                        $object->created_user_id = $user->id;
                        $object->created = $date->toSql();

                        // Attempt to save the details to the database.
                        if (!$table->save($object))
                        {
                                $this->setError($table->getError());
                                return false;
                        }
                }

                return true;
        }
        
	/**
	 * Method to get categories of an item.
	 *
	 * @access  public
	 * @param   object  $item   The item to check.
	 * @return  array   An array of categories assigned to the item.
	 */ 
        public function load($item)
        {
		// Initialiase variables.
                $db = JFactory::getDBO();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $query = $db->getQuery(true)
                        ->select('c.id, c.title')
                        ->from('#__hwdms_category_map AS cmap')
                        ->join('LEFT', '#__categories AS c ON c.id = cmap.category_id')
                        ->where($db->quoteName('cmap.element_id').' = '.$db->quote($item->id))
                        ->where($db->quoteName('cmap.element_type').' = '.$db->quote($this->elementType))
                        ->where($db->quoteName('c.id').' > 0'); // Make sure we don't get any invalid categories from a broken map
                
                // Restrict based on access.
                if ($config->get('entice_mode') == 0)
                {
                        $query->where('c.access IN ('.$groups.')');
                }         
                
                // Filter by published category.
		if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) && (!$user->authorise('core.edit', 'com_hwdmediashare')))
                {
			$query->where('c.published IN (1)');
		}
                else
                {
                        $query->where('c.published IN (0,1)');
                }
                
                try
                {                
                        $db->setQuery($query);
                        $result = $db->loadObjectList();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }
                
                return $result;
        }
        
	/**
	 * Method to get categories of an item (in format for a form input).
	 *
	 * @access  public
	 * @param   object  $item   The item to check.
	 * @return  array   An array of categories assigned to the item.
	 */ 
        public function getInputValue($item)
        {
                // Get categories.
                $categories = $this->load($item);

                $return = array();
                foreach ($categories as $category)
                {
                        $return[] = (int) $category->id;
                }

                return $return;
        }
        
        /**
	 * Method to render the HTML to display a list of categories.
	 *
	 * @access  public
         * @static
	 * @param   object  $item   The item.
	 * @return  string  The HTML to display the categories.
	 */ 
	public static function renderCategories($item)
	{            
                if (!isset($item))
                {
                        return false;
                }

                $links = array();
                
                if (count($item->categories) > 0)
                {
                        foreach ($item->categories as $value)
                        {
                                $links[] = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($value->id)).'">' . $value->title . '</a>';
                        }
                }
                else
                {
                        return false;
                }             

                return implode(", ", $links);
	}        
}
