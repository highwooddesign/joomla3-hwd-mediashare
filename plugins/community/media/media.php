<?php
/**
 * @version    SVN $Id: media.php 1616 2013-07-15 13:15:44Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      27-Feb-2012 16:29:22
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

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

	function plgCommunityMedia(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
        
	function onProfileDisplay()
	{
		$config	=& CFactory::getConfig();
		$this->loadUserParams();
 
		$uri		= JURI::base();
		$my		=& JFactory::getUser();
		$user		=& CFactory::getActiveProfile();
		$doc            =& JFactory::getDocument();

                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');
                JLoader::register('hwdMediaShareHelperNavigation', JPATH_ROOT.'/components/com_hwdmediashare/helpers/navigation.php');

                // Download links
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('utilities');
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());
                
		// Need to load Mootools before our JS
                JHtml::_('behavior.framework', true);
                $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
		$doc->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/hwd.min.js');
		
                $cache =& JFactory::getCache('community');
		$callback = array($this, '_getMediaHTML');
 
		$content = $cache->call($callback, $this->params, $user);
 
		return $content; 
	}
 
	function _getMediaHTML($params, $user) 
        {
                $app = & JFactory::getApplication();
                $doc = & JFactory::getDocument();

                jimport( 'joomla.application.component.model' );
                // Get an instance of the generic user model
                //JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                //$model =& JModel::getInstance('User', 'hwdMediaShareModel', array('ignore_request' => true));
                $version = new JVersion();
                ($version->RELEASE >= 3.0 ? JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models') : JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models'));
                $model = ($version->RELEASE >= 3.0 ? JModelLegacy::getInstance('User', 'hwdMediaShareModel', array('ignore_request' => true)) : JModel::getInstance('User', 'hwdMediaShareModel', array('ignore_request' => true)));

                $model->setState('user.id', $user->id);

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
                
                $hwdms = hwdMediaShareFactory::getInstance();
                $this->config = $hwdms->getConfig();
                $this->config->merge( $params );

		// Check if we need to load the Joomla styles
                if ($this->config->get('load_joomla_css') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
                
                $model->params = $this->config;               
                $model->setState('params', $appParams);               
                
                // Get data from the model
		$channel = $model->getChannel();

                $media = $model->getMedia();
                $activities = $model->getActivities();
                $favourites = $model->getFavourites();
                $groups = $model->getGroups();
                $playlists = $model->getPlaylists();
                $albums = $model->getAlbums();
                $subscribers = $model->getSubscribers();          

		$state	= $model->getState();

		// Check for errors.
		if (count($errors = $model->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
                
                $this->columns=	        $params->get('list_columns', 3);
                $this->display=	'details';                
                $this->return=                base64_encode(JFactory::getURI()->toString());
                $this->utilities = hwdMediaShareUtilities::getInstance();

                $this->channel=	$channel;

                $this->media=	$media;
		$this->activities=		$activities;
		$this->favourites=		$favourites;
		$this->groups=	$groups;
		$this->playlists=		$playlists;
		$this->albums=	$albums;
		$this->subscribers=		$subscribers;

                $this->state=		$state;
                $this->params=		$this->config;

                // Setup the modal display
                if ($this->params->get('display') == 'modal')
                {
                        $this->params->set('modal', 1);
                }                
               
                ob_start();
                require 'assets/tmpl/default.php';
                $html = ob_get_contents();
                ob_end_clean();

                return $html; 
	}
        
	function loadTemplate($template)
	{
		// Build the template and base path for the layout
		$path = JPATH_ROOT.'/plugins/community/media/assets/tmpl/'.$template.'.php';

		// If the template has a layout override use it
		if (file_exists($path))
		{
                        ob_start();
                        require $path;
                        $html = ob_get_contents();
                        ob_end_clean();

                        return $html; 
		}
                
                return false;
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getActivities( &$item, $parent = true )
	{
                hwdMediaShareFactory::load('activities');
                return hwdMediaShareActivities::getActivities($item, $parent);
        } 
        
	function onSystemStart()
	{
		$plugin =& JPluginHelper::getPlugin('community', 'media');
		$params = new JRegistry( $plugin->params );
                
                $user =& CFactory::getActiveProfile();
                
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');

                if(!class_exists('CFactory'))
                {
                        require_once( JPATH_BASE . '/components/com_community/libraries/core.php');
                }
                
                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('plg_community_media', JPATH_ADMINISTRATOR, $lang->getTag());

                // Initialize the toolbar object
                $toolbar =& CFactory::getToolbar();

                // Adding new 'tab' in JomSocial toolbar
                if ($params->get('toolbar_media_menu') == "show") $toolbar->addGroup('HWDMS', JText::_('PLG_COMMUNITY_MENU_MEDIA'), JRoute::_(hwdMediaShareHelperRoute::getMediaRoute()));
                if ($params->get('toolbar_mymedia_menu') == "show") $toolbar->addItem('HWDMS', 'HWDVS_ALL', JText::_('PLG_COMMUNITY_MENU_MYMEDIA'), JRoute::_(hwdMediaShareHelperRoute::getMyMediaRoute()));
                if ($params->get('toolbar_upload_menu') == "show") $toolbar->addItem('HWDMS', 'HWDVS_UPLOAD', JText::_('PLG_COMMUNITY_MENU_UPLOAD'), JRoute::_(hwdMediaShareHelperRoute::getUploadRoute()));
	}

	function onActivityContentDisplay($act)
	{
		$plugin =& JPluginHelper::getPlugin('community', 'media');
		$params = new JRegistry( $plugin->params );
                
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');

                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('downloads');
                JLoader::register('JHtmlHwdIcon', JPATH_SITE.'/components/com_hwdmediashare/helpers/icon.php');
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
		$doc->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/hwd.min.js');
                
                switch (@$act->cmd) 
                {
                    case "media.add":
                    default:
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                        $table->load( $act->cid );
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');
                        $item->media_type = hwdMediaShareMedia::loadMediaType($item);;
                        ob_start();
                        ?>
                        <div class="media-item">
                            <!-- Media Type -->
                            <?php if ($this->params->get('list_meta_thumbnail') != 'hide') :?>
                            <?php if ($this->params->get('list_meta_type_icon') != 'hide') :?>
                            <div class="media-item-format-1-<?php echo $item->media_type; ?>">
                                <img src="<?php echo JHtml::_('hwdicon.overlay', '1-'.$item->media_type, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_MEDIA_TYPE'); ?>" />
                            </div>
                            <?php endif; ?>
                            <?php if ($item->duration > 0) :?>
                            <div class="media-duration">
                                <?php echo hwdMediaShareMedia::secondsToTime($item->duration); ?>
                            </div>
                            <?php endif; ?>
                            <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->id)); ?>">
                                <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item)); ?>" border="0" alt="" style="max-width:120px;" />
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                        break;
                }
		return $html;
	}
        
	function onAfterMediaAdd($media)
	{
                $plugin =& JPluginHelper::getPlugin('community', 'media');
		$params = new JRegistry( $plugin->params );

                JLoader::register('hwdMediaShareHelperRoute', JPATH_SITE.'/components/com_hwdmediashare/helpers/route.php');
                
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                if (file_exists(JPATH_SITE.'/components/com_community/libraries/core.php'))
                {
                        require_once(JPATH_SITE.'/components/com_community/libraries/core.php');
                        require_once(JPATH_SITE.'/components/com_community/libraries/error.php');

                        if ($params->get('activity_new_media') == 1)
                        {
                                if ($params->get('api', 1) == 1)
                                {                             
                                        // @TODO: Why on earth do I need to do this JomSocial?
                                        JTable::addIncludePath( JPATH_SITE.'/administrator/components/com_community/tables/' );
                                        JTable::getInstance( 'Configuration' , 'CommunityTable' );

                                        // Load the HWDMediaShare language file
                                        $lang =& JFactory::getLanguage();
                                        $lang->load('plg_community_media', JPATH_ADMINISTRATOR, $lang->getTag());

                                        $act = new stdClass();
                                        $act->cmd       = 'media.add';
                                        $act->actor     = $media['created_user_id'];
                                        $act->target    = 0; // No target
                                        $act->title 	= JText::sprintf('COM_HWDMS_X_UPLOADED_A_NEW_MEDIA', '{actor}', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($media['id'])).'">'.$media['title'].'</a>');
                                        $act->content   = '';
                                        $act->app       = 'media';
                                        $act->cid       = $media['id'];
                                        $act->params	= ''; 

                                        CFactory::load('libraries', 'activities');

                                        // Adding support for LIKE and COMMENT in stream
                                        $act->comment_type = $act->cmd;
                                        $act->comment_id = CActivities::COMMENT_SELF;
                                        $act->like_type = $act->cmd;
                                        $act->like_id = CActivities::LIKE_SELF;
                                }
                                else
                                {
                                        $act = new stdClass();
                                        $act->cmd       = 'media.add';
                                        $act->actor     = $media['created_user_id'];
                                        $act->target    = 0; // No target
                                        $act->title 	= 'string';
                                        $act->content   = 'content';
                                        $act->app       = 'media.add';
                                        $act->cid       = $media['id'];
                                        $act->params	= '';                                    
                                }

                                // Insert into activity stream
                                CActivityStream::add($act); 
                        }

                        if ($params->get('points_new_media') == 1)
                        {
                                include_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php');
                                //CuserPoints::assignPoint('com_hwdvideoshare.onAfterVideoApproval', $my->id);
                        }
                }
		return true;
	}
        
        public function onCommunityStreamRender($act)
        {
                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('plg_community_media', JPATH_ADMINISTRATOR, $lang->getTag());

                // Get actor
                $actor = CFactory::getUser($act->actor);           
                $actorLink = '<a class="cStream-Author" href="' .CUrlHelper::userLink($actor->id).'">'.$actor->getDisplayName().'</a>';

                // Define the stream
                $stream = new stdClass();
                $stream->actor = $actor;
                
                // Get media
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Media', 'hwdMediaShareTable');

                // Attempt to load the row.
                if ($act->cid > 0 && $table->load($act->cid))
                {    
                        // Convert the JTable to a clean JObject.
                        $properties = $table->getProperties(1);
                        $media = JArrayHelper::toObject($properties, 'JObject');

                        if (!empty($media->title))
                        {
                                $stream->headline = JText::sprintf('PLG_COMMUNITY_ACT_X_ADDED_NEW_MEDIA_Y', $actorLink, '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($act->cid)).'">'.$media->title.'</a>');
                                $stream->message = '';
                        }
                }
   
		return $stream; 
        }        
}