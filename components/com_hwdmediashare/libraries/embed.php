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

class hwdMediaShareEmbed extends JObject
{        
	/**
	 * The variable to hold the item details.
         * 
         * @access      public
	 * @var         object
	 */
	public $_item;
        
	/**
	 * The variable holding the host (retrieved from the embed code).
         * 
         * @access      public
	 * @var         string
	 */
        public $_host;
    
	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed  $properties  Either and associative array or another
	 *                              object to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareEmbed object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareEmbed Object.
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
	 * Method to process an embed code.
         * 
         * @access  public
         * @return  boolean True on success.
	 */
	public function addEmbed()
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
                $safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
                $safeHtmlFilter->tagBlacklist = array ('applet', 'body', 'bgsound', 'base', 'basefont', 'frame', 'frameset', 'head', 'html', 'id', 'ilayer', 'layer', 'link', 'meta', 'name', 'script', 'style', 'title', 'xml');

                // Retrieve filtered jform data.
                hwdMediaShareFactory::load('upload');
                $data = hwdMediaShareUpload::getProcessedUploadData();

                // Clean the raw embed code.
                $embed_code = $safeHtmlFilter->clean($data['embed_code']);
                if (empty($embed_code))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_EMBED_CODE'));
                        return false; 
                }

                if (preg_match('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $embed_code, $matches))
                {                    
                        $this->_host = parse_url($matches[0], PHP_URL_HOST);
                        $this->_host = preg_replace('#^www\.(.+\.)#i', '$1', $this->_host);
                }

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');

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
                        $post['modified']               = $date->toSql();
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
                        $post['title']                  = (isset($data['title']) ? $data['title'] : $this->_host);
                        $post['alias']                  = (isset($data['alias']) ? JFilterOutput::stringURLSafe($data['alias']) : JFilterOutput::stringURLSafe($post['title']));
                        $post['description']            = (isset($data['description']) ? $data['description'] : '');
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
}
                