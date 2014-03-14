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

class hwdMediaShareControllerActivity extends JControllerForm
{
	/**
	 * The URL view list variable.
	 * @var    string
	 */
    	protected $view_list = "activities";

	/**
	 * Method to run batch operations.
	 *
	 * @param   object      $model  The model.
	 *
	 * @return  boolean     True if successful, false otherwise and internal error is set.
	 */
	public function batch($model = null)
	{            
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Activity', '', array());

		// Preset the redirect
		$this->setRedirect('index.php?option=com_hwdmediashare&view='.$this->view_list.$this->getRedirectToListAppend());

		return parent::batch($model);
	}        
}
