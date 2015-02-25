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

JFormHelper::loadFieldClass('list');
JFormHelper::loadFieldClass('limitbox');

class JFormFieldHwdLimitBox extends JFormFieldLimitbox
{
	/**
	 * The name of the form field type.
         * 
         * @access  protected
	 * @var     string
	 */
	public $type = 'HwdLimitBox';

	/**
	 * Method to get the field options.
	 *
	 * @access  protected
	 * @return  array      The field option objects.
	 */
	protected function getOptions()
	{
                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $this->defaultLimits = array();
                
                // Set limit options (up tp a maximu of 150)
                if (($config->get('list_limit', 6) * 1) <= 150) $this->defaultLimits[] = (int) $config->get('list_limit', 6) * 1;
                if (($config->get('list_limit', 6) * 2) <= 150) $this->defaultLimits[] = (int) $config->get('list_limit', 6) * 2;
                if (($config->get('list_limit', 6) * 3) <= 150) $this->defaultLimits[] = (int) $config->get('list_limit', 6) * 3;
                if (($config->get('list_limit', 6) * 4) <= 150) $this->defaultLimits[] = (int) $config->get('list_limit', 6) * 4;
                if (($config->get('list_limit', 6) * 5) <= 150) $this->defaultLimits[] = (int) $config->get('list_limit', 6) * 5;

                return parent::getOptions();
	}
}
