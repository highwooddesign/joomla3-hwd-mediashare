<?php
/**
 * @version    SVN $Id: mediaitem.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      17-Nov-2011 17:13:13
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerMediaItem extends JControllerForm
{
	/**
	 * @since	0.1
	 */
	protected $view_item = 'mediaitem';
        
	/**
	 * @since	0.1
	 */
	protected $view_list = 'media';

	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	hwdMediaShareControllerMediaItem
	 * @see		JController
	 * @since	0.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
                $this->registerTask('unfavour', 'favour');
	}

        /**
	 * Increment the like counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function like()
	{
                $app = & JFactory::getApplication();

                $model = $this->getModel();
                if (!$model->like())
                {
                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                }

                $app->redirect( base64_decode(JRequest::getVar('return', '')) );
	}
        
	/**
	 * Increment the dislike counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function dislike()
	{
                $app = & JFactory::getApplication();

                $model = $this->getModel();
                if (!$model->dislike())
                {
                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                }

                $app->redirect( base64_decode(JRequest::getVar('return', '')) );
	}

	/**
	 * Method to toggle the favour setting of a list of items.
	 *
	 * @return	void
	 * @since	0.1
	 */
	function favour()
	{
		// Initialise variables.
		$app    = & JFactory::getApplication();
                $user	= JFactory::getUser();
		$id	= JRequest::getInt('id');
		$values	= array('favour' => 1, 'unfavour' => 0);
		$task	= $this->getTask();

		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($user->id))
                {
                        JError::raiseWarning(404, JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                }
                else
                {
                        if (empty($id))
                        {
                                JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
                        }
                        else
                        {
                                $params = new StdClass;
                                $params->elementType = 1;
                                $params->elementId = $id;
                                hwdMediaShareFactory::load('favourites');
                                $model = hwdMediaShareFavourites::getInstance();
                                if (!$model->$task($params))
                                {
                                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                                }
                        }
                }
		$app->redirect( base64_decode(JRequest::getVar('return', '')) );
	}

        /**
	 * Method to report a media item
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function report()
	{
                $app = & JFactory::getApplication();

                $model = $this->getModel();
                if (!$model->report())
                {
                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                }

                return;
	}
        
        /**
	 * Method to report a media item
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function password()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                // Get the model.
                $model = $this->getModel();
                if (!$model->password())
                {
                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                }

		JFactory::getApplication()->redirect( base64_decode(JRequest::getVar('return', '')) );
	}
        
        /**
	 * Method to report a media item
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function dob()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                // Get the model.
                $model = $this->getModel();
                if (!$model->dob())
                {
                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                }

		JFactory::getApplication()->redirect( base64_decode(JRequest::getVar('return', '')) );
	}
        
	/**
	 * Increment the dislike counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function link()
	{
                $app = & JFactory::getApplication();

                $model = $this->getModel();
                if (!$model->link())
                {
                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                }

                return;
	}        
}