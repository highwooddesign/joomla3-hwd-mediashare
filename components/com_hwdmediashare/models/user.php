<?php
/**
 * @version    SVN $Id: user.php 894 2013-01-07 15:32:54Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Nov-2011 19:39:53
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelUser extends JModelList
{
        /**
	 * @since	0.1
	 */
        public $elementType = 5;
        
        /**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.user';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_hwdmediashare';

        /**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_numMedia = 0;
	protected $_numFavourites = 0;
        protected $_numAlbums = 0;
        protected $_numGroups = 0;
        protected $_numPlaylists = 0;
        protected $_numSubsribers = 0;
        protected $_numSubscriptions = 0;
        
        /**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_model = null;

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
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	0.1
	 */
	public function getTable($type = 'UserChannel', $prefix = 'hwdMediaShareTable', $config = array())
	{
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                return JTable::getInstance($type, $prefix, $config);
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getChannel($pk = null)
	{
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $channelId = $this->getState('user.id', JRequest::getInt( 'id' ));
                
                // Autocreate channel if necessary
                if ($config->get('channel_auto_create') == 1)
                {
                        if (!$hwdms->autoCreateChannel($channelId))
                        {
                                JError::raiseWarning(500, $model->getError());
                        }
                }
            
                if ($channelId > 0)
                {
                        // Load channel
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('UserChannel', 'hwdMediaShareTable');
                        $table->load( $channelId );

                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');

                        // Here we redirect to the users page if the channel doesn't exist but only if we are viewing from the HWDMediaShare component. This
                        // prevents unexpected redirects when loading modules, etc.
                        if ($config->get('channel_auto_create') == 0 && !$row->id)
                        {
                                if (JRequest::getVar('option') == 'com_hwdmediashare')
                                {
                                        JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_ERROR_CHANNEL_DOES_NOT_EXIST') );
                                        JFactory::getApplication()->redirect(hwdMediaShareHelperRoute::getUsersRoute());
                                }
                                else
                                {
                                        return false;
                                }
                        }                        
                        
                        // We set this to access the params in the populateState() method.
                        $this->_item = $row;
                        
                        hwdMediaShareFactory::load('tags');
                        $row->tags = hwdMediaShareTags::getInput($row);
                        hwdMediaShareFactory::load('customfields');
                        $row->customfields = hwdMediaShareCustomFields::get($row);

                        $object = new StdClass;
                        $object->elementType = 5;
                        $object->elementId = $row->id;
                        hwdMediaShareFactory::load('subscriptions');
                        $row->subscribed = hwdMediaShareSubscriptions::get($object);
                        
                        // Add data to object
                        $user = & JFactory::getUser($row->id);
                        if ($config->get('author') == 1)
                        {
                                $row->title = $user->username;
                        }
                        else
                        {
                                $row->title = $user->name;
                        }
                        // We select the email so that gravatar will work correctly.
                        $row->email = $user->name;

                        $row->nummedia = $this->_numMedia;
                        
                        return $row;
                }
                else
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_ITEM_DOES_NOT_EXIST'));
			return false;
                }
	}
        
        /**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	0.1
	 */
	protected function populateState()
	{
		// Initialise variables.
		$app	= JFactory::getApplication();
                $user = JFactory::getUser();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

                // Load the user state, default to current logged in user if none supplied
		if (JRequest::getWord('option') == 'com_hwdmediashare' && JRequest::getWord('view') == 'account')
                {
                        $id = (int) $user->id;
                }
                else
                {
                        $id = JRequest::getInt('id', $user->id);

                }
		$this->setState('user.id', $id);
                
		$return = JRequest::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);
                
		$listOrder = JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created'));
                if (!in_array($listOrder, $this->filter_fields))
                {
			$listOrder = 'a.created';
		}
		$this->setState($this->_context.'.list.ordering', $listOrder);

		$listDirn = JRequest::getCmd('filter_order_Dir', 'DESC');
                if (in_array(strtolower($listOrder), array('a.title', 'author', 'a.ordering')))
                {
                        $listDirn = 'ASC';
                }
                else if (!in_array(strtoupper($listDirn), array('ASC', 'DESC', '')))
                {
                        $listDirn = 'DESC';
		}
                $this->setState($this->_context.'.list.direction', $listDirn);

		// Load the filter state.
		$search = $this->getUserStateFromRequest('filter.search', 'filter_search');
		$this->setState('filter.search', $search);

                $mediaType = $this->getUserStateFromRequest($this->context.'.filter.mediaType', 'filter_mediaType', $config->get('list_default_media_type', '' ), 'integer', false);
                // If we are viewing a menu item that has a media type filter applied, then we need to show that instead of the user state.
                if ($config->get('list_default_media_type')) $mediaType = $config->get('list_default_media_type');
                $this->setState('filter.mediaType', $mediaType);

		// Load the display state.
		$display = $this->getUserStateFromRequest('media.media-display', 'display', $config->get('list_default_display', 'details' ), 'none', false);
                if (!in_array(strtolower($display), array('details', 'gallery', 'list'))) $display = 'details';
                $this->setState('media.media-display', $display);

                // Load the display state.
		$display = $this->getUserStateFromRequest('media.display', 'display', $config->get('list_default_display', 'details' ), 'none', false);
                if (!in_array(strtolower($display), array('details', 'list'))) $display = 'details';
		$this->setState('media.display', $display);
                
                $catids = $config->get('catid');
                $this->setState('filter.category_id.include', (bool) $config->get('category_filtering_type', 1));

		// Category filter
		if ($catids) {                    
			if ($config->get('show_child_category_articles', 0) && (int) $config->get('levels', 0) > 0) {
				// Get an instance of the generic categories model
				$categories = JModelLegacy::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true));
				$categories->setState('params', $appParams);
				$levels = $config->get('levels', 1) ? $config->get('levels', 1) : 9999;
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

			$this->setState('filter.category_id', $catids);
		}

		// New Parameters
		$this->setState('filter.featured', $config->get('show_featured', 'show'));
		$this->setState('filter.author_id', $config->get('created_by', ""));
		$this->setState('filter.author_id.include', $config->get('author_filtering_type', 1));
		$this->setState('filter.author_alias', $config->get('created_by_alias', ""));
		$this->setState('filter.author_alias.include', $config->get('author_alias_filtering_type', 1));
		$excluded_articles = $config->get('excluded_articles', '');

                // API filter parameters, not stored in the state
		JRequest::getInt('filter_group_id') > 0 ? $this->setState('filter.group_id', JRequest::getInt('filter_group_id')) : null;
		JRequest::getInt('filter_album_id') > 0 ? $this->setState('filter.album_id', JRequest::getInt('filter_album_id')) : null;
		JRequest::getInt('filter_playlist_id') > 0 ? $this->setState('filter.playlist_id', JRequest::getInt('filter_playlist_id')) : null;
		JRequest::getInt('filter_category_id') > 0 ? $this->setState('filter.category_id', JRequest::getInt('filter_category_id')) : null;
		JRequest::getInt('filter_favourites_id') > 0 ? $this->setState('filter.favourites_id', JRequest::getInt('filter_favourites_id')) : null;
		JRequest::getInt('filter_author_id') > 0 ? $this->setState('filter.author_id', JRequest::getInt('filter_author_id')) : null;
		JRequest::getWord('filter_featured') != '' ? $this->setState('filter.featured', JRequest::getWord('filter_featured')) : null;
		JRequest::getWord('filter_author_filtering_type') != '' ? $this->setState('filter.author_id.include', JRequest::getWord('filter_author_filtering_type')) : null;
		JRequest::getVar('filter_tag') != '' ? $this->setState('filter.tag', JRequest::getVar('filter_tag')) : null;

                // List state information.
		parent::populateState($listOrder, $listDirn);
                
                // Set HWD listing states
                $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                $this->setState('list.limit', $limit);

                if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                $this->setState('list.start', $limitstart);     
	}

      	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
        }

        /**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 *
	 * @since   11.1
	 */
	public function getPagination()
	{
		return $this->_model->getPagination();
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getMedia()
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set list filter based on parameters and views
		if ($this->params->get('count'))
                {
                        $model->setState('list.start', 0);
                        $model->setState('list.limit', (int) $this->params->get('count', 6));
                }
                else
                {
                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $model->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $model->setState('list.start', $limitstart); 
                }

                // Set other filters
                $model->setState('filter.author_id', $this->getState('user.id'));
                $model->setState('filter.author_id.include', 1);

                // Ordering
                $model->setState('com_hwdmediashare.media.list.ordering', $this->params->get('ordering', JRequest::getCmd('filter_order', $config->get('list_order_media', 'a.created'))));
                $model->setState('com_hwdmediashare.media.list.direction', $this->params->get('ordering_direction', 'DESC'));

                // If we are in a layout, then we'll fitler, otherwise just display all in the overview page
                $layout = JRequest::getWord('layout', '');
                if (!empty($layout))
                {
                        $mediaType = $this->getUserStateFromRequest($this->context.'.filter.mediaType', 'filter_mediaType', $this->params->get('list_default_media_type', '' ), 'integer', false);
                }
                else
                {
                        $mediaType = $this->params->get('list_default_media_type', '' );
                }
                $model->setState('filter.mediaType', $mediaType);
                
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
                                if (empty($items[$i]->author))
                                {
                                        $items[$i]->author = JText::_('COM_HWDMS_GUEST');
                                }
                                // Load category library object
                                hwdMediaShareFactory::load('category');
                                $categoryLib = hwdMediaShareCategory::getInstance();
                                $categoryLib->elementType = 1;
                                $items[$i]->categories = $categoryLib->get($items[$i]);
                        }
                }

		$this->_numMedia = $model->getTotal();
		$this->_model = $model;

                return $items; 
	}

        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getFavourites()
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                      
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set list filter based on parameters and views
		if ($this->params->get('count'))
                {
                        $model->setState('list.start', 0);
                        $model->setState('list.limit', (int) $this->params->get('count', 6));
                }
                else
                {
                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $model->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $model->setState('list.start', $limitstart); 
                }
                
                // Set other filters
                $model->setState('filter.favourites_id', $this->getState('user.id'));
                
		// Ordering
		$model->setState('com_hwdmediashare.media.list.ordering', $this->params->get('ordering', 'a.created'));
		$model->setState('com_hwdmediashare.media.list.direction', $this->params->get('ordering_direction', 'DESC'));

                $model->setState('filter.mediaType', $this->params->get('list_default_media_type', ''));
                
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

		// Load the filter state.
		$search = $this->getUserStateFromRequest('filter.search', 'filter_search');
		$model->setState('filter.search', $search);
                
                if ($items = $model->getItems())
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                                if (empty($items[$i]->author))
                                {
                                        $items[$i]->author = JText::_('COM_HWDMS_GUEST');
                                }
                                hwdMediaShareFactory::load('category');
                                $items[$i]->categories = hwdMediaShareCategory::get($items[$i]);
                        }
                }
                
		$this->_numFavourites = $model->getTotal();                
		$this->_model = $model;

                return $items; 
	}
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getGroups()
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Groups', 'hwdMediaShareModel', array('ignore_request' => true));

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set list filter based on parameters and views
		if ($this->params->get('count'))
                {
                        $model->setState('list.start', 0);
                        $model->setState('list.limit', (int) $this->params->get('count', 6));
                }
                else
                {
                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('com_hwdmediashare.group.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $model->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $model->setState('list.start', $limitstart); 
                }
                
                // Set other filters
                $model->setState('filter.author_id', $this->getState('user.id'));
                $model->setState('filter.author_id.include', 1);

		// Load the filter state.
		$search = $this->getUserStateFromRequest('filter.search', 'filter_search');
		$model->setState('filter.search', $search);
                
		// Ordering
		$model->setState('com_hwdmediashare.group.list.ordering', $this->params->get('ordering', 'a.created'));
		$model->setState('com_hwdmediashare.group.list.direction', $this->params->get('ordering_direction', 'DESC'));

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

                if ($items = $model->getItems())
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                        }
                }
                
		$this->_numGroups = $model->getTotal();
		$this->_model = $model;

		return $items; 
	}
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getPlaylists()
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Playlists', 'hwdMediaShareModel', array('ignore_request' => true));
                          
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set list filter based on parameters and views
		if ($this->params->get('count'))
                {
                        $model->setState('list.start', 0);
                        $model->setState('list.limit', (int) $this->params->get('count', 6));
                }
                else
                {
                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('com_hwdmediashare.playlist.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $model->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $model->setState('list.start', $limitstart); 
                }
                
                // Set other filters
                $model->setState('filter.author_id', $this->getState('user.id'));
                $model->setState('filter.author_id.include', 1);

		// Load the filter state.
		$search = $this->getUserStateFromRequest('filter.search', 'filter_search');
		$model->setState('filter.search', $search);
                
		// Ordering
		$model->setState('com_hwdmediashare.playlist.list.ordering', $this->params->get('ordering', 'a.created'));
		$model->setState('com_hwdmediashare.playlist.list.direction', $this->params->get('ordering_direction', 'DESC'));

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

                if ($items = $model->getItems())
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                        }
                }
                
		$this->_numPlaylists = $model->getTotal();
		$this->_model = $model;

		return $items; 
	}
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getAlbums()
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Albums', 'hwdMediaShareModel', array('ignore_request' => true));
                         
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set list filter based on parameters and views
		if ($this->params->get('count'))
                {
                        $model->setState('list.start', 0);
                        $model->setState('list.limit', (int) $this->params->get('count', 6));
                }
                else
                {
                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('com_hwdmediashare.album.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $model->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $model->setState('list.start', $limitstart); 
                }
                
                // Set other filters
                $model->setState('filter.author_id', $this->getState('user.id'));
                $model->setState('filter.author_id.include', 1);

		// Load the filter state.
		$search = $this->getUserStateFromRequest('filter.search', 'filter_search');
		$model->setState('filter.search', $search);
                
		// Ordering
		$model->setState('com_hwdmediashare.album.list.ordering', $this->params->get('ordering', 'a.created'));
		$model->setState('com_hwdmediashare.album.list.direction', $this->params->get('ordering_direction', 'DESC'));

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

                if ($items = $model->getItems())
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                        }
                }
                                
		$this->_numAlbums = $model->getTotal();                
		$this->_model = $model;

		return $items;                
	}

        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getActivities()
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Activities', 'hwdMediaShareModel', array('ignore_request' => true));
                         
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set list filter based on parameters and views
		if ($this->params->get('count'))
                {
                        $model->setState('list.start', 0);
                        $model->setState('list.limit', (int) $this->params->get('count', 6));
                }
                else
                {
                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $model->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $model->setState('list.start', $limitstart); 
                }
                
                // Set other filters
                $model->setState('user.id', $this->getState('user.id'));

		// Ordering
		$model->setState('com_hwdmediashare.activities.list.ordering', 'a.created');
		$model->setState('com_hwdmediashare.activities.list.direction', 'DESC');

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

                if ($items = $model->getItems())
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                        }
                }
                
		return $items;
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getSubscribers()
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true));
                  
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set list filter based on parameters and views
		if ($this->params->get('count'))
                {
                        $model->setState('list.start', 0);
                        $model->setState('list.limit', (int) $this->params->get('count', 6));
                }
                else
                {
                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $model->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $model->setState('list.start', $limitstart); 
                }
                
                // Set other filters
                $model->setState('filter.subscribers_id', $this->getState('user.id'));

		// Ordering
		$model->setState('com_hwdmediashare.user.list.ordering', $this->params->get('ordering', 'a.created'));
		$model->setState('com_hwdmediashare.user.list.direction', $this->params->get('ordering_direction', 'DESC'));

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

                if ($items = $model->getItems())
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                        }
                }
                                
		$this->_numSubscribers = $model->getTotal();
		$this->_model = $model;

		return $items;
	}

        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getSubscriptions()
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true));
                    
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set list filter based on parameters and views
		if ($this->params->get('count'))
                {
                        $model->setState('list.start', 0);
                        $model->setState('list.limit', (int) $this->params->get('count', 6));
                }
                else
                {
                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $model->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $model->setState('list.start', $limitstart);  
                }
                
                // Set other filters
                $model->setState('filter.subscriptions_id', $this->getState('user.id'));

		// Ordering
		$model->setState('com_hwdmediashare.user.list.ordering', $this->params->get('ordering', 'a.created'));
		$model->setState('com_hwdmediashare.user.list.direction', $this->params->get('ordering_direction', 'DESC'));

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

                if ($items = $model->getItems())
                {
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                        }
                }
                                
		$this->_numSubscriptions = $model->getTotal();
		$this->_model = $model;

		return $items;
	}
        
	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function subscribe()
	{
                $app = JFactory::getApplication();
                
                if (!JFactory::getUser()->authorise('hwdmediashare.subscribe','com_hwdmediashare'))
                {
                        if (JRequest::getVar('task') == 'user.subscribe')
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        }
                        else
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_LOGIN'));
                        }
                        return false;
                }
                        
                $array = JRequest::get( 'post' );
                $user = JFactory::getUser();

                $params = new StdClass;
                $params->elementType = 5;
                $params->elementId = JRequest::getInt('id');
                $params->userId = $user->id;

                hwdMediaShareFactory::load('subscriptions');
                $lib = hwdMediaShareSubscriptions::getInstance();
                if (!$lib->subscribe($params))
                {
                        $this->setError($lib->getError());
                        return false;
                }
                
                return true;
	}
	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function unsubscribe()
	{
                $app = JFactory::getApplication();
                
                if (!JFactory::getUser()->authorise('hwdmediashare.subscribe','com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;
                }
                
                $array = JRequest::get( 'post' );
                $user = JFactory::getUser();

                $params = new StdClass;
                $params->elementType = 5;
                $params->elementId = JRequest::getInt('id');
                $params->userId = $user->id;

                hwdMediaShareFactory::load('subscriptions');
                $lib = hwdMediaShareSubscriptions::getInstance();
                if (!$lib->unsubscribe($params))
                {
                        $this->setError($lib->getError());
                        return false;
                }
                
                return true;
	}
	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
            $hitcount = JRequest::getInt('hitcount', 1);

            if ($hitcount)
            {
                // Initialise variables.
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('user.id');
                $db = $this->getDbo();

                $db->setQuery(
                        'UPDATE #__hwdms_users' .
                        ' SET hits = hits + 1' .
                        ' WHERE id = '.(int) $pk
                );

                if (!$db->query()) {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
            }

            return true;
	}
	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function like()
	{
            $app = JFactory::getApplication();
                
            if (!JFactory::getUser()->authorise('hwdmediashare.like','com_hwdmediashare'))
            {
                    return JError::raiseWarning(404, JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
            }
            
            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) JRequest::getInt('id');
            $db = $this->getDbo();

            $db->setQuery(
                    'UPDATE #__hwdms_users' .
                    ' SET likes = likes + 1' .
                    ' WHERE id = '.(int) $pk
            );

            if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }

            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_USER_LIKED') );
            return true;
	}
	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function dislike()
	{
            $app = JFactory::getApplication();
                
            if (!JFactory::getUser()->authorise('hwdmediashare.like','com_hwdmediashare'))
            {
                    return JError::raiseWarning(404, JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
            }
            
            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) JRequest::getInt('id');
            $db = $this->getDbo();

            $db->setQuery(
                    'UPDATE #__hwdms_users' .
                    ' SET dislikes = dislikes + 1' .
                    ' WHERE id = '.(int) $pk
            );

            if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }

            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_USER_DISLIKED') );
            return true;
	}
        /**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function publish($pks, $value = 0)
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

                $db =& JFactory::getDBO();
                $query = "
                  UPDATE ".$db->quoteName('#__hwdms_users')."
                    SET ".$db->quoteName('published')." = ".$db->quote($value)."
                    WHERE ".$db->quoteName('id')." = ".implode(" OR ".$db->quoteName('id')." = ", $pks)."
                  ";
                $db->setQuery($query);

                // Check for a database error.
		if (!$db->query())
                {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

                // Clear the component's cache
		$this->cleanCache();

		return true;
	}
	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function delete($pks)
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

                $db =& JFactory::getDBO();
                $query = "
                  UPDATE ".$db->quoteName('#__hwdms_users')."
                    SET ".$db->quoteName('published')." = ".$db->quote('-2')."
                    WHERE ".$db->quoteName('id')." = ".implode(" OR ".$db->quoteName('id')." = ", $pks)."
                  ";
                $db->setQuery($query);

                // Check for a database error.
		if (!$db->query())
                {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

                // Clear the component's cache
		$this->cleanCache();

		return true;
	}

        /**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function report()
	{
                $app = JFactory::getApplication();
                
                if (!JFactory::getUser()->authorise('hwdmediashare.report','com_hwdmediashare'))
                {
                        return JError::raiseWarning(404, JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                }
            
                $array = JRequest::get( 'post' );
                $user = JFactory::getUser();

                $params = new StdClass;
                $params->elementType = 5;
                $params->elementId = JRequest::getInt('id');
                $params->reportId = JRequest::getInt('report_id');
                $params->description = JRequest::getVar('description');
                $params->userId = $user->id;

                hwdMediaShareFactory::load('reports');
                hwdMediaShareReports::add($params);

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                $utilities->printModalNotice('COM_HWDMS_NOTICE_USER_REPORTED', 'COM_HWDMS_NOTICE_USER_REPORTED_DESC'); 
                return;
	}
}
