<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.comments_disqus
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediashareComments_facebook extends JObject
{               
	/**
	 * Returns the plgHwdmediashareComments_facebook object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return plgHwdmediashareComments_facebook object.
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
	 * Method to insert the Facebook commenting system.
         * 
	 * @return  void
	 **/
	public function getComments($item, $elementType=1)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $doc = JFactory::getDocument();
                
                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'comments_facebook');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_comments_facebook', JPATH_SITE . '/administrator');

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_COMMENTS_FACEBOOK_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Set Facebook AppID value to allow moderation of comments
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