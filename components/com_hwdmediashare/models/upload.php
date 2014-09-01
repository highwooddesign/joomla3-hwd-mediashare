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

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/addmedia.php';

class hwdMediaShareModelUpload extends hwdMediaShareModelAddMedia 
{
	/**
	 * Model context string.
         * 
         * @access      public
	 * @var         string
	 */ 
	public $context = 'com_hwdmediashare.upload';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @access  protected
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * @return  void
	 */
	public function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $user = JFactory::getUser();

		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

                // List state information.
		parent::populateState($ordering, $direction);                 
	}

	/**
	 * Method to get the record form.
	 *
	 * @access  public
	 * @return  boolean  True if need to show the terms, false otherwise.
	 */
	public function getTerms()
	{
                // Initialise variables.
		$app = JFactory::getApplication();

		// Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Check if the terms option is enabled.
                if ($config->get('upload_terms') == 0) return false;
                
                // Check if terms have been accepted already by the user.
                if ($app->getUserState('media.terms') == 1) return false;
                
                return true;    
	}
}
