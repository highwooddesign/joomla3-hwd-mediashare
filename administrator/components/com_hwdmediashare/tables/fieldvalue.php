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

class hwdMediaShareTableFieldValue extends JTable
{
	var $id 		= null;
	var $element_type	= null;
	var $field_id	        = null;
	var $value		= null;
	var $access		= null;

	/**
	 * Constructor.
	 * @return	void
	 */
	function __construct($db)
	{
                parent::__construct('#__hwdms_fields_values', 'id', $db);
	}
        
	/**
	 * Overloaded load function
	 *
	 * @param       int     $pk     primary key
	 * @param       boolean $reset  reset data
         * 
	 * @return      boolean
	 */
	public function load($elementType, $elementId, $fieldId)
	{
		$db = $this->getDBO();
                
		$query	= 'SELECT * FROM ' . $db->quoteName( '#__hwdms_fields_values' ) . ' '
				. 'WHERE ' . $db->quoteName('field_id') . ' = ' . $db->Quote( $fieldId ) . ''
                                . 'AND ' . $db->quoteName('element_type') . '=' . $db->Quote( $elementType ) . ''
                                . 'AND ' . $db->quoteName('element_id') . '=' . $db->Quote( $elementId );
		$db->setQuery($query);
		if ($result = $db->loadObject())
                {
                        return $this->bind($result);
                }
                else
                {
                        return false;
                }
	}
}
