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

class hwdMediaShareViewLinkedAlbums extends JViewLegacy
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
                // Get data from the model.
                $this->items = $this->get('Items');
                $this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
                $this->mediaId = JFactory::getApplication()->input->get('media_id', '', 'int');

                hwdMediaShareFactory::load('downloads');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		// Display the template
		parent::display($tpl);

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css");                
	}

	/**
	 * Display appropriate button to either link or unlink the media from the playlist.
	 * @return  void
	 */
	public function getButton($row, $i)
	{
                $task = $row->connection ? 'unlink' : 'link';
                $buttonClass = $row->connection ? 'btn btn-danger' : 'btn';

                // Start output
                ob_start();
                ?>
                <div class="btn-wrapper pull-right">
                        <a class="<?php echo $buttonClass; ?>" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','linkedalbums.<?php echo $task; ?>')">
                                <?php echo ($row->connection ? JText::_('COM_HWDMS_BTN_REMOVE_MEDIA_FROM_ALBUM') : JText::_('COM_HWDMS_BTN_ADD_MEDIA_TO_ALBUM')); ?>
                        </a>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
	}
}
