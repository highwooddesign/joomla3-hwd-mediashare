<?php
/**
 * @version    SVN $Id: uber.raw.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      14-Jan-2012 10:48:13
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerUber extends JControllerForm
{
	/**
	 * Method to route to uber upload libraries
	 */
        function link_upload()
        {
                // Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                header('Content-type: text/javascript');

                hwdMediaShareFactory::load('uber.ubr_link_upload');

                // Exit the application.
                return;
        }
        
	/**
	 * Method to route to uber upload libraries
	 */
        function set_progress()
        {
                // Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                header('Content-type: text/javascript');

                hwdMediaShareFactory::load('uber.ubr_set_progress');

                // Exit the application.
                return;
        }
        
	/**
	 * Method to route to uber upload libraries
	 */
        function get_progress()
        {
                // Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                header('Content-type: text/javascript');

                hwdMediaShareFactory::load('uber.ubr_get_progress');

                // Exit the application.
                return;
        }
}
