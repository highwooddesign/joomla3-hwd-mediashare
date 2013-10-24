<?php
/**
 * @version    $Id: comments_disqus.php 538 2012-10-03 10:22:59Z dhorsfall $
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
class plgHwdmediashareComments_disqus
{               
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

		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareComments_disqus';
                        $instance = new $c;
		}

		return $instance;
	}
    
        /**
	 * Method to add a file to the database
         * 
	 * @since   0.1
	 **/
	public function getComments()
	{
		$plugin =& JPluginHelper::getPlugin('hwdmediashare', 'comments_disqus');
		$params = new JRegistry( $plugin->params );
                
                // If shortname not defined then return
		if ($params->get('shortname') == '') return; 
                
                // Set a unique identifier for this thread
                $identifier = substr(md5($params->get('shortname')),0,10).'_hwdms_'.JRequest::getWord('view').JRequest::getInt('id');

                ob_start();
                ?>
                <div id="disqus_thread"></div>
                <script type="text/javascript">
                    var disqus_shortname = '<?php echo $params->get('shortname'); ?>';
                    var disqus_identifier = '<?php echo $identifier; ?>';
                    var disqus_config = function(){
                            this.language = '<?php echo $params->get('language'); ?>';
                    };
                                
                    (function() {
                        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
                        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                    })();
                </script>
                <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments</a></noscript>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;            
        }   
}