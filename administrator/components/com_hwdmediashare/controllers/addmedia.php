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
	 * The name of the view to use with this controller.
         * 
         * @access      protected
	 * @var         string
	 */
    	protected $view = "addmedia";
        
	/**
	 * The name of the listing view to use with this controller.
         * 
         * @access      protected
	 * @var         string
	 */
    	protected $view_list = "media";
        
        /**
	 * Proxy for getModel.
	 *
	 * @access  public
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.          
         * @return  object  The model.
	 */
	public function getModel($name = 'AddMedia', $prefix = 'hwdMediaShareModel', $config = array())
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
        /**
	 * Proxy for cancel.
	 *
	 * @access	public
	 * @param       string  $key  The name of the primary key of the URL variable.
         * @return      void
	 */
	public function cancel($key = null)        
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));

		return true;
	}
        
        /**
	 * Method to process a php upload.
	 *
	 * @access	public
         * @return      void
	 */
        public function upload()
        {
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Define input field to process from the request.
                $upload = new stdClass();
                $upload->input  = 'Filedata';
                
                // Load HWD library.
                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();

                // Process the upload.
                if ($model->process($upload))
                {
                        $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_item->title));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_item->id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }

	/**
	 * Method to process an uber upload.
	 *
	 * @access	public
         * @return      void
	 */
        public function uber()
        {
                // Load HWD library.
                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();

                // Process the upload.
                if ($model->uber())
                {
                        $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_item->title));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_item->id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }

	/**
	 * Method to process remote media import.
	 *
	 * @access	public
         * @return      void
	 */
        public function remote()
        {
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

                // Load HWD library.
                hwdMediaShareFactory::load('remote');
                $model = hwdMediaShareRemote::getInstance();

                // Process the remote url.
                if ($model->addRemote())
                {              
                        if ($model->_count > 1)
                        {
                                $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X_REMOTES', $model->_count));
                                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
                        }
                        else
                        {
                                $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_REMOTE_MEDIA_FROM_X', $model->_host));
                                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_item->id, false)); 
                        }  
                                             
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }        
        }

	/**
	 * Method to process embed code import.
	 *
	 * @access	public
         * @return      void
	 */
        public function embed()
        {
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

                // Load HWD library.
                hwdMediaShareFactory::load('embed');
                $model = hwdMediaShareEmbed::getInstance();

                // Process the embed code.
                if ($model->addEmbed())
                {
                        $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_EMBED_CODE_FROM_X', $model->_host));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_item->id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }
        
	/**
	 * Method to process remote file import.
	 *
	 * @access	public
         * @return      void
	 */
        public function link()
        {
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

                // Load HWD library.
                hwdMediaShareFactory::load('remote');
                $model = hwdMediaShareRemote::getInstance();

                // Process the remote link.
                if ($model->addLink())
                {
                        $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_X', $model->_item->title));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_item->id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }
        
	/**
	 * Method to process rtmp import.
	 *
	 * @access	public
         * @return      void
	 */
        public function rtmp()
        {
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

                // Load HWD library.
                hwdMediaShareFactory::load('rtmp');
                $model = hwdMediaShareRtmp::getInstance();

                // Process the remote stream.
                if ($model->addRtmp($upload))
                {
                        $this->setMessage(JText::_('COM_HWDMS_SUCCESSFULLY_ADDED_RTMP_STREAM'));
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=editmedia.edit&id=' . $model->_item->id, false));                                              
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view, false));
                }
        }
        
	/**
	 * Method to process bulk import from server directory.
	 *
	 * @access	public
         * @return      void
	 */
        public function import()
        {
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                // Load HWD library.
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
	 * Method to display the directory scan tree view.
	 *
	 * @access	public
         * @return      void
	 */
        public function scan()
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
	 * Method to process two part upload process.
	 *
	 * @access	public
         * @return      void
	 */
        public function processForm()
        {
		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'addmedia';
		$vFormat	= 'html';

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			// Get the model for the view.
			$model = $this->getModel($vName, 'hwdMediaShareModel', array('ignore_request' => false));

			// Push the model into the view (as default).
			$view->setModel($model, true);

			// Push document object into the view.
			$view->document = $document;
                        $view->show_form = false;

			$view->display();
		}
        }
}
