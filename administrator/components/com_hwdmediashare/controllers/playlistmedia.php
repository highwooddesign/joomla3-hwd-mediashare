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

class hwdMediaShareControllerPlaylistMedia extends JControllerAdmin
{
	/**
	 * Constructor.
	 * @return	void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$playlistId = JRequest::getInt('playlist_id');
                $this->view_list = "playlistmedia&tmpl=component&playlist_id=$playlistId";
	}
        
        /**
	 * Proxy for getModel.
	 * @return	void
	 */
	public function getModel($name = 'PlaylistMediaItem', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
        /**
	 * Method to unlink media from a playlist
	 * @return	void
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
	 * Method to link media to a playlist
	 * @return	void
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
