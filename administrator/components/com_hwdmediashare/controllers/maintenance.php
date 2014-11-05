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

class hwdMediaShareControllerMaintenance extends JControllerForm
{
        /**
	 * Proxy for cancel.
	 *
	 * @access  public
	 * @param   string  $key  The name of the primary key of the URL variable.
         * @return  void
	 */
	public function cancel($key = null)
	{
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=dashboard', false));
	}
}
