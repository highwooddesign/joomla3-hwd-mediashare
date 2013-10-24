<?php
/**
 * @version    SVN $Id: user.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      27-Nov-2011 17:39:47
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerUser extends JControllerForm
{
	/**
	 * @since	0.1
	 */
	protected $view_item = 'user';
        
	/**
	 * @since	0.1
	 */
	protected $view_list = 'users';
        
	/**
	 * Increment the like counter for the user.
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
	 * Increment the dislike counter for the user.
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
	 * Method to subscribe to a user.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function subscribe()
	{
                $app = & JFactory::getApplication();

                $model = $this->getModel();
                if (!$model->subscribe())
                {
                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                }
                
                $app->redirect( base64_decode(JRequest::getVar('return', '')) );
	}
        
	/**
	 * Method to unsubscribe from a user.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function unsubscribe()
	{
                $app = & JFactory::getApplication();

                $model = $this->getModel();
                if (!$model->unsubscribe())
                {
                        JFactory::getApplication()->enqueueMessage( $model->getError() );
                }
                
                $app->redirect( base64_decode(JRequest::getVar('return', '')) );
	}
        
	/**
	 * Method to report a user
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
}
