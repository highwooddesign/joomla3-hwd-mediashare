<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.content.media
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgContentMedia extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @access protected
	 * @var    boolean
	 */
	protected $autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @access  public
	 * @param   object  $subject   The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 * @return  void
	 */
	public function __construct($subject, $config = array())
	{
		parent::__construct($subject, $config);

                // Load HWD assets.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');
                JLoader::register('hwdMediaShareHelperNavigation', JPATH_ROOT . '/components/com_hwdmediashare/helpers/navigation.php');
                
                // Include JHtml helpers.
                JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/helpers/html');
                JHtml::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/helpers/html');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');

                // Load HWD config (and force reset).
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig(null, true);
                $config->set('load_lite_css', 1);
                
                // Load the HWD language file.
                $lang = JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);


                // Add assets to the head tag.
                JHtml::_('hwdhead.core', $config);
	}
        
	/**
	 * Displays the HWDMediaShare content in an article.
	 *
         * @access  public
	 * @param   string   $context   The context of the content being passed to the plugin
	 * @param   object   $row       The article object
	 * @param   object   $params    The article params
	 * @param   integer  $page      The 'page' number
	 * @return  string   A html string containing code for Disqus commenting system.
	 */
	public function onContentPrepare($context, $row, $params, $page = 0)
	{
		// Initialise variables.
                $app = JFactory::getApplication();

                // Simple admin check to determine whether plugin should process further.
		if ($app->isAdmin()) return true;

                // Simple performance check to determine whether plugin should process further.
		if (strpos($row->text, 'media') === false)
                {
			return true;
		}

		// Expression to search for.
		$regex	= '/{media\s+(.*?)}/i';

		// Find all instances of plugin and put in $matches.
		// $matches[0] is full pattern match, $matches[1] is the contents.
		preg_match_all($regex, $row->text, $matches, PREG_SET_ORDER);

                // Only process if any matches.
		if ($matches)
                {
			foreach ($matches as $match)
                        {
                                // Load HWD config (and force reset).
                                $hwdms = hwdMediaShareFactory::getInstance();
                                $this->config = $hwdms->getConfig(null, true);

                                // Merge with plugin parameters.
                                $this->config->merge($this->params);
                
                                $matcheslist =  explode(',', $match[1]);

                                $data = '';

                                foreach ($matcheslist as $list)
                                {
                                        $data.= "$list\n";
                                }
                            
                                // Merge data from the content code.
                                $options = new JRegistry($data);                               
                                $this->config->merge($options);

                                switch ($this->config->get('load', 'media'))
                                {
                                        case 'media':
                                                $output = $this->_loadMedia();
                                        break;
                                        case 'album':
                                                $output = $this->_loadAlbum();
                                        break;
                                }

                                // We should replace only first occurrence.
				$row->text = preg_replace("|$match[0]|", addcslashes($output, '\\'), $row->text, 1);
			}
		}
	}

	protected function _loadMedia()
	{         
                if (!$this->config->get('id'))
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }
 
                // Load media model to get media information.
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('MediaItem', 'hwdMediaShareModel', array('ignore_request' => true));
                
                // Populate the model states.
                $model->populateState();
         
                // Load the media item.
                $item = $model->getItem($this->config->get('id'));
                
                if (!$item)
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }

                // Base this helper on the JViewLegacy model.
                $helper	= new JViewLegacy;
                
                // Get data.
                $helper->params = $this->config;             
                $helper->item = $item;
                $helper->utilities = hwdMediaShareUtilities::getInstance();
                $helper->return = base64_encode(JFactory::getURI()->toString());

                $layout = JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts/mediaitem_layout_'.$this->config->get('layout', 'blog').'.php';
                if (JFile::exists($layout))
                {
                        return JLayoutHelper::render('mediaitem_layout_'.$this->config->get('layout', 'blog'), $helper, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts');
                }
                else
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }
	}
}
