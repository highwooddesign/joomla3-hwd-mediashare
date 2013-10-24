<?php

/**
 * Vzaar API Framework
 * @author Skitsanos
 */
require_once 'OAuth.php';
require_once 'HttpRequest.php';
require_once 'AccountType.php';
require_once 'User.php';
require_once 'VideoDetails.php';
require_once 'VideoList.php';
require_once 'UploadSignature.php';

date_default_timezone_set('UTC');
// Check for CURL
if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll')) exit("\nERROR: CURL extension not loaded\n\n");

Class Profile
{
	const Small = 1;
	const Medium = 2;
	const Large = 3;
	const HighDefinition = 4;
	const Original = 5;
}

Class VideoStatus
{
	const PROCESSING = 1; //Processing not complete
	const AVAILABLE = 2; //Available (processing complete, video ready)
	const EXPIRED = 3; //Expired
	const ON_HOLD = 4; //On Hold (waiting for encoding to be available)
	const FAILED = 5; //Encoding Failed
	const ENCODING_UNAVAILABLE = 6; //Encoding Unavailable
	const NOT_AVAILABLE = 7; //n/a
	const REPLACED = 8; //Replaced
	const DELETED = 9; //Deleted
}

Class VideoStatusDescriptions
{
	const PROCESSING = "Processing not complete";
	const AVAILABLE = "Available (processing complete, video ready)";
	const EXPIRED = "Expired";
	const ON_HOLD = "On Hold (waiting for encoding to be available)";
	const FAILED = "Ecoding Failed";
	const ENCODING_UNAVAILABLE = "Encoding Unavailable";
	const NOT_AVAILABLE = "n/a";
	const REPLACED = "Replaced";
	const DELETED = "Deleted";
}

class Vzaar
{
	public static $url;
	public static $token = '';
	public static $secret = '';
	public static $enableFlashSupport = false;

	/**
	 * The live vzaar API URL ("https://vzaar.com/"). This is the default URL.
	 *
	 */
	const URL_LIVE = "https://vzaar.com/";

	public static function getUserEndpoint($user)
	{
		return Vzaar::URL_LIVE . 'users/' . $user . '.json';
	}

	public static function getVideosEndpoint($user, $count = 1)
	{
		return Vzaar::URL_LIVE . 'api/' . $user . '/videos.json?count=' . $count;
	}

	/**
	 *
	 * @return <string>
	 */
	public static function whoAmI()
	{
		$_url = Vzaar::URL_LIVE . 'api/test/whoami.json';

		$req = Vzaar::setAuth($_url);

		$c = new HttpRequest($_url);

		array_push($c->headers, $req->to_header());
		array_push($c->headers, 'User-Agent: Vzaar OAuth Client');
		return json_decode($c->send())->vzaar_api->test->login;
	}

	/**
	 * This API call returns the details and rights for each vzaar account
	 * type along with it's relevant metadata
	 * http://vzaar.com/api/accounts/{account}.json
	 * @param <integer> $account is the vzaar account type. This is an integer.
	 * @return <AccountType>
	 */
	public static function getAccountDetails($account)
	{
		$_url = Vzaar::URL_LIVE;

		$c = new HttpRequest($_url . 'api/accounts/' . $account . '.json');
		return AccountType::fromJson($c->send());
	}

	/**
	 * This API call returns the user's public details along with it's
	 * relevant metadata
	 * @param <string> $account
	 */
	public static function getUserDetails($account)
	{
		$_url = Vzaar::URL_LIVE;

		$req = new HttpRequest($_url . 'api/' . $account . '.json');

		return User::fromJson($req->send());
	}

	/**
	 * This API call returns a list of the user's active videos along with it's
	 * relevant metadata
	 * http://vzaar.com/api/vzaar/videos.xml?title=vzaar
	 * @param <string> $username is the vzaar login name for the user. Note: This must be the username and not the email address
	 * @param <integer> $count
	 * @return <VideoList>
	 */
	public static function getVideoList($username, $auth = false, $count = 20, $labels = '', $status = '')
	{
		$_url = Vzaar::URL_LIVE . 'api/' . $username . '/videos.json?count=' . $count;
		if ($labels != '') $_url .= "&labels=" . $labels;

		if ($status != '') $_url .= '&status' . $status;

		$req = new HttpRequest($_url);

		if ($auth) array_push($req->headers, Vzaar::setAuth($_url, 'GET')->to_header());

		return VideoList::fromJson($req->send());
	}

	/**
	 * This API call returns a list of the user's active videos along with it's
	 * relevant metadata
	 * @param <string> $username the vzaar login name for the user. Note: This must be the actual username and not the email address
	 * @param <string> $title Return only videos with title containing given string
	 * @param <integer> $count Specifies the number of videos to retrieve per page. Default is 20. Maximum is 100
	 * @param <integer> $page Specifies the page number to retrieve. Default is 1
	 * @param <string> $sort Values can be asc (least_recent) or desc (most_recent). Defaults to desc
	 * @return <VideoList>
	 */
	public static function searchVideoList($username, $auth = false, $title = '', $labels = '', $count = 20, $page = 1, $sort = 'desc')
	{
		$_url = Vzaar::URL_LIVE . 'api/' . $username . '/videos.json?count=' . $count . '&page=' . $page . '&sort=' . $sort;
		if ($labels != '' || $labels != null) $_url .= "&labels=" . $labels;

		if ($title != '') $_url .= '&title=' . urlencode($title);

		$req = new HttpRequest($_url);

		if ($auth) array_push($req->headers, Vzaar::setAuth($_url, 'GET')->to_header());

		return VideoList::fromJson($req->send());
	}

	/**
	 * vzaar uses the oEmbed open standard for allowing 3rd parties to
	 * integrated with the vzaar. You can use the vzaar video URL to easily
	 * obtain the appropriate embed code for that video
	 * @param <integer> $id is the vzaar video number for that video
	 * @return <VideoDetails>
	 */
	public static function getVideoDetails($id, $auth = false)
	{
		$_url = Vzaar::URL_LIVE . 'api/videos/' . $id . '.json';

		$req = new HttpRequest($_url);

		if ($auth) array_push($req->headers, Vzaar::setAuth($_url, 'GET')->to_header());

		return VideoDetails::fromJson($req->send());
	}

	/**
	 *
	 * @param <string> $path
	 * @return <string> GUID of the file uploaded
	 */
	public static function uploadVideo($path)
	{
		$signature = Vzaar::getUploadSignature();

		$c = new HttpRequest('https://' . $signature['vzaar-api']['bucket'] . '.s3.amazonaws.com/');

		$c->method = 'POST';
		$c->uploadMode = true;
		$c->verbose = false;
		$c->useSsl = true;

		array_push($c->headers, 'User-Agent: Vzaar API Client');
		array_push($c->headers, 'x-amz-acl: ' . $signature['vzaar-api']['acl']);
		array_push($c->headers, 'Enclosure-Type: multipart/form-data');

		$s3Headers = array('AWSAccessKeyId' => $signature['vzaar-api']['accesskeyid'], 'Signature' => $signature['vzaar-api']['signature'], 'acl' => $signature['vzaar-api']['acl'], 'bucket' => $signature['vzaar-api']['bucket'], 'policy' => $signature['vzaar-api']['policy'], 'success_action_status' => 201, 'key' => $signature['vzaar-api']['key'], 'file' => "@" . $path);

		$reply = $c->send($s3Headers, $path);
		echo($reply);
		$xmlObj = new XMLToArray($reply, array(), array(), true, false);
		$arrObj = $xmlObj->getArray();
		$key = explode('/', $arrObj['PostResponse']['Key']);
		return $key[sizeOf($key) - 2];
	}

	/**
	 *
	 */
	public static function getUploadSignature($redirectUrl = null)
	{
		$_url = Vzaar::URL_LIVE . "api/videos/signature";

		if (Vzaar::$enableFlashSupport)
		{
			$_url .= '?flash_request=true';
		}

		if ($redirectUrl != null)
		{
			if (Vzaar::$enableFlashSupport)
			{
				$_url .= '&success_action_redirect=' . $redirectUrl;
			}
			else
			{
				$_url .= '?success_action_redirect=' . $redirectUrl;
			}
		}

		$req = Vzaar::setAuth($_url, 'GET');

		$c = new HttpRequest($_url);
		$c->method = 'GET';
		array_push($c->headers, $req->to_header());
		array_push($c->headers, 'User-Agent: Vzaar OAuth Client');

		return UploadSignature::fromXml($c->send());
	}

	public static function getUploadSignatureAsXml($redirectUrl = null)
	{
		$_url = Vzaar::URL_LIVE . "api/videos/signature";

		if (Vzaar::$enableFlashSupport)
		{
			$_url .= '?flash_request=true';
		}

		if ($redirectUrl != null)
		{
			if (Vzaar::$enableFlashSupport)
			{
				$_url .= '&success_action_redirect=' . $redirectUrl;
			}
			else
			{
				$_url .= '?success_action_redirect=' . $redirectUrl;
			}
		}

		$req = Vzaar::setAuth($_url, 'GET');

		$c = new HttpRequest($_url);
		$c->method = 'GET';
		array_push($c->headers, $req->to_header());
		array_push($c->headers, 'User-Agent: Vzaar OAuth Client');

		return $c->send();
	}

	public static function deleteVideo($id)
	{
		$_url = Vzaar::URL_LIVE . "api/videos/" . $id . ".xml";

		$req = Vzaar::setAuth($_url, 'DELETE');

		$data = '<?xml version="1.0" encoding="UTF-8"?><vzaar-api><_method>delete</_method></vzaar-api>';

		$c = new HttpRequest($_url);
		$c->method = 'DELETE';
		array_push($c->headers, $req->to_header());
		array_push($c->headers, 'User-Agent: Vzaar OAuth Client');
		array_push($c->headers, 'Connection: close');
		array_push($c->headers, 'Content-Type: application/xml');

		return $c->send($data);
	}

	public static function editVideo($id, $title, $description, $private = 'false', $seoUrl = '')
	{
		$_url = Vzaar::URL_LIVE . "api/videos/" . $id . ".xml";

		$req = Vzaar::setAuth($_url, 'POST');

		$data = '<?xml version="1.0" encoding="UTF-8"?><vzaar-api><_method>put</_method><video><title>' . $title . '</title><description>' . $description . '</description>';
		if ($private != '') $data .= '<private>' . $private . '</private>';
		if ($seoUrl != '') $data .= '<seo_url>' . $seoUrl . '</seo_url>';
		$data .= '</video></vzaar-api>';

		$c = new HttpRequest($_url);
		$c->method = 'POST';
		array_push($c->headers, $req->to_header());
		array_push($c->headers, 'User-Agent: Vzaar OAuth Client');
		array_push($c->headers, 'Connection: close');
		array_push($c->headers, 'Content-Type: application/xml');

		return $c->send($data);
	}

	/**
	 * This API call tells the vzaar system to process a newly uploaded video. This will encode it if necessary and
	 * then provide a vzaar video idea back.
	 * http://developer.vzaar.com/docs/version_1.0/uploading/process
	 * @static
	 * @param string $guid Specifies the guid to operate on
	 * @param string $title Specifies the title for the video
	 * @param string $description Specifies the description for the video
	 * @param string $labels
	 * @param Profile $profile Specifies the size for the video to be encoded in. If not specified, this will use the vzaar default
	 * @param string $replace Specifies the video ID of an existing video that you wish to replace with the new video.
	 * @param boolean $transcoding If True forces vzaar to transcode the video, false makes vzaar use the original source file (available only for mp4 and flv files)
	 * @return string
	 */
	public static function processVideo($guid, $title, $description, $labels, $profile = Profile::Medium, $transcoding = false, $replace = '')
	{
		$_url = Vzaar::URL_LIVE . "api/videos";

		if ($replace != '') $replace = '<replace_id>' . $replace . '</replace_id>';

		$req = Vzaar::setAuth($_url, 'POST');

		$data = '<vzaar-api>
		    <video>' . $replace . '
			<guid>' . $guid . '</guid>
		        <title>' . $title . '</title>
		        <description>' . $description . '</description>
		        <labels>' . $labels . '</labels>
	        	<profile>' . $profile . '</profile>';
		if ($transcoding) $data .= '<transcoding>true</transcoding>';
		$data .= '</video> </vzaar-api>';

		$c = new HttpRequest($_url);
		$c->verbose = false;
		$c->method = "POST";
		array_push($c->headers, $req->to_header());
		array_push($c->headers, 'User-Agent: Vzaar OAuth Client');
		array_push($c->headers, 'Connection: close');
		array_push($c->headers, 'Content-Type: application/xml');

		$apireply = new XMLToArray($c->send($data));
		return $apireply->_data[0]["vzaar-api"]["video"];
	}

	public static function setAuth($_url, $_method = 'GET')
	{
		$consumer = new OAuthConsumer('', '');
		$token = new OAuthToken(Vzaar::$secret, Vzaar::$token);
		$req = OAuthRequest::from_consumer_and_token($consumer, $token, $_method, $_url);
		$req->set_parameter('oauth_signature_method', 'HMAC-SHA1');
		$req->set_parameter('oauth_signature', $req->build_signature(new OAuthSignatureMethod_HMAC_SHA1, $consumer, $token));
		return $req;
	}

}

class XMLToArray
{

	var $_data = Array();
	var $_name = Array();
	var $_rep = Array();
	var $_parser = 0;
	var $_ignore = Array(), $_replace = Array(), $_showAttribs;
	var $_level = 0;

	function XMLToArray($data, $ignore = Array(), $replace = Array(), $showattribs = false, $toupper = false)
	{
		$this->_showAttribs = $showattribs;
		$this->_parser = xml_parser_create();

		xml_set_object($this->_parser, $this);
		if ($toupper)
		{
			foreach ((array)$ignore as $key => $value) $this->_ignore[strtoupper($key)] = strtoupper($value);
			foreach ((array)$replace as $key => $value) $this->_replace[strtoupper($key)] = strtoupper($value);
			xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, true);
		}
		else
		{
			$this->_ignore = &$ignore;
			$this->_replace = &$replace;
			xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);
		}
		xml_set_element_handler($this->_parser, '_startElement', '_endElement');
		xml_set_character_data_handler($this->_parser, '_cdata');

		$this->_data = array();
		$this->_level = 0;
		if (!xml_parse($this->_parser, $data, true))
		{
			//new Error("XML Parse Error: ".xml_error_string(xml_get_error_code($this->_parser))."n on line: ".xml_get_current_line_number($this->_parser),true);
			return false;
		}
		xml_parser_free($this->_parser);
	}

	function & getArray()
	{
		return $this->_data[0];
	}

	function & getReplaced()
	{
		return $this->_data['_Replaced_'];
	}

	function & getAttributes()
	{
		return $this->_data['_Attributes_'];
	}

	function _startElement($parser, $name, $attrs)
	{
		if (!isset($this->_rep[$name])) $this->_rep[$name] = 0;
		if (!in_array($name, $this->_ignore))
		{
			$this->_addElement($name, $this->_data[$this->_level], $attrs, true);
			$this->_name[$this->_level] = $name;
			$this->_level++;
		}
	}

	function _endElement($parser, $name)
	{
		if (!in_array($name, $this->_ignore) && isset($this->_name[$this->_level - 1]))
		{
			if (isset($this->_data[$this->_level])) $this->_addElement($this->_name[$this->_level - 1], $this->_data[$this->_level - 1], $this->_data[$this->_level], false);

			unset($this->_data[$this->_level]);
			$this->_level--;
			$this->_rep[$name]++;
		}
	}

	function _cdata($parser, $data)
	{
		if (!empty($this->_name[$this->_level - 1])) $this->_addElement($this->_name[$this->_level - 1], $this->_data[$this->_level - 1], str_replace(array('&gt;', '&lt;', '&quot;', '&amp;'), array('>', '<', '"', '&'), $data), false);
	}

	function _addElement(&$name, &$start, $add = array(), $isattribs = false)
	{
		if (((sizeof($add) == 0) && is_array($add)) || !$add)
		{
			if (!isset($start[$name])) $start[$name] = '';
			$add = '';
			//if (is_array($add)) return;
			//return;
		}
		if (!empty($this->_replace[$name]) && ('_ARRAY_' === strtoupper($this->_replace[$name])))
		{
			if (!$start[$name]) $this->_rep[$name] = 0;
			$update = &$start[$name][$this->_rep[$name]];
		}
		elseif (!empty($this->_replace[$name]))
		{
			if ($add[$this->_replace[$name]])
			{
				$this->_data['_Replaced_'][$add[$this->_replace[$name]]] = $name;
				$name = $add[$this->_replace[$name]];
			}
			$update = &$start[$name];
		}
		else
		{
			$update = &$start[$name];
		}

		if ($isattribs && !$this->_showAttribs)
		{
			return;
		}
		elseif ($isattribs) $this->_data['_Attributes_'][$this->_level][$name][] = $add;
		elseif (is_array($add) && is_array($update)) $update += $add;
		elseif (is_array($update)) return;
		elseif (is_array($add)) $update = $add;
		elseif ($add) $update .= $add;
	}

}

?>