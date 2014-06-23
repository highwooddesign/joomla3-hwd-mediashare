<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_soundcloud_audiobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwdSoundcloudAudioBoxHelper extends JObject
{
	/**
	 * Class data
	 * @var array
	 */    
	public $params;
	public $module;
        public $height = 450;

	public function __construct($module, $params)
	{
                // Load caching.
                $cache = JFactory::getCache();
                $cache->setCaching(1);

                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

                // Get data.
                $this->module = $module;                
                $this->params = $params;                
                $this->item = $cache->call(array($this, 'getItem'));  
                
                $pos = strpos($this->item, '/tracks/');
                if ($pos !== false) $this->height = 166;                
	}


	public function getItem()
	{
                $resolverUrl =  'http://api.soundcloud.com/resolve.json?url='.$this->get('params')->get('url', 'https://soundcloud.com/boozeandbeats/catchafiresam').'&client_id=YOUR_CLIENT_ID';
                $buffer = $this->getBuffer($resolverUrl);
                $resolverData = json_decode($buffer);

                if (!empty($resolverData->location))
                { 
                        preg_match("/api.soundcloud.com(.*).json/siU", $buffer, $match);
                        if (!empty($match[1]))
                        {
                                return 'http://api.soundcloud.com' . $match[1];
                        }
                }
                
		return false;
	}

	public function getBuffer($url)
	{
                // A large number of CURL installations will not support SSL, so switch back to http
                $url = str_replace("https", "http", $url);

                if ($url)
                {
                        if (function_exists('curl_init'))
                        {
                                $useragent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)";

                                $curl_handle = curl_init();
                                curl_setopt($curl_handle, CURLOPT_URL, $url);
                                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
                                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($curl_handle, CURLOPT_REFERER, 'http://soundcloud.com');
                                curl_setopt($curl_handle, CURLOPT_USERAGENT, $useragent);
                                $buffer = curl_exec($curl_handle);
                                curl_close($curl_handle);

                                if (!empty($buffer))
                                {
                                        return $buffer;
                                }
                                else
                                {
                                        return false;
                                }
                        }
                }

		return false;
	}
}