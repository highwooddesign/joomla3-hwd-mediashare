<?php
/**
 * @version    SVN $Id: linkedplaylists.php 164 2012-01-29 15:40:23Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      24-Oct-2011 17:31:09
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla table library
jimport('joomla.database.table');

/**
 * Linked playlist table class
 */
class hwdMediaShareTableLinkedPlaylists extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__hwdms_playlist_map', 'id', $db);
	}
}