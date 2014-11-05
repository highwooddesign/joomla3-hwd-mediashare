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

class hwdMediaShareControllerAlbumMedia extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
         * 
         * @access  protected
	 * @var     string
	 */
	protected $text_prefix = 'COM_HWDMS';

	/**
	 * The name of the listing view to use with this controller.
         * 
         * @access  protected
	 * @var     string
	 */
    	protected $view_list = "albummedia";
                
        /**
	 * Proxy for getModel.
	 *
	 * @access  public
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.          
         * @return  object  The model.
	 */
	public function getModel($name = 'AlbumMediaItem', $prefix = 'hwdMediaShareModel', $config = array())
	{
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
	}
        
        /**
	 * Method to unlink media from an album.
	 *
	 * @access  public
         * @return  void
	 */
	public function unlink()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
                // We select the mid array which contains the media ID integers. The cid array will 
                // contain the original checkbox values which relate to the mapping table
		$cid = $this->input->get('mid', array(), 'array');
		$albumId = $this->input->get('album_id', '', 'int');
		$add = $this->input->get('add', '', 'int');

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

			// Unlink the items.
			if ($model->unlink($cid, $albumId))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_MEDIA_UNLINKED_FROM_ALBUM', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&tmpl=component&album_id=' . $albumId . '&add=' . $add, false));
	}
        
        /**
	 * Method to link media to an album.
	 *
	 * @access  public
         * @return  void
	 */
	public function link()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
                // We select the mid array which contains the media ID integers. The cid array will 
                // contain the original checkbox values which relate to the mapping table
		$cid = $this->input->get('mid', array(), 'array');
		$albumId = $this->input->get('album_id', '', 'int');
		$add = $this->input->get('add', '', 'int');

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

			// Link the items.
			if ($model->link($cid, $albumId))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_MEDIA_LINKED_TO_ALBUM', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
                
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&tmpl=component&album_id=' . $albumId . '&add=' . $add, false));
	}
}
