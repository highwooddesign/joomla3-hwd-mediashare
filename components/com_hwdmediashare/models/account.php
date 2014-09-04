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

// Base this model on the user model.
require_once JPATH_SITE.'/components/com_hwdmediashare/models/channel.php';

class hwdMediaShareModelAccount extends hwdMediaShareModelChannel
{
	/**
	 * Method to set the filterFormName variable for the account pages, 
         * allowing different filters in different layouts.
         * 
         * @access  public
	 * @return  void
	 */
	public function getFilterFormName()
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $layout = $app->input->get('layout', 'media', 'word');
		$this->filterFormName = 'filter_account_' . $layout;
	}    
}
