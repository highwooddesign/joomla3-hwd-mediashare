<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareHelperNavigation
{
	/**
	 * Method to insert Javascript declaration for live site variable.
         * 
         * @access  public
         * @static
         * @return  void
	 */
	public static function setJavascriptVars()
	{
                $doc = JFactory::getDocument();
                $js = array();
                $js[] = 'var hwdms_live_site = "' . JURI::root() . 'index.php";';
                $doc->addScriptDeclaration( implode("\n", $js) );
        }    
        
	/**
	 * Method to insert internal navigation using hwdMediaShare Joomla menu,
         * or fallback static menu.
         * 
         * @access  public
         * @static
         * @return  string  The markup for the menu.
	 */
	public static function getInternalNavigation()
	{
            	// Initialise variables.
                $app = JFactory::getApplication();
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Get request variables.
                $view = $app->input->get('view', '', 'word');
                $tmpl = $app->input->get('tmpl', '', 'word');
                
                $html = '';

                if ($config->get('internal_navigation') != 0 && $tmpl != 'component')
                {               
                        JLoader::register('modMenuHelper', JPATH_SITE . '/modules/mod_menu/helper.php');

                        $params	= new JRegistry( '{"menutype":"hwdmediashare","show_title":""}' );

                        $list           = modMenuHelper::getList($params);
                        $app            = JFactory::getApplication();
                        $menu           = $app->getMenu();
                        $active         = $menu->getActive();
                        $active_id      = isset($active) ? $active->id : $menu->getDefault()->id;
                        $path           = isset($active) ? $active->tree : array();
                        $showAll	= $params->get('showAllChildren');
                        $class_sfx	= htmlspecialchars($params->get('class_sfx'));

                        ob_start();
                        ?>
                        <div class="media-mediamenu">
                            <?php if(count($list)): ?>
                                <ul class="nav nav-pills">
                                    <?php foreach ($list as $i => $item): ?>
                                        <li class=""><a href="<?php echo $item->flink; ?>"><?php echo $item->title; ?></a></li>
                                    <?php endforeach; ?>
                                </ul>                            
                            <?php else: ?>
                                <ul class="nav nav-pills">
                                    <li class="<?php echo ($view == 'discover' ? 'active ' : false); ?>media-medianav-discover"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=discover'); ?>"><?php echo JText::_('COM_HWDMS_DISCOVER'); ?></a></li>
                                    <li class="<?php echo (($view == 'media' || $view == 'mediaitem') ? 'active ' : false); ?>media-medianav-media"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=media'); ?>"><?php echo JText::_('COM_HWDMS_MEDIA'); ?></a></li>
                                    <li class="<?php echo (($view == 'categories' || $view == 'category') ? 'active ' : false); ?>media-medianav-categories"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=categories'); ?>"><?php echo JText::_('COM_HWDMS_CATEGORIES'); ?></a></li>
                                    <li class="<?php echo (($view == 'albums' || $view == 'album') ? 'active ' : false); ?>media-medianav-albums"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=albums'); ?>"><?php echo JText::_('COM_HWDMS_ALBUMS'); ?></a></li>
                                    <li class="<?php echo (($view == 'groups' || $view == 'group') ? 'active ' : false); ?>media-medianav-groups"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groups'); ?>"><?php echo JText::_('COM_HWDMS_GROUPS'); ?></a></li>
                                    <li class="<?php echo (($view == 'playlists' || $view == 'playlist') ? 'active ' : false); ?>media-medianav-playlists"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlists'); ?>"><?php echo JText::_('COM_HWDMS_PLAYLISTS'); ?></a></li>
                                    <li class="<?php echo (($view == 'users' || $view == 'user') ? 'active ' : false); ?>media-medianav-channels"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=channels'); ?>"><?php echo JText::_('COM_HWDMS_CHANNELS'); ?></a></li>
                                    <li class="<?php echo ($view == 'upload' ? 'active ' : false); ?>media-medianav-upload"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload'); ?>"><?php echo JText::_('COM_HWDMS_UPLOAD'); ?></a></li>
                                    <li class="<?php echo ($view == 'account' ? 'active ' : false); ?>media-medianav-account"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account'); ?>"><?php echo JText::_('COM_HWDMS_MY_ACCOUNT'); ?></a></li>
                                    <li class="<?php echo ($view == 'search' ? 'active ' : false); ?>media-medianav-search"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=search'); ?>"><?php echo JText::_('COM_HWDMS_SEARCH'); ?></a></li>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                }
                
		return $html;
	}
        
	/**
	 * Method to return the cached page navigation object.
         * 
         * @access  public
         * @static
         * @param   object  $current    The current media object.
         * @param   object  $params     Parameter options.
         * @return  object  The navigation object.
	 */
	public static function pageNavigation($current, $params)
	{
                // We force method caching for this potentially complex query.
                $cache = JFactory::getCache();
                $cache->setCaching(1);
                return $cache->call(array('hwdMediaShareHelperNavigation', 'cachedPageNavigation' ), $current, $params);                        
	}
        
	/**
	 * Method to return the page navigation object.
         * 
         * @access  public
         * @static
         * @param   object  $current    The current media object.
         * @param   object  $params     Parameter options.
         * @return  object  The navigation object.
	 */
	public static function cachedPageNavigation($current, $params)
	{
            	// Initialise variables.
                $app = JFactory::getApplication();
                $db = JFactory::getDBO();

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Get request variables.
		$view = $app->input->get('view', '', 'cmd');
		$print = $app->input->get('print', '', 'bool');
		$category_id = $app->input->get('category_id', '', 'int');
		$playlist_id = $app->input->get('playlist_id', '', 'int');
		$album_id = $app->input->get('album_id', '', 'int');
		$group_id = $app->input->get('group_id', '', 'int');

		if ($print)
                {
			return false;
		}

                $nav = new JObject;
                $nav->hasNav = false;
                $nav->category = null;
                $nav->playlist = null;
                $nav->album = null;
                $nav->group = null;

                if ($view == 'mediaitem' && ($category_id || $playlist_id || $album_id || $group_id))
                {
                        $nav->hasNav = true;
                        
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                        $model->context = 'com_hwdmediashare.navigation';
                        $model->populateState();  

                        if ($category_id)
                        {
                                $model->setState('filter.category_id', $category_id);
                                
                                // Get ordering.
                                if (!$list = $app->getUserStateFromRequest('com_hwdmediashare.category.list', 'list', array(), 'array'))
                                {
                                        $list['fullordering'] = $config->get('list_order_media', 'a.created DESC');
                                }
                                
                                // Load category.
				JLoader::register('hwdMediaShareModelCategory', JPATH_ROOT . '/components/com_hwdmediashare/models/category.php');
				$categoryModel = JModelLegacy::getInstance('category', 'hwdMediaShareModel', array('ignore_request' => true));
				$categoryModel->getItems(); 
				$nav->category = $categoryModel->getCategory((int) $category_id); 
                                if ($nav->category === false && $categoryModel->getError())
                                {
                                        $nav->hasNav = false;
                                        return $nav;
                                }                
                        }      
                        elseif ($playlist_id)
                        {
                                $model->setState('filter.playlist_id', $playlist_id); 
                                
                                // Get ordering.
                                if (!$list = $app->getUserStateFromRequest('com_hwdmediashare.playlist.list', 'list', array(), 'array'))
                                {
                                        $list['fullordering'] = $config->get('list_order_media', 'a.created DESC');
                                }
                                
                                // Load playlist.
				JLoader::register('hwdMediaShareModelPlaylist', JPATH_ROOT . '/components/com_hwdmediashare/models/playlist.php');
				$playlistModel = JModelLegacy::getInstance('playlist', 'hwdMediaShareModel', array('ignore_request' => true));
				$nav->playlist = $playlistModel->getPlaylist((int) $playlist_id); 
                                if ($nav->playlist === false && $playlistModel->getError())
                                {
                                        $nav->hasNav = false;
                                        return $nav;
                                }                            
                        } 
                        elseif ($album_id)
                        {
                                $model->setState('filter.album_id', $album_id);
                                
                                // Get ordering.
                                if (!$list = $app->getUserStateFromRequest('com_hwdmediashare.album.list', 'list', array(), 'array'))
                                {
                                        $list['fullordering'] = $config->get('list_order_media', 'a.created DESC');
                                }
                                
                                // Load album.
				JLoader::register('hwdMediaShareModelAlbum', JPATH_ROOT . '/components/com_hwdmediashare/models/album.php');
				$albumModel = JModelLegacy::getInstance('album', 'hwdMediaShareModel', array('ignore_request' => true));
				$nav->album = $albumModel->getAlbum((int) $album_id);  
                                if ($nav->album === false && $albumModel->getError())
                                {
                                        $nav->hasNav = false;
                                        return $nav;
                                }                                    
                        }
                        elseif ($group_id)
                        {
                                $model->setState('filter.group_id', $group_id);
                                
                                // Get ordering.
                                if (!$list = $app->getUserStateFromRequest('com_hwdmediashare.group.list', 'list', array(), 'array'))
                                {
                                        $list['fullordering'] = $config->get('list_order_media', 'a.created DESC');
                                } 

                                // Load group.
				JLoader::register('hwdMediaShareModelGroup', JPATH_ROOT . '/components/com_hwdmediashare/models/group.php');
				$groupModel = JModelLegacy::getInstance('group', 'hwdMediaShareModel', array('ignore_request' => true));
				$nav->group = $groupModel->getGroup((int) $group_id); 
                                if ($nav->group === false && $groupModel->getError())
                                {
                                        $nav->hasNav = false;
                                        return $nav;
                                }                                    
                        } 

                        // Set the ordering.
                        $ordering = explode(' ', $list['fullordering']);   
                        $model->setState('list.ordering', $ordering[0]);
                        $model->setState('list.direction', $ordering[1]); 
                              
                        try
                        {                
                                $db->setQuery($model->getListQuery());
                                $list = $db->loadObjectList('id');
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }

                        if (count($list))
                        {
                                if (!is_array($list))
                                {
                                        $list = array();
                                }

                                reset($list);

                                // Location of current item in array list.
                                $location = array_search($current->id, array_keys($list));

                                $navs = array_values($list);

                                $nav->prev = null;
                                $nav->next = null;

                                if ($location -1 >= 0)	
                                {
                                        // The previous media item cannot be in the array position -1.
                                        $nav->prev = $navs[$location-1];
                                }

                                if (($location +1) < count($navs)) 
                                {
                                        // The next media item cannot be in an array position greater than the number of array postions.
                                        $nav->next = $navs[$location+1];
                                }
                        }
                }
                
                return $nav;
	}        
}
