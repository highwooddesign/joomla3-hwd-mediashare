<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.hwdmediashare.comments_jcomments
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgHwdmediashareComments_jcomments extends JObject
{               
	/**
	 * Class constructor.
	 *
	 * @access	public
         * @return      void
	 */
	public function __construct()
	{
		parent::__construct();
	}
        
	/**
	 * Returns the plgHwdmediashareComments_jcomments object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access	public
	 * @return      object      The plgHwdmediashareComments_jcomments object.
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'plgHwdmediashareComments_jcomments';
                        $instance = new $c;
		}

		return $instance;
	}
    
        /**
	 * Method to insert the jComments commenting system.
         * 
	 * @access	public
	 * @param       object      $item           The item being discussed.
	 * @param       integer     $elementType    The element type being discussed: http://hwdmediashare.co.uk/learn/api/68-api-definitions
	 * @return      void
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
		$plugin = JPluginHelper::getPlugin('hwdmediashare', 'comments_jcomments');
		
                // Load the language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_hwdmediashare_comments_jcomments', JPATH_SITE . '/administrator');

                if (!$plugin)
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_COMMENTS_JCOMMENTS_ERROR_NOT_PUBLISHED'));
                        return false;
                }

                // Load parameters.
                $params = new JRegistry($plugin->params);

                // Define request parameters.
                $extension = $app->input->get('option', '', 'word');
                $view = $app->input->get('view', '', 'word');
                
                // Check we are viewing a media item.
                if ($extension != "com_hwdmediashare" && $view != "mediaitem")
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_COMMENTS_JCOMMENTS_ERROR_NOT_VIEWING_MEDIA'));
                        return false;
                }

		// Load jComments libraries.
                $comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
                if(!JFile::exists($comments))
                {
                        $this->setError(JText::_('PLG_HWDMEDIASHARE_COMMENTS_JCOMMENTS_ERROR_COMPONENT_NOT_INSTALLED'));
                        return false;                    
                }

                require_once($comments);

                // Return the commenting system.
                return JComments::showComments($item->id, $extension, $item->title);
        }   
}