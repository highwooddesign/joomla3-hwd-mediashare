<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_soundcloud_jplayer
 *
 * @copyright   (C) 2014 Joomlabuzz.com
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwd_soundcloud_jplayerHelper
{
	/**
	 * The contents of the "User-Agent: " header to be used in TTP requests. 
         * 
         * @access  public
	 * @var     string
	 */   
	public $agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0';
        
	/**
	 * The contents of the "Referer: " header to be used in HTTP requests. 
         * 
         * @access  public
	 * @var     string
	 */   
	public $host = 'https://www.soundcloud.com/';
        
	/**
	 * The Soundcloud App ID. 
         * 
         * @access  public
	 * @var     string
	 */   
	public $client_id = 'ccca12f49e58948d604565b83de2e667';

        /**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   array   $module  The module object.
	 * @param   array   $params  The module parameters object.
         * @return  void
	 */          
	public function __construct($module, $params)
	{
                // Get data.
                $this->module = $module;                
                $this->params = $params;                
                $this->pid = rand();                

                // Add assets to the head tag.
                $this->addHead();                
	}

	/**
	 * Method to add page assets to the <head> tags.
	 *
	 * @access  public
         * @return  void
	 */        
	public function addHead()
	{
		JHtml::_('bootstrap.framework');
                $doc = JFactory::getDocument();
		//$doc->addStyleSheet(JURI::root() . 'modules/mod_hwd_soundcloud_jplayer/css/jplayer.css'); 
		$doc->addscript(JURI::root() . 'modules/mod_hwd_soundcloud_jplayer/js/jquery.jplayer.min.js'); 
		$doc->addscript(JURI::root() . 'modules/mod_hwd_soundcloud_jplayer/js/jplayer.playlist.js'); 

                // Extract the layout.
                list($template, $layout) = explode(':', $this->params->get('layout', '_:default'));
                if (file_exists(__DIR__ . '/css/' . $layout . '.css'))
                {
                        $doc->addStyleSheet(JURI::base( true ) . '/modules/mod_hwd_soundcloud_jplayer/css/' . $layout . '.css');
                }
                if (file_exists(__DIR__ . '/js/' . $layout . '.js'))
                {
                        $doc->addScript(JURI::base( true ) . '/modules/mod_hwd_soundcloud_jplayer/js/' . $layout . '.js');
                }
                
                // Load caching.
                $cache = JFactory::getCache('mod_hwd_soundcloud_jplayer');
                $cache->setLifeTime($this->params->get('methodcache', 43829));
                $cache->setCaching(1);              
                $listUrl = $cache->call(array($this, 'resolveUrl'), $this->params);
                //$listUrl = $this->resolveUrl();

                ob_start();
?>
jQuery(document).ready(function(){
 
        $clientID = "<?php echo $this->client_id; ?>",
        $listurl = "<?php echo $listUrl; ?>",

        $i = 0,
        tracks = new Array(),
        trackdata = new Array(),
        playlist = new Array();

        // Get the JSON object (the playlist)
        jQuery.ajax({
                url: $listurl,
                async: false,
                dataType: 'json',
                success: function(listdata) {
                        // Iterate through the object and create array of tracks
                        if(listdata.hasOwnProperty(tracks)){
                                // Playlist
                                jQuery.each(listdata.tracks, function(key, val) {
                                        tracks[$i] = val;
                                        $i++;                        
                                });
                        // Other kinds (users, etc) will return tracks directly.
                        } else {
                                if("kind" in listdata){
                                        // Track
                                        tracks[$i] = listdata;
                                        $i++;   
                                } else {
                                        // User, Group
                                        jQuery.each(listdata, function(key, val) {
                                                if("kind" in val){
                                                        if (val.kind == 'track') {
                                                                tracks[$i] = val;
                                                                $i++;   
                                                        }
                                                }                     
                                        });
                                }
                        }

                        // Now, for each of the tracks, save the necessary track info formatted as options for jPlayerPlaylist, all in another array 
                        for (var i = 0; i < tracks.length; i++) {
                                trackdata[i] = {
                                        title: tracks[i].title,
                                        mp3: tracks[i].stream_url + '?client_id=' + $clientID,
                                        url: tracks[i].permalink_url,
                                        free: true
                                }
                        }

                        // Next, stack all these arrays into one array for use in the jPlayer playlist 
                        for (i = 0; i < trackdata.length; i++) {
                                playlist.push(trackdata[i]);
                        }
                }
        });

        
        // Instantiate the jPlayer playlist object, using the Soundcloud playlist array   
        new jPlayerPlaylist({
                        jPlayer: "#jquery_jplayer_<?php echo $this->pid; ?>",
                        cssSelectorAncestor: "#jp_container_<?php echo $this->pid; ?>"
                },
                playlist, {
                        playlistOptions: {
                                autoPlay: false,
                                displayTime: 0,
                                freeItemClass: 'soundcloudLink'
                        },
                        loop: true, // For restarting the playlist after the last track
                        swfPath: "../js",
                        supplied: "mp3, m4a, oga",
                        smoothPlayBar: true,
                        keyEnabled: true,
                        errorAlerts: false,
                        warningAlerts: false
                });
});
<?php
$script = ob_get_contents();
ob_end_clean();

                $doc->addScriptDeclaration($script);                                            
	} 

        /**
	 * Method to get a SoundCloud embed item.
	 *
	 * @access  public
         * @return  string  The embed item.
	 */         
	public function resolveUrl()
	{
                // Initialise variables.
		$http = JHttpFactory::getHttp();
                $resolverUrl =  'https://api.soundcloud.com/resolve.json?url='.$this->params->get('url').'&client_id=' . $this->client_id;

                $response = $http->get($resolverUrl);

                if ($response->body) 
                {
                        $resolverData = json_decode($response->body);
                        if ($resolverData->kind && $resolverData->uri)
                        { 
                                switch ($resolverData->kind)
                                {
                                        case 'user': 
                                        case 'group': 
                                                return $resolverData->uri . '/tracks?client_id=' . $this->client_id;
                                        break;
                                        default:
                                                return $resolverData->uri . '?client_id=' . $this->client_id;
                                        break;                                    
                                }
                        }
                }

		return false;
	}
}
