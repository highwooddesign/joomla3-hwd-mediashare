<?php
/**
 * @version    SVN $Id: playlist.php 1596 2013-06-14 13:34:58Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Oct-2011 17:06:52
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelPlaylist extends JModelList
{
        /**
	 * The album element type integer
	 * http://hwdmediashare.co.uk/learn/api/68-api-definitions
	 */
        public $elementType = 4;

        /**
	 * Model context string
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.playlist';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_hwdmediashare';

	/**
	 * @var    integer  Number of media in the album
	 */
	private $_numMedia = null;
        
	/**
	 * @var    object  The media model object
	 */
	private $_model = null;
        
	/**
	 * @var    object  The album
	 */
	private $_playlist = null;

        /**
	 * @var    object  The album media
	 */
	private $_items = null;

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
				'playlist_order', 'playlist_order',                           
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
	public function getTable($type = 'Playlist', $prefix = 'hwdMediaShareTable', $config = array())
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
	public function getPlaylist($pk = null)
	{
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
        
                // First check for item id in the state
                if (empty($pk)) {
                        $pk = $this->getState('filter.playlist_id');
                }

                // Then check in the url parameters
                if (empty($pk)) {
                        $pk = JRequest::getInt( 'id', '0' );
                }
                
                if ($pk > 0)
                {   
                        // Load playlist
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Playlist', 'hwdMediaShareTable');
                        $table->load( $pk );

                        $properties = $table->getProperties(1);
                        $this->_playlist = JArrayHelper::toObject($properties, 'JObject');

                        hwdMediaShareFactory::load('tags');
                        $this->_playlist->tags = hwdMediaShareTags::getInput($this->_playlist);
                        hwdMediaShareFactory::load('customfields');
                        $this->_playlist->customfields = hwdMediaShareCustomFields::get($this->_playlist);

                        // Add data to object
                        if ($this->_playlist->created_user_id > 0)
                        {   
                                if (!empty($this->_playlist->created_user_id_alias))
                                { 
                                        $this->_playlist->author = $this->_playlist->created_user_id_alias;
                                }
                                else
                                {
                                        $user = & JFactory::getUser($this->_playlist->created_user_id);
                                        $config->get('author') == 0 ? $this->_playlist->author = $user->name : $this->_playlist->author = $user->username;
                                }
                        }
                        else
                        {
                                $this->_playlist->author = JText::_('COM_HWDMS_GUEST');
                        }
                        
                        $this->_playlist->nummedia = $this->_numMedia;

                        return $this->_playlist;
                }
                else
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_ITEM_DOES_NOT_EXIST'));
			return false;
                }


	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
         *
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

                // Set HWD listing states
                $limit = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $config->get('list_limit', $app->getCfg('list_limit') )); // Get global list limit from request, with default value read from HWD configuration and fallback to global Joomla value
                $model->setState('list.limit', $limit);
                $this->setState('list.limit', $limit);

                if (JRequest::getVar('limitstart', 0, '', 'int') == 0) JRequest::setVar('limitstart', 0); // We want to go to page one, unless a different page has been specifically selected
                $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);

                $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
                $model->setState('list.start', $limitstart); 

                // Set other filters
                $model->setState('filter.playlist_id', $this->getState('filter.playlist_id'));

                // Load the filter state.
		$search = $this->getUserStateFromRequest('filter.search', 'filter_search');
                $model->setState('filter.search', $search); 
		$this->setState('filter.search', $search);
                
                // Check if the ordering field is in the white list, otherwise use a default value.
                $listOrder = $app->getUserStateFromRequest($this->_context.'.list.ordering', 'filter_order', JRequest::getCmd('filter_order', $this->_playlist->params->get('list_order_media', $config->get('list_order_media', 'playlist_order'))));
                if (!in_array($listOrder, $this->filter_fields))
                {
                        $listOrder = 'playlist_order';
                }
                $model->setState('com_hwdmediashare.media.list.ordering', $listOrder);                
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
                $model->setState('com_hwdmediashare.media.list.direction', $listDirn);
                $this->setState($this->_context.'.list.direction', $listDirn);

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
                
                $mediaType = $this->getUserStateFromRequest('filter.mediaType', 'filter_mediaType', $config->get('list_default_media_type', '' ), 'integer', false);
                // If we are viewing a menu item that has a media type filter applied, then we need to show that instead of the user state.
                if ($config->get('list_default_media_type')) $mediaType = $config->get('list_default_media_type');
                $model->setState('filter.mediaType', $mediaType);
                $this->setState('filter.mediaType', $mediaType);
                
		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());
    
                if ($this->_items = $model->getItems())
                {
                        for ($i=0, $n=count($this->_items); $i < $n; $i++)
                        {
                                if (empty($this->_items[$i]->author))
                                {
                                        $this->_items[$i]->author = JText::_('COM_HWDMS_GUEST');
                                }
                                // Load category library object
                                hwdMediaShareFactory::load('category');
                                $categoryLib = hwdMediaShareCategory::getInstance();
                                $categoryLib->elementType = 1;
                                $this->_items[$i]->categories = $categoryLib->get($this->_items[$i]);
                        }
                }

		$this->_numMedia = $model->getTotal();
		$this->_model = $model;

                return $this->_items; 
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
            	$app = JFactory::getApplication();

		// Load state from the request.
		$id = JRequest::getInt('id');
		$this->setState('filter.playlist_id', $id);

		$return = JRequest::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		parent::populateState();  
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
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.playlist_id');
                $db = $this->getDbo();

                $db->setQuery(
                        'UPDATE #__hwdms_playlists' .
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
                    'UPDATE #__hwdms_playlists' .
                    ' SET likes = likes + 1' .
                    ' WHERE id = '.(int) $pk
            );

            if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }

            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_PLAYLIST_LIKED') );
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
                    'UPDATE #__hwdms_playlists' .
                    ' SET dislikes = dislikes + 1' .
                    ' WHERE id = '.(int) $pk
            );

            if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }

            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_PLAYLIST_DISLIKED') );
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
                  UPDATE ".$db->quoteName('#__hwdms_playlists')."
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
                  UPDATE ".$db->quoteName('#__hwdms_playlists')."
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
                $params->elementType = 4;
                $params->elementId = JRequest::getInt('id');
                $params->reportId = JRequest::getInt('report_id');
                $params->description = JRequest::getVar('description');
                $params->userId = $user->id;

                hwdMediaShareFactory::load('reports');
                hwdMediaShareReports::add($params);

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                $utilities->printModalNotice('COM_HWDMS_NOTICE_PLAYLIST_REPORTED', 'COM_HWDMS_NOTICE_PLAYLIST_REPORTED_DESC'); 
                return;
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
}
