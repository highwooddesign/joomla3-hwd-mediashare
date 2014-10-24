<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.community.media
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

if (file_exists(JPATH_BASE.'/components/com_community/libraries/core.php'))
{
	require_once( JPATH_BASE.'/components/com_community/libraries/core.php');
}
else
{
	return true;
}

class plgCommunityMedia extends CApplications
{
	var $name	= 'hwdMediaShare';
	var $_name	= 'hwdmediashare';

	/**
	 * Constructor.
	 *
         * @access   public
	 * @param    object  $subject  The object to observe
	 * @param    object  $config   A JRegistry object that holds the plugin configuration.
	 * @returns  void
	 */
	public function plgCommunityMedia($subject, $config)
	{
		parent::__construct($subject, $config);
	}
        
	/**
	 * Generates the HTML to display the user profile tab.
	 *
         * @access  public
	 * @return  string  Either string HTML for profile content, or false if fails.
	 */
	public function onProfileDisplay()
	{
                // Load HWD assets.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');

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

                // Load HWD config, merge with parameters (and force reset).
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig($this->params, true);
                
                // Load the HWD language file.
                $lang = JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, true);
                
		// Get the user.
                $this->user = CFactory::getRequestUser();
                
                // Get data.
                $helper = new JViewLegacy;
                $helper->params = $config;                
                $helper->items = $this->getItems();
                $helper->utilities = hwdMediaShareUtilities::getInstance();
                $helper->columns = $config->get('list_columns', 3);
                $helper->return = base64_encode(JFactory::getURI()->toString());

                // Add assets to the head tag.
                JHtml::_('hwdhead.core', $config);
                
                ob_start();
                ?>
<div id="hwd-container">
  <div class="media-details-view">
    <?php if (empty($helper->items)): ?>
      <div class="alert alert-no-items">
        <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
      </div>
    <?php else: ?>
      <?php echo JLayoutHelper::render('media_details', $helper, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    <?php endif; ?>
  </div> 
</div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;
	}

	/**
	 * Performs system startup processes.
	 *
         * @access  public
	 * @return  boolean  True on success, false on fail.
	 */
	public function onSystemStart()
	{
                if(!class_exists('CFactory'))
                {
                        require_once( JPATH_BASE . '/components/com_community/libraries/core.php');
                }
                
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');

                // Load the HWD language file.
                $lang = JFactory::getLanguage();
                $lang->load('plg_community_media', JPATH_ADMINISTRATOR, $lang->getTag());

                // Initialize the toolbar object.
                $toolbar = CFactory::getToolbar();

                // Adding new toolbar options.
                if ($this->params->get('toolbar_media_menu')) $toolbar->addGroup('HWDMS', JText::_('PLG_COMMUNITY_MEDIA_MENU_MEDIA'), JRoute::_(hwdMediaShareHelperRoute::getMediaRoute()));
                if ($this->params->get('toolbar_mymedia_menu')) $toolbar->addItem('HWDMS', 'HWDVS_ALL', JText::_('PLG_COMMUNITY_MEDIA_MENU_MYMEDIA'), JRoute::_(hwdMediaShareHelperRoute::getMyMediaRoute()));
                if ($this->params->get('toolbar_upload_menu')) $toolbar->addItem('HWDMS', 'HWDVS_UPLOAD', JText::_('PLG_COMMUNITY_MEDIA_MENU_UPLOAD'), JRoute::_(hwdMediaShareHelperRoute::getUploadRoute()));
	}

	/**
	 * Render stream display.
	 *
         * @access  public
         * @param   object  $act  The activity object.
	 * @return  object  The stream object.
	 */
	public function onCommunityStreamRender($act)
        {
                // Load HWD assets.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');
                JLoader::register('hwdMediaShareHelperNavigation', JPATH_ROOT.'/components/com_hwdmediashare/helpers/navigation.php');

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

                // Load HWD config, merge with parameters (and force reset).
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig($this->params, true);

                // Load lite CSS.
                $config->set('load_lite_css', 1);

                // Load the HWD language file.
                $lang = JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);
                
                // Get data.
                $helper = new JViewLegacy;
                $helper->params = $config;                
                $helper->item = $this->getItem($act->cid);
                $helper->utilities = hwdMediaShareUtilities::getInstance();
                $helper->columns = $config->get('list_columns', 3);
                $helper->return = base64_encode(JFactory::getURI()->toString());

                // Add assets to the head tag.
                JHtml::_('hwdhead.core', $config);

                ob_start();
                ?>
<div class="hwd-container">
  <div class="media-details-view">
    <?php echo JLayoutHelper::render('mediaitem_layout_activity', $helper, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
  </div> 
</div>
                <?php
                $message = ob_get_contents();
                ob_end_clean();
                
                $HWDact = new stdClass();
                $HWDact->actor = $act->actor;
                $HWDact->author = $config->get('author') == 0 ? JFactory::getUser($act->actor)->name : JFactory::getUser($act->actor)->username;
                $HWDact->action = $act->cid;
                $HWDact->target = 0;
                $HWDact->verb = 2;
                
                $stream = new stdClass();
                $stream->actor = CFactory::getUser($act->actor);
                $stream->target = null;
                $stream->headline = hwdMediaShareActivities::renderActivityHtml($HWDact);
                $stream->message = $message;
                $stream->group = "";
                $stream->attachments = array();

                return $stream;
        }      

	/**
	 * Method to get a list of items.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */        
	public function getItems()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $doc = JFactory::getDocument();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));               

                // Populate state (and set the context).
                $model->context = 'cb_media';
		$model->populateState();

		// Set the start and limit states.
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $this->params->get('count', 6));

		// Set the ordering states.
                $model->setState('list.ordering', 'a.created');
                $model->setState('list.direction', 'DESC');

                // Set the author states.
                $model->setState('filter.author_id', $this->user->id);
                $model->setState('filter.author_id.include', 1);

                return $model->getItems();           
	}
        
	/**
	 * Method to get a single item.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */        
	public function getItem($pk)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $doc = JFactory::getDocument();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('MediaItem', 'hwdMediaShareModel', array('ignore_request' => true));               

                // Populate state (and set the context).
                $model->context = 'plg_community_media';
		$model->populateState();

		// Set the media.id state.
		$model->setState('media.id', $pk);

                return $model->getItem();           
	}            
}
