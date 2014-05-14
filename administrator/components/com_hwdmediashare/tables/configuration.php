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
	/**
	 * Constructor.
	 * @return	void
	 */
	function __construct($db)
	{
		parent::__construct('#__hwdms_config', 'id', $db);
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
                        foreach($array['rules'] as $action=>$identity)
                        {
                                foreach($identity as $rule=>$value)
                                {
                                        if($value == "") unset($array['rules'][$action][$rule]);
                                }
                        }

                        $this->setRules($array['rules']);
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
