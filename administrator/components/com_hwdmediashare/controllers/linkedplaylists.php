<?php
/**
 * @version    SVN $Id: linkedplaylists.php 218 2012-02-29 13:32:42Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      24-Oct-2011 15:49:27
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerLinkedPlaylists extends JControllerAdmin
{
        var $view_list = "linkedplaylists";

        /**
	 * Method to unlink
	 * @since	0.1
	 */
	public function unlink()
	{
                $mediaId        = JRequest::getInt( 'media_id', '' );
                $app            =& JFactory::getApplication();
		$model		=& $this->getModel( $this->view_list );
		$id		= JRequest::getVar( 'cid' , '' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_LINKED');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $params = new StdClass;
                $params->mediaId = $mediaId;

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
		$app->redirect( 'index.php?option=com_hwdmediashare&view='.$this->view_list.'&tmpl=component&media_id='.$mediaId , $message );
	}
        
        /**
	 * Method to link
	 * @since	0.1
	 */
	public function link()
	{
                $mediaId        = JRequest::getInt( 'media_id', '' );
                $app            =& JFactory::getApplication();
		$model		=& $this->getModel( $this->view_list );
		$id		= JRequest::getVar( 'cid' , '' );
		$errors		= false;
		$message	= JText::_('COM_HWDMS_LINKED');

                if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_HWDMS_INVALID_ID') );
		}

                $params = new StdClass;
                $params->mediaId = $mediaId;

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
		$app->redirect( 'index.php?option=com_hwdmediashare&view='.$this->view_list.'&tmpl=component&media_id='.$mediaId , $message );
	}
}
