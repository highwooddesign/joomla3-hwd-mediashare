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

class hwdMediaShareModelChannel extends JModelList
{
	/**
	 * Model context string.
         * 
         * @access      public
	 * @var         string
	 */
	public $context = 'com_hwdmediashare.channel';

	/**
	 * The channel data.
         * 
         * @access      protected
	 * @var         object
	 */
	protected $_channel;
        
	/**
	 * The channel items.
         * 
         * @access      protected
	 * @var         object
	 */           
	protected $_items;
        
	/**
	 * The channel albums.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_albums = null;
        
	/**
	 * The channel favourites.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_favourites = null;
        
	/**
	 * The channel groups.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_groups = null;
        
	/**
	 * The channel media.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_media = null;
        
	/**
	 * The channel memberships.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_memberships = null;
        
	/**
	 * The channel playlists.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_playlists = null;
        
	/**
	 * The channel subscribers.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_subscribers = null;
        
	/**
	 * The channel subscriptions.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_subscriptions = null;
        
	/**
	 * The channel activity.
         * 
         * @access      protected
	 * @var         object
	 */   
	protected $_activity = null;
        
	/**
	 * The model used for obtaining group items.
         * 
         * @access      protected
	 * @var         object
	 */  
	protected $_model = null;
        
	/**
	 * The number of albums associated with this channel.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_numAlbums = 0;
        
	/**
	 * The number of favourites associated with this channel.
         * 
         * @access      protected
	 * @var         integer
	 */        
	protected $_numFavourites = 0;
        
	/**
	 * The number of groups associated with this channel.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_numGroups = 0;
        
	/**
	 * The number of media associated with this channel.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_numMedia = 0;
        
	/**
	 * The number of memberships associated with this channel.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_numMemberships = 0;
        
	/**
	 * The number of playlists associated with this channel.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_numPlaylists = 0;
        
	/**
	 * The number of subscribers associated with this channel.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_numSubscribers = 0;
        
	/**
	 * The number of subscriptions associated with this channel.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_numSubscriptions = 0;

	/**
	 * Class constructor. Defines a white list of column filters.
	 *
	 * @access	public
	 * @param       array       $config     An optional associative array of configuration settings.
         * @return      void
	 */ 
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'title', 'a.title',
				'viewed', 'a.viewed',                            
				'likes', 'a.likes',
				'dislikes', 'a.dislikes',
				'ordering', 'a.ordering', 'map.ordering', 'pmap.ordering',
				'created_user_id', 'a.created_user_id', 'created_user_id_alias', 'a.created_user_id_alias', 'author',
                                'created', 'a.created',
				'modified', 'a.modified',
				'hits', 'a.hits',
                                'random', 'random',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get a table object, and load it if necessary.
	 *
	 * @access  public
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Channel', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Method to set the filterFormName variable for the account pages, 
         * allowing different filters in different layouts.
         * 
         * @access  public
	 * @return  void
	 */
	public function getFilterFormName()
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $layout = $app->input->get('layout', 'media', 'word');
		$this->filterFormName = 'filter_channel_' . $layout;
	} 
        
	/**
	 * Method to get a single channel.
	 *
         * @access  public
	 * @param   integer     $pk     The id of the primary key.
	 * @return  mixed       Object on success, false on failure.
	 */
	public function getChannel($pk = null)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                
                // Get the filter.channel_id value.
		$pk = (int) (!empty($pk)) ? $pk : $this->getState('filter.channel_id');

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Autocreate channel if necessary.
                if ($config->get('channel_auto_create') == 1)
                {
                        if (!$utilities->autoCreateChannel($pk))
                        {
                                $this->setError($utilities->getError());
                                return false;
                        }
                }
                
		// Get a table instance.
		$table = $this->getTable();

		// Attempt to load the table row.
		$return = $table->load($pk);

		// Check for a table object error.
		if ($return === false && $table->getError())
                {
			$this->setError($table->getError());
			return false;
		}

                // Check published state and access permissions.
                if ($published = $this->getState('filter.published'))
                {
                        if (is_array($published) && !in_array($table->published, $published)) 
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNPUBLISHED'));
                                return false;
                        }
                        elseif (is_int($published) && $table->published != $published) 
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNPUBLISHED'));
                                return false;
                        }
                }
                                
                // Check approval status and access permissions, but allow users to see own unapproved items.
                $user = JFactory::getUser();
                if ($status = $this->getState('filter.status'))
                {
                        if (is_array($status) && !in_array($table->status, $status) && $table->id != $user->id) 
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNAPPROVED'));
                                return false;
                        }
                        elseif (is_int($status) && $table->status != $status && $table->id != $user->id) 
                        {
                                $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_UNAPPROVED'));
                                return false;
                        }
                }

                // Check group access level and access permissions.
                $groups = $user->getAuthorisedViewLevels();
                if (!in_array($table->access, $groups)) 
                {                                    
                        $option = $app->input->get('option');
                        $view = $app->input->get('view');
                        if ($option == 'com_hwdmediashare' && $view == 'channel') 
                        {
                                JFactory::getApplication()->enqueueMessage( JText::_( 'COM_HWDMS_ERROR_ITEM_NOAUTHORISED' ) ); 
                                JFactory::getApplication()->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : 'index.php' );
                        }
                        
                        $this->setError(JText::_('COM_HWDMS_ERROR_ITEM_NOAUTHORISED'));
                        return false;
                }
                
		$properties = $table->getProperties(1);
		$this->_channel = JArrayHelper::toObject($properties, 'JObject');

		// Convert params field to registry.
		if (property_exists($this->_channel, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($this->_channel->params);
			$this->_channel->params = $registry;

                        // Check if this album has a custom ordering.
                        if ($ordering = $this->_channel->params->get('list_order_media')) 
                        {
                                // Force this new ordering
                                $orderingParts = explode(' ', $ordering); 
                                $app = JFactory::getApplication();
                                $list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array');
                                $list['fullordering'] = $ordering;
                                $app->setUserState($this->context . '.list', $list);
                                $this->setState('list.ordering', $orderingParts[0]);
                                $this->setState('list.direction', $orderingParts[1]);     
                        } 
		}

		if ($pk)
		{
                        // Add the tags.
                        $this->_channel->tags = new JHelperTags;
                        $this->_channel->tags->getItemTags('com_hwdmediashare.channel', $this->_channel->id);
                        
                        // Add the custom fields.
                        hwdMediaShareFactory::load('customfields');
                        $HWDcustomfields = hwdMediaShareCustomFields::getInstance();
                        $HWDcustomfields->elementType = 5;
                        $this->_channel->customfields = $HWDcustomfields->load($this->_channel);
                        
                        // Add the number of items for the channel.
                        $this->_channel->numalbums = $this->_numAlbums;
                        $this->_channel->numfavourites = $this->_numFavourites;
                        $this->_channel->numgroups = $this->_numGroups;
                        $this->_channel->nummedia = $this->_numMedia;
                        $this->_channel->nummemberships = $this->_numMemberships;
                        $this->_channel->numplaylists = $this->_numPlaylists;
                        $this->_channel->numsubscribers = $this->_numSubscribers;
                        $this->_channel->numsubscriptions = $this->_numSubscriptions;
                        
                        // Add the title.
                        if (empty($this->_channel->title))
                        {   
                                $user = JFactory::getUser($this->_channel->id);
                                $this->_channel->title = $config->get('author') == 0 ? $user->name : $user->username;
                        }

                        // Add subscription status.
                        hwdMediaShareFactory::load('subscriptions');
                        $HWDsubscriptions = hwdMediaShareSubscriptions::getInstance();
                        $HWDsubscriptions->elementType = 5;
                        $this->_channel->isSubscribed = $HWDsubscriptions->isSubscribed($this->_channel->id);
		}

		return $this->_channel;
	}

	/**
	 * Method to get a list of albums associated with this channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getAlbums()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                            
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Albums', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('filter.author_id', $this->getState('filter.channel_id'));
                $this->_model->setState('filter.author_id.include', 1);
                
                // When viewing account page, allow users to see their own unapproved items.
                $option = $app->input->get('option');
                $view = $app->input->get('view');
                if ($option == 'com_hwdmediashare' && $view == 'account')
                {
                        $this->_model->setState('filter.status', array(0,1,2,3));
                } 
                
                if ($this->_albums = $this->_model->getItems())
                {
                        $this->_numAlbums = $this->_model->getTotal();
                }

                return $this->_albums; 
	}
        
	/**
	 * Method to get a list of favourites associated with this channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getFavourites()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                            
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('filter.favourites_id', $this->getState('filter.channel_id'));
                
                // When viewing account page, allow users to see their own unapproved items.
                $option = $app->input->get('option');
                $view = $app->input->get('view');
                if ($option == 'com_hwdmediashare' && $view == 'account')
                {
                        $this->_model->setState('filter.status', array(0,1,2,3));
                } 
                
                if ($this->_favourites = $this->_model->getItems())
                {
                        $this->_numFavourites = $this->_model->getTotal();
                }

                return $this->_favourites; 
	}
        
	/**
	 * Method to get a list of groups associated with this channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getGroups()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                            
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Groups', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('filter.author_id', $this->getState('filter.channel_id'));
                $this->_model->setState('filter.author_id.include', 1);
                
                // When viewing account page, allow users to see their own unapproved items.
                $option = $app->input->get('option');
                $view = $app->input->get('view');
                if ($option == 'com_hwdmediashare' && $view == 'account')
                {
                        $this->_model->setState('filter.status', array(0,1,2,3));
                } 
                
                if ($this->_groups = $this->_model->getItems())
                {
                        $this->_numGroups = $this->_model->getTotal();
                }

                return $this->_groups; 
	}
        
	/**
	 * Method to get a list of media associated with this channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getMedia()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->context = 'com_hwdmediashare.channel';
                $this->_model->populateState();
                $this->_model->setState('filter.author_id', $this->getState('filter.channel_id'));
                $this->_model->setState('filter.author_id.include', 1);
                
                // When viewing account page, allow users to see their own unapproved items.
                $option = $app->input->get('option');
                $view = $app->input->get('view');
                if ($option == 'com_hwdmediashare' && $view == 'account')
                {
                        $this->_model->setState('filter.status', array(0,1,2,3));
                } 

                if ($this->_items = $this->_model->getItems())
                {
                        $this->_numMedia = $this->_model->getTotal();
                }

                return $this->_items; 
	}

	/**
	 * Method to get a list of memberships associated with this channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getMemberships()
	{        
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Groups', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('filter.member_id', $this->getState('filter.channel_id'));
                
                if ($this->_memberships = $this->_model->getItems())
                {
                        $this->_numMemberships = $this->_model->getTotal();
                }

                return $this->_memberships; 
	}
        
	/**
	 * Method to get a list of playlists associated with this channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getPlaylists()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                            
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Playlists', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('filter.author_id', $this->getState('filter.channel_id'));
                $this->_model->setState('filter.author_id.include', 1);
                
                // When viewing account page, allow users to see their own unapproved items.
                $option = $app->input->get('option');
                $view = $app->input->get('view');
                if ($option == 'com_hwdmediashare' && $view == 'account')
                {
                        $this->_model->setState('filter.status', array(0,1,2,3));
                } 
                
                if ($this->_playlists = $this->_model->getItems())
                {
                        $this->_numPlaylists = $this->_model->getTotal();
                }

                return $this->_playlists; 
	}
        
	/**
	 * Method to get a list of subscribers associated with this channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getSubscribers()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('filter.subscribers_id', $this->getState('filter.channel_id'));

                if ($this->_subscribers = $this->_model->getItems())
                {
                        $this->_numSubscribers = $this->_model->getTotal();
                }

                return $this->_subscribers; 
	}

	/**
	 * Method to get a list of subscriptions associated with this channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getSubscriptions()
	{       
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('filter.subscriptions_id', $this->getState('filter.channel_id'));

                if ($this->_subscriptions = $this->_model->getItems())
                {
                        $this->_numSubscriptions = $this->_model->getTotal();
                }

                return $this->_subscriptions; 
	}
        
	/**
	 * Method to get a list of activities associated with this channel.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getActivities()
	{
                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance('Activities', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->_model->populateState();
                $this->_model->setState('user.id', $this->getState('filter.channel_id'));
		$this->_model->setState('list.ordering', 'a.created');
		$this->_model->setState('list.direction', 'desc');
                
                $this->_activities = $this->_model->getItems();

                return $this->_activities; 
	}
        
	/**
	 * Method to get a JPagination object for the data set.
	 *
         * @access  public
	 * @return  JPagination  A JPagination object for the data set.
	 */
	public function getPagination()
	{
                return $this->_model->getPagination(); 
	}
        
	/**
	 * Method to get number of albums for the channel.
	 *
         * @access  public
	 * @return  integer The number of albums.
	 */
	public function getNumAlbums()
	{
                return (int) $this->_numAlbums; 
	}
        
	/**
	 * Method to get number of favourites for the channel.
	 *
         * @access  public
	 * @return  integer The number of favourites.
	 */
	public function getNumFavourites()
	{
                return (int) $this->_numFavourites; 
	}
        
	/**
	 * Method to get number of groups for the channel.
	 *
         * @access  public
	 * @return  integer The number of groups.
	 */
	public function getNumGroups()
	{
                return (int) $this->_numGroups; 
	}
        
	/**
	 * Method to get number of media for the channel.
	 *
         * @access  public
	 * @return  integer The number of media.
	 */
	public function getNumMedia()
	{
                return (int) $this->_numMedia; 
	}
        
	/**
	 * Method to get number of memberships for the channel.
	 *
         * @access  public
	 * @return  integer The number of memberships.
	 */
	public function getNumMemberships()
	{
                return (int) $this->_numMemberships; 
	} 
        
	/**
	 * Method to get number of playlists for the channel.
	 *
         * @access  public
	 * @return  integer The number of playlists.
	 */
	public function getNumPlaylists()
	{
                return (int) $this->_numPlaylists; 
	}

	/**
	 * Method to get number of subscribers for the channel.
	 *
         * @access  public
	 * @return  integer The number of subscribers.
	 */
	public function getNumSubscribers()
	{
                return (int) $this->_numSubscribers; 
	}        

	/**
	 * Method to get number of subscriptions for the channel.
	 *
         * @access  public
	 * @return  integer The number of subscriptions.
	 */
	public function getNumSubscriptions()
	{
                return (int) $this->_numSubscriptions; 
	} 
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @access  protected
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * @return  void
	 */
	public function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $user = JFactory::getUser();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

                // Load the channel state from the request (or from the user object if viewing account).
		if ($app->input->get('option', '', 'word') == 'com_hwdmediashare' && $app->input->get('view', '', 'word') == 'account')
                {
                        $id = (int) $user->id;
                }
                else
                {
                        $id = $app->input->getInt('id');
                }
		$this->setState('filter.channel_id', $id);

		if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) && (!$user->authorise('core.edit', 'com_hwdmediashare')))
                {
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.published',	1);
			$this->setState('filter.status',	1);

			// Filter by start and end dates.
			$this->setState('filter.publish_date', true);
		}
                else
                {
			// Allow access to unpublished and unapproved items.
			$this->setState('filter.published',	array(0,1));
			$this->setState('filter.status',	array(0,1,2,3));
                }
                
                // Only set these states when in the com_hwdmediashare.media context.
                if ($this->context == 'com_hwdmediashare.channel')
                {       
                        // Load the display state.
                        $this->setState('media.display', 'details');
              
                        // Check for list inputs and set default values if none exist
                        // This is required as the fullordering input will not take default value unless set
                        $orderingFull = $config->get('list_order_media', 'a.created DESC');
                        $orderingParts = explode(' ', $orderingFull);
                        $ordering = $orderingParts[0];
                        $direction = $orderingParts[1];                        
                        if (!$list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
                        {
                                $list['fullordering'] = $orderingFull;
                                $list['limit'] = $config->get('list_limit', 6);
                                $app->setUserState($this->context . '.list', $list);
                        }
                }  
                
                // List state information.
                parent::populateState($ordering, $direction);             
	}

	/**
	 * Increment the hit counter for the record.
	 *
         * @access  public
	 * @param   integer  $pk  Optional primary key of the record to increment.
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.channel_id');

			$table = $this->getTable();
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}

	/**
	 * Increment the like counter for the record.
	 *
         * @access  public
	 * @param   integer  $pk     Optional primary key of the record to increment.
	 * @param   integer  $value  The value of the property to increment.
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function like($pk = 0, $value = 1)
	{            
                $user = JFactory::getUser();
                if (!$user->authorise('hwdmediashare.like', 'com_hwdmediashare'))
                {
			$this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
			return false;
                }
                
                $pk = (!empty($pk)) ? $pk : (int) $this->getState('filter.channel_id');

                $table = $this->getTable();
                $table->load($pk);
                $table->like($pk, $value);

                return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
         * @access  public
	 * @param   array    $pks    A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 * @return  boolean  True on success.
	 */
	public function publish($pks, $value = 0)
	{
		// Initialiase variables.
                $user = JFactory::getUser();
                
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		// Access checks.
		foreach ($pks as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.channel.'. (int) $id))
			{
				// Prune items that the user can't change.
				unset($pks[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}
                
		if (empty($pks))
		{
			$this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
			return false;
		}

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
                                    ->update($db->quoteName('#__hwdms_users'))
                                    ->set('published = ' . $db->quote((int) $value))
                                    ->where('id IN (' . implode(',', $pks) . ')');
                        $db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to report a channel.
         * 
         * @access  public
	 * @return  boolean True on success, false on failure.
	 */
	public function report()
	{
		// Initialiase variables.
		$user = JFactory::getUser();
                $date = JFactory::getDate();                
		$input = JFactory::getApplication()->input;

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Load HWD report table.
		$table = $this->getTable('Report', 'hwdMediaShareTable');    

                if (!$user->authorise('hwdmediashare.report', 'com_hwdmediashare'))
                {
                        $this->setError(JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                        return false;                    
                }
                                        
                // Create an object to bind to the database.
                $object = new StdClass;
                $object->element_type = 5;
                $object->element_id = $input->get('id', 0, 'int');
                $object->user_id = $user->id;
                $object->report_id = $input->get('report_id', 0, 'int');
                $object->description = $input->get('description', '', 'string');
                $object->created = $date->toSql();
                
                // Attempt to save the report details to the database.
                if (!$table->save($object))
                {
                        $this->setError($table->getError());
                        return false;
                }

		return true;
	}  
}
