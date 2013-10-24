<?php
/**
 * @version    SVN $Id: playlistmedia.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      01-Nov-2011 22:20:09
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerPlaylistMedia extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$playlistId = JRequest::getInt('playlist_id');
                $this->view_list = "playlistmedia&tmpl=component&playlist_id=$playlistId";
	}
        
        /**
	 * Proxy for getModel.
	 * @since	0.1
	 */
	public function getModel($name = 'PlaylistMediaItem', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
        /**
	 * Proxy for getModel.
	 * @since	0.1
	 */
	public function unlink()
	{
                $playlistId     = JRequest::getInt( 'playlist_id', '' );
                $app            =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_LINKED');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $params = new StdClass;
                $params->playlistId = $playlistId;

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->unlink( $id[ $i ], $params ) )
			{
				$errors	= true;
			}
		}

		if( $errors )
		{
			$message = JText::_('COM_HWDMS_ERROR');
		}
		$app->redirect( 'index.php?option=com_hwdmediashare&view='.$this->view_list.'&tmpl=component&playlist_id='.$playlistId , $message );
	}
        
        /**
	 * Proxy for getModel.
	 * @since	0.1
	 */
	public function link()
	{
                $playlistId     = JRequest::getInt( 'playlist_id', '' );
                $app            =& JFactory::getApplication();
		$model		=& $this->getModel();
		$id		= JRequest::getVar( 'cid' , '' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_LINKED');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $params = new StdClass;
                $params->playlistId = $playlistId;

		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->link( $id[ $i ], $params ) )
			{
				$errors	= true;
			}
		}

		if( $errors )
		{
			$message = JText::_('COM_HWDMS_ERROR');
		}
                
		$app->redirect( 'index.php?option=com_hwdmediashare&view='.$this->view_list.'&tmpl=component&playlist_id='.$playlistId , $message );
	}
}
