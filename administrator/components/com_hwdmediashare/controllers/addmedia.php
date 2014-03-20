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

class hwdMediaShareControllerAddMedia extends JControllerForm
{
	/**
	 * The URL view variable.
	 * @var    string
	 */
    	protected $view = "addmedia";
    	protected $view_list = "dashboard";
        
        /**
	 * Proxy for getModel.
	 * @return	void
	 */
	public function getModel($name = 'AddMedia', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to process file upload
	 * @return	void
	 */
        public function upload()
        {
		// Check for request forgeries
		// JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Define input field to process from the request.
                $upload = new stdClass();
                $upload->input  = 'Filedata';
                
                // Get the upload library.
                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();

                // Process the upload.
                if ($model->process($upload))
                {
                        $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }

	/**
	 * Method to process uber upload
	 * @return	void
	 */
        function uber()
        {
		// Check for request forgeries
		// JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

                // Get the upload library.
                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();

                // Process the upload.
                if ($model->uber($upload))
                {
                        $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_title));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }

	/**
	 * Method to process embed code import
	 * @return	void
	 */
        function embed()
        {
		// Check for request forgeries
		// JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

                // Get the embed library.
                hwdMediaShareFactory::load('embed');
                $model = hwdMediaShareEmbed::getInstance();

                // Process the embed code.
                if ($model->addEmbed())
                {
                        $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_EMBED_CODE_FROM_X', $model->_host));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }

	/**
	 * Method to process remote media import
	 * @return	void
	 */
        function remote()
        {
		// Check for request forgeries
		// JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

                // Get the remote library.
                hwdMediaShareFactory::load('remote');
                $model = hwdMediaShareRemote::getInstance();

                // Process the remote url.
                if ($model->addRemote())
                {
                        if ($model->_count > 1)
                        {
                                $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X_REMOTES', $model->_count));
                                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                        }
                        else
                        {
                                $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_EMBED_CODE_FROM_X', $model->_host));
                                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_id, false)); 
                        }  
                                             
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }        
        }

	/**
	 * Method to process remote file import
	 * @return	void
	 */
        function link()
        {
		// Check for request forgeries
		// JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

                // Load the remote library.
                hwdMediaShareFactory::load('remote');
                $model = hwdMediaShareRemote::getInstance();

                // Process the remote link.
                if ($model->addLink())
                {
                        $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X', $model->_title));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }
        
	/**
	 * Method to process rtmp import
	 * @return	void
	 */
        function rtmp()
        {
		// Check for request forgeries
		// JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

                // Load the rtmp library.
                hwdMediaShareFactory::load('rtmp');
                $model = hwdMediaShareRtmp::getInstance();

                // Process the remote stream.
                if ($model->addRtmp($upload))
                {
                        $this->setMessage(JText::_('COM_HWDMS_SUCCESSFULLY_ADDED_RTMP_STREAM'));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }
        
	/**
	 * Method to process bulk import from server directory
	 * @return	void
	 */
        function import()
        {
		// Check for request forgeries
		// JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                // Load the remote library.
                hwdMediaShareFactory::load('remote');
                $model = hwdMediaShareRemote::getInstance();

                // Process the local import data.
                if ($model->addImport())
                {
                        $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X_IMPORTS', $model->_count));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=addmedia.scan&tmpl=component', false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=addmedia.scan&tmpl=component', false));                                              
                }
        }
        
	/**
	 * Method to display the directory scan tree view
	 * @return	void
	 */
        function scan()
        {
		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'addmedia';
		$vFormat	= 'html';

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			// Get the model for the view.
			$model = $this->getModel($vName);

			// Push the model into the view (as default).
			$view->setModel($model, true);

			// Push document object into the view.
			$view->document = $document;

			$view->scan();
		}
        }
        
	/**
	 * Method to process two part upload process
	 * @return	void
	 */
        function processForm()
        {
		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'addmedia';
		$vFormat	= 'html';

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			// Get the model for the view.
			$model = $this->getModel($vName);

			// Push the model into the view (as default).
			$view->setModel($model, true);

			// Push document object into the view.
			$view->document = $document;
                        $view->show_form = false;

			$view->display();
		}
        }
}
