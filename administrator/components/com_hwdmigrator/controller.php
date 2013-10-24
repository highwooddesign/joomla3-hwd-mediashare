<?php
/**
 * @version    SVN $Id: controller.php 425 2012-06-28 07:48:57Z dhorsfall $
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
 * General Controller of hwdMigrator component
 */
class hwdMigratorController extends JControllerLegacy {
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
		hwdMigratorHelper::addSubmenu(JRequest::getCmd('view', 'dashboard'));
	}
}
