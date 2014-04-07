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
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'HwdLimitBox';

	/**
	 * Method to get the options to populate to populate list
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
                // Get HWD config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $this->defaultLimits = array(
                                        (int) $config->get('list_limit', 6)*1,
                                        (int) $config->get('list_limit', 6)*2,
                                        (int) $config->get('list_limit', 6)*3,
                                        (int) $config->get('list_limit', 6)*4,
                                        (int) $config->get('list_limit', 6)*5
                                       );

                return parent::getOptions();
	}
}
