<?php
/**
 * @package     Joomla.site
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
	 * Method to process a php upload.
	 *
	 * @access  public
         * @return  void
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
                        $this->setRedirect(hwdMediaShareHelperRoute::getMediaItemRoute($model->_item->id));
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(hwdMediaShareHelperRoute::getUploadRoute());
                } 
        }

	/**
	 * Method to process an uber upload.
	 *
	 * @access  public
         * @return  void
	 */
        public function uber()
        {
                // Load HWD library.
                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();

                // Process the upload.
                if ($model->uber())
                {
                        if ($model->_count > 1)
                        {
                                $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X_MEDIA_FILES', $model->_count));
                                $this->setRedirect(hwdMediaShareHelperRoute::getMyMediaRoute());
                        }
                        else
                        {    
                                $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_UPLOADED_X', $model->_item->title));
                                $this->setRedirect(hwdMediaShareHelperRoute::getMediaItemRoute($model->_item->id));
                        }
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(hwdMediaShareHelperRoute::getUploadRoute());
                } 
        }

	/**
	 * Method to process remote media import.
	 *
	 * @access  public
         * @return  void
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
                                $this->setRedirect(hwdMediaShareHelperRoute::getMyMediaRoute());
                        }
                        else
                        {
                                $this->setMessage(JText::sprintf('COM_HWDMS_SUCCESSFULLY_ADDED_REMOTE_MEDIA_FROM_X', $model->_host));
                                $this->setRedirect(hwdMediaShareHelperRoute::getMediaItemRoute($model->_item->id));
                        }  
                                             
                }
                else
                {
                        $this->setMessage($model->getError());
                        $this->setRedirect(hwdMediaShareHelperRoute::getUploadRoute());
                }  
        }

	/**
	 * Method to process file upload using FancyUpload2
	 * @since	0.1
	 */
        function addCdnUpload()
        {
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $pluginClass = 'plgHwdmediashare'.$config->get('platform');
                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('platform').'/'.$config->get('platform').'.php';
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $platform = call_user_func(array($pluginClass, 'getInstance'));
                        if (!$platform->addCdnUpload())
                        {
                                JError::raiseWarning(500, $model->getError());
                                $this->setRedirect(hwdMediaShareHelperRoute::getUploadRoute());
                        }
                        else
                        {
                                JFactory::getApplication()->enqueueMessage(JText::_('COM_HWDMS_SUCCESSFULLY_UPLOADED'));
                                $this->setRedirect(hwdMediaShareHelperRoute::getMediaItemRoute($model->_item->id));
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
	public function accepttos()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
                
                $app = & JFactory::getApplication();
                $app->setUserState( "media.terms", "1" );
		$app->redirect(base64_decode(JRequest::getVar('return', '')));
	}      
}
