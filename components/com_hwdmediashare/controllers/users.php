<?php
/**
 * @version    SVN $Id: users.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      27-Nov-2011 17:39:51
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerUsers extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	hwdMediaShareControllerUsers
	 * @see		JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
                $this->registerTask('unpublish', 'publish');
	}
        
        /**
	 * Proxy for getModel.
	 * @since	0.1
	 */
	public function getModel($name = 'User', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to toggle the published setting of a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function publish()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', JRequest::getInt('id', array()), '', 'array');
		$values	= array('publish' => 1, 'unpublish' => 0);
		$task	= $this->getTask();

		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.album.'.(int) $id)) {
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
			if (!$model->publish($ids, $value))
                        {
				JError::raiseWarning(500, $model->getError());
			}
		}

                $this->setRedirect(base64_decode(JRequest::getVar('return', '')));
	}
        
	/**
	 * Method to toggle the delete setting of a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function delete()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', JRequest::getInt('id', array()), '', 'array');
		$task	= $this->getTask();

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.delete', 'com_hwdmediashare.album.'.(int) $id)) {
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

                $this->setRedirect(base64_decode(JRequest::getVar('return', '')));
	}
}
