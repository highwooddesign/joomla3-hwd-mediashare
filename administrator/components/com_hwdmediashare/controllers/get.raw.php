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

class hwdMediaShareControllerGet extends JControllerForm
{        
	/**
	 * Method to deliver a media file through HWD.
	 * @return void
	 */
        public static function file()
        {
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareDownloads::push();
		JFactory::getApplication()->close();
        }
}
