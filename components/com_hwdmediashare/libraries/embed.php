<?php
/**
 * @version    SVN $Id: embed.php 779 2012-12-10 16:14:56Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Feb-2012 14:40:37
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework embed class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareEmbed extends JObject
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
			$c = 'hwdMediaShareEmbed';
                        $instance = new $c;
		}

		return $instance;
	}
    
        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function addEmbed()
	{
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $app = & JFactory::getApplication();
                $date =& JFactory::getDate();

                $data = JRequest::getVar('jform', array(), 'post', 'array');
                
                hwdMediaShareFactory::load('upload');
                $key = hwdMediaShareUpload::generateKey();

                jimport( 'joomla.filter.filterinput' );

                // We will apply the safeHtml filter to the variable, and define additional allowed tags
                $safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
                $safeHtmlFilter->tagBlacklist = array ('applet', 'body', 'bgsound', 'base', 'basefont', 'frame', 'frameset', 'head', 'html', 'id', 'ilayer', 'layer', 'link', 'meta', 'name', 'script', 'style', 'title', 'xml');
                $embed_code = $safeHtmlFilter->clean($data['embed_code']);

                if (empty($embed_code))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_EMBED_CODE'));
                        return false; 
                }
                
                $pattern = '`.*?((http|ftp)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i';
                if (preg_match($pattern,$data['embed_code'],$matches)) 
                {
                        $this->_host = parse_url($matches[1], PHP_URL_HOST);
                }
                
                if (hwdMediaShareUpload::keyExists($key))
                {
                        $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
                        return false; 
                }

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('Media', 'hwdMediaShareTable');

                $post                          = array();

                // Check if we need to replace an existing media item
                if ($data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                {
                        $post['id']                     = $data['id'];
                        //$post['asset_id']             = '';
                        //$post['ext_id']               = '';
                        //$post['media_type']           = '';
                        //$post['key']                  = '';
                        //$post['title']                = '';
                        //$post['alias']                = '';
                        //$post['description']          = '';
                        $post['type']                   = 3; // Embed code
                        $post['source']                 = '';
                        $post['storage']                = '';
                        //$post['duration']             = '';
                        $post['streamer']               = '';
                        $post['file']                   = '';
                        $post['embed_code']             = $embed_code;
                        //$post['thumbnail']            = '';
                        //$post['thumbnail_ext_id']     = '';
                        //$post['location']             = '';
                        //$post['viewed']               = '';
                        //$post['private']              = '';
                        //$post['likes']                = '';
                        //$post['dislikes']             = '';
                        //$post['status']               = '';
                        //$post['published']            = '';
                        //$post['featured']             = '';
                        //$post['checked_out']          = '';
                        //$post['checked_out_time']     = '';
                        //$post['access']               = '';
                        //$post['download']             = '';
                        //$post['params']               = '';
                        //$post['ordering']             = '';
                        //$post['created_user_id']      = '';
                        //$post['created_user_id_alias']= '';
                        //$post['created']              = '';
                        //$post['publish_up']           = '';
                        //$post['publish_down']         = '';
                        $post['modified_user_id']       = $user->id;
                        $post['modified']               = $date->format('Y-m-d H:i:s');
                        //$post['hits']                 = '';
                        //$post['language']             = '';              
                }
                else
                {
                        //$post['id']                   = '';
                        //$post['asset_id']             = '';
                        //$post['ext_id']               = '';
                        //$post['media_type']           = '';
                        $post['key']                    = $key;
                        $post['title']                  = (empty($this->_host) ? 'New media' : $this->_host);
                        $post['alias']                  = JFilterOutput::stringURLSafe($post['title']);
                        //$post['description']          = '';
                        $post['type']                   = 3; // Embed code
                        $post['source']                 = '';
                        $post['storage']                = '';
                        //$post['duration']             = '';
                        $post['streamer']               = '';
                        $post['file']                   = '';
                        $post['embed_code']             = $embed_code;
                        //$post['thumbnail']            = '';
                        //$post['thumbnail_ext_id']     = '';
                        //$post['location']             = '';
                        //$post['viewed']               = '';
                        //$post['private']              = '';
                        //$post['likes']                = '';
                        //$post['dislikes']             = '';
                        $post['status']                 = 1;
                        $post['published']              = 0;
                        $post['featured']               = 0;
                        //$post['checked_out']          = '';
                        //$post['checked_out_time']     = '';
                        $post['access']                 = 1;
                        //$post['download']             = '';
                        //$post['params']               = '';
                        //$post['ordering']             = '';
                        $post['created_user_id']        = $user->id;
                        //$post['created_user_id_alias']= '';
                        $post['created']                = $date->format('Y-m-d H:i:s');
                        $post['publish_up']             = $date->format('Y-m-d H:i:s');
                        $post['publish_down']           = '0000-00-00 00:00:00';
                        $post['modified_user_id']       = $user->id;
                        $post['modified']               = $date->format('Y-m-d H:i:s');
                        $post['hits']                   = 0;
                        $post['language']               = '*';
                }
                    
                // Bind it to the table
                if (!$row->bind( $post ))
                {
                        $this->setError($row->getError());
                        return false; 
                }

                // Store it in the db
                if (!$row->store())
                {
                        $this->setError($row->getError());
                        return false; 
                }
                
                $this->_id = $row->id;

                hwdMediaShareUpload::assignAssociations($row);
                hwdMediaShareFactory::load('events');
                $events = hwdMediaShareEvents::getInstance();
                $events->triggerEvent('onAfterMediaAdd', $row);

                return true;
        }
}
                