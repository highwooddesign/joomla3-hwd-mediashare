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

class hwdMediaShareControllerPlaylist extends JControllerForm
{
    	/**
	 * The URL view list variable.
	 * @var    string
	 */
    	protected $view_list = "playlists";
        
	/**
	 * The ID of this element type.
	 * @var    string
	 */
    	protected $elementType = 4;
}
