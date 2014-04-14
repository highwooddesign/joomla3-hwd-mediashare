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

class hwdMediaShareControllerProcesses extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 * @var    string
	 */
	protected $text_prefix = 'COM_HWDMS';
        
	/**
	 * Constructor.
	 * @return	void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
                
		// Define standard task mappings.                
                $this->registerTask('resetall', 'reset');
	}
        
        /**
	 * Proxy for getModel.
	 * @return	void
	 */
	public function getModel($name = 'Process', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}

        /**
	 * Method to allow return to process manager from ajax run page.
	 * @return	void
	 */
	public function close()
	{
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
        
	/**
	 * Method to reset the attempt counter on a list of processes.
	 * @return	void
	 */
	public function reset()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		// Initialise variables.
		$values	= array('resetall' => 1, 'reset' => 0);
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

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Approve the items.
			if ($model->reset($cid, $value))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_PROCESSES_RESET', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
        
	/**
	 * Method to remove all processes marked as successful.
	 * @return	void
	 */
	public function deletesuccessful()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the database.
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select($db->quoteName(array('id')));
                $query->from($db->quoteName('#__hwdms_processes'));
                $query->where($db->quoteName('status') . ' = '. $db->quote(2));
                $db->setQuery($query);
                $results = $db->loadObjectList();
                
                // Loop through the data array
                $cid = array();
                foreach ($results as $result)
                {
                        $cid[] = (int) $result->id;
                }

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Approve the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_SUCCESSUL_PROCESSES_REMOVED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
        
	/**
	 * Method to remove all processes marked as unnecessary.
	 * @return	void
	 */
	public function deleteunnecessary()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the database.
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select($db->quoteName(array('id')));
                $query->from($db->quoteName('#__hwdms_processes'));
                $query->where($db->quoteName('status') . ' = '. $db->quote(4));
                $db->setQuery($query);
                $results = $db->loadObjectList();
                
                // Loop through the data array
                $cid = array();
                foreach ($results as $result)
                {
                        $cid[] = (int) $result->id;
                }

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Approve the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_PROCESSES_REMOVED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
}
