<?php
/**
 * @version    SVN $Id: hwdmigrator.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMigrator component helper
 */
abstract class hwdMigratorHelper
{
        /**
	 * Configure the Linkbar
	 */
	public static function addSubmenu($submenu)
	{
		// JSubMenuHelper::addEntry(JText::_('COM_HWDMIGRATOR_DASHBOARD'), 'index.php?option=com_hwdmigrator&view=dashboard', $submenu == 'dashboard');
		// Set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-hwdmigrator {background-image: url(../media/com_hwdmigrator/assets/images/icons/48/icon-48-hwdmigrator.png);padding-left:60px!important;}');
        }
}
