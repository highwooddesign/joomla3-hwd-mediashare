<?php
/**
 * @version    SVN $Id: maintenance.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-Feb-2012 09:46:33
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework embed class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareMaintenance extends JObject
{        
	var $_host;
        var $_id;
    
        /**
	 * Class constructor.
	 *
	 * @param   array  $config  A configuration array including optional elements.
	 *
	 * @since   0.1
	 */
	public function __construct($config = array())
	{
	}

	/**
	 * Returns the hwdMediaShareRemote object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareMedia A hwdMediaShareRemote object.
	 * @since   0.1
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareMaintenance';
                        $instance = new $c;
		}

		return $instance;
	}
    
        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function cleanCategoryMap()
	{
                $db =& JFactory::getDBO();

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_category_map')."
                        WHERE ".$db->quoteName('category_id')." = ".$db->quote(0)."
                      ";

                $db->setQuery($query);
                if (!$db->query())
                {
                        $this->setError(nl2br($db->getErrorMsg()));
                }
                
                return true;
        }
        
        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function cleanTagMap()
	{
                $db =& JFactory::getDBO();

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_category_map')."
                        WHERE ".$db->quoteName('category_id')." = ".$db->quote(0)."
                      ";

                $db->setQuery($query);
                if (!$db->query())
                {
                        $this->setError(nl2br($db->getErrorMsg()));
                }
                
                return true;
        }
        
        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function emptyuploadtokens()
	{
                $db =& JFactory::getDBO();

                $query = "
                      DELETE
                        FROM ".$db->quoteName('#__hwdms_upload_tokens')."
                        WHERE ".$db->quoteName('datetime')." < (NOW() - INTERVAL 10 MINUTE)
                      ";

                $db->setQuery($query);
                if (!$db->query())
                {
                        $this->setError(nl2br($db->getErrorMsg()));
                }
                
                return true;
        }
}
                