<?php
/**
 * @version    SVN $Id: rtmp.php 1540 2013-05-30 11:30:02Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Feb-2012 14:40:29
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework rtmp class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareRtmp extends JObject
{        
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
			$c = 'hwdMediaShareRtmp';
                        $instance = new $c;
		}

		return $instance;
	}
        
        /**
	 * Method to process an embed code import
         *
	 * @since   0.1
	 */
	public function addRtmp()
	{
                $db =& JFactory::getDBO();
                $user = & JFactory::getUser();
                $app = & JFactory::getApplication();
                $date =& JFactory::getDate();

                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $error = false;

                $data = JRequest::getVar('jform', array(), 'post', 'array');
                
                hwdMediaShareFactory::load('upload');
                $key = hwdMediaShareUpload::generateKey();

                jimport( 'joomla.filter.filterinput' );

                // We will apply the most strict filter to the variable
                $noHtmlFilter = JFilterInput::getInstance();
                $streamer = $noHtmlFilter->clean($data['streamer']);
                $file = $noHtmlFilter->clean($data['file']);
                $title = basename($file);
                $type = intval($data['media_type']);

                if (hwdMediaShareUpload::keyExists($key))
                {
                        $this->setError(JText::_('COM_HWDMS_KEY_EXISTS'));
                        return false; 
                }
                
                if (empty($streamer))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_RTMP_STREAMER_FOUND'));
                        return false; 
                }
                
                if (empty($file))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_RTMP_FILE_FOUND'));
                        return false; 
                }

                // Set approved/pending
                (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('media', 'hwdMediaShareTable');

                $post = array();
                
                // Check if we need to replace an existing media item
                if ($data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                {
                        $post['id']                     = $data['id'];
                        //$post['asset_id']             = '';
                        //$post['ext_id']               = '';
                        $post['media_type']             = (($type > 0) ? $type : '');
                        //$post['key']                  = '';
                        //$post['title']                = '';
                        //$post['alias']                = '';
                        //$post['description']          = '';
                        $post['type']                   = 4; // Rtmp
                        $post['source']                 = '';
                        $post['storage']                = '';
                        //$post['duration']             = '';
                        $post['streamer']               = $streamer;
                        $post['file']                   = $file;
                        $post['embed_code']             = '';
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
                        $post['media_type']             = (($type > 0) ? $type : '');
                        $post['key']                    = $key;
                        $post['title']                  = (empty($title) ? 'New media' : $title);
                        $post['alias']                  = JFilterOutput::stringURLSafe($post['title']);
                        //$post['description']          = '';
                        $post['type']                   = 4; // Rtmp
                        $post['source']                 = '';
                        $post['storage']                = '';
                        //$post['duration']             = '';
                        $post['streamer']               = $streamer;
                        $post['file']                   = $file;
                        $post['embed_code']             = '';
                        //$post['thumbnail']            = '';
                        //$post['thumbnail_ext_id']     = '';
                        //$post['location']             = '';
                        //$post['viewed']               = '';
                        //$post['private']              = '';
                        //$post['likes']                = '';
                        //$post['dislikes']             = '';
                        $post['status']                 = $status;
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
        
	/**
	 * Method to render a video
         * 
         * @since   0.1
	 **/
	public function get($item)
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Check for cloudfront services
                if (strpos($item->streamer, '.cloudfront.net') !== false) {
                    hwdMediaShareFactory::load('aws.cloudfront');
                    $player = call_user_func(array('hwdMediaShareCloudfront', 'getInstance'));
                    $item->file = urldecode($player->update_stream_name($item));
                }

                if ($item->streamer && $item->file)
                {
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                        $jpg = hwdMediaShareDownloads::jpgUrl($item);

                        // Import hwdMediaShare plugins
                        JLoader::register($pluginClass, $pluginPath);
                        $player = call_user_func(array($pluginClass, 'getInstance'));
                        $params = new JRegistry('{"streamer":"'.$item->streamer.'","file":"'.$item->file.'","jpg":"'.$jpg.'"}');
                        return $player->getRtmpPlayer($params);
                }
	} 
}