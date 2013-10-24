<?php
/**
 * @version    SVN $Id: tagmap.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla table library
jimport('joomla.database.table');

/**
 * Tag map table class
 */
class hwdMediaShareTableTagMap extends JTable
{
	var $id = null;

        /**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
                parent::__construct('#__hwdms_tag_map', 'id', $db);
	}
}
