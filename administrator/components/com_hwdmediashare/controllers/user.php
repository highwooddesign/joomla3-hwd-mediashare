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

class hwdMediaShareControllerUser extends JControllerForm
{
    	/**
	 * The URL view list variable.
	 * @var    string
	 */
    	protected $view_list = "users";
        
	/**
	 * The ID of this element type.
	 * @var    string
	 */
    	protected $elementType = 5;

        /**
	 * Proxy for edit, to generate the channel row in the database.
	 * @return	void
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
	 * Proxy for add, to direct to the Joomla user manager.
	 * @return	void
	 */
	public function add()
	{
		// Redirect to create Joomla user
                $this->setMessage(JText::_('COM_HWDMS_ADD_NEW_USER_CHANNEL_NOTICE'));
		$this->setRedirect(JRoute::_('index.php?option=com_users&task=user.add', false));
	}
}
