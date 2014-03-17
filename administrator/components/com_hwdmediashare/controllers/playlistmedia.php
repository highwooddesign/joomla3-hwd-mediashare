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

class hwdMediaShareControllerPlaylistMedia extends JControllerAdmin
{
	/**
	 * The URL view list variable.
	 * @var    string
	 */
    	protected $view_list = "playlistmedia";
        
        /**
	 * Proxy for getModel.
	 * @return	void
	 */
	public function getModel($name = 'PlaylistMediaItem', $prefix = 'hwdMediaShareModel')
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
        /**
	 * Method to unlink media from a playlist
	 * @return	void
	 */
	public function unlink()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
                // We select the mid array which contains the media ID integers. The cid array will 
                // contain the original checkbox values which relate to the mapping table
		$cid = JFactory::getApplication()->input->get('mid', array(), 'array');
		$playlistId = JFactory::getApplication()->input->get('playlist_id', '', 'int');

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

			// Approve the items.
			if ($model->unlink($cid, $playlistId))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_MEDIA_UNLINKED_FROM_PLAYLIST', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&tmpl=component&playlist_id=' . $playlistId, false));
	}
        
        /**
	 * Method to link media to a playlist
	 * @return	void
	 */
	public function link()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
                // We select the mid array which contains the media ID integers. The cid array will 
                // contain the original checkbox values which relate to the mapping table
		$cid = JFactory::getApplication()->input->get('mid', array(), 'array');
		$playlistId = JFactory::getApplication()->input->get('playlist_id', '', 'int');

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

			// Approve the items.
			if ($model->link($cid, $playlistId))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_MEDIA_LINKED_TO_PLAYLIST', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&tmpl=component&playlist_id=' . $playlistId, false));
	}
}
