<?php
/**
 * @version    SVN $Id: media.php 428 2012-07-04 13:47:20Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      27-Nov-2011 16:53:02
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerMedia extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	hwdMediaShareControllerMedia
	 * @see		JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
                $this->registerTask('unpublish', 'publish');
                
                $this->registerTask('assignCategory', 'assign');
                $this->registerTask('unassignCategory', 'assign');
                $this->registerTask('assignAlbum', 'assign');
                $this->registerTask('unassignAlbum', 'assign');
                $this->registerTask('assignPlaylist', 'assign');
                $this->registerTask('unassignPlaylist', 'assign');
                $this->registerTask('assignGroup', 'assign');
                $this->registerTask('unassignGroup', 'assign');
	}
        
        /**
	 * Proxy for getModel.
	 * @since	0.1
	 */
	public function getModel($name = 'MediaItem', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to toggle the publish setting of a list of items.
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
			// Get a level row instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
			$table = JTable::getInstance('Media', 'hwdMediaShareTable');
			$table->load($id);
			// Convert the JTable to a clean JObject.
			$properties = $table->getProperties(1);
			$item = JArrayHelper::toObject($properties, 'JObject');
 
			if (!($user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare') && ($item->created_user_id == $user->id)))) 
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
			// Get a level row instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
			$table = JTable::getInstance('Media', 'hwdMediaShareTable');
			$table->load($id);
			// Convert the JTable to a clean JObject.
			$properties = $table->getProperties(1);
			$item = JArrayHelper::toObject($properties, 'JObject');
 
			if (!($user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare') && ($item->created_user_id == $user->id)))) 
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

                $this->setRedirect(base64_decode(JRequest::getVar('return', '')));
	}
        
        /**
	 * Method to toggle the publish setting of a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function assign()
	{               
                // Base this model on the backend version.
                JLoader::register('hwdMediaShareModelEditMedia', JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/editmedia.php');
                $model          = JModelLegacy::getInstance('EditMedia','hwdMediaShareModel');
                $id		= JRequest::getVar( 'cid' , '' , 'post' );
		$message	= JText::_('COM_HWDMS_SUCCESSFULLY_PERFORMED_BATCH_TASK');
		$task           = $this->getTask();

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->$task( $id[ $i ] ) )
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

                $this->setRedirect('index.php?option=com_hwdmediashare&view=account&layout=media', $message);
	}
}
