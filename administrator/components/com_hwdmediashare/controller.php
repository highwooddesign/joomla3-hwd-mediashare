<?php
/**
 * @version    SVN $Id: controller.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of hwdMediaShare component
 */
class hwdMediaShareController extends JControllerLegacy {
	/**
	 * Display task
	 *
	 * @return void
	 */
	function display($cachable = false)
	{
		// Set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'dashboard'));

                // Call parent behavior
		parent::display($cachable);

                // Set the submenu
		hwdMediaShareHelper::addSubmenu(JRequest::getCmd('view', 'dashboard'));
	}
}
