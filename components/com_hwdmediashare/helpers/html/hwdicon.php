<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class JHtmlHwdIcon
{
	/**
	 * Method to return the icon url showing the media type.
         * 
         * @access  public
         * @param   string  $type   The type of icon to display.
         * @param   object  $item   The item being dsplayed.
         * @return  string  The url of the icon.
	 */
	public static function overlay($type, $item = null)
	{
                switch ($type) 
                {
                        case "1-1":
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/audio.png';
                        break;
                        case "1-2":
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/document.png';
                        break;
                        case "1-3":
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/image.png';
                        break;
                        case "1-4":
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/video.png';
                        break;
                        case 2:
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/album.png';
                        break;
                        case 3:
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/group.png';
                        break;
                        case 4:
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/playlist.png';
                        break;
                        case 5:
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/user.png';
                        break;
                        case 6:
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/category.png';
                        break;
                        default:
                                return JURI::root(true).'/media/com_hwdmediashare/assets/images/icons/24/image.png';
                        break;
                }
	}
}