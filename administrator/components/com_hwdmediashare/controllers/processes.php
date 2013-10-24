<?php
/**
 * @version    SVN $Id: processes.php 459 2012-08-13 12:58:37Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      09-Nov-2011 10:20:49
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerProcesses extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	hwdMediaShareControllerPlaylists
	 * @see		JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
                $this->registerTask('resetall', 'reset');
	}
        
        /**
	 * Proxy for getModel.
	 * @since	0.1
	 */
	public function getModel($name = 'Process', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to reset attempts on a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function reset()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$task	= $this->getTask();
		$values	= array('resetall' => 1, 'reset' => 0);

		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.process.'.(int) $id)) 
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
			if (!$model->reset($ids, $value))
                        {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_hwdmediashare&view=processes');
	}
        
	/**
	 * Method to remove all processes marked as successful.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function deletesuccessful()
	{
                // Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
                
                $db =& JFactory::getDBO();
                $query = "
                  SELECT id
                    FROM ".$db->quoteName('#__hwdms_processes')."
                    WHERE ".$db->quoteName('status')." = ".$db->quote(2).";
                  ";
                $db->setQuery($query);
                $ids = $db->loadResultArray();
                
		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.process.'.(int) $id)) 
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
			if (!$model->delete($ids))
                        {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_hwdmediashare&view=processes');
	}
        
	/**
	 * Method to remove all processes marked as unnecessary.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function deleteunnecessary()
	{
                // Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
                
                $db =& JFactory::getDBO();
                $query = "
                  SELECT id
                    FROM ".$db->quoteName('#__hwdms_processes')."
                    WHERE ".$db->quoteName('status')." = ".$db->quote(4).";
                  ";
                $db->setQuery($query);
                $ids = $db->loadResultArray();
                
		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.process.'.(int) $id)) 
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
			if (!$model->delete($ids))
                        {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_hwdmediashare&view=processes');
	}
}
