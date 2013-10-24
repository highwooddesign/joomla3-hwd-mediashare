<?php
/**
 * @version    SVN $Id: addmedia.php 1597 2013-06-19 10:27:47Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      19-Jan-2012 13:32:50
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerAddMedia extends JControllerForm
{
	/**
	 * Method to process file upload
	 * @since	0.1
	 */
        function upload()
        {
                $app = & JFactory::getApplication();
                $user = JFactory::getUser();

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $upload = new stdClass();
                $upload->input  = 'Filedata';

                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();

                // Add embed code
                if (!$model->process($upload))
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect(hwdMediaShareHelperRoute::getUploadRoute());
                }
                else
                {
                        // Check if we are redirecting to the editor window
                        if (JRequest::getWord('redirect') == 'editor') 
                        {
                                $this->setRedirect('index.php?option=com_hwdmediashare&amp;view=media&amp;layout=editor&amp;tmpl=component&amp;function=jSelectMediaForum');
                                return;
                        }
                        
                        if ($user->id)
                        {
                                if ($config->get('approve_new_media'))
                                {
                                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title));
                                        $this->setRedirect(hwdMediaShareHelperRoute::getMyMediaRoute());
                                }
                                else
                                {
                                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title).' (<a href="index.php?option=com_hwdmediashare&task=mediaform.edit&id='.$model->_id.'&return='.base64_encode(hwdMediaShareHelperRoute::getMediaItemRoute($model->_id)).'">'.JText::_('COM_HWDMS_EDIT_MEDIA').'</a>)');
                                        $this->setRedirect(hwdMediaShareHelperRoute::getMediaItemRoute($model->_id));
                                }
                        }
                        else
                        {
                                if ($config->get('approve_new_media'))
                                {
                                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title));
                                        $this->setRedirect(hwdMediaShareHelperRoute::getMediaRoute());
                                }
                                else
                                {
                                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title));
                                        $this->setRedirect(hwdMediaShareHelperRoute::getMediaItemRoute($model->_id));
                                }
                        }
                }
        }

	/**
	 * Method to process uber upload
	 * @since	0.1
	 */
        function uber()
        {
                $app = & JFactory::getApplication();
                $user = JFactory::getUser();

                $upload = new stdClass();
                $upload->input  = 'Filedata';

                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();

                // Add embed code
                if (!$model->uber($upload))
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect(hwdMediaShareHelperRoute::getUploadRoute());
                }
                else
                {
                        // Check if we are redirecting to the editor window
                        if (JRequest::getWord('redirect') == 'editor') 
                        {
                                $this->setRedirect('index.php?option=com_hwdmediashare&amp;view=media&amp;layout=editor&amp;tmpl=component&amp;function=jSelectMediaForum');
                                return;
                        }
                        
                        if ($user->id)
                        {
                                JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title).' (<a href="index.php?option=com_hwdmediashare&task=mediaform.edit&id='.$model->_id.'">'.JText::_('COM_HWDMS_EDIT_MEDIA').'</a>)');
                                $this->setRedirect(hwdMediaShareHelperRoute::getMyMediaRoute());
                        }
                        else
                        {
                                JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title));
                                $this->setRedirect(hwdMediaShareHelperRoute::getMediaItemRoute($model->_id));
                        }
                }
        }

	/**
	 * Method to process embed code import
	 * @since	0.1
	 */
        function remote()
        {
                $app = & JFactory::getApplication();
                $user = JFactory::getUser();

                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                hwdMediaShareFactory::load('remote');
                $model = hwdMediaShareRemote::getInstance();

                // Add embed code
                if (!$model->addRemote())
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect(hwdMediaShareHelperRoute::getUploadRoute());
                }
                else
                {
                        // Check if we are redirecting to the editor window
                        if (JRequest::getWord('redirect') == 'editor') 
                        {
                                $this->setRedirect('index.php?option=com_hwdmediashare&amp;view=media&amp;layout=editor&amp;tmpl=component&amp;function=jSelectMediaForum');
                                return;
                        }
                        
                        if ($user->id)
                        {
                                if ($model->_count > 1 || $config->get('approve_new_media'))
                                {
                                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X_REMOTES', $model->_count));
                                        $this->setRedirect(hwdMediaShareHelperRoute::getMyMediaRoute());
                                }
                                else
                                {
                                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X', $model->_title).' (<a href="index.php?option=com_hwdmediashare&task=mediaform.edit&id='.$model->_id.'&return='.base64_encode(hwdMediaShareHelperRoute::getMediaItemRoute($model->_id)).'">'.JText::_('COM_HWDMS_EDIT_MEDIA').'</a>)');
                                        $this->setRedirect(hwdMediaShareHelperRoute::getMediaItemRoute($model->_id));
                                }
                        }
                        else
                        {
                                if ($model->_count > 1 || $config->get('approve_new_media'))
                                {
                                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X_REMOTES', $model->_count));
                                        $this->setRedirect(hwdMediaShareHelperRoute::getMediaRoute());
                                }
                                else
                                {
                                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X', $model->_title));
                                        $this->setRedirect(hwdMediaShareHelperRoute::getMediaItemRoute($model->_id));
                                }
                        }
                }
        }

        /**
	 * Method to report a media item
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function terms()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                // Get the model.
                $model = $this->getModel('Upload', 'hwdMediaShareModel');
                if (!$model->terms())
                {
                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                }

		JFactory::getApplication()->redirect( base64_decode(JRequest::getVar('return', '')) );
	}
}
