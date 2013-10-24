<?php
/**
 * @version    SVN $Id: user.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerUser extends JControllerForm
{
    	var $view_list = "users";
        var $elementType = 5;

	/**
	 * Proxy for edit.
	 * @since	0.1
	 */
	public function edit($key = null, $urlVar = null)
	{
                // Get the model.
                $hwdms = hwdMediaShareFactory::getInstance();

                // Autocreate channel
                if (!$hwdms->autoCreateChannel(JRequest::getInt('id')))
                {
                        JError::raiseWarning(500, $model->getError());
                }

                return parent::edit($key, $urlVar);
	}
        
	/**
	 * Proxy for add.
	 * @since	0.1
	 */
	public function add()
	{
		// Redirect to create Joomla user
                $app     =& JFactory::getApplication();
                $message = JText::_('COM_HWDMS_ADD_NEW_USER_CHANNEL_NOTICE');
		$app->redirect( 'index.php?option=com_users&task=user.add' , $message );
	}
}
