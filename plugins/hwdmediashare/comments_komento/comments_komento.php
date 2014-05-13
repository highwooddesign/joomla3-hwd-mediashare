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

class plgHwdmediashareComments_komento extends JObject
{
	/**
	 * Returns the plgHwdmediashareComments_komento object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return plgHwdmediashareComments_komento object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareComments_komento';
                        $instance = new $c;
		}

		return $instance;
	}

        /**
	 * Method to insert the Komento commenting system.
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
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'comments_komento');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_comments_komento', JPATH_SITE . '/administrator');

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_COMMENTS_KOMENTO_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Load Komento libraries.
                $bootstrap = JPATH_ROOT . '/components/com_komento/bootstrap.php';
                if(!JFile::exists($bootstrap))
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_COMMENTS_KOMENTO_ERROR_COMPONENT_NOT_INSTALLED'));
                        return false;                    
                }

                require_once($bootstrap);

                // Define request parameters.
                $extension = $app->input->get('option', '', 'word');
                
                // Passing in the data.
                $options		= array();
                $options['enable']	= true;
                //$options['trigger']	= $eventTrigger;
                //$options['context']	= $context;
                //$options['params']	= $params;
                //$options['page']	= $page;

                // Ready to Commentify!
                return Komento::commentify($extension, $item, $options);
        }
}
