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

class hwdMediaShareModelActivities extends JModelList
{
	/**
	 * Model context string.
	 * @var string
	 */
	public $context = 'com_hwdmediashare.activities';

	/**
	 * Model data
	 * @var array
	 */
	protected $_items = null;
        
    	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'verb', 'a.verb',
				'created', 'a.created',
				'id', 'a.id',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if ($items = parent::getItems())
		{            
                        for ($x = 0, $count = count($items); $x < $count; $x++)
                        {
                                if (empty($items[$x]->author)) $items[$x]->author = JText::_('COM_HWDMS_GUEST');
                        }
                }

		return $items;
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @return  JDatabaseQuery  database query
	 */
        public function getListQuery()
        {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.actor, a.action, a.target, a.verb, a.created, a.access, a.params'
			)
		);
                
                // From the activities table.
                $query->from('#__hwdms_activity AS a');

                // Join over the users for the author, with value based on configuration.
                $config->get('author') == 0 ? $query->select('ua.name AS author') : $query->select('ua.username AS author');
		$query->join('LEFT', '#__users AS ua ON ua.id=a.actor');

		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');
		if (!empty($listOrder) && !empty($listDirn))
		{
                        $query->order($db->escape($listOrder.' '.$listDirn));                
		}    

                // Filter by actor.
		if ($actor = $this->getState('filter.actor'))
                {
                        $query->where('a.actor = ' . $db->quote($actor));
		}
                
                // Filter by action and targets.
		$action = $this->getState('filter.action');
                $target = $this->getState('filter.target');
                if ($action && $target)
                {
                        $query->where('(a.action = ' . $db->quote($action) . ' OR a.target = ' . $db->quote($target) . ')');
		}
                else if ($action)
                {
                        $query->where('a.action = ' . $db->quote($action));
                }
                else if ($target)
                {
                        $query->where('a.target = ' . $db->quote($target));
		}
                
                // Filter by verb.
		$verb = $this->getState('filter.verb');
		if (is_array($verb)) 
                {
			JArrayHelper::toInteger($verb);
			$verb = implode(',', $verb);
			if ($verb) 
                        {
                                $query->where('a.verb IN ('.$verb.')');
			}
		}
                else if (is_numeric($verb))
                {
			$query->where('a.verb = '.(int) $verb);
		}

                //echo nl2br(str_replace('#__','jos_',$query));
		return $query;
        }
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $user = JFactory::getUser();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);
                
		// List state information.
		parent::populateState('a.created', 'desc');
	}      
}
