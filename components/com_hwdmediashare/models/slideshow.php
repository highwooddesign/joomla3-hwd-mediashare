<?php
/**
 * @version    SVN $Id: slideshow.php 1393 2013-04-23 13:15:32Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-Oct-2011 13:00:18
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Base this model on the backend version.
require_once JPATH_SITE.'/components/com_hwdmediashare/models/mediaitem.php';

// Import Joomla modelitem library
jimport('joomla.application.component.modelform');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelSlideshow extends hwdMediaShareModelMediaItem
{
	/**
	 * The active id.
	 *
	 * @var    array
	 */
	protected $_key = null;
        
	/**
	 * The slideshow items.
	 *
	 * @var    array
	 */
	protected $_items = null;

        /**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'created', 'a.created',
				'hits', 'a.hits',
				'title', 'a.title',
				'likes', 'a.likes',
				'dislikes', 'a.dislikes',
				'modified', 'a.modified',
				'viewed', 'a.viewed',
				'title', 'a.title',
                                'author', 'author',
                                'created', 'a.created',
                                'ordering', 'a.ordering',
                                'random', 'random',
			);
		}

		parent::__construct($config);
	}


        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItems($pk = null)
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) 100);
                
                // Set other filters
                $listOrder = 'a.created';
                if ($category_id = JRequest::getInt('category_id'))
                {
                        $model->setState('filter.category_id', $category_id);
                        $listOrder = $app->getUserStateFromRequest('com_hwdmediashare.media.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created')));
                }   
                if ($playlist_id = JRequest::getInt('playlist_id'))
                {
                        $model->setState('filter.playlist_id', $playlist_id);
                        $listOrder = $app->getUserStateFromRequest('com_hwdmediashare.media.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created')));
                        $listOrder = 'pmap.ordering';
                }        
                if ($album_id = JRequest::getInt('album_id'))
                {
                        $model->setState('filter.album_id', $album_id);
                        $listOrder = $app->getUserStateFromRequest('com_hwdmediashare.media.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created')));
                } 
                if ($group_id = JRequest::getInt('group_id'))
                {
                        $model->setState('filter.group_id', $group_id);
                        $listOrder = $app->getUserStateFromRequest('com_hwdmediashare.media.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created')));
                } 

                $listDirn = 'DESC';
                if (in_array(strtolower($listOrder), array('a.title', 'author', 'a.ordering', 'pmap.ordering')))
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

                if ($items = $model->getItems())
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                                $this->_key = $items[$i]->id;                             
                                break;
                        }
                }

		$this->_items = $items; 
		return $items; 
	}
        
	/**
	 * Method to get media data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getItem($id = null)
	{
                // Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Load the object state, and if missing load from the loaded items instead.
		$id = JRequest::getInt('key', $this->_key);
                
                // If we have no available state then we can't view the slideshow
                if (empty($id)) 
                { 
                    JFactory::getApplication()->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_NOAUTHORISED_ITEM' ) ); 
                    JFactory::getApplication()->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : JRoute::_(base64_decode(JRequest::getVar('return', hwdMediaShareHelperRoute::getMediaItemRoute($this->item->id)))) );
                }
                
                // Update the media.id state
		$this->setState('media.id', $id);               

                if ($item = parent::getItem($id))
                {
                        return $item;
                }
                
		return false;
	}
        
	/**
	 * Method to get media data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getElement($id = null)
	{
                // Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Load the element
                if (JRequest::getInt('category_id'))
                {
                        $id = JRequest::getInt('category_id');
                        jimport( 'joomla.application.component.model' );
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $model =& JModelLegacy::getInstance('Category', 'hwdMediaShareModel', array('ignore_request' => true));
                        $model->setState('filter.category_id', $id);

                        if ($category = $model->getCategory()) 
                        {                            
                                $category->type = 'COM_HWDMS_CATEGORY';                            
                                $category->order = 'COM_HWDMS_OPTION_MOST_RECENT';                            
                                $category->link = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($category->id)).'">'.$category->title.'</a>';                            
                                return $category;
                        }
                }  
                else if (JRequest::getInt('playlist_id'))
                {
                        $id = JRequest::getInt('playlist_id');
                        jimport( 'joomla.application.component.model' );
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $model =& JModelLegacy::getInstance('Playlist', 'hwdMediaShareModel', array('ignore_request' => true));
                        $model->setState('filter.playlist_id', $id);

                        if ($playlist = $model->getPlaylist()) 
                        {
                                $playlist->type = 'COM_HWDMS_PLAYLIST';                            
                                $playlist->order = 'COM_HWDMS_OPTION_MOST_RECENT';                            
                                $playlist->link = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getPlaylistRoute($playlist->id)).'">'.$playlist->title.'</a>';                            
                                return $playlist;
                        }
                }
                else if (JRequest::getInt('album_id'))
                {
                        $id = JRequest::getInt('album_id');
                        jimport( 'joomla.application.component.model' );
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $model =& JModelLegacy::getInstance('Album', 'hwdMediaShareModel', array('ignore_request' => true));
                        $model->setState('filter.album_id', $id);

                        if ($album = $model->getAlbum()) 
                        {
                                $album->type = 'COM_HWDMS_ALBUM';                            
                                $album->order = 'COM_HWDMS_OPTION_MOST_RECENT';                            
                                $album->link = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($album->id)).'">'.$album->title.'</a>';                            
                                return $album;
                        }
                }
                else if (JRequest::getInt('group_id'))
                {
                        $id = JRequest::getInt('group_id');
                        jimport( 'joomla.application.component.model' );
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $model =& JModelLegacy::getInstance('Group', 'hwdMediaShareModel', array('ignore_request' => true));
                        $model->setState('filter.group_id', $id);

                        if ($group = $model->getGroup()) 
                        {
                                $group->type = 'COM_HWDMS_GROUP';                            
                                $group->order = 'COM_HWDMS_OPTION_MOST_RECENT';  
                                $group->link = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getGroupRoute($group->id)).'">'.$group->title.'</a>';                            

                                return $group;
                        }
                }  

                return null;
	}
}