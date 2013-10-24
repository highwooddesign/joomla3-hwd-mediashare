<?php
/**
 * @version    $Id: comments_komento.php 892 2013-01-07 12:01:09Z dhorsfall $
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
class plgHwdmediashareComments_komento
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
			$c = 'plgHwdmediashareComments_komento';
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

                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'comments_komento');

                // Die if plugin not avaliable
                if (isset($plugin->params))
                {
                        $params = new JRegistry( $plugin->params );
                }
                else
                {
                        $params = new JRegistry();
                }

                // @task: load bootstrap
                $bootstrap	= JPATH_ROOT . DS . 'components' . DS . 'com_komento' . DS . 'bootstrap.php';

                jimport('joomla.filesystem.file');

                if( !JFile::exists( $bootstrap ) )
                {
                        // Missing bootstrap
                        return false;
                }

                $bootstrap = require_once( $bootstrap );

                // Get a row instance.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');

                // temporary fix from Komento's team
                $tempId = explode( ':', JRequest::getString( 'id' ) );
                $tempId = $tempId[0];

                // Attempt to load the row.
                // if ($table->load(JRequest::getCmd( 'id' ))) // temporary fix from Komento's team
                if ($table->load($tempId))
                {
                        // Convert the JTable to a clean JObject.
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');

                        // Passing in the data
                        $options		= array();
                        $options['enable']	= true;
                        //$options['trigger']	= $eventTrigger;
                        //$options['context']	= $context;
                        //$options['params']	= $params;
                        //$options['page']	= $page;

                        $extension = JRequest::getCmd( 'option' );

                        // Ready to Commentify!
                        return Komento::commentify( $extension, $item, $options );
                }
                else if ($error = $table->getError())
                {
                        $this->setError($error);
                }

                return false;
        }
}
