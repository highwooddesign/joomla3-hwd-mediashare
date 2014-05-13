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

class plgHwdmediashareComments_disqus extends JObject
{               
	/**
	 * Returns the plgHwdmediashareComments_disqus object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return plgHwdmediashareComments_disqus object.
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
	 * Method to insert the Disqus commenting system.
         * 
	 * @return  void
	 **/
	public function getComments($item, $elementType=1)
	{
		// Initialise variables.
                $app = JFactory::getApplication();

                // Load plugin.
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'comments_disqus');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_comments_disqus', JPATH_SITE . '/administrator');

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_COMMENTS_DISQUS_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // If shortname not defined then return.
		if ($params->get('shortname') == '')
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_COMMENTS_DISQUS_ERROR_NO_SHORTNAME'));
                        return false;
                }                    
                
                // Set a unique identifier for this thread.
                $identifier = substr(md5($params->get('shortname')), 0, 10) . '_hwdms_' . $app->input->get('view', '', 'word') . $app->input->get('id', '', 'integer');

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