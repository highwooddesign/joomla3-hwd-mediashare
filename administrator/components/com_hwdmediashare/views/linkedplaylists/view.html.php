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

class hwdMediaShareViewLinkedPlaylists extends JViewLegacy
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
                $this->filterForm = $this->get('FilterForm');
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
	 * Display appropriate button to either link or unlink the playlist from the media item.
	 * @return  void
	 */
	public function getConnection($row, $i)
	{
                $task = $row->connection ? 'unlink' : 'link';
                $text = $row->connection ? JText::_('COM_HWDMS_UNLINK') : JText::_('COM_HWDMS_LINK');

                // Start output
                ob_start();
                ?>
<div class="btn-wrapper pull-right">
<a title="" class="btn hasTooltip btn-primary" href="index.php?option=com_hwdmediashare&task=playlistmedia.<?php echo $task; ?>&tmpl=component&playlist_id=28&tmpl=component&playlist_id=28&add=0" data-original-title="Filter the list items">
<?php echo $text; ?>
</a>
</div> 
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
	}
}
