<?php
/**
 * @version    SVN $Id: media.php 1541 2013-05-31 12:17:41Z dhorsfall $
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
 * Media table class
 */
class hwdMediaShareTableMedia extends JTable
{
	var $id = null;
	var $key = null;
        
        /**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct( '#__hwdms_media', 'id', $db );
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
		// Convert the params field to a string.
                if (isset($array['params']) && is_array($array['params']))
		{
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			//$array['params'] = (string)$parameter;

                        /** We don't want to loose the existing password! So we check if a new one has been passed, then merge the old one if empty **/
                        $new = $parameter->get('password'); 
                        if (empty($new))
                        {                                
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                                $table->load( $array['id'] );
                                $properties = $table->getProperties(1);
                                $row = JArrayHelper::toObject($properties, 'JObject');
                                $old = $row->params->get('password');                                
                                if (!empty($old)) $parameter->set('password',$row->params->get('password'));
                        }
                        
                        // Go ahead and set the params again
			$array['params'] = (string)$parameter;
		}

                // Bind the rules. 
		if (isset($array['rules']) && is_array($array['rules'])) 
                { 
			$rules = new JRules($array['rules']); 
			$this->setRules($rules); 
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
        
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_hwdmediashare.media.'.(int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 * @since	1.6
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_hwdmediashare');
		return $asset->id;
	}
}
