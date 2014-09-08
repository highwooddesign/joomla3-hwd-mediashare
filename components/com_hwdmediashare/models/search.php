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

class hwdMediaShareModelSearch extends JModelList
{
	/**
	 * Model context string.
         * 
         * @access      public
	 * @var         string
	 */   
	public $context = 'com_hwdmediashare.search';

	/**
	 * The search data.
         * 
         * @access      protected
	 * @var         object
	 */
	protected $_items;
        
	/**
	 * The model used for obtaining items.
         * 
         * @access      protected
	 * @var         object
	 */  
	protected $_model = null;
        
	/**
	 * The number of results.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_total = 0;
        
	/**
	 * The execution time of the search query.
         * 
         * @access      protected
	 * @var         integer
	 */        
        protected $_time = 0;

	/**
	 * Set to true if a search query is executed.
         * 
         * @access      protected
	 * @var         boolean
	 */        
        protected $_status = false;
        
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

		// Check the session for previously entered form data.
		$this->_data = JFactory::getApplication()->getUserState('com_hwdmediashare.search.data', array());

		parent::__construct($config);
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @access  public
	 * @param   array       $data      Data for the form.
	 * @param   boolean     $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed       A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hwdmediashare.search', 'search', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @access  protected
         * @return  mixed       The data for the form.
	 */
	protected function loadFormData()
	{
                return $this->_data;
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
                $type = $app->input->get('type', 'media', 'word');
		$this->filterFormName = 'filter_' . $type;                  
	}  
        
	/**
	 * Method to get a list of items based on the form data.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                
                // Get the type from the request.
                $this->type = $app->input->get('type', 'media', 'word');
                if (!in_array(strtolower($this->type), array('albums', 'channels', 'groups', 'media', 'playlists'))) $display = 'media';

                // Time the search query execution (START).
                $start = microtime(true);

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $this->_model = JModelLegacy::getInstance($this->type, 'hwdMediaShareModel', array('ignore_request' => true));               

		// Check a search term exists.
		if (count($this->_data))
                {                    
                        $this->_status = true;

                        $this->_model->context = 'com_hwdmediashare.search';
                        $this->_model->populateState();
                        $this->_model->setState('list.ordering', $this->getState('list.ordering'));
                        $this->_model->setState('list.direction', $this->getState('list.direction'));
                        
                        // Filter by search data.
                        $this->_model->setState('filter.search.method', 'match');
                        $this->_model->setState('filter.search', $this->_data['keyword']);

                        if ($this->_items = $this->_model->getItems())
                        {
                                $this->_total = $this->_model->getTotal();
                        }
                }

                // Time the search query execution (END).
                $this->_time = round(microtime(true) - $start, 4);

		return $this->_items;
	}

	/**
	 * Method to get total number of results.
	 *
         * @access  public
	 * @return  integer The number of results.
	 */
	public function getTotal()
	{
                return (int) $this->_total; 
	}

	/**
	 * Method to get the execution time for a search query.
	 *
         * @access  public
	 * @return  float   The execution time.
	 */
	public function getTime()
	{
                return (float) $this->_time; 
	}

	/**
	 * The status of the query execution.
	 *
         * @access  public
	 * @return  boolean  True if a search query has been executed.
	 */
	public function getStatus()
	{
                return (boolean) $this->_status; 
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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @access  protected
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $user = JFactory::getUser();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

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
                
                // Only set these states when in the com_hwdmediashare.album context.
                if ($this->context == 'com_hwdmediashare.search')
                {             
                        // Load the display state.
                        $display = $this->getUserStateFromRequest('media.display', 'display', $config->get('list_default_display', 'details' ), 'word', false);
                        if (!in_array(strtolower($display), array('details', 'gallery', 'list'))) $display = 'details';
                        $this->setState('media.display', $display);

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
}