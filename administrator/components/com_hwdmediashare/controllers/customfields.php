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
	 * Constructor.
	 * @return	void
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
	 * @return	void
	 */
	public function getModel($name = 'CustomField', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to toggle the "searchable" setting of a list of custom fields.
	 * @return	void
	 */
	function searchable()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	=& JFactory::getApplication();
                $user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('searchable' => 1, 'unsearchable' => 0);
		$task	= $this->getTask();

		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.media.'.(int) $id)) 
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
                        for( $i = 0; $i < count($ids); $i++ )
                        {
                                $model = $this->getModel();
                                if( !$model->searchable( $ids[$i], $value ) )
                                {
                                        JError::raiseWarning(500, $model->getError());
                                }
                        }
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view='.$this->view_list , $message );
	}
        
	/**
	 * Method to toggle the "visible" setting of a list of custom fields.
	 * @return	void
	 */
	function visible()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	=& JFactory::getApplication();
                $user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('visible' => 1, 'invisible' => 0);
		$task	= $this->getTask();

		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.media.'.(int) $id)) 
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
                        for( $i = 0; $i < count($ids); $i++ )
                        {
                                $model = $this->getModel();
                                if( !$model->visible( $ids[$i], $value ) )
                                {
                                        JError::raiseWarning(500, $model->getError());
                                }
                        }
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view='.$this->view_list , $message );
	}
        
	/**
	 * Method to toggle the "required" setting of a list of custom fields.
	 * @return	void
	 */
	function required()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	=& JFactory::getApplication();
                $user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('required' => 1, 'unrequired' => 0);
		$task	= $this->getTask();

		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.media.'.(int) $id)) 
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
                        for( $i = 0; $i < count($ids); $i++ )
                        {
                                $model = $this->getModel();
                                if( !$model->required( $ids[$i], $value ) )
                                {
                                        JError::raiseWarning(500, $model->getError());
                                }
                        }
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view='.$this->view_list , $message );
	}
        
	/**
	 * Method to set the values for multiple custom fields.
	 * @return	void
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
                $value['element_type'] = JRequest::getInt('batch_element_type');
                $value['searchable'] = JRequest::getInt('batch_searchable');
                $value['visible'] = JRequest::getInt('batch_visible');
                $value['required'] = JRequest::getInt('batch_required');

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

