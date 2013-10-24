<?php
/**
 * @version    SVN $Id: album.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      27-Nov-2011 12:08:40
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerAlbum extends JControllerForm
{
        /**
	 * @since	0.1
	 */
	protected $view_item = 'album';
        
	/**
	 * @since	0.1
	 */
	protected $view_list = 'albums';
        
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
}
