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

class hwdMediaShareControllerGroups extends JControllerAdmin
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
                $this->registerTask('unfeature', 'feature');
                $this->registerTask('unapprove', 'approve');
	}
        
        /**
	 * Proxy for getModel.
	 *
	 * @access	public
         * @return      object      The model.
	 */
	public function getModel($name = 'Group', $prefix = 'hwdMediaShareModel', $config = array())
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to toggle the status setting of a list of groups.
	 *
	 * @access	public
         * @return      void
	 */
	public function approve()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		// Initialise variables.
		$values	= array('approve' => 1, 'unapprove' => 0);
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

			// Approve the items.
			if ($model->approve($cid, $value))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_'.strtoupper($task).'D', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
        
	/**
	 * Method to toggle the featured setting of a list of groups.
	 *
	 * @access	public
         * @return      void
	 */
	public function feature()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		// Initialise variables.
		$values	= array('feature' => 1, 'unfeature' => 0);
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

			// Feature/unfeature the items.
			if ($model->feature($cid, $value))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_'.strtoupper($task).'D', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
}
