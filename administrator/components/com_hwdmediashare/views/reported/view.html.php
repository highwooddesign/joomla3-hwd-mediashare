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

class hwdMediaShareViewReported extends JViewLegacy
{
	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function display($tpl = null)
	{
                $jinput = JFactory::getApplication()->input;
                $layout = $jinput->get('layout', '', 'word');
                $tmpl = $jinput->get('tmpl', '', 'word');
                
                // Get data from the model based on layout.
                switch ($layout)
                {
                        case 'media':
                        case 'albums':
                        case 'groups':
                        case 'playlists':
                        case 'channels':
                                $this->items = $this->get('Items');
                                $this->pagination = $this->get('Pagination');
                                $this->state = $this->get('State');
                        break;                     
                        default:
                                $this->media = $this->get('media');
                                $this->albums = $this->get('albums');
                                $this->groups = $this->get('groups');
                                $this->playlists = $this->get('playlists');
                                $this->channels = $this->get('channels');
                        break;
                }
                
                // Import HWD libraries.
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('thumbnails');

                $this->return = base64_encode(JFactory::getURI()->toString());

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal' && $tmpl !== 'component')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}
                
		// Display the template
		parent::display($tpl);
                
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css"); 
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @access  protected
	 * @return  void
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_HWDMS_REPORTED_ITEMS'), 'notification');

                JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs'); 
	}
        
	/**
	 * Method to get the type of report in a human readable format. 
	 *
	 * @access  public
         * @param   object  The report item.
	 * @return  string  The report type.
	 */
	public function getReportType($item)
	{
                switch ($item->report_id)
                {
                        case 1:
                                return JText::_('COM_HWDMS_SEXUAL_CONTENT');
                        break;
                        case 2:
                                return JText::_('COM_HWDMS_VIOLENT_OR_REPULSIVE_CONTENT');
                        break;
                        case 3:
                                return JText::_('COM_HWDMS_HATEFUL_OR_ABUSIVE_CONTENT');
                        break;
                        case 4:
                                return JText::_('COM_HWDMS_HARMFUL_ACTS');
                        break;
                        case 5:
                                return JText::_('COM_HWDMS_CHILD_ABUSE');
                        break;
                        case 6:
                                return JText::_('COM_HWDMS_SPAM');
                        break;
                        case 7:
                                return JText::_('COM_HWDMS_INFRINGES_MY_RIGHTS');
                        break;
                        case 8:
                                return JText::_('COM_HWDMS_BROKEN_MEDIA');
                        break;
                }
	}
}