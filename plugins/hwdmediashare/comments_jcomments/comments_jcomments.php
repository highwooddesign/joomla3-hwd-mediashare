<?php
/**
 * @version    $Id: comments_jcomments.php 884 2013-01-07 12:00:29Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import hwdMediaShare remote library
hwdMediaShareFactory::load('remote');

/**
 * hwdMediaShare framework files class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class plgHwdmediashareComments_jcomments
{               
        /**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct()
	{
	}
        
	/**
	 * Returns the hwdMediaShareFiles object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareFiles A hwdMediaShareFiles object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareComments_jcomments';
                        $instance = new $c;
		}

		return $instance;
	}
    
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getComments()
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $extension = JRequest::getCmd( 'option' );
                $view = JRequest::getCmd( 'view' );
                
                // Check we are viewing a media item
                if (!($extension == "com_hwdmediashare" && $view == "mediaitem")) return false;
                
                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'comments_jcomments');
                
                // Die if plugin not avaliable
                if (isset($plugin->params)) 
                {
                        $params = new JRegistry( $plugin->params );
                }
                else
                {
                        $params = new JRegistry();
                }

		// @task: Load jcomments
                $comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';

                jimport('joomla.filesystem.file');

                if( !JFile::exists( $comments ) )
                {
                        // Missing jcomments
                        return false;
                }

                $comments = require_once( $comments );

                // Get a row instance.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');

                // Attempt to load the row.
                if ($table->load(JRequest::getCmd( 'id' )))
                {
                        // Convert the JTable to a clean JObject.
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');
                        
                        // Ready to Commentify!                        
                        return JComments::showComments($item->id, $extension, $item->title);
                }
                else if ($error = $table->getError()) 
                {
                        //@TODO: Add suitable error handling
                        //$this->setError($error);
                        //jexit();
                }
                        
                return false;          
        }   
}