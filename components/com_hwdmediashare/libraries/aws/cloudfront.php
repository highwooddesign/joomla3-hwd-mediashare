<?php
/**
 * @version    SVN $Id: cloudfront.php 1454 2013-04-30 10:37:51Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      22-Mar-2013 10:38:29
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework cloudfront support class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareCloudfront extends JObject
{    
	// Path to your private key.  Be very careful that this file is not accessible from the web!
	var $private_key_filename = ''; 
	var $key_pair_id = '';

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
			$c = 'hwdMediaShareCloudfront';
                        $instance = new $c;
		}

		return $instance;
	}
        
        function rsa_sha1_sign($policy, $private_key_filename)
        {
                $signature = "";

                // load the private key
                $fp = fopen($private_key_filename, "r");
                $priv_key = fread($fp, 8192);
                fclose($fp);

                $pkeyid = openssl_get_privatekey($priv_key);

                // compute signature
                openssl_sign($policy, $signature, $pkeyid);

                // free the key from memory
                openssl_free_key($pkeyid);

                return $signature;
        }

        function url_safe_base64_encode($value) 
        {
                $encoded = base64_encode($value);
                // replace unsafe characters +, = and / with the safe characters -, _ and ~
                return str_replace(
                    array('+', '=', '/'),
                    array('-', '_', '~'),
                    $encoded);
        }

        function create_stream_name($stream, $policy, $signature, $key_pair_id, $expires)
        {
                $result = $stream;
                // if the stream already contains query parameters, attach the new query parameters to the end
                // otherwise, add the query parameters
                $separator = strpos($stream, '?') == FALSE ? '?' : '&';
                // the presence of an expires time means we're using a canned policy
                if($expires) {
                    //$result .= $path . $separator . "Expires=" . $expires . "&Signature=" . $signature . "&Key-Pair-Id=" . $key_pair_id;
                    $result .= $separator . "Expires=" . $expires . "&Signature=" . $signature . "&Key-Pair-Id=" . $key_pair_id;
                }
                // not using a canned policy, include the policy itself in the stream name
                else {
                    //$result .= $path . $separator . "Policy=" . $policy . "&Signature=" . $signature . "&Key-Pair-Id=" . $key_pair_id;
                    $result .= $separator . "Expires=" . $expires . "&Signature=" . $signature . "&Key-Pair-Id=" . $key_pair_id;
                }

                // new lines would break us, so remove them
                return str_replace('\n', '', $result);
        }

        function encode_query_params($stream_name)
        {
                // the adobe flash player has trouble with query parameters being passed into it,
                // so replace the bad characters with their url-encoded forms
                return str_replace(
                    array('?', '=', '&'),
                    array('%3F', '%3D', '%26'),
                    $stream_name);
        }

        function get_canned_policy_stream_name($video_path, $private_key_filename, $key_pair_id, $expires)
        {
                // this policy is well known by CloudFront, but you still need to sign it, since it contains your parameters
                $canned_policy = '{"Statement":[{"Resource":"' . $video_path . '","Condition":{"DateLessThan":{"AWS:EpochTime":'. $expires . '}}}]}';
                // the policy contains characters that cannot be part of a URL, so we base64 encode it
                $encoded_policy = $this->url_safe_base64_encode($canned_policy);
                // sign the original policy, not the encoded version
                $signature = $this->rsa_sha1_sign($canned_policy, $private_key_filename);
                // make the signature safe to be included in a url
                $encoded_signature = $this->url_safe_base64_encode($signature);

                // combine the above into a stream name
                $stream_name = $this->create_stream_name($video_path, null, $encoded_signature, $key_pair_id, $expires);
                // url-encode the query string characters to work around a flash player bug
                return $this->encode_query_params($stream_name);
        }

        function get_custom_policy_stream_name($video_path, $private_key_filename, $key_pair_id, $policy)
        {
                // the policy contains characters that cannot be part of a URL, so we base64 encode it
                $encoded_policy = $this->url_safe_base64_encode($policy);
                // sign the original policy, not the encoded version
                $signature = $this->rsa_sha1_sign($policy, $private_key_filename);
                // make the signature safe to be included in a url
                $encoded_signature = $this->url_safe_base64_encode($signature);

                // combine the above into a stream name
                $stream_name = $this->create_stream_name($video_path, $encoded_policy, $encoded_signature, $key_pair_id, null);
                // url-encode the query string characters to work around a flash player bug
                return $this->encode_query_params($stream_name);
        }

        function update_stream_name($item) 
        {
                if (empty($item->file)) return $item->file;
                if (empty($this->private_key_filename)) return $item->file;
                if (empty($this->key_pair_id)) return $item->file;

                $expires = time() + 300; // 5 min from now
                $canned_policy_stream_name = $this->get_canned_policy_stream_name($item->file, $this->private_key_filename, $this->key_pair_id, $expires);
                return $canned_policy_stream_name;
                $client_ip = $_SERVER['REMOTE_ADDR'];
                $policy =
                '{'.
                    '"Statement":['.
                        '{'.
                            '"Resource":"'. $item->file . '",'.
                            '"Condition":{'.
                                '"IpAddress":{"AWS:SourceIp":"' . $client_ip . '/32"},'.
                                '"DateLessThan":{"AWS:EpochTime":' . $expires . '}'.
                            '}'.
                        '}'.
                    ']' .
                '}';
                $custom_policy_stream_name = $this->get_custom_policy_stream_name($item->file, $this->private_key_filename, $this->key_pair_id, $policy);
        }
}