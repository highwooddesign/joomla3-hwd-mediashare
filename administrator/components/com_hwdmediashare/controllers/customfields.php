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

class hwdMediaShareControllerCustomFields extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
         * 
         * @access      protected
	 * @var         string
	 */
	protected $text_prefix = 'COM_HWDMS';
            
	/**
	 * Class constructor.
	 *
	 * @access	public
         * @return      void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.
		$this->registerTask('unsearchable',	'searchable');	
		$this->registerTask('unvisible',	'visible');
                $this->registerTask('unrequired',	'required');
	}

        /**
	 * Proxy for getModel.
	 *
	 * @access	public
         * @return      object      The model.
	 */
	public function getModel($name = 'CustomField', $prefix = 'hwdMediaShareModel', $config = array())
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to toggle the "searchable" setting of a list of custom fields.
	 *
	 * @access	public
         * @return      void
	 */
	public function searchable()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		// Initialise variables.
		$values	= array('searchable' => 1, 'unsearchable' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers.
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Toogle "searchable" setting of the items.
			if ($model->searchable($cid, $value))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_FIELDS_'.strtoupper($task), count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
 	}
        
	/**
	 * Method to toggle the "visible" setting of a list of custom fields.
	 *
	 * @access	public
         * @return      void
	 */
	public function visible()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		// Initialise variables.
		$values	= array('visible' => 1, 'unvisible' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers.
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Toogle "visible" setting of the items.
			if ($model->visible($cid, $value))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_FIELDS_'.strtoupper($task), count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
        
	/**
	 * Method to toggle the "required" setting of a list of custom fields.
	 *
	 * @access	public
         * @return      void
	 */
	public function required()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		// Initialise variables.
		$values	= array('required' => 1, 'unrequired' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers.
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Toogle "required" setting of the items.
			if ($model->required($cid, $value))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_FIELDS_'.strtoupper($task), count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
}
