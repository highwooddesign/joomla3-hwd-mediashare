<?php
/**
 * @version    SVN $Id: addmedia.raw.php 690 2012-10-24 10:34:07Z dhorsfall $
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
	 * @since	0.1
	 */
        public $elementType = 1;
        
	/**
	 * Method to process file upload using FancyUpload2
	 * @since	0.1
	 */
        function upload()
        {
                $app = & JFactory::getApplication();
                $user = JFactory::getUser();

                // Cross check and restore session from Flash request
                if( $user->id == 0 )
                {
                        $tokenId	= JRequest::getVar( 'token' , '' );
                        $userId		= JRequest::getVar( 'uploaderid' , '' );

                        $user		= hwdMediaShareFactory::getUserFromTokenId( $tokenId , $userId );

                        $session = & JFactory::getSession();
                        $session->set('user',$user);
                }

                $data = array();
                //$data['catid'] = JRequest::getVar('catid', array(), 'post', 'array');
                $data['catid'] = JRequest::getInt('catid');
                $data['album_id'] = JRequest::getInt('album_id');
                $data['playlist_id'] = JRequest::getInt('playlist_id');
                $data['group_id'] = JRequest::getInt('group_id');
                JRequest::setVar('jform', $data);
                
                $upload = new stdClass();
                $upload->input  = 'Filedata';
                
                hwdMediaShareFactory::load('upload');
                $model = hwdMediaShareUpload::getInstance();
                
                // Add embed code
                if (!$model->process($upload))
                {
                        $result = array(
                                'status' => '0',
                                'error' => $model->getError(),
                                'code' => 0
                        );
                }
                else
                {
                        $result = array(
                                'status' => '1',
                                'name' => $model->_title,
                                'id' => $model->_id
                        );
                }                          
                        
                header('Content-type: application/json');
                echo json_encode($result);
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

                // Import hwdMediaShare plugins
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $player = call_user_func(array($pluginClass, 'getInstance'));
                        return $player->addCdnUpload();
                }
        }
}
