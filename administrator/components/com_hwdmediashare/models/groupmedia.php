<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareModelGroupMedia extends JModelList
{
	/**
	 * The model used to get the list of media.
         * 
         * @access  protected
	 * @var     object
	 */    
        protected $model;

	/**
	 * Class constructor. Defines a white list of column filters.
	 *
	 * @access  public
	 * @param   array   $config  An optional associative array of configuration settings.
         * @return  void
	 */    
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'title', 'a.title',
				'created', 'a.created',
				'map.ordering',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get a list of items.
	 *
	 * @access  public
	 * @return  mixed   An array of data items on success, false on failure.
	 */
	public function getItems()
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                
                JModelLegacy::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/models');
                $this->model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $this->model->populateState();
                $this->model->setState('filter.add_to_group', $app->input->get('add', '0', 'int'));
                $this->model->setState('filter.group_id',  $app->input->get('group_id', '', 'int'));
                $this->model->setState('list.ordering', ($app->input->get('add', '0', 'int') == 0 ? 'map.ordering' : 'a.created'));
                $this->model->setState('list.direction', ($app->input->get('add', '0', 'int') == 0 ? 'ASC' : 'DESC'));
                
                return $this->model->getItems(); 
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @access  public
	 * @return  JPagination  A JPagination object for the data set.
	 */
	public function getPagination()
	{
                return $this->model->getPagination(); 
	}
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @access  protected
	 * @param   string     $ordering   An optional ordering field.
	 * @param   string     $direction  An optional direction (asc|desc).
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the parameters (required as this model is extended in the frontend).
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);
                            
                // Initialise variables.
                $app = JFactory::getApplication();
                
                $this->setState('filter.add_to_group', $app->input->get('add', '0', 'int'));
                $this->setState('filter.group_id', $app->input->get('group_id', '', 'int'));

                $ordering = ($app->input->get('add', '0', 'int') == 0 ? 'map.ordering' : 'a.created');
                $direction = ($app->input->get('add', '0', 'int') == 0 ? 'ASC' : 'DESC');
                
		// List state information.
		parent::populateState($ordering, $direction);
	}
}
