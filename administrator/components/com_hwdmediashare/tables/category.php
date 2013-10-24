<?php
/**
 * @version    SVN $Id: category.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      27-Nov-2011 17:24:29
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla table library
jimport('joomla.database.table');
jimport('joomla.database.tablenested');

/**
 * Category table class
 */
class hwdMediaShareTableCategory extends JTableNested
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__categories', 'id', $db);
	}
}