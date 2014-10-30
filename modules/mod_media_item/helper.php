<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_item
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modMediaItemHelper extends JViewLegacy
{
	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   array  $module  The module object.
	 * @param   array  $params  The module parameters.
         * @return  void
	 */       
	public function __construct($module, $params)
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

                // Load HWD config, merge with module parameters (and force reset).
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig($params, true);

                // Load lite CSS.
                $config->set('load_lite_css', 1);
                                
                // Load the HWD language file.
                $lang = JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag());
                
                // Get data.
                $this->module = $module;                
                $this->params = $config;                
                $this->item = $this->getItem();
                $this->utilities = hwdMediaShareUtilities::getInstance();
                $this->columns = $params->get('list_columns', 3);
                $this->return = base64_encode(JFactory::getURI()->toString());

                // Add assets to the head tag.
                $this->addHead();
	}

	/**
	 * Method to add page assets to the <head> tags.
	 *
	 * @access  public
         * @return  void
	 */        
	public function addHead()
	{
                // Initialise variables.
		$doc = JFactory::getDocument();
                
                // Add page assets.
                JHtml::_('hwdhead.core', $this->params);
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
                
                // Extract the layout.
                list($template, $layout) = explode(':', $this->params->get('layout', '_:default'));

                if (file_exists(JPATH_ROOT.'/modules/mod_media_item/css/' . $layout . '.css'))
                {
                        $doc->addStyleSheet(JURI::base( true ) . '/modules/mod_media_item/css/' . $layout . '.css');
                }
	}

	/**
	 * Method to get a single item.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */        
	public function getItem()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $doc = JFactory::getDocument();
                $cache = JFactory::getCache('com_hwdmediashare');
                
                // Force method caching.
                $cache->setCaching(1);
                
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));               

                // Populate state (and set the context).
                $model->context = 'mod_media_media';
		$model->populateState();

                // Extract the layout.
                list($template, $layout) = explode(':', $this->params->get('layout', '_:default'));
                
                // Calculate carousel limit.
                $climit = (4 * $this->params->get('slidesToScroll')) * 2;
                
		// Set the start and limit states.
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) ($layout == 'carousel' ? $climit : $this->params->get('count', 0)));

		// Set the ordering states.
                $ordering = $this->params->get('list_order_media', 'a.created DESC');
                $orderingParts = explode(' ', $ordering); 
                $model->setState('list.ordering', $orderingParts[0]);
                $model->setState('list.direction', $orderingParts[1]);

                switch ($this->params->get('display_filter')) 
                {
                        // * SPECIAL * Return selected single media.
                        case 100:
                                $model = JModelLegacy::getInstance('MediaItem', 'hwdMediaShareModel', array('ignore_request' => true));               
                                $model->context = 'mod_media_item';
                                $model->populateState();
                                $model->setState('media.id', (int) $this->params->get('media_id'));
                                return $model->getItem();                        
                        break; 
                        
                        // Filter by selected album.
                        case 1:
                                if ((int) $this->params->get('album_id', 0) == 0) return;
                                $model->setState('filter.album_id', (int) $this->params->get('album_id', 0));
                                break; 
                            
                        // Filter by selected or viewed category.
                        case 2:
                                $model->setState('filter.category_id.include', (bool) $this->params->get('category_filtering_type', 1));
                                $catids = $this->params->get('catid');                                        

                                // Clean empty elements.
                                if (is_array($catids)) $catids = array_filter($catids);
                                
                                if ($catids)
                                {
                                        if ($this->params->get('show_child_category_articles', 0) && (int) $this->params->get('levels', 0) > 0) 
                                        {
                                                // Get an instance of the categories model.
                                                $categories = JModelLegacy::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true));
                                                $categories->setState('params', $app->getParams());
                                                $levels = $this->params->get('levels', 1) ? $this->params->get('levels', 1) : 9999;
                                                $categories->setState('filter.get_children', $levels);
                                                $categories->setState('filter.published', 1);
                                                $additional_catids = array();

                                                foreach($catids as $catid)
                                                {
                                                        $categories->setState('filter.parentId', $catid);
                                                        $recursive = true;
                                                        $items = $categories->getItems($recursive);

                                                        if ($items)
                                                        {
                                                                foreach($items as $category)
                                                                {
                                                                        $condition = (($category->level - $categories->getParent()->level) <= $levels);
                                                                        if ($condition) {
                                                                                $additional_catids[] = $category->id;
                                                                        }
                                                                }
                                                        }
                                                }

                                                $catids = array_unique(array_merge($catids, $additional_catids));
                                        }

                                        $model->setState('filter.category_id', $catids);
                                }
                                break;  
                            
                        // Filter by selected group.
                        case 3:
                                if ((int) $this->params->get('group_id', 0) == 0) return;
                                $model->setState('filter.group_id', (int) $this->params->get('group_id', 0));
                                break; 

                        // Filter by selected playlist.
                        case 4:
                                if ((int) $this->params->get('playlist_id', 0) == 0) return;
                                $model->setState('filter.playlist_id', (int) $this->params->get('playlist_id', 0));
                                break; 
                            
                        // Filter by selected user.
                        case 5:
                                $model->setState('filter.author_id', $this->params->get('created_by', ''));
                                $model->setState('filter.author_id.include', $this->params->get('author_filtering_type', 1));
                                break; 

                        // Filter by selected dates.
                        case 6:
                                $date_filtering = $this->params->get('date_filtering', 'off');
                                if ($date_filtering !== 'off') 
                                {
                                        $model->setState('filter.date_filtering', $date_filtering);
                                        $model->setState('filter.date_field', $this->params->get('date_field', 'a.created'));
                                        $model->setState('filter.start_date_range', $this->params->get('start_date_range', '1000-01-01 00:00:00'));
                                        $model->setState('filter.end_date_range', $this->params->get('end_date_range', '9999-12-31 23:59:59'));
                                        $model->setState('filter.relative_date', $this->params->get('relative_date', 30));
                                }
                                break; 
                            
                        // Filter by being watched now.
                        case 10:
                                $model->setState('filter.date_filtering', 'relative');
                                $model->setState('filter.date_field', 'a.viewed');
                                $model->setState('filter.relative_date', 1);
                                $model->setState('list.ordering', 'a.viewed');
                                $model->setState('list.direction', 'desc');
                                break; 

                        // Filter by relation to current page.
                        case 11:
                                $title = $doc->getTitle();
                                $model->setState('filter.search.method', 'match');
                                $model->setState('filter.search', $title);
                                break;

                        // Filter by viewed album.
                        case 12:
                                if (!$album_id = $this->getAlbum()) return;
                                $model->setState('filter.album_id', (int) $album_id);
                                break; 
                                
                        // Filter by viewed category.
                        case 13:
                                if (!$category_id = $this->getCategory()) return;
                                $model->setState('filter.category_id', (int) $category_id);
                                break; 
                                
                        // Filter by viewed group.
                        case 14:
                                if (!$group_id = $this->getGroup()) return;
                                $model->setState('filter.group_id', (int) $group_id);
                                break; 
                                
                        // Filter by viewed playlist.
                        case 15:
                                if (!$playlist_id = $this->getPlaylist()) return;
                                $model->setState('filter.playlist_id', (int) $playlist_id);
                                break; 
                                
                        // Filter by viewed user.
                        case 16:
                                if (!$user_id = $cache->call(array($this, 'getUser'), $app->input)) return;
                                $model->setState('filter.author_id', $user_id);
                                $model->setState('filter.author_id.include', 1);
                                $model->setState('filter.author_alias', '');
                                $model->setState('filter.author_alias.include', 1);
                                break; 
                                
                        // Filter by linked media of viewed media.
                        case 17:
                                if (!$media_id = $cache->call(array($this, 'getMedia'))) return;
                                break; 
                                
                        // Filter by responses of viewed media.
                        case 18:
                                if (!$media_id = $cache->call(array($this, 'getMedia'))) return;
                                break; 
                }

		// Additional filters.
                $model->setState('filter.media_type', $this->params->get('list_media_type', 0));                
		$model->setState('filter.featured', $this->params->get('show_featured', 'show'));

                $this->item = $model->getItem();

                return $this->item;               
	}
        
        /**
	 * Method to get the album ID.
	 *
	 * @access  public
	 * @return  integer The album ID if found, or false.
	 **/
	public function getAlbum()
	{     
                $app = JFactory::getApplication();

                $option	= $app->input->get('option', '', 'word');
                $view = $app->input->get('view', '', 'word');
                $id = $app->input->get('id', '', 'int');
                $album_id = $app->input->get('album_id', '', 'int');
    
                if ($option == 'com_hwdmediashare' && $view == 'album' && $id)
                {
                        return $id;
                }

                if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $album_id)
                {
                        return $album_id;
                }
                
                return false;
	} 
        
        /**
	 * Method to get the category ID.
	 *
	 * @access  public
	 * @return  integer The category ID if found, or false.
	 **/
	public function getCategory()
	{
                $app = JFactory::getApplication();

                $option	= $app->input->get('option', '', 'word');
                $view = $app->input->get('view', '', 'word');
                $id = $app->input->get('id', '', 'int');
                $category_id = $app->input->get('category_id', '', 'int');
    
                if ($option == 'com_hwdmediashare' && $view == 'category' && $id)
                {
                        return $id;
                }

                if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $category_id)
                {
                        return $category_id;
                }
                
                return false;
	}
        
        /**
	 * Method to get the group ID.
	 *
	 * @access  public
	 * @return  integer The group ID if found, or false.
	 **/
	public function getGroup()
	{
                $app = JFactory::getApplication();

                $option	= $app->input->get('option', '', 'word');
                $view = $app->input->get('view', '', 'word');
                $id = $app->input->get('id', '', 'int');
                $group_id = $app->input->get('group_id', '', 'int');
    
                if ($option == 'com_hwdmediashare' && $view == 'group' && $id)
                {
                        return $id;
                }

                if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $group_id)
                {
                        return $group_id;
                }
                
                return false;
	}
        
        /**
	 * Method to get the playlist ID.
	 *
	 * @access  public
	 * @return  integer The playlist ID if found, or false.
	 **/
	public function getPlaylist()
	{
                $app = JFactory::getApplication();

                $option	= $app->input->get('option', '', 'word');
                $view = $app->input->get('view', '', 'word');
                $id = $app->input->get('id', '', 'int');
                $playlist_id = $app->input->get('playlist_id', '', 'int');
    
                if ($option == 'com_hwdmediashare' && $view == 'playlist' && $id)
                {
                        return $id;
                }

                if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $playlist_id)
                {
                        return $playlist_id;
                }
                
                return false;
	}
        
        /**
	 * Method to get the user ID.
	 *
	 * @access  public
	 * @return  integer The user ID if found, or false.
	 **/
	public function getUser()
	{
                $app = JFactory::getApplication();

                $option	= $app->input->get('option', '', 'word');
                $view = $app->input->get('view', '', 'word');
                $id = $app->input->get('id', '', 'int');
    
                if ($option == 'com_hwdmediashare' && $view == 'user' && $id)
                {
                        return $id;
                }

                if ($option == 'com_hwdmediashare' && $view == 'mediaitem')
                {
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                        if ($table->load($id))
                        {                               
                                $properties = $table->getProperties(1);
                                $media = JArrayHelper::toObject($properties, 'JObject');
                                $author = (int) $media->created_user_id; 
                                return $author;
                        }
                }
                
                return false;
	}
}
