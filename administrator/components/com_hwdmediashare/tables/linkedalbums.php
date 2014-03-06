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

// Import Joomla table library
jimport('joomla.database.table');

class hwdMediaShareTableLinkedAlbums extends JTable
{
	/**
	 * Constructor.
	 * @return	void
	 */
	function __construct($db)
	{
		parent::__construct('#__hwdms_album_map', 'id', $db);
	}
}
