<?php
/**
 * @version    SVN $Id: search.php 961 2013-01-30 09:07:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-Nov-2011 16:51:34
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modelform');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelSearch extends JModelForm
{
        /**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.search';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_hwdmediashare';
        
        /**
	 * Sezrch data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Search total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Search areas
	 *
	 * @var integer
	 */
	var $_area = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		//Get configuration
		$app	= JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get the pagination request variables
		$this->setState('limit', $app->getUserStateFromRequest('com_search.limit', 'limit', $config->get('list_limit'), 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		// Set the search parameters
		$keyword	= urldecode(JRequest::getString('searchword'));
		$match		= JRequest::getWord('searchphrase', 'all');
		$ordering	= JRequest::getWord('ordering', 'newest');
		$this->setSearch($keyword, $match, $ordering);

		//Set the search area
		$area = JRequest::getVar('area', 1);               
		$this->setArea($area);
	}

	/**
	 * Method to set the search parameters
	 *
	 * @access	public
	 * @param string search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 */
	function setSearch($keyword, $match = 'all', $ordering = 'newest')
	{
		if (isset($keyword)) 
                {
			$this->setState('origkeyword', $keyword);
			if($match !== 'exact') 
                        {
				$keyword = preg_replace('#\xE3\x80\x80#s', ' ', $keyword);
			}
			$this->setState('keyword', $keyword);
		}

		if (isset($match)) 
                {
			$this->setState('match', $match);
		}

		if (isset($ordering)) 
                {
			$this->setState('ordering', $ordering);
		}
	}

	/**
	 * Method to set the search area
	 *
	 * @access	public
	 * @param	array	Active area
	 * @param	array	Search area
	 */
	function setArea($active = 1, $search = array())
	{
            	$keys = array(1 => 'media',2 => 'albums',3 => 'groups',4 => 'playlists');       
		$this->_area['active'] = array($keys[$active]);
		$this->_area['search'] = $search;                  
	}

	/**
	 * Method to get weblink item data for the category
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$area = $this->getArea();

			JPluginHelper::importPlugin('search');
			$dispatcher = JDispatcher::getInstance();
			$results = $dispatcher->trigger('onContentSearch', array(
				$this->getState('keyword'),
				$this->getState('match'),
				$this->getState('ordering'),
				$area['active'])
			);

			$rows = array();
			foreach ($results as $result) 
                        {
				$rows = array_merge((array) $rows, (array) $result);
			}

			$this->_total = count($rows);
			if ($this->getState('limit') > 0) 
                        {
				$this->_data	= array_splice($rows, $this->getState('limitstart'), $this->getState('limit'));
			} 
                        else
                        {
				$this->_data = $rows;
			}
		}

		return $this->_data;
	}

	/**
	 * Method to get the total number of weblink items for the category
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to get a pagination object of the weblink items for the category
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to get the search area
	 *
	 * @since 1.5
	 */
	function getArea()
	{
                $doc = & JFactory::getDocument();
                
                // Get HWDMediaShare config
                $hwdms  = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Load the Category data
		if (empty($this->_area['search']))
		{
			$areas = array();
                        $areas['media'] = 'Media';
                        if ($config->get('enable_albums')) $areas['albums'] = 'Albums';    
                        if ($config->get('enable_groups')) $areas['groups'] = 'Groups';      
                        if ($config->get('enable_playlists')) $areas['playlists'] = 'Playlists';       
               
			$this->_area['search'] = $areas;
		}

		return $this->_area;
	}
        /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	0.1
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		$return = JRequest::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		parent::populateState();
	}        
}