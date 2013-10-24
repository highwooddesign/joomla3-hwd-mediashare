<?php
/**
 * @version    SVN $Id: addmedia.php 787 2012-12-17 14:19:38Z dhorsfall $
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
class hwdMediaShareControllerAddMedia extends JControllerForm
{
	/**
	 * Method to process file upload
	 * @since	0.1
	 */
        function upload()
        {
                $app = & JFactory::getApplication();

                $upload = new stdClass();
                $upload->input  = 'Filedata';

                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();

                // Add embed code
                if (!$model->process($upload))
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect('index.php?option=com_hwdmediashare&view=addmedia');
                }
                else
                {
                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title));
                        $this->setRedirect('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.$model->_id);
                }
        }

	/**
	 * Method to process uber upload
	 * @since	0.1
	 */
        function uber()
        {
                $app = & JFactory::getApplication();

                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();

                // Add embed code
                if (!$model->uber())
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect('index.php?option=com_hwdmediashare&view=addmedia');
                }
                else
                {
                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title).' (<a href="index.php?option=com_hwdmediashare&task=editmedia.edit&id='.$model->_id.'">'.JText::_('COM_HWDMS_EDIT_MEDIA').'</a>)');
                        $this->setRedirect('index.php?option=com_hwdmediashare&view=media');
                }
        }

	/**
	 * Method to process embed code import
	 * @since	0.1
	 */
        function embed()
        {
		// Check for request forgeries
		// JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$task	= $this->getTask();

                // Load the embed library
                hwdMediaShareFactory::load('embed');
                $model = hwdMediaShareEmbed::getInstance();

                // Add embed code
                if (!$model->addEmbed())
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect('index.php?option=com_hwdmediashare&view=addmedia');
                }
                else
                {
                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_EMBED_CODE_FROM_X', $model->_host));
                        $this->setRedirect('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.$model->_id); 
                }
        }

	/**
	 * Method to process embed code import
	 * @since	0.1
	 */
        function remote()
        {
		// Check for request forgeries
		// JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$task	= $this->getTask();

                // Load the embed library
                hwdMediaShareFactory::load('remote');
                $model = hwdMediaShareRemote::getInstance();

                // Add remote urls
                if (!$model->addRemote())
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect('index.php?option=com_hwdmediashare&view=addmedia');
                }
                else
                {
                        if ($model->_count > 1)
                        {
                                JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X_REMOTES', $model->_count));
                                $this->setRedirect('index.php?option=com_hwdmediashare&view=media');
                        }
                        else
                        {
                                JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X', $model->_title));
                                $this->setRedirect('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.$model->_id);
                        }                    
                }
        }

	/**
	 * Method to process embed code import
	 * @since	0.1
	 */
        function link()
        {
		// Check for request forgeries
		// JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$task	= $this->getTask();

                // Load the embed library
                hwdMediaShareFactory::load('remote');
                $model = hwdMediaShareRemote::getInstance();

                // Add remote urls
                if (!$model->addLink())
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect('index.php?option=com_hwdmediashare&view=addmedia');
                }
                else
                {
                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X', $model->_title));
                        $this->setRedirect('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.$model->_id); 
                }
        }
        
	/**
	 * Method to process embed code import
	 * @since	0.1
	 */
        function rtmp()
        {
		// Check for request forgeries
		// JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$task	= $this->getTask();

                // Load the embed library
                hwdMediaShareFactory::load('rtmp');
                $model = hwdMediaShareRtmp::getInstance();

                // Add embed code
                if (!$model->addRtmp())
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect('index.php?option=com_hwdmediashare&view=addmedia');
                }
                else
                {
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_HWDMS_SUCCESSFULLY_ADDED_RTMP_STREAM'));
                        $this->setRedirect('index.php?option=com_hwdmediashare&task=editmedia.edit&id='.$model->_id);
                }
        }
        
	/**
	 * Method to process embed code import
	 * @since	0.1
	 */
        function import()
        {
		// Check for request forgeries
		// JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$task	= $this->getTask();

                // Load the embed library
                hwdMediaShareFactory::load('remote');
                $model = hwdMediaShareRemote::getInstance();

                // Add remote urls
                if (!$model->addImport())
                {
                        JError::raiseWarning(500, $model->getError());
                        $this->setRedirect('index.php?option=com_hwdmediashare&task=addmedia.scan&tmpl=component');
                }
                else
                {
                        JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X', $model->_title).' (<a href="index.php?option=com_hwdmediashare&task=editmedia.edit&id='.$model->_id.'" target="_top">'.JText::_('COM_HWDMS_EDIT_MEDIA').'</a>)');
                        $this->setRedirect('index.php?option=com_hwdmediashare&task=addmedia.scan&tmpl=component');
                }
        }
        
	/**
	 * Method to process embed code import
	 * @since	0.1
	 */
        function scan()
        {
		// Check for request forgeries
		// JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
            
                $view = $this->getView('addmedia','html');
                $view->setModel( $this->getModel( 'addmedia' ), true );
                $view->scan();
        }
}
