<?php
/**
 *    @version 1.0.6
 *    @package hwdMediaShare
 *    @copyright (C) 2011 - 2012 Highwood Design Ltd
 *    @license Creative Commons Attribution-Non-Commercial-No Derivative Works 3.0 Unported Licence
 *    @license http://creativecommons.org/licenses/by-nc-nd/3.0/
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class plgHwdmediashareEvent_Twitter extends JPlugin
{
	function onAfterMediaAdd($media)
	{
                $app = & JFactory::getApplication();
            
                JLoader::register('hwdMediaShareHelperRoute', JPATH_SITE.'/components/com_hwdmediashare/helpers/route.php');
            
                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'event_twitter');
		$pluginParams = new JRegistry( $plugin->params );

		//And define the parameters. For example like this..
		$post_new_media         = $pluginParams->def( 'post_new_media', '0' );
		$format_new_media       = $pluginParams->def( 'format_new_media', 'New media on [[SITENAME]], [[MEDIANAME]] [[URL]]' );

		if ($post_new_media == 1)
		{
			if ($app->isAdmin())
                        {
                            $link = JURI::root().'index.php?option=com_hwdmediashare&view=mediaitem&id='.$media['id'];
                        }
                        else
                        {
                            $link = 'http://'.$_SERVER['HTTP_HOST'].JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($media['id']));
                        }
			$short_link = bitlyAPI::shorten($link);

			if(!empty($short_link))
			{
				$post_link = $short_link;
			} 
                        else
                        {
				$post_link = $link;
			}

			$jconfig = new jconfig();
			$message = $format_new_media;
			$message = str_replace('[[SITENAME]]', $jconfig->sitename, $message);
			$message = str_replace('[[URL]]', $post_link, $message);

			$ini_len = strlen($message)-13;
			$allowable_len = 139 - $ini_len;

			$message = str_replace('[[MEDIANAME]]', substr($media['title'], 0, $allowable_len), $message);
			$message = substr($message, 0, 139);

			$result = $this->sendTwitter($message);

                        if($result)
                        {
                                //JFactory::getApplication()->enqueueMessage( JText::_('This new media has been posted on Twitter!') );
                        } 
                        else 
                        {
                                JFactory::getApplication()->enqueueMessage( JText::_('We failed to post this media on Twitter') );
                        }
		}
                
	}    
    
	function twitter() {
	  die('Cannot instantiate this class(Twitter) in: '.__FILE__);
	}

	/**
	* Attempts to contact twitter and post a message
	*
	* @param string $uname         = Twitter User Name
	* @param string $pWord         = Twitter Password
	* @param string $message      = The message to post through the communication system
	* @param string $apiUrl      = Twitter API Url. (Optional - defaulted to standard XML API)
	* @return boolean
	**/
	function sendTwitter($message='',$apiUrl='http://twitter.com/statuses/update.xml')
	{
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'event_twitter');
		$pluginParams = new JRegistry( $plugin->params );

		//And define the parameters. For example like this..
		$oauth_token         = $pluginParams->def( 'oauth_token', '' );
		$oauth_token_secret  = $pluginParams->def( 'oauth_token_secret', '' );

		require_once JPATH_SITE.'/plugins/hwdmediashare/event_twitter/assets/twitterOAuth.php';

		define("CONSUMER_KEY", "nP9VR3SWsOjpayiPVrenaA");
		define("CONSUMER_SECRET", "Fo8ZV3ydT5Q1yhKkGMj3Qvul17r3XBvTL0v6ahGNuo");
		define("OAUTH_TOKEN", $oauth_token);
		define("OAUTH_SECRET", $oauth_token_secret);

		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
		$content = $connection->get('account/verify_credentials');

		if($connection->post('statuses/update', array('status' => $message)))
		{                    
                    return true;
		}
		else
		{
                    return false;
		}
	}
}

class bitlyAPI
{
	function hwdmsUpdateTwitter() {
	  die('Cannot instantiate this class(Twitter) in: '.__FILE__);
	}

	/**
	* Attempts to contact twitter and post a message
	*
	* @param string $uname         = Twitter User Name
	* @param string $pWord         = Twitter Password
	* @param string $message      = The message to post through the communication system
	* @param string $apiUrl      = Twitter API Url. (Optional - defaulted to standard XML API)
	* @return boolean
	**/
	function shorten($link='')
	{
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'event_twitter');
		$pluginParams = new JRegistry( $plugin->params );

		//And define the parameters. For example like this..
		$bitly_username        = $pluginParams->def( 'bitly_username', '' );
		$bitly_api_key         = $pluginParams->def( 'bitly_api_key', '' );

		$link = urlencode($link);
		$apiUrl = 'http://api.bit.ly/shorten?version=2.0.1&longUrl='.$link.'&login='.$bitly_username.'&apiKey='.$bitly_api_key;

		$curl_handle=curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$apiUrl);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,30);
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);

		if(strpos($buffer,'"statusCode": "OK"') === false)
		{
			return false;
		}
		else
		{
			preg_match('/"shortUrl": "([^"]+)/', $buffer, $match);
			if (!empty($match[1]))
			{
				return $match[1];
			}
			else
			{
				return false;
			}
		}
	}
}
?>