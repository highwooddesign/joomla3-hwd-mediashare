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

abstract class JHtmlHwdImage
{
	/**
	 * Function to write a <img></img> element for a user avatar.
	 *
	 * @access  public
         * @static 
	 * @param   integer  $userId    The ID of the user.
	 * @param   integer  $size      The size of the avatar.
	 * @param   mixed    $attribs   String or associative array of attribute(s) to use.
	 * @return  string   The markup for the <img> element.
	 */
	public static function avatar($userId = 0, $size = 50, $attribs = null)
	{
                // Initialise variables.
                $user = JFactory::getUser($userId);

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $alt = $config->get('author') == 0 ? $user->name : $user->username;

                return '<img src="' . JRoute::_(hwdMediaShareThumbnails::getAvatar($user)) . '" alt="' . $alt . '" width="' . $size . '" height="' . $size . '" '
                . trim((is_array($attribs) ? JArrayHelper::toString($attribs) : $attribs) . ' /')
                . '>';
	}
}
