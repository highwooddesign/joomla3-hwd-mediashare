<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmigrator
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMigratorController extends JControllerLegacy
{
	/**
	 * Proxy view method for MVC based architecture.
	 *
	 * @access	public
	 * @param       boolean     $cachable       If true, the view output will be cached
	 * @param       array       $urlparams      An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 * @return      object      A JControllerLegacy object to support chaining.
	 */
	public function display($cachable = false, $urlparams = array())
	{
		// Set the default view name.
		$view = $this->input->get('view', 'dashboard');
                $this->input->set('view', $view);

		parent::display($cachable);

		return $this;
	}
}
