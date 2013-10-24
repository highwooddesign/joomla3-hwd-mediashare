<?php
/**
 * @version    SVN $Id: mediaitem.php 852 2013-01-07 11:25:58Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-Oct-2011 13:00:18
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelMediaItem extends JModelItem
{
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
        
        public $_likes = 0;
        public $_dislikes = 0;
        
        var $elementType = 1;

        /**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	0.1
	 */
	public function getTable($type = 'Media', $prefix = 'hwdMediaShareTable', $config = array())
	{
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                return JTable::getInstance($type, $prefix, $config);
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

		// Load the object state.
		$id	= JRequest::getInt('id');
		$this->setState('media.id', $id);

		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) &&  (!$user->authorise('core.edit', 'com_hwdmediashare')))
                {
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.published',	1);
			$this->setState('filter.status',	1);

			// Filter by start and end dates.
			$this->setState('filter.publish_date', true);
		}
                else
                {
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.published',	array(0,1));
			$this->setState('filter.status',	1);
                }
                
		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);
                
                $listOrder = JRequest::getCmd('filter_order', 'ordering');
                $this->setState($this->_context.'.list.ordering', $listOrder);

                $listDirn  = JRequest::getCmd('filter_order_Dir', 'ASC');
                $this->setState($this->_context.'.list.direction', $listDirn);
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);
	}

	/**
	 * Method to get article data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function getItem($id = null)
	{
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Check entry
                $option = JFactory::getApplication()->input->get('option');
                $view = JFactory::getApplication()->input->get('view');
                $task = JFactory::getApplication()->input->get('task');
          
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('media.id');
			}
                        
			// Get a level row instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
			$table = JTable::getInstance('Media', 'hwdMediaShareTable');

			// Attempt to load the row.
			if ($table->load($id))
			{                               
				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
                                
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
                                        if (is_array($published) && !in_array($this->_item->published, $published)) 
                                        {
                                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNPUBLISHED'));
					}
                                        else if (is_int($published) && $table->published != $published) 
                                        {
                                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNPUBLISHED'));
					}
				}
                                
                                if ($status = $this->getState('filter.status'))
				{
                                        if ($table->status != $status) 
                                        {
                                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNAPPROVED'));
					}
				}

                                // Compute view access permissions.
				$user = JFactory::getUser();
				$groups = $user->getAuthorisedViewLevels();
                                if (!in_array($this->_item->access, $groups)) 
                                {                                    
                                        $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_NOAUTHORISED'));
                                        if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $task != 'embed') 
                                        {
                                                JFactory::getApplication()->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_ITEM_NOAUTHORISED' ) ); 
                                                JFactory::getApplication()->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : 'index.php' );
                                        }
                                }
			}
			else if ($error = $table->getError()) {
				$this->setError($error);
			}
		}

                // If we can't load the item return false
                if (!$this->_item) return false;
                
                jimport( 'joomla.html.parameter' );
                
                $user = & JFactory::getUser();               
                $params = $this->_item->params;
                
                if ($params->get('author_only') == 1 && $user != $this->_item->created_user_id)
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_ONLY_AUTHOR_ACCESS'));
                }

                if ($params->get('age_restriction') == 1)
                {                    
                        $dob = JFactory::getApplication()->getUserState( "media.dob" );
                        if (!$dob)
                        {
                                if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $task != 'embed') 
                                {                                   
                                        $this->_item->agerestricted = true;
                                        return $this->_item;
                                }
                                else
                                {
                                        $this->setError(JText::_('COM_HWDMS_ERROR_AGE_RESTRICTED'));
                                }                            
                        }
                        else
                        {
                                $born = strtotime($dob);
                                $required = strtotime("- ".$params->get('age')." year", time());
                                if ($born > $required)
                                {
                                        if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $task != 'embed') 
                                        {          
                                                JFactory::getApplication()->enqueueMessage(JText::_('COM_HWDMS_NOTICE_TOO_YOUNG_TO_VIEW'));
                                                $this->_item->agerestricted = true;
                                                return $this->_item;
                                        }
                                        else
                                        {
                                                $this->setError(JText::_('COM_HWDMS_NOTICE_TOO_YOUNG_TO_VIEW'));
                                        } 
                                }                            
                        }
                }
                                
                if ($params->get('password_protect') == 1)
                {
                        $pw = JFactory::getApplication()->getUserState( 'media.media-password-'.$this->_item->id );
                        if ($pw != $this->_item->params->get('password'))
                        {
                                if ($option == 'com_hwdmediashare' && $view == 'mediaitem' && $task != 'embed') 
                                {          
                                        if ($pw) JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_ERROR_PASSWORD_INCORRECT') );
                                        $this->_item->passwordprotected = true;
                                        return $this->_item;
                                }
                                else
                                {
                                        $this->setError(JText::_('COM_HWDMS_ERROR_PASSWORD_PROTECTED'));
                                }                             

                        }
                }

                // Check for errors.
		if (count($errors = $this->getErrors())) return $this->_item;
                
                if ($this->_item)
                {
                        hwdMediaShareFactory::load('media');
                        $this->_item->media_type = hwdMediaShareMedia::loadMediaType($this->_item);
                        
                        // Add data to object
                        if ($this->_item->created_user_id > 0)
                        {   
                                if (!empty($this->_item->created_user_id_alias))
                                { 
                                        $this->_item->author = $this->_item->created_user_id_alias;
                                }
                                else
                                {
                                        $user = & JFactory::getUser($this->_item->created_user_id);
                                        $config->get('author') == 0 ? $this->_item->author = $user->name : $this->_item->author = $user->username;
                                }
                        }
                        else
                        {
                                $this->_item->author = JText::_('COM_HWDMS_GUEST');
                        }
                        
                        hwdMediaShareFactory::load('category');
                        $this->_item->categories = hwdMediaShareCategory::get($this->_item);
                        hwdMediaShareFactory::load('tags');
                        $this->_item->tags = hwdMediaShareTags::get($this->_item);
                        hwdMediaShareFactory::load('customfields');
                        $this->_item->customfields = hwdMediaShareCustomFields::get($this->_item);

                        $object = new StdClass;
                        $object->activityType = 1;
                        $object->elementType = 1;
                        $object->elementId = $this->_item->id;
                        $this->_item->activities = $this->getActivities();

                        $object = new StdClass;
                        $object->elementType = 5;
                        $object->elementId = $this->_item->created_user_id;
                        hwdMediaShareFactory::load('subscriptions');
                        $this->_item->subscribed = hwdMediaShareSubscriptions::get($object);
                        
                        $object = new StdClass;
                        $object->elementType = 1;
                        $object->elementId = $this->_item->id;
                        hwdMediaShareFactory::load('favourites');
                        $this->_item->favoured = hwdMediaShareFavourites::get($object);

                        hwdMediaShareFactory::load('googlemaps.GoogleMap');
                        hwdMediaShareFactory::load('googlemaps.JSMin');
                        hwdMediaShareFactory::load('googlemaps.map');
                        $map = new hwdMediaShareMap();
                        $map->addMarkerByAddress($this->_item->location,$this->_item->title,$this->_item->description);
                        $map->getJavascriptHeader();
                        $map->getJavascriptMap();
                        $map->setWidth('100%');
                        $this->_item->map = $map->getOnLoad().$map->getMap().$map->getSidebar();
                        $this->_item->map = $map->getOnLoad().$map->getMap();
                        
                        $this->_item->navigation = hwdMediaShareHelperNavigation::pageNavigation($this->_item, $params);
                
                        $this->_item->linkedalbums = $this->getLinkedAlbums(); 
                        $this->_item->linkedgroups = $this->getLinkedGroups();                          
                        $this->_item->linkedplaylists = $this->getLinkedPlaylists();                          
                        $this->_item->linkedmedia = $this->getLinkedMedia();                          
                        $this->_item->linkedpages = $this->getLinkedPages();
                }

                return $this->_item;
	}
        /**
	 * Method to get article data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getRelated($id = null)
	{ 
		$lang = JFactory::getLanguage();
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                
                // Slashes cause errors, <> get stripped anyway later on. # causes problems.
		$badchars = array('#','>','<','\\');
		$searchword = trim(str_replace($badchars, '', JRequest::getString('HwdmsRelatedDummySearch', $this->_item->title)));
               
		// Limit searchword
		$upper_limit = $lang->getUpperLimitSearchWord();
                $upper_limit = $upper_limit*2;
                $searchword = JHtmlString::truncate($searchword, $upper_limit);
                
		$this->setState('related.searchword', $searchword);

                JPluginHelper::importPlugin('search');
                $dispatcher = JDispatcher::getInstance();
                $results = $dispatcher->trigger('onContentSearch', array(
                        $searchword,
                        'any',
                        'newest',
                        array('media')
			)
                );
                
                $rows = array();
                foreach ($results as $result) 
                {
                        $rows = array_merge((array) $rows, (array) $result);
                }
                
                // Show 3 results on page
                $this->_related = array_splice($rows, 0, 3);
                return $this->_related;
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
		$model->setState('element.type', '1');
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
            $date =& JFactory::getDate();

            $hitcount = JRequest::getInt('hitcount', 1);

            if ($hitcount)
            {
                // Initialise variables.
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('media.id');
                $db = $this->getDbo();

                $db->setQuery(
                        'UPDATE #__hwdms_media' .
                        ' SET hits = hits + 1, viewed = ' . $db->quote($date->format('Y-m-d H:i:s')) .
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
            // Load the parameters.
            $hwdms = hwdMediaShareFactory::getInstance();
            $config = $hwdms->getConfig();
            
            $doc = & JFactory::getDocument();            
            $app = JFactory::getApplication();
                
            if (!JFactory::getUser()->authorise('hwdmediashare.like','com_hwdmediashare'))
            {
                    if ($doc->getType() == 'raw')
                    {
                            $this->setError(JText::_('COM_HWDMS_ERROR_LOGIN'));
                    }
                    else
                    {
                            $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                    }
                    return false;
            }
                
            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) JRequest::getInt('id');
            
            // Check if liked this session
            if (JFactory::getApplication()->getUserState( "media.media-like-".$pk ) == 1)
            {
                    $this->setError(JText::_('COM_HWDMS_ERROR_ALREADYRATED'));
                    return false;
            }
            
            $db = $this->getDbo();

            $db->setQuery(
                    'UPDATE #__hwdms_media' .
                    ' SET likes = likes + 1' .
                    ' WHERE id = '.(int) $pk
            );

            if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }
            
            // Set session state
            JFactory::getApplication()->setUserState( "media.media-like-".$pk, "1" );
                 
            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_MEDIA_LIKED') );
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
            // Load the parameters.
            $hwdms = hwdMediaShareFactory::getInstance();
            $config = $hwdms->getConfig();
            
            $doc = & JFactory::getDocument();            
            $app = JFactory::getApplication();

            if (!JFactory::getUser()->authorise('hwdmediashare.like','com_hwdmediashare'))
            {
                    if ($doc->getType() == 'raw')
                    {
                            $this->setError(JText::_('COM_HWDMS_ERROR_LOGIN'));
                    }
                    else
                    {
                            $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                    }
                    return false;
            }
            
            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) JRequest::getInt('id');
            
            // Check if liked this session
            if (JFactory::getApplication()->getUserState( "media.media-like-".$pk ) == 1)
            {
                    $this->setError(JText::_('COM_HWDMS_ERROR_ALREADYRATED'));
                    return false;
            }
            
            $db = $this->getDbo();

            $db->setQuery(
                    'UPDATE #__hwdms_media' .
                    ' SET dislikes = dislikes + 1' .
                    ' WHERE id = '.(int) $pk
            );

            if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }
            
            // Set session state
            JFactory::getApplication()->setUserState( "media.media-like-".$pk, "1" );
           
            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_MEDIA_DISLIKED') );
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
                  UPDATE ".$db->quoteName('#__hwdms_media')."
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
                  UPDATE ".$db->quoteName('#__hwdms_media')."
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
                $params->elementType = 1;
                $params->elementId = JRequest::getInt('id');
                $params->reportId = JRequest::getInt('report_id');
                $params->description = JRequest::getVar('description');
                $params->userId = $user->id;

                hwdMediaShareFactory::load('reports');
                hwdMediaShareReports::add($params);

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                $utilities->printModalNotice('COM_HWDMS_NOTICE_MEDIA_REPORTED', 'COM_HWDMS_NOTICE_MEDIA_REPORTED_DESC'); 
                return;
	} 
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedAlbums($pk = null)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                JLoader::register('hwdMediaShareModelAlbums', JPATH_ROOT.'/components/com_hwdmediashare/models/albums.php');
                $query = hwdMediaShareModelAlbums::getListQuery();
                // Limit this query
                //$query.= ' LIMIT 0, '.$this->getState('list.limit');                
                $query.= ' LIMIT 0, 5';                
 
                $db->setQuery($query);
                $rows = $db->loadObjectList();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $rows;
                }
	}
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedGroups($pk = null)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                JLoader::register('hwdMediaShareModelGroups', JPATH_ROOT.'/components/com_hwdmediashare/models/groups.php');
                $query = hwdMediaShareModelGroups::getListQuery();
                // Limit this query
                //$query.= ' LIMIT 0, '.$this->getState('list.limit');                
                $query.= ' LIMIT 0, 5';  
                
                $db->setQuery($query);
                $rows = $db->loadObjectList();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $rows;
                }
	}
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedPlaylists($pk = null)
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                JLoader::register('hwdMediaShareModelPlaylists', JPATH_ROOT.'/components/com_hwdmediashare/models/playlists.php');
                $query = hwdMediaShareModelPlaylists::getListQuery();
                // Limit this query
                //$query.= ' LIMIT 0, '.$this->getState('list.limit');                
                $query.= ' LIMIT 0, 5';  
                
                $db->setQuery($query);
                $rows = $db->loadObjectList();

                if ($db->getErrorMsg())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
                else
                {
			return $rows;
                }
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedMedia($pk = null)
	{
	}
        
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getLinkedPages($pk = null)
	{
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function password($pk = null)
	{
                $data = JRequest::getVar('jform', array(), 'post', 'array');

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load(intval($data['id']));
                $properties = $table->getProperties(1);
                $item = JArrayHelper::toObject($properties, 'JObject');

                if (empty($data['password']))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NO_PASSWORD_PROVIDED'));
                        return false; 
                }

                $pw = md5($item->key . $data['password']);               

                JFactory::getApplication()->setUserState( "media.media-password-$item->id", $pw );
                
                return true; 
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function dob($pk = null)
	{               
                $data = JRequest::getVar('jform', array(), 'post', 'array');

                if (empty($data['dob']))
                {
                        $this->setError(JText::_('COM_HWDMS_NO_DOB_PROVIDED'));
                        return false; 
                }
                                
                JFactory::getApplication()->setUserState( "media.dob", $data['dob'] );
                
                return true;
	}
        
        /**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function link()
	{
                $app = JFactory::getApplication();

                $array = JRequest::get( 'post' );
                $user = JFactory::getUser();

                // Base this model on the backend version.
                JLoader::register('hwdMediaShareModelEditMedia', JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/editmedia.php');
                $model          = JModelLegacy::getInstance('EditMedia', 'hwdMediaShareModel');                       
                $id             = JRequest::getInt('id');
                $album_id       = JRequest::getInt('album_id');
                $category_id    = JRequest::getInt('category_id');
                $group_id       = JRequest::getInt('group_id');
                $playlist_id    = JRequest::getInt('playlist_id');

                if ($album_id > 0)
                {
                        JRequest::setVar('assign_album_id', $album_id);
                        if( !$model->assignAlbum( $id ) )
                        {
                                JError::raiseWarning(500, $model->getError());
                        }
                }
                
                if ($category_id > 0)
                {
                        JRequest::setVar('assign_category_id', $category_id);
                        if( !$model->assignCategory( $id ) )
                        {
                                JError::raiseWarning(500, $model->getError());
                        }
                }
                
                if ($group_id > 0)
                {
                        JRequest::setVar('assign_group_id', $group_id);
                        if( !$model->assignGroup( $id ) )
                        {
                                JError::raiseWarning(500, $model->getError());
                        }
                }
                
                if ($playlist_id > 0)
                {
                        JRequest::setVar('assign_playlist_id', $playlist_id);
                        if( !$model->assignPlaylist( $id ) )
                        {
                                JError::raiseWarning(500, $model->getError());
                        }
                }     
                
                JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_SUCCESSFULLY_ADDED_MEDIA_TO_ELEMENTS') );
                JFactory::getApplication()->redirect('index.php?option=com_hwdmediashare&task=mediaform.link&id='.$id.'&tmpl=component&Itemid='.JRequest::getInt('Itemid'));
                return true;
	} 
}
