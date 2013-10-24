<?php
/**
 * @version    SVN $Id: subscriptions.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      07-Nov-2011 21:21:57
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerSubscriptions extends JControllerAdmin
{
        /**
	 * Proxy for getModel.
	 * @since	0.1
	 */
	public function getModel($name = 'Subscription', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
}
