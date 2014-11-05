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

class hwdMediaShareControllerChannels extends JControllerLegacy
{
	/**
	 * The prefix to use with controller messages.
         * 
         * @access  protected
	 * @var     string
	 */
	protected $text_prefix = 'COM_HWDMS';
        
	/**
	 * The URL view list variable to use with this controller.
	 *
         * @access  protected
	 * @var     string
	 */
    	protected $view_list = "channels";
        
	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   array  $config  An optional associative array of configuration settings.
         * @return  void
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
                $this->registerTask('unsubscribe', 'subscribe');

		// Check if the cid array exists, otherwise populate with the id.
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
	public function getModel($name = 'Channel', $prefix = 'hwdMediaShareModel', $config = array('ignore_request' => true))
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to toggle the published value of a list of channels.
	 *
	 * @access  public
         * @return  void
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

			// Publish/unpublish the channels.
			if ($model->publish($cid, $value))
			{
                                switch ($task)
                                {
                                        case 'delete':
                                                $this->setMessage(JText::plural($this->text_prefix . '_N_CHANNELS_DELETED', count($cid)));
                                        break;
                                        default:
                                                $this->setMessage(JText::plural($this->text_prefix . '_N_CHANNELS_'.strtoupper($task).'ED', count($cid)));
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
	 * Method to like or dislike a single channel.
	 *
	 * @access  public
         * @return  void
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

			// Like/dislike the channel.
			if ($model->like($cid, $value))
			{
				$this->setMessage(JText::_($this->text_prefix . '_NOTICE_CHANNEL_'.strtoupper($task).'D'));
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
	 * Method to report a single channel.
	 *
	 * @access  public
         * @return  void
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

			// Report the channel.
			if ($model->report($cid))
			{
				$utilities->printModalNotice('COM_HWDMS_NOTICE_CHANNEL_REPORTED', 'COM_HWDMS_NOTICE_CHANNEL_REPORTED_DESC'); 
			}
			else
			{
				$utilities->printModalNotice('COM_HWDMS_NOTICE_CHANNEL_REPORT_FAILED', $model->getError()); 
			}
		}
	}

	/**
	 * Method to subscribe/unsubscribe to a single channel.
	 *
	 * @access  public
         * @return  void
	 */
	public function subscribe()
	{
		// Check for request forgeries.
		JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));

		// Get items to subscribe from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Initialise variables.
		$values	= array('subscribe' => 'subscribe', 'unsubscribe' => 'unsubscribe');
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 'subscribe', 'word');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');     
		}
		else
		{
			// Get the model.
                        hwdMediaShareFactory::load('subscriptions');
                        $model = hwdMediaShareSubscriptions::getInstance();                     
                        $model->elementType = 5;

			// Make sure the item ids are integers.
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Subscribe to the channel.
			if ($model->$value($cid))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_CHANNELS_'.strtoupper($task).'D', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
                $return = base64_decode($this->input->get('return', null, 'base64'));
		$this->setRedirect($return ? $return : JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
}
