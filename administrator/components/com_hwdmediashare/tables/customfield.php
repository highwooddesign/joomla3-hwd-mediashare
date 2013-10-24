<?php
/**
 * @version    SVN $Id: customfield.php 496 2012-08-29 13:26:32Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla table library
jimport('joomla.database.table');

/**
 * Custom field table class
 */
class hwdMediaShareTableCustomField extends JTable
{
	var $id = null;

        /**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__hwdms_fields', 'id', $db);
	}
        
	/**
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see         JTable:bind
	 * @since       0.1
	 */
	public function bind($array, $ignore = '') 
	{
                if (isset($_REQUEST['params']) && is_array($_REQUEST['params']))
		{
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($_REQUEST['params']);
			$array['params'] = (string)$parameter;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded load function
	 *
	 * @param       int $pk primary key
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see         JTable:load
	 */
	public function load($pk = null, $reset = true) 
	{
		if (parent::load($pk, $reset)) 
		{
			// Convert the params field to a registry.
			$params = new JRegistry;
			$params->loadString($this->params);
			$this->params = $params;
			return true;
		}
		else
		{
			return false;
		}
	}
}
