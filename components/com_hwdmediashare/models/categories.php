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

class hwdMediaShareModelCategories extends JModelList
{
	/**
	 * Model context string.
         * 
         * @access      public
	 * @var         string
	 */    
	public $context = 'com_hwdmediashare.categories';

	/**
	 * Model extension string.
         * 
         * @access      public
	 * @var         string
	 */ 
	public $extension = 'com_hwdmediashare';
        
	/**
	 * The node items.
         * 
         * @access      protected
	 * @var         object
	 */ 
	protected $_items = null;
        
	/**
	 * The parent of this node.
         * 
         * @access      protected
	 * @var         object
	 */ 
	protected $_parent = null;

	/**
	 * Class constructor.
	 *
	 * @access	public
	 * @param       array       $config     An optional associative array of configuration settings.
         * @return      void
	 */  
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
        
	/**
	 * Method to get a list of items.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if(!count($this->_items))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry();
			if($active)
			{
				$params->loadString($active->params);
			}
			$options = array();
			$options['countItems'] = $params->get('show_cat_num_links', 1) || !$params->get('show_empty_categories_cat', 0);
                        $options['countItems'] = 1;
                        $categories = JCategories::getInstance('hwdMediaShare', $options);
			$this->_parent = $categories->get($this->getState('filter.parentId', 'root'));
			if(is_object($this->_parent))
			{
				$this->_items = $this->_parent->getChildren();
			}
                        else
                        {
				$this->_items = false;
			}
		}

		return $this->_items;
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
                
		// Define the extension filter.
		$this->setState('filter.extension', $this->extension);

		// Get the parent id if defined.
		$parentId = $app->input->get('id', '0', 'integer');
		$this->setState('filter.parentId', $parentId);
                
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

		$this->setState('filter.language', $app->getLanguageFilter());

		// Load the display state.
		$display = $this->getUserStateFromRequest('media.display_categories', 'display', $config->get('category_list_default_display', 'tree'), 'word', false);
                if (!in_array(strtolower($display), array('details', 'tree'))) $display = 'tree';
		$this->setState('media.display_categories', $display);
      
		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Get the parent of this node
	 *
         * @access  public
	 * @return  void
	 */
	public function getParent()
	{
		if(!is_object($this->_parent))
		{
			$this->getItems();
		}
		return $this->_parent;
	}
}
