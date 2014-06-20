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

class hwdMediaShareControllerExtensions extends JControllerAdmin
{
        /**
	 * Proxy for getModel.
	 *
	 * @access  public
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.          
         * @return  object  The model.
	 */
	public function getModel($name = 'Extension', $prefix = 'hwdMediaShareModel', $config = array())
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
}
