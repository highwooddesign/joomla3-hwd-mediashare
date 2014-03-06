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

class hwdMediaShareTableConfiguration extends JTable
{
	var $id                 = null;
	var $name		= null;
	var $date		= null;
        var $params		= null;
        var $asset_id		= null;

	/**
	 * Constructor.
	 * @return	void
	 */
	function __construct(&$db)
	{
		parent::__construct('#__hwdms_config', 'id', $db);
                
                // Here, we set the asset_id for the table. Since Joomla 2.5.7 there is new code 
                // to "Create an asset_id or heal one that is corrupted" which broke HWDMediaShare 
                // because the configuration table doesn't have an asset_id column. This is a work
                // around for that issue.                
		$name = $this->_getAssetName();
		$asset = JTable::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
		$asset->loadByName($name);
		$this->asset_id = $asset->id;
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
                // Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
                {
                        // Unset empty (inherited) rules to avoid them being set to Denied
                        foreach( $array['rules'] as $action=>$identity )
                        {
                            foreach( $identity as $rule=>$value )
                            {
                                if( $value == "" ) unset( $array['rules'][$action][$rule] );
                            }
                        }

                        $this->setRules($array['rules']);
                        //@TODO: Remove this
                        $this->_trackAssets = true;
		}
		return parent::bind($array, $ignore);
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
		return 'com_hwdmediashare';
        }
}
