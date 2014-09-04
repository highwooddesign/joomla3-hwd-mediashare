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

class hwdMediaShareControllerChannel extends JControllerForm
{
	/**
	 * The name of the listing view to use with this controller.
         * 
         * @access      protected
	 * @var         string
	 */
    	protected $view_list = "channels";

        /**
	 * Proxy for edit.
	 *
	 * @access	public
	 * @param       string  $key     The name of the primary key of the URL variable.
	 * @param       string  $urlVar  The name of the URL variable if different from the primary key.
         * @return      void
	 */
	public function edit($key = null, $urlVar = null)
	{
                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                // Get the ID.
                $recordId = $this->input->getInt('id');

                // Autocreate channel.
                if ($utilities->autoCreateChannel($recordId))
                {
                        return parent::edit($key, $urlVar);                                             
                }
                else
                {
                        $this->setMessage($utilities->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
                }   
	}
        
	/**
	 * Proxy for add, to redirect to the Joomla user manager.
	 *
	 * @access	public
         * @return      void
	 */
	public function add()
	{
		// Redirect to create Joomla user.
                $this->setMessage(JText::_('COM_HWDMS_ADD_NEW_CHANNEL_NOTICE'));
		$this->setRedirect(JRoute::_('index.php?option=com_users&task=user.add', false));
	}

	/**
	 * Method to run batch operations.
	 *
	 * @access	public
	 * @param       object      $model      The model.
	 * @return      boolean     True if successful, false otherwise and internal error is set.
	 */    
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model.
		$model = $this->getModel('Channel', '', array());

		// Preset the redirect.
		$this->setRedirect(JRoute::_('index.php?option=com_hwdmediashare&view=' . $this->view_list . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}             
}
