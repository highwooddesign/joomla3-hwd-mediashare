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

class hwdMediaShareControllerAlbums extends JControllerForm
{
	/**
	 * The prefix to use with controller messages.
         * 
         * @access      protected
	 * @var         string
	 */
	protected $text_prefix = 'COM_HWDMS';
        
	/**
	 * The URL view list variable to use with this controller.
	 *
         * @access      protected
	 * @var         string
	 */
    	protected $view_list = "albums";
        
	/**
	 * Class constructor.
	 *
	 * @access	public
	 * @param       array       $config     An optional associative array of configuration settings.
         * @return      void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
                
		// Define standard task mappings.                
                $this->registerTask('unpublish', 'publish');
                $this->registerTask('delete', 'publish');
                $this->registerTask('unfeature', 'feature');
                $this->registerTask('unapprove', 'approve');
                $this->registerTask('dislike', 'like');
                
		// Check if the cid array exists, otherwise populate with the id
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
                $id = JFactory::getApplication()->input->get('id', 0, 'int');
                if (empty($cid) && $id) JFactory::getApplication()->input->set('cid', array($id));
	}
        
        /**
	 * Proxy for getModel.
	 *
	 * @access  public
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.          
         * @return  object  The model.
	 */
	public function getModel($name = 'Album', $prefix = 'hwdMediaShareModel', $config = array('ignore_request' => true))
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to toggle the published value of a list of albums.
	 *
	 * @access	public
         * @return      void
	 */
	public function publish()
	{
		// Check for request forgeries.
		JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Initialise variables.
		$values	= array('publish' => 1, 'unpublish' => 0, 'delete' => -2);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers.
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Publish/unpublish the albums.
			if ($model->publish($cid, $value))
			{
                                switch ($task)
                                {
                                        case 'delete':
                                                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
                                        break;
                                        default:
                                                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_'.strtoupper($task).'ED', count($cid)));
                                }
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
                $return = base64_decode($this->input->get('return', null, 'base64'));
		$this->setRedirect($return ? $return : JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}

	/**
	 * Method to like or dislike a single album.
	 *
	 * @access	public
         * @return      void
	 */
	public function like()
	{
		// Check for request forgeries.
		JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));

		// Get items to like from the request.
		$cid = JFactory::getApplication()->input->get('id', 0, 'int');

		// Initialise variables.
		$values	= array('like' => 1, 'dislike' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (!is_numeric($cid) || $cid < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Like/dislike the album.
			if ($model->like($cid, $value))
			{
				$this->setMessage(JText::_($this->text_prefix . '_NOTICE_ALBUM_'.strtoupper($task).'D'));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
                $return = base64_decode($this->input->get('return', null, 'base64'));
		$this->setRedirect($return ? $return : JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
        
	/**
	 * Method to report a single album.
	 *
	 * @access	public
         * @return      void
	 */
	public function report()
	{
		// Check for request forgeries.
		JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));

                // Load HWD utilities.
                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
		// Get items to report from the request.
		$cid = JFactory::getApplication()->input->get('id', 0, 'int');

		if (!is_numeric($cid) || $cid < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Report the album.
			if ($model->report($cid))
			{
				$utilities->printModalNotice('COM_HWDMS_NOTICE_ALBUM_REPORTED', 'COM_HWDMS_NOTICE_ALBUM_REPORTED_DESC'); 
			}
			else
			{
				$utilities->printModalNotice('COM_HWDMS_NOTICE_ALBUM_REPORT_FAILED', $model->getError()); 
			}
		}
	}          
}
