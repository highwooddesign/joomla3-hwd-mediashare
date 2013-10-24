<?php
/**
 * @version    $Id: remote_youtubecom.php 1668 2013-08-21 12:33:07Z dhorsfall $
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
class plgHwdmediashareRemote_youtubecom extends JObject
{    
	/**
	 * Remote media type integer.
	 *
	 * @var		int
	 */
	public $mediaType = 4;
        
        var $_url;
        var $_host;
        var $_buffer;
        var $_title;
        var $_description;
        var $_source;
        var $_duration;
        var $_thumbnail;
        
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
                unset($instance);
		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareRemote_youtubecom';
                        $instance = new $c;
		}

		return $instance;
	}
    
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getTitle()
	{
                if( !$this->_title )
		{
                        hwdMediaShareFactory::load('remote');
                        $this->getBuffer();
                        $this->_title = hwdMediaShareRemote::getTitle($this->_buffer);
                        $this->_title = str_replace(" - YouTube", "", $this->_title);
                }
                return $this->_title;            
        }   

        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getDescription()
	{
                if( !$this->_description )
		{
                        hwdMediaShareFactory::load('remote');
                        $this->getBuffer();
                        $this->_description = hwdMediaShareRemote::getDescription($this->_buffer); 
                }             
                return $this->_description;            
        }  
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getSource()
	{
                if( !$this->_source )
		{
                        hwdMediaShareFactory::load('remote');
                        $this->getBuffer();
                        $this->_source = hwdMediaShareRemote::getSource();
                }             
                return $this->_source;             
        } 

        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getDuration()
	{
                if( !$this->_duration )
		{
                        $duration = '';
                        
                        //preg_match("/amp;length_seconds=(.*)\\\\u/siU", $this->_buffer, $match);
                        //preg_match("/length_seconds=(.*)\\\\u/siU", $this->_buffer, $match);
                        preg_match("/\"length_seconds\": (.*),/siU", $this->_buffer, $match);
                        
                        if (!empty($match[1]))
                        {
                                $duration = (int) $match[1];
                        }
   
                        $duration == 0 ? $this->_duration = null : $this->_duration = $duration;
                }   

                return $this->_duration;
        } 

        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getThumbnail()
	{
                if( !$this->_thumbnail )
		{
                        $this->_thumbnail = plgHwdmediashareRemote_youtubecom::parse($this->_url, 'hqthumb');
                }             
                return $this->_thumbnail;              
        } 
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getBuffer()
	{
                $this->getHost();
                $this->getUrl();

                if (!$this->_buffer)
                {
                        hwdMediaShareFactory::load('remote');
                        $this->_buffer = hwdMediaShareRemote::getBuffer($this->_url);
                }

		return $this->_buffer;
	}

        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getHost()
	{
                if (!$this->_host)
                {
                        hwdMediaShareFactory::load('remote');
                        $this->_host = hwdMediaShareRemote::getHost();
                }

		return $this->_host;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getUrl()
	{
                if (!$this->_url)
                {
                        hwdMediaShareFactory::load('remote');
                        $this->_url = hwdMediaShareRemote::getUrl();
                }

		return $this->_url;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function display($item)
	{
		$doc = JFactory::getDocument();
                
                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'remote_youtubecom');
		$params = new JRegistry( @$plugin->params );

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $autoplay = (JRequest::getInt('media_autoplay', $config->get('media_autoplay')) == 1 ? '1' : '0');
                                
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();

                $this->width = $utilities->getMediaWidth();
                $this->height = (int) $config->get('mediaitem_height') ? $config->get('mediaitem_height') : $this->width*$config->get('video_aspect',0.75);

                // Youtube ID
                $id = plgHwdmediashareRemote_youtubecom::parse($item->source, '');

                // Pull parameters from the original Youtube url and transfer these to the iframe tag where appropriate
                $url = parse_url($item->source);
                parse_str($url['query'], $ytvars);
                if (isset($ytvars['cc_load_policy'])) $params->set('cc_load_policy', $ytvars['cc_load_policy']);
                if (isset($ytvars['cc_lang_pref'])) $params->set('cc_lang_pref', $ytvars['cc_lang_pref']);
                if (isset($ytvars['hl'])) $params->set('hl', $ytvars['hl']);

                if ($params->get('play_local') == 1)
                {
                        $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                        // Import hwdMediaShare plugins
                        if (file_exists($pluginPath))
                        {
                                JLoader::register($pluginClass, $pluginPath);
                                $player = call_user_func(array($pluginClass, 'getInstance'));
                                if (method_exists($player, 'getYoutubePlayer'))
                                {
                                        $params = new JRegistry('{"id":"'.$id.'"}');
                                        return $player->getYoutubePlayer($params);
                                }
                        }
                }

                $this->width = '100%';
                $this->height = '100%';
                $doc->addStyleDeclaration('#hwd-container .media-respond { max-width:'.$config->get('mediaitem_size', '500').'px!important;}');

                ob_start();
                ?>
                <div class="media-respond">
                  <div class="media-aspect" data-aspect="<?php echo $config->get('video_aspect', '0.75'); ?>"></div>
                  <div class="media-content">
                    <iframe width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" src="<?php echo JURI::getInstance()->getScheme(); ?>://www.youtube.com/embed/<?php echo $id; ?>?wmode=opaque&amp;autoplay=<?php echo $autoplay; ?>&amp;autohide=<?php echo $params->get('autohide',2); ?>&amp;border=<?php echo $params->get('border',0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy',1); ?>&amp;cc_lang_pref=<?php echo $params->get('cc_lang_pref','en'); ?>&amp;hl=<?php echo $params->get('hl','en'); ?>&amp;color=<?php echo $params->get('color','red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls',1); ?>&amp;fs=<?php echo $params->get('fs',1); ?>&amp;hd=<?php echo $params->get('hd',0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy',1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding',1); ?>&amp;rel=<?php echo $params->get('rel',1); ?>&amp;theme=<?php echo $params->get('theme','dark'); ?>" frameborder="0" allowfullscreen></iframe>	
                  </div>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;
	}
        
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/        
        protected function parse($url, $return='embed', $width='', $height='', $rel=0)
        {
                $urls = parse_url($url);

                // Url is http://youtu.be/xxxx
                if($urls['host'] == 'youtu.be'){
                    $id = ltrim($urls['path'],'/');
                }
                // Url is http://www.youtube.com/embed/xxxx
                else if(strpos($urls['path'],'embed') == 1){
                    $id = end(explode('/',$urls['path']));
                }
                // Url is xxxx only
                else if(strpos($url,'/')===false){
                    $id = $url;
                }
                // http://www.youtube.com/watch?feature=player_embedded&v=m-t4pcO99gI
                // Url is http://www.youtube.com/watch?v=xxxx
                else
                {
                    parse_str($urls['query'], $arrayVars);
                    $id = $arrayVars['v'];    
                }
                
                // Return embed iframe
                if($return == 'embed')
                {
                    return '<iframe width="'.($width?$width:560).'" height="'.($height?$height:349).'" src="http://www.youtube.com/embed/'.$id.'?rel='.$rel.'" frameborder="0" allowfullscreen></iframe>';
                }
                // Return normal thumb
                else if($return == 'thumb')
                {
                    return 'http://i1.ytimg.com/vi/'.$id.'/default.jpg';
                }
                // Return hqthumb
                else if($return == 'hqthumb')
                {
                    return 'http://i1.ytimg.com/vi/'.$id.'/hqdefault.jpg';
                }
                // Return id
                else
                {
                    return $id;
                }
        }
        
       /**
	 * Method to create a SWF link to use as an open graph video tag.
         *
	 * @since   0.1
	 **/
	public function getOgVideoTag($item)
	{
            // Youtube ID
            $id = plgHwdmediashareRemote_youtubecom::parse($item->source, '');
            return 'http://www.youtube.com/v/'.$id.'?version=3&amp;autohide=1';
        } 
}