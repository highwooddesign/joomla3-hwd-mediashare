<?php
/**
 * @version    SVN $Id: group.php 1595 2013-06-14 13:34:42Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Nov-2011 20:29:26
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelGroup extends JModelList
{
        /**
	 * @since	0.1
	 */
        public $elementType = 3;

        /**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.group';

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
	protected $_numMedia = null;
	protected $_numMembers = null;

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
	public function getTable($type = 'Group', $prefix = 'hwdMediaShareTable', $config = array())
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
	public function getGroup($pk = null)
	{
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // First check for item id in the state
                if (empty($pk)) {
                        $pk = $this->getState('filter.group_id');
                }

                // Then check in the url parameters
                if (empty($pk)) {
                        $pk = JRequest::getInt( 'id', '0' );
                }
                
                if ($pk > 0)
                {   
                        // Load group
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Group', 'hwdMediaShareTable');
                        $table->load( $pk );

                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');

                        hwdMediaShareFactory::load('tags');
                        $row->tags = hwdMediaShareTags::getInput($row);
                        hwdMediaShareFactory::load('customfields');
                        $row->customfields = hwdMediaShareCustomFields::get($row);

                        // Add data to object
                        if ($row->created_user_id > 0)
                        {   
                                if (!empty($row->created_user_id_alias))
                                { 
                                        $row->author = $row->created_user_id_alias;
                                }
                                else
                                {
                                        $user = & JFactory::getUser($row->created_user_id);
                                        $config->get('author') == 0 ? $row->author = $user->name : $row->author = $user->username;
                                }
                        }
                        else
                        {
                                $row->author = JText::_('COM_HWDMS_GUEST');
                        }
                        
                        $row->nummedia = $this->_numMedia;
                        $row->nummembers = $this->_numMembers;
    
                        hwdMediaShareFactory::load('googlemaps.GoogleMap');
                        hwdMediaShareFactory::load('googlemaps.JSMin');
                        hwdMediaShareFactory::load('googlemaps.map');
                        $map = new hwdMediaShareMap();
                        $map->addKMLOverlay(JURI::root().'index.php?option=com_hwdmediashare&view=media&format=feed&type=rssgeo&filter_group_id='.$row->id);
                        $map->getJavascriptHeader();
                        $map->getJavascriptMap();
                        $map->setWidth('100%');
                        $map->setHeight('100%');
                        $map->setMapType('map');
                        $row->map = $map->getOnLoad().$map->getMap().$map->getSidebar();
                        $row->map = $map->getOnLoad().$map->getMap();

                        $params = new StdClass;
                        $params->elementType = 3;
                        $params->elementId = $row->id;

                        $row->activities = $this->getActivities();

                        $row->ismember = hwdMediaShareModelGroup::isMember();

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
                                
                // Load the object state.
		$id = JRequest::getInt('id');
		$this->setState('filter.group_id', $id);
                
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
	public function getMedia($pk = null)
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                
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
                        // Get hwdMediaShare config
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig();

                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $this->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $this->setState('list.start', $limitstart);
                }
                
                // Set other filters
                $model->setState('filter.group_id', $this->getState('filter.group_id'));
                
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
	public function getMembers($pk = null)
	{
                if (!isset($this->params)) $this->params = new JRegistry();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true));
                                
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
                        // Get hwdMediaShare config
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig();

                        // Set HWD listing states
                        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                        $this->setState('list.limit', $limit);

                        if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                        $this->setState('list.start', $limitstart);
                }
                
                // Set other filters
                $model->setState('filter.group_id', $this->getState('filter.group_id'));

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

		$this->_numMembers = $model->getTotal();
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
	public function getActivities($pk = null)
	{
		// Get an instance of the generic articles model
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model =& JModelLegacy::getInstance('Activities', 'hwdMediaShareModel', array('ignore_request' => true));
                
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', 6);

		// Ordering
		$model->setState('com_hwdmediashare.activities.list.ordering', 'a.created');
		$model->setState('com_hwdmediashare.activities.list.direction', 'DESC');

                // For activity query to load recursively
                $model->setState('reply.id', '0');
		$model->setState('element.type', '3');
		$model->setState('element.id', JRequest::getInt('id'));  
                
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
                        hwdMediaShareModelActivities::getChildren($items);                        
                        return $items;
                }

		return false;
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
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.group_id');
                $db = $this->getDbo();

                $db->setQuery(
                        'UPDATE #__hwdms_groups' .
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
                    'UPDATE #__hwdms_groups' .
                    ' SET likes = likes + 1' .
                    ' WHERE id = '.(int) $pk
            );

            if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }

            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_GROUP_LIKED') );
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
                    'UPDATE #__hwdms_groups' .
                    ' SET dislikes = dislikes + 1' .
                    ' WHERE id = '.(int) $pk
            );

            if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }

            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_GROUP_DISLIKED') );
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
                  UPDATE ".$db->quoteName('#__hwdms_groups')."
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
                  UPDATE ".$db->quoteName('#__hwdms_groups')."
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
                $params->elementType = 3;
                $params->elementId = JRequest::getInt('id');
                $params->reportId = JRequest::getInt('report_id');
                $params->description = JRequest::getVar('description');
                $params->userId = $user->id;

                hwdMediaShareFactory::load('reports');
                hwdMediaShareReports::add($params);

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                $utilities->printModalNotice('COM_HWDMS_NOTICE_GROUP_REPORTED', 'COM_HWDMS_NOTICE_GROUP_REPORTED_DESC'); 
                return;
	}

	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function join()
	{
            $date =& JFactory::getDate();
            $app = JFactory::getApplication();
            $user = JFactory::getUser();

            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) JRequest::getInt('id');

            // Check user is logged
            if (!$user->id)
            {
                    JFactory::getApplication()->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_ITEM_NOAUTHORISED' ) ); 
                    return false;
            }
                                
            $db =& JFactory::getDBO();

            $query = "
                    SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_group_members')."
                    WHERE ".$db->quoteName('group_id')." = ".$db->quote($pk)."
                    AND ".$db->quoteName('member_id')." = ".$db->quote($user->id)."
                    ";

            $db->setQuery($query);
            $result = $db->loadResult();

            // Loop over categories assigned to elementid
            if($result == 0)
            {
                    JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                    $row =& JTable::getInstance('GroupMembers', 'hwdMediaShareTable');

                    // Create an object to bind to the database
                    $object = new StdClass;
                    $object->id = null;
                    $object->group_id = $pk;
                    $object->member_id = $user->id;
                    $object->approved = 1;
                    $object->created = $date->format('Y-m-d H:i:s');

                    if (!$row->bind($object))
                    {
                            return JError::raiseWarning( 500, $row->getError() );
                    }

                    if (!$row->store())
                    {
                            JError::raiseError(500, $row->getError() );
                    }
                    
                    JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                    $table =& JTable::getInstance('Group', 'hwdMediaShareTable');
                    $table->load( $pk );
                    $properties = $table->getProperties(1);
                    $row = JArrayHelper::toObject($properties, 'JObject');

                    hwdMediaShareFactory::load('events');
                    $events = hwdMediaShareEvents::getInstance();
                    $events->triggerEvent('onAfterJoinGroup', $row);
            }

            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_JOINED_GROUP') );
            return true;
	}

	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function leave()
	{
            $date =& JFactory::getDate();
            $app = JFactory::getApplication();
            $user = JFactory::getUser();

            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) JRequest::getInt('id');

            // Check user is logged
            if (!$user->id)
            {
                    JFactory::getApplication()->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_ITEM_NOAUTHORISED' ) ); 
                    return false;
            }
            
            $db =& JFactory::getDBO();

			$query = "
				  DELETE
                    FROM ".$db->quoteName('#__hwdms_group_members')."
                    WHERE ".$db->quoteName('group_id')." = ".$db->quote($pk)."
                    AND ".$db->quoteName('member_id')." = ".$db->quote($user->id)."
                    ";

            $db->setQuery($query);
            $result = $db->loadResult();

            JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
            $table =& JTable::getInstance('Group', 'hwdMediaShareTable');
            $table->load( $pk );
            $properties = $table->getProperties(1);
            $row = JArrayHelper::toObject($properties, 'JObject');

            hwdMediaShareFactory::load('events');
            $events = hwdMediaShareEvents::getInstance();
            $events->triggerEvent('onAfterLeaveGroup', $row);
                    
            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_LEFT_GROUP') );
            return true;
	}

	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function isMember()
	{
            $date =& JFactory::getDate();
            $app = JFactory::getApplication();
            $user = JFactory::getUser();

            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) JRequest::getInt('id');

            $db =& JFactory::getDBO();

            $query = "
                    SELECT COUNT(*)
                    FROM ".$db->quoteName('#__hwdms_group_members')."
                    WHERE ".$db->quoteName('group_id')." = ".$db->quote($pk)."
                    AND ".$db->quoteName('member_id')." = ".$db->quote($user->id)."
                    ";

            $db->setQuery($query);
            $result = $db->loadResult();

            // Loop over categories assigned to elementid
            if($result == 0)
            {
                    return false;
            }
            
            return true;
	}
        
      	/**
	 * Method to get the item count.
	 *
	 * @return	int		The number of media in the album.
	 */
	public function getNumMedia()
	{
		return $this->_numMedia;
        } 
        
      	/**
	 * Method to get the item count.
	 *
	 * @return	int		The number of media in the album.
	 */
	public function getNumMembers()
	{
		return $this->_numMembers;
        } 
}

