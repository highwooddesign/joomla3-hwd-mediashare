<?php
/**
 * @version    SVN $Id: account.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Jan-2012 15:29:54
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerAccount extends JControllerForm
{
        /**
	 * @since	0.1
	 */
        public $elementType = 5;
        
        /**
	 * @since	0.1
	 */
	protected $view_item = 'account';

	/**
	 * @since	0.1
	 */
	protected $view_list = 'users';
        
	/**
	 * Method to remove favourites from a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function removefavourite()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', JRequest::getInt('id', array()), '', 'array');
		$task	= $this->getTask();

		if (empty($ids))
                {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
                {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->removefavourite($ids))
                        {
				JError::raiseWarning(500, $model->getError());
			}
		}

                $this->setRedirect('index.php?option=com_hwdmediashare&view=account&layout=favourites' , JText::_('COM_HWDMS_MEDIA_REMOVED_FROM_FAVOURITES'));
	}     
        
	/**
	 * Method to unsubscribe a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function unsubscribe()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', JRequest::getInt('id', array()), '', 'array');
		$task	= $this->getTask();

		if (empty($ids))
                {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
                {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->unsubscribe($ids))
                        {
				JError::raiseWarning(500, $model->getError());
			}
		}

                $this->setRedirect('index.php?option=com_hwdmediashare&view=account&layout=subscriptions' , JText::_('COM_HWDMS_MEDIA_UNSUBSCRIBED'));
	}            
}