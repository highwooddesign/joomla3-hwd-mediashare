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

class hwdMediaShareController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean         If true, the view output will be cached
	 * @param   array           An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController     This object to support chaining.
	 */
	function display($cachable = false, $urlparams = array())
	{
		// Set the default view name.
		$view = $this->input->get('view', 'dashboard');
                $this->input->set('view', $view);

                // Set the submenu.
		hwdMediaShareHelper::addSubmenu($view);
                
		parent::display($cachable);

		return $this;
	}
}
