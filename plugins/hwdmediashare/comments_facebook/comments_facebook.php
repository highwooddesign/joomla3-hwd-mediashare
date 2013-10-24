<?php
/**
 * @version    $Id: comments_facebook.php 538 2012-10-03 10:22:59Z dhorsfall $
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
class plgHwdmediashareComments_facebook
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
			$c = 'plgHwdmediashareComments_facebook';
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
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $plugin =& JPluginHelper::getPlugin('hwdmediashare', 'comments_facebook');
                
                // Die if plugin not avaliable
                if (isset($plugin->params)) 
                {
                        $params = new JRegistry( $plugin->params );
                }
                else
                {
                        $params = new JRegistry();
                }

                jimport( 'joomla.environment.uri' );
                
                $doc = & JFactory::getDocument();
                $doc->setMetaData("fb:app_id", $config->get('facebook_appid'));
                
                ob_start();
                ?>
                <div id="fb-root"></div>
                <script>(function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo $config->get('facebook_appid'); ?>";
                fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
                <div class="fb-comments" data-href="<?php echo JURI::getInstance()->toString(); ?>" data-num-posts="2" data-width="<?php echo $params->get('width', 470); ?>" data-colorscheme="<?php echo $params->get('color', 'light'); ?>"></div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;            
        }   
}