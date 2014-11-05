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

class hwdMediaShareRtmp extends JObject
{        
	/**
	 * Holds the new item details.
         * 
         * @access  public
	 * @var     object
	 */
	public $_item;

	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed   $properties  Associative array to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareRtmp object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareRtmp Object.
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
	 * Method to process an rtmp stream.
         * 
         * @access  public
         * @return  boolean  True on success.
	 */
	public function addRtmp()
	{
                // Initialise variables.            
                $db = JFactory::getDBO();
                $user = JFactory::getUser();
                $app = JFactory::getApplication();
                $date = JFactory::getDate();
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();               
                
                // Check authorised.
                if (!$user->authorise('hwdmediashare.import', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                }   
                
                // We will apply the safeHtml filter to the variable, but define additional allowed tags.
                jimport('joomla.filter.filterinput');
                $noHtmlFilter = JFilterInput::getInstance();
                
                // Retrieve filtered jform data.
                hwdMediaShareFactory::load('upload');
                $data = hwdMediaShareUpload::getProcessedUploadData();
                
                if (empty($data['streamer']))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_RTMP_STREAMER_FOUND'));
                        return false; 
                }
                
                if (empty($data['file']))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_RTMP_FILE_FOUND'));
                        return false; 
                }

                // Set approved/pending.
                (!$app->isAdmin() && $config->get('approve_new_media')) == 1 ? $status = 2 : $status = 1; 
                $config->get('approve_new_media') == 1 ? $status = 2 : $status = 1; 

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('media', 'hwdMediaShareTable');

                $post = array();
                
                // Check if we need to replace an existing media item.
                if (isset($data['id']) && $data['id'] > 0 && $app->isAdmin() && $user->authorise('core.edit', 'com_hwdmediashare'))
                {
                        // Attempt to load the existing table row.
                        $return = $table->load($data['id']);

                        // Check for a table object error.
                        if ($return === false && $table->getError())
                        {
                                $this->setError($table->getError());
                                return false;
                        }

                        $properties = $table->getProperties(1);
                        $replace = JArrayHelper::toObject($properties, 'JObject');

                        // Here, we need to remove all files already associated with this media item
                        hwdMediaShareFactory::load('files');
                        $HWDfiles = hwdMediaShareFiles::getInstance();
                        $HWDfiles->deleteMediaFiles($replace);

                        //$post['id']                   = '';
                        //$post['asset_id']             = '';
                        //$post['ext_id']               = '';
                        $post['media_type']             = (($data['media_type'] > 0) ? $data['media_type'] : '');
                        //$post['key']                  = '';
                        //$post['title']                = '';
                        //$post['alias']                = '';
                        //$post['description']          = '';
                        $post['type']                   = 4; // Rtmp
                        $post['source']                 = '';
                        $post['storage']                = '';
                        //$post['duration']             = '';
                        $post['streamer']               = $noHtmlFilter->clean($data['streamer']);
                        $post['file']                   = $noHtmlFilter->clean($data['file']);
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
                        $post['modified']               = $date->toSql();
                        //$post['hits']                 = '';
                        //$post['language']             = '';              
                }
                else
                {
                        //$post['id']                   = '';
                        //$post['asset_id']             = '';
                        //$post['ext_id']               = '';
                        $post['media_type']             = (($data['media_type'] > 0) ? $data['media_type'] : '');
                        $post['key']                    = $key;
                        $post['title']                  = (isset($data['title']) ? $data['title'] : basename($data['file']));
                        $post['alias']                  = (isset($data['alias']) ? JFilterOutput::stringURLSafe($data['alias']) : JFilterOutput::stringURLSafe($post['title']));
                        $post['description']            = (isset($data['description']) ? $data['description'] : '');
                        $post['type']                   = 4; // Rtmp
                        $post['source']                 = '';
                        $post['storage']                = '';
                        //$post['duration']             = '';
                        $post['streamer']               = $noHtmlFilter->clean($data['streamer']);
                        $post['file']                   = $noHtmlFilter->clean($data['file']);
                        $post['embed_code']             = '';
                        //$post['thumbnail']            = '';
                        //$post['thumbnail_ext_id']     = '';
                        //$post['location']             = '';
                        //$post['viewed']               = '';
                        //$post['private']              = '';
                        //$post['likes']                = '';
                        //$post['dislikes']             = '';
                        $post['status']                 = $status;
                        $post['published']              = (isset($data['published']) ? $data['published'] : 1);
                        $post['featured']               = (isset($data['featured']) ? $data['featured'] : 0);
                        //$post['checked_out']          = '';
                        //$post['checked_out_time']     = '';
                        $post['access']                 = (isset($data['access']) ? $data['access'] : 1);
                        //$post['download']             = '';
                        //$post['params']               = '';
                        //$post['ordering']             = '';
                        $post['created_user_id']        = $user->id;
                        //$post['created_user_id_alias']= '';
                        $post['created']                = $date->toSql();
                        $post['publish_up']             = $date->toSql();
                        $post['publish_down']           = '0000-00-00 00:00:00';
                        $post['modified_user_id']       = $user->id;
                        $post['modified']               = $date->toSql();
                        $post['hits']                   = 0;
                        $post['language']               = (isset($data['language']) ? $data['language'] : '*');
                }

                // Save the data to the database.
                if (!$table->save($post))
                {
                        $this->setError($table->getError());
                        return false; 
                }

                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');

                return true;
        }
        
	/**
	 * Method to render an rtmp stream in a player.
         * 
         * @access  public
         * @static
         * @param   object  $item  The object holding the media details.
         * @return  boolean True on success.
	 */
	public static function display($item)
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                // Check for cloudfront services.
                if (strpos($item->streamer, '.cloudfront.net') !== false)
                {
                        hwdMediaShareFactory::load('aws.cloudfront');
                        $player = call_user_func(array('hwdMediaShareCloudfront', 'getInstance'));
                        $item->file = urldecode($player->update_stream_name($item));
                }

                if ($item->streamer && $item->file)
                {
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                        // Import HWD player plugin.
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $HWDplayer = call_user_func(array($pluginClass, 'getInstance'));
                                
                                // Setup sources for player.
                                $sources = new JRegistry(array(
                                    'streamer' => $item->streamer,
                                    'file' => $item->file,
                                ));

                                if ($player = $HWDplayer->getRtmpPlayer($item, $sources))
                                {
                                        return $player;
                                }
                                else
                                {
                                        return $utilities->printNotice($HWDplayer->getError(), '', 'info', true);
                                }
                        }
                }
	} 
}
