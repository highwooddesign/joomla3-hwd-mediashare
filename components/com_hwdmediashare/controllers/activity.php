<?php
/**
 * @version    SVN $Id: activity.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      20-Nov-2011 16:44:55
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerActivity extends JControllerForm
{
        /**
	 * @since	0.1
	 */
	protected $view_item = 'activity';
        
	/**
	 * @since	0.1
	 */
	protected $view_list = 'activities';
        
	/**
	 * Increment the like counter for the item.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function like()
	{
                $app = & JFactory::getApplication();

                $model = $this->getModel();
                $model->like();

                $app->redirect( base64_decode(JRequest::getVar('return', '')) );
	}
        
	/**
	 * Increment the dislike counter for the item.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function dislike()
	{
                $app = & JFactory::getApplication();

                $model = $this->getModel();
                $model->dislike();

                $app->redirect( base64_decode(JRequest::getVar('return', '')) );
	}
        
	/**
	 * Method to report an item
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function report()
	{
                $app = & JFactory::getApplication();
                
                $model = $this->getModel();
                $model->report();
                
                return;
	}         
        
        /**
	 * Method to comment on an item.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function comment()
	{           
                $app = & JFactory::getApplication();

                $model = $this->getModel();
                $model->comment();

                $app->redirect( base64_decode(JRequest::getVar('return', '')) );
	}
        
	/**
	 * Method to reply to an activity on an item
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function reply()
	{
                $app = & JFactory::getApplication();
                
                $model = $this->getModel();
                $model->reply();
                
                return;
	}         
}
