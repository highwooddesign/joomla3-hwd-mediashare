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

class hwdMediaShareViewPlaylistMedia extends JViewLegacy
{
	public $items;
        
	public $state;
        
	public $params;
        
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
		$this->params = $this->state->params;
                $this->playlistId = JFactory::getApplication()->input->get('playlist_id', '', 'int');

                // Include JHtml helpers.
                JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
                JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->_prepareDocument();
                
		// Display the template.
		parent::display($tpl);
	}
        
	/**
	 * Prepares the document.
	 *
         * @access  protected
	 * @return  void
	 */
	protected function _prepareDocument()
	{
                // Add page assets.
                JHtml::_('hwdhead.core', $this->params);
	}

	/**
	 * Display appropriate button to either link or unlink the media from the album.
         * 
         * @access  public
         * @param   object  $item   The content item.
         * @param   integer $i      The checkbox key.
         * @return  string  The markup for the button.
	 */
	public function getButton($item, $i)
	{
                $task = $item->connection ? 'unlink' : 'link';
                $buttonClass = $item->connection ? 'btn btn-danger' : 'btn';

                // Start output buffer.
                ob_start();
                ?>
                <div class="btn-wrapper pull-right">
                        <a class="<?php echo $buttonClass; ?>" href="javascript:void(0);" onclick="return listItemTask('mb<?php echo $i; ?>','playlistmedia.<?php echo $task; ?>')">
                                <?php echo ($item->connection ? JText::_('COM_HWDMS_BTN_REMOVE_FROM_PLAYLIST') : JText::_('COM_HWDMS_BTN_ADD_TO_PLAYLIST')); ?>
                        </a>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;           
	}
}