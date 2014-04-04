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

class hwdMediaShareControllerCategories extends JControllerForm
{
	/**
	 * The prefix to use with controller messages.
	 * @var    string
	 */
	protected $text_prefix = 'COM_HWDMS';
        
	/**
	 * The URL view list variable.
	 * @var    string
	 */
    	protected $view_list = "categories";
        
	/**
	 * Constructor.
	 * @return	void
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
	 * @return	void
	 */
	public function getModel($name = 'Category', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
	/**
	 * Method to toggle the published value of a list of categories.
	 * @return	void
	 */
	function publish()
	{
		// Check for request forgeries
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

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Publish/unpublish the categories.
			if ($model->publish($cid, $value))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_'.strtoupper($task).'ED', count($cid)));
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
	 * Method to report a single category.
	 * @return	void
	 */
	public function report()
	{
		// Check for request forgeries
		JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('id', 0, 'int');

		if (!is_numeric($cid) || $cid < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Report the category.
			if ($model->report($cid))
			{
				$this->setMessage(JText::_($this->text_prefix . '_NOTICE_ALBUM_REPORTED'));
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
