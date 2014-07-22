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

class hwdMediaShareTableCustomField extends JTable
{
	/**
	 * Class constructor. Overridden to explicitly set the table and key fields.
	 *
	 * @access	public
	 * @param       JDatabaseDriver  $db     JDatabaseDriver object.
         * @return      void
	 */  
	public function __construct($db)
	{
		parent::__construct('#__hwdms_fields', 'id', $db);
	}
        
	/**
	 * Method to bind an associative array or object to the JTable instance.
	 *
         * @access  public
	 * @param   mixed   $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed   $ignore  An optional array or space separated list of properties to ignore while binding.
	 * @return  boolean True on success.
	 * @link    http://docs.joomla.org/JTable/bind
	 * @throws  InvalidArgumentException
	 */
	public function bind($src, $ignore = '')
	{        
                // Initialise variables.
                $app = JFactory::getApplication();
                
                // Load params from the request.
		$data = $app->input->get('params', array(), 'array');

		// Convert the params fields to a string.
                if (isset($data) && is_array($data) && count($data) > 0)
		{
			$registry = new JRegistry;
			$registry->loadArray($data);
			$src['params'] = (string) $registry;
		}       
                
		return parent::bind($src, $ignore);
	}
}
