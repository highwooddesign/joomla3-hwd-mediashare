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

class hwdMediaShareControllerLinkedPages extends JControllerAdmin
{
    	/**
	 * The URL view list variable.
	 * @var    string
	 */
    	protected $view_list = "linkedpages";

        /**
	 * Method to unlink pages from a media item
	 * @return	void
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
	 * Method to link pages to a media item
	 * @return	void
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
