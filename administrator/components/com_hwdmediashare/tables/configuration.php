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

class hwdMediaShareTableConfiguration extends JTable
{
	/**
	 * Class constructor. Overridden to explicitly set the table and key fields.
	 *
	 * @access  public
	 * @param   JDatabaseDriver  $db  JDatabaseDriver object.
         * @return  void
	 */ 
	public function __construct($db)
	{
		parent::__construct('#__hwdms_config', 'id', $db);
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.
	 *
         * @access  public
	 * @param   mixed    $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed    $ignore  An optional array or space separated list of properties to ignore while binding.
	 * @return  boolean  True on success.
	 * @link    http://docs.joomla.org/JTable/bind
	 * @throws  InvalidArgumentException
	 */
	public function bind($src, $ignore = '')
        {
                // Bind the rules.
		if (isset($src['rules']) && is_array($src['rules']))
                {
                        // Unset empty (inherited) rules to avoid them being set to Denied
                        foreach($src['rules'] as $action=>$identity)
                        {
                                foreach($identity as $rule=>$value)
                                {
                                        if($value == "") unset($src['rules'][$action][$rule]);
                                }
                        }

                        $this->setRules($src['rules']);
		}
                
		return parent::bind($src, $ignore);
	}

	/**
	 * Method to compute the default name of the asset.
	 *
	 * @access  protected
	 * @return  string
	 */
        protected function _getAssetName()
        {
		return 'com_hwdmediashare';
        }
}
