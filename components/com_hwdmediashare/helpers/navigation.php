<?php
/**
 * @version    SVN $Id: navigation.php 1550 2013-06-11 10:54:58Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      14-Nov-2011 20:36:41
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare Navigation Helper
 *
 * @package	hwdMediaShare
 * @since       0.1
 */
abstract class hwdMediaShareHelperNavigation
{
        /**
	 * Method to insert Javascript declaration for live site variable,
	 *
	 * @since	0.1
	 */
	public static function setJavascriptVars()
	{
                $doc = & JFactory::getDocument();
                $js = array();
                $js[] = 'var hwdms_live_site = "' . JURI::root() . 'index.php";';
                $doc->addScriptDeclaration( implode("\n", $js) );
        }    
        
        /**
	 * Method to insert internal navigation using hwdMediaShare Joomla menu,
         * or fallback static menu
	 *
	 * @since	0.1
	 */
	public static function getInternalNavigation()
	{
                $app	= JFactory::getApplication();
                $view   = JRequest::getWord('view');
                $html   = '';
                $tmpl   = JRequest::getWord( 'tmpl', '' );
                
                JHtml::_('behavior.modal');

                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

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
                        <!-- Media Main Navigation -->
                        <div class="media-mediamenu">
                            <?php
                            if(count($list)) 
                            {
                                    require JModuleHelper::getLayoutPath('mod_menu', $params->get('layout', 'default'));
                            }
                            else
                            {
                            ?>
                                <ul class="menu">
                                    <li class="<?php echo ($view == 'discover' ? 'active ' : false); ?>media-medianav-discover"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=discover'); ?>"><?php echo JText::_('COM_HWDMS_DISCOVER'); ?></a></li>
                                    <li class="<?php echo (($view == 'media' || $view == 'mediaitem') ? 'active ' : false); ?>media-medianav-media"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=media'); ?>"><?php echo JText::_('COM_HWDMS_MEDIA'); ?></a></li>
                                    <li class="<?php echo (($view == 'categories' || $view == 'category') ? 'active ' : false); ?>media-medianav-categories"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=categories'); ?>"><?php echo JText::_('COM_HWDMS_CATEGORIES'); ?></a></li>
                                    <li class="<?php echo (($view == 'albums' || $view == 'album') ? 'active ' : false); ?>media-medianav-albums"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=albums'); ?>"><?php echo JText::_('COM_HWDMS_ALBUMS'); ?></a></li>
                                    <li class="<?php echo (($view == 'groups' || $view == 'group') ? 'active ' : false); ?>media-medianav-groups"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=groups'); ?>"><?php echo JText::_('COM_HWDMS_GROUPS'); ?></a></li>
                                    <li class="<?php echo (($view == 'playlists' || $view == 'playlist') ? 'active ' : false); ?>media-medianav-playlists"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=playlists'); ?>"><?php echo JText::_('COM_HWDMS_PLAYLISTS'); ?></a></li>
                                    <li class="<?php echo (($view == 'users' || $view == 'user') ? 'active ' : false); ?>media-medianav-channels"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=users'); ?>"><?php echo JText::_('COM_HWDMS_USER_CHANNELS'); ?></a></li>
                                    <li class="<?php echo ($view == 'upload' ? 'active ' : false); ?>media-medianav-upload"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=upload'); ?>"><?php echo JText::_('COM_HWDMS_UPLOAD'); ?></a></li>
                                    <li class="<?php echo ($view == 'account' ? 'active ' : false); ?>media-medianav-account"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account'); ?>"><?php echo JText::_('COM_HWDMS_MY_ACCOUNT'); ?></a></li>
                                    <li class="<?php echo ($view == 'search' ? 'active ' : false); ?>media-medianav-search"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=search'); ?>"><?php echo JText::_('COM_HWDMS_SEARCH'); ?></a></li>
                                </ul>
                            <?php
                            }
                            ?>
                        </div>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                }
		return $html;
	}
	/**
	 * Method to insert accoutn navigation menu,
	 *
	 * @since	0.1
	 */
	public static function getAccountNavigation()
	{
                $user = JFactory::getUser();
                $uri	= JFactory::getURI();
                
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                ob_start();
                ?>                     
                <!-- Media Main Navigation -->
                <div class="media-accountmenu">
                  <ul class="media-accountnav">
                    <li class="media-accountnav-overview"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account'); ?>"><?php echo JText::_('COM_HWDMS_OVERVIEW'); ?></a></li>
                    <li class="media-accountnav-profile"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=userform.edit&id='.$user->id.'&return='.base64_encode($uri)); ?>"><?php echo JText::_('COM_HWDMS_PROFILE'); ?></a></li>
                    <li class="media-accountnav-media"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=media'); ?>"><?php echo JText::_('COM_HWDMS_MY_MEDIA'); ?></a></li>
                    <li class="media-accountnav-favourites"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=favourites'); ?>"><?php echo JText::_('COM_HWDMS_MY_FAVOURITES'); ?></a></li>
                    <?php if ($config->get('enable_albums')): ?><li class="media-accountnav-albums"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=albums'); ?>"><?php echo JText::_('COM_HWDMS_MY_ALBUMS'); ?></a></li><?php endif; ?>
                    <?php if ($config->get('enable_groups')): ?><li class="media-accountnav-groups"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=groups'); ?>"><?php echo JText::_('COM_HWDMS_MY_GROUPS'); ?></a></li><?php endif; ?>
                    <?php if ($config->get('enable_playlists')): ?><li class="media-accountnav-playlists"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=playlists'); ?>"><?php echo JText::_('COM_HWDMS_MY_PLAYLISTS'); ?></a></li><?php endif; ?>
                    <?php if ($config->get('enable_subscriptions')): ?><li class="media-accountnav-subscriptions"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=subscriptions'); ?>"><?php echo JText::_('COM_HWDMS_MY_SUBSCRIPTIONS'); ?></a></li><?php endif; ?>
                  </ul>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();

		return $html;
	}
        
        /**
	 * @since	0.1
	 */
	public function pageNavigation($row, $params, $page=0)
	{
                // Get a reference to the global cache object.
                $cache = & JFactory::getCache();
                $cache->setCaching( 1 );
                return $cache->call( array( 'hwdMediaShareHelperNavigation', 'cachedPageNavigation' ), $row, $params, $page );                        
	}
        
        /**
	 * @since	0.1
	 */
	public function cachedPageNavigation($row, $params, $page=0)
	{
		$view = JRequest::getCmd('view');
		$print = JRequest::getBool('print');

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Get additional filters
		$category_id = JRequest::getInt('category_id');
		$playlist_id = JRequest::getInt('playlist_id');
		$album_id = JRequest::getInt('album_id');
		$group_id = JRequest::getInt('group_id');

		if ($print)
                {
			return false;
		}

                $nav = new StdClass;

                if ($view == 'mediaitem')
                {
			$db	= JFactory::getDbo();
			$user	= JFactory::getUser();
			$app	= JFactory::getApplication();
			$lang	= JFactory::getLanguage();
			$nullDate = $db->getNullDate();

			$date	= JFactory::getDate();
			$now	= $date->toSql();

			$uid	= $row->id;

                        // Get media model
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $model =& JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));

                        // Set application parameters in model
                        $app = JFactory::getApplication();
                        $appParams = $app->getParams();
                        $model->setState('params', $appParams);

                        // Get all items     
                        $model->setState('list.limit', 0);
                        $model->setState('list.start', 0); 

                        // Set other filters
			$listOrder = 'a.created';
                        if ($category_id)
                        {
                                $model->setState('filter.category_id', $category_id); 
                                $listOrder = $app->getUserStateFromRequest('com_hwdmediashare.media.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created')));
                        }      
                        elseif ($playlist_id)
                        {
                                $model->setState('filter.playlist_id', $playlist_id); 
                                $listOrder = $app->getUserStateFromRequest('com_hwdmediashare.media.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created')));
                        } 
                        elseif ($album_id)
                        {
                                $model->setState('filter.album_id', $category_id);
                                $listOrder = $app->getUserStateFromRequest('com_hwdmediashare.media.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created')));
                        }
                        elseif ($group_id)
                        {
                                $model->setState('filter.group_id', $group_id); 
                                $listOrder = $app->getUserStateFromRequest('com_hwdmediashare.media.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created')));
                        } 

                        $listDirn = 'DESC';
                        if (in_array(strtolower($listOrder), array('a.title', 'author', 'a.ordering')))
                        {
                                $listDirn = 'ASC';
                        }

                        // Ordering
                        $model->setState('com_hwdmediashare.media.list.ordering', $listOrder);
                        $model->setState('com_hwdmediashare.media.list.direction', $listDirn);   
                
                        $user = JFactory::getUser();
                        if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) &&  (!$user->authorise('core.edit', 'com_hwdmediashare')))
                        {
                                // Limit to published for people who can't edit or edit.state.
                                $model->setState('filter.published',	1);
                                $model->setState('filter.status',	1);

                                // Filter by start and end dates.
                                $model->setState('filter.publish_date', true);
                        }
                        else
                        {
                                // Limit to published for people who can't edit or edit.state.
                                $model->setState('filter.published',	array(0,1));
                                $model->setState('filter.status',	1);
                        }
                
                        // Filter by language
                        $model->setState('filter.language', $app->getLanguageFilter());

			$query = $model->getListQuery();
                        // @TODO: Needs urgent work
                        $query.= ' LIMIT 0, 1000';                          
                        $db->setQuery($query);
			$list = $db->loadObjectList('id');

			// This check needed if incorrect Itemid is given resulting in an incorrect result.
			if (!is_array($list))
                        {
				$list = array();
			}

			reset($list);

			// Location of current item in array list.
			$location = array_search($uid, array_keys($list));

			$navs = array_values($list);

                        $nav->prev = null;
			$nav->next = null;

			if ($location -1 >= 0)	
                        {
				// The previous content item cannot be in the array position -1.
				$nav->prev = $navs[$location -1];
			}

			if (($location +1) < count($navs)) 
                        {
				// The next content item cannot be in an array position greater than the number of array postions.
				$nav->next = $navs[$location +1];
			}
		}

		return $nav;
	}        
}
