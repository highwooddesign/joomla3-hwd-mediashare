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

class hwdMediaShareControllerPlaylist extends JControllerForm
{
	/**
	 * The name of the listing view to use with this controller.
         * 
         * @access      protected
	 * @var         string
	 */
    	protected $view_list = "playlists";

	/**
	 * Method to run batch operations.
	 *
	 * @access	public
	 * @param       object      $model      The model.
	 * @return      boolean     True if successful, false otherwise and internal error is set.
	 */    
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model.
		$model = $this->getModel('Playlist', '', array());

		// Preset the redirect.
		$this->setRedirect(JRoute::_('index.php?option=com_hwdmediashare&view=' . $this->view_list . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}     
}
