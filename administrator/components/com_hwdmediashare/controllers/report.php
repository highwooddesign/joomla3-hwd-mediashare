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

class hwdMediaShareControllerReport extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
         * 
         * @access  protected
	 * @var     string
	 */
	protected $text_prefix = 'COM_HWDMS';
            
        /**
	 * Proxy for getModel.
	 *
	 * @access  public
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.          
         * @return  object  The model.
	 */
	public function getModel($name = 'Report', $prefix = 'hwdMediaShareModel', $config = array())
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}

        /**
	 * Method to dismiss a report.
	 *
	 * @access  public
         * @return  void
	 */
	public function dismiss()
	{
		// Check for request forgeries.
		JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

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

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}  
        
	/**
	 * Method to remove content that has been reported.
	 *
	 * @access  public
         * @return  void
	 */
	public function remove()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		// Initialise variables.
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

			// Approve the items.
			if ($model->remove($cid))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}
        
	/**
	 * Get the return URL, if a "return" variable has been 
         * passed in the request.
         * 
	 * @access  protected
         * @return  string     The return URL.
	 */
	protected function getReturnPage()
	{
		$return = $this->input->get('return', null, 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return)))
		{
			return JUri::base();
		}
		else
		{
			return base64_decode($return);
		}
	}        
}
