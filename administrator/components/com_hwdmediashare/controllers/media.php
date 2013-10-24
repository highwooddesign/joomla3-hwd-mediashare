<?php
/**
 * @version    SVN $Id: media.php 320 2012-04-17 10:55:13Z dhorsfall $
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
class hwdMediaShareControllerMedia extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	ContentControllerArticles
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
	public function getModel($name = 'EditMedia', $prefix = 'hwdMediaShareModel')
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
			if (!$user->authorise('core.edit.state', 'com_hwdmediashare.media.'.(int) $id)) {
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

		$this->setRedirect('index.php?option=com_hwdmediashare&view=media');
	}
        
	/**
	 * Method to toggle the featured setting of a list of articles.
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
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->feature($ids, $value))
                        {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_hwdmediashare&view=media');
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

	/**
	 * Method to assign the category settings of a list of items.
	 * @since	0.1
	 */
	public function assignCategory()
	{
		$app	        =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_ASSIGNED_CATEGORY');

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->assignCategory( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view=media' , $message );
	}
        
	/**
	 * Method to unassign the category settings of a list of items.
	 * @since	0.1
	 */
	public function unassignCategory()
	{
		$app	        =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_REMOVED_CATEGORY');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->unassignCategory( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view=media' , $message );
	}
        
	/**
	 * Method to unassign the album setting of a list of items.
	 * @since	0.1
	 */
	public function assignAlbum()
	{
		$app	        =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_ASSIGNED_ALBUM');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->assignAlbum( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view=media' , $message );
	}
        
	/**
	 * Method to unassign the album settings of a list of items.
	 * @since	0.1
	 */
	public function unassignAlbum()
	{
		$app	        =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_REMOVED_ALBUM');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->unassignAlbum( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view=media' , $message );
	}
        
	/**
	 * Method to assign the playlist settings of a list of items.
	 * @since	0.1
	 */
	public function assignPlaylist()
	{
		$app	        =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_ASSIGNED_PLAYLIST');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->assignPlaylist( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view=media' , $message );
	}
        
	/**
	 * Method to unassign the playlist settings of a list of items.
	 * @since	0.1
	 */
	public function unassignPlaylist()
	{
		$app	        =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_REMOVED_PLAYLIST');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->unassignPlaylist( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view=media' , $message );
	}
        
	/**
	 * Method to assign the group settings of a list of items.
	 * @since	0.1
	 */
	public function assignGroup()
	{
		$app	        =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_ASSIGNED_GROUP');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->assignGroup( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view=media' , $message );
	}
        
	/**
	 * Method to unassign the user settings of a list of items.
	 * @since	0.1
	 */
	public function unassignGroup()
	{
		$app	        =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_REMOVED_GROUP');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->unassignGroup( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view=media' , $message );
	}
        
	/**
	 * Method to assign the group settings of a list of items.
	 * @since	0.1
	 */
	public function assignProcess()
	{
		$app	        =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_ASSIGNED_PROCESS');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->assignProcess( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$app->redirect( 'index.php?option=com_hwdmediashare&view=media' , $message );
	}
}
