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

// Import Joomla table library
jimport('joomla.database.table');

class hwdMediaShareTableCustomField extends JTable
{
	/**
	 * Constructor.
	 * @return	void
	 */
	function __construct($db)
	{
		parent::__construct('#__hwdms_fields', 'id', $db);
	}
        
	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array to bind
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 */
	public function bind($array, $ignore = '')
	{
		$data = JFactory::getApplication()->input->get('params', array(), 'array');

		// Convert the params fields to a string.
                if (isset($data) && is_array($data) && count($data) > 0)
		{
			$registry = new JRegistry;
			$registry->loadArray($data);
			$array['params'] = (string) $registry;
		}               
		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded load function
	 *
	 * @param       int     $pk     primary key
	 * @param       boolean $reset  reset data
         * 
	 * @return      boolean
	 */
	public function load($pk = null, $reset = true) 
	{
		if (parent::load($pk, $reset)) 
		{
                        // Convert the params string to an array.
                        if (property_exists($this, 'params'))
                        {
                                $registry = new JRegistry;
                                $registry->loadString($this->params);
                                $this->params = $registry;
                        }                    
			return true;
		}
		else
		{
			return false;
		}
	}
}
