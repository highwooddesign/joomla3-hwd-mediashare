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

class hwdMediaShareTableLinkedAlbums extends JTable
{
	/**
	 * Class constructor. Overridden to explicitly set the table and key fields.
	 *
	 * @access  public
	 * @param   JDatabaseDriver  $db  JDatabaseDriver object.
         * @return  void
	 */ 
	public function __construct($db)
	{
		parent::__construct('#__hwdms_album_map', 'id', $db);
	}
}
