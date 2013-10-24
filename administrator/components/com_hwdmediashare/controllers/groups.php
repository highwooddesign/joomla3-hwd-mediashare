<?php
/**
 * @version    SVN $Id: groups.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerGroups extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	hwdMediaShareControllerGroups
	 * @see		JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
                $this->registerTask('unfeature', 'feature');
                $this->registerTask('unapprove', 'approve');
	}
        
        /**
	 * Proxy for getModel.
	 * @since	0.1
	 */
	public function getModel($name = 'Group', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to toggle the featured setting of a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function approve()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('approve' => 1, 'unapprove' => 0);
		$task	= $this->getTask();
                
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.group.'.(int) $id)) {
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids))
                {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
                {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->approve($ids, $value))
                        {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_hwdmediashare&view=groups');
	}
        
	/**
	 * Method to toggle the featured setting of a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function feature()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('feature' => 1, 'unfeature' => 0);
		$task	= $this->getTask();

		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.group.'.(int) $id)) 
                        {
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids))
                {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
                {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->feature($ids, $value))
                        {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_hwdmediashare&view=groups');
	}
        
	/**
	 * Method to toggle the featured setting of a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function batch()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$task	= $this->getTask();

		$value = array();
                $value['user'] = JRequest::getInt('batch_user');
                $value['access'] = JRequest::getInt('batch_access');
                $value['language'] = JRequest::getVar('batch_language');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit', 'com_hwdmediashare.activity.'.(int) $id)) 
                        {
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids))
                {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
                {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->batch($ids, $value))
                        {
				JError::raiseWarning(500, $model->getError());
			}
                        else
                        {
                                JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_SUCCESSFULLY_PERFORMED_BATCH_OPERATION') );
                        }
		}

		$this->setRedirect('index.php?option=com_hwdmediashare&view='.$this->view_list);
	}
}
