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

class hwdMediaShareViewGroupMedia extends JViewLegacy 
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $groupId;
        
	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function display($tpl = null)
	{
                // Get data from the model.
                $this->items = $this->get('Items');
                $this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
                $this->groupId = JFactory::getApplication()->input->get('group_id', '', 'int');

                // Import HWD libraries.
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('thumbnails');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		// Display the template.
		parent::display($tpl);

		$this->document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css");                
	}

	/**
	 * Display appropriate button to either link or unlink the media from the group.
	 *
	 * @access  public
	 * @return  string  The HTML to render the button.
	 */
	public function getButton($row, $i)
	{
                $task = $row->connection ? 'unlink' : 'link';
                $class = $row->connection ? 'btn btn-danger' : 'btn';
                $text = $row->connection ? 'COM_HWDMS_BTN_REMOVE_FROM_GROUP' : 'COM_HWDMS_BTN_ADD_TO_GROUP';

                ob_start();
                ?>
                <div class="btn-wrapper pull-right">
                        <a class="<?php echo $class; ?>" href="javascript:void(0);" onclick="return listItemTask('mb<?php echo $i; ?>','groupmedia.<?php echo $task; ?>')"><?php echo JText::_($text); ?></a>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
	}
}
