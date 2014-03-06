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

class hwdMediaShareTableAlbum extends JTable
{
	/**
	 * Constructor.
	 * @return	void
	 */
	function __construct($db)
	{
		parent::__construct('#__hwdms_albums', 'id', $db);
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
		// Convert the params fields to a string.
                if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
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
        
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_hwdmediashare.album.'.(int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_hwdmediashare');
		return $asset->id;
	}
}
