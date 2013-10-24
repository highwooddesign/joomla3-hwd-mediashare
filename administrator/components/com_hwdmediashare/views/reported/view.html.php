<?php
/**
 * @version    SVN $Id: view.html.php 1245 2013-03-08 14:13:12Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      20-Feb-2012 20:13:08
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * hwdMediaShare View
 */
class hwdMediaShareViewReported extends JViewLegacy {
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null)
	{
                $layout = JRequest::getWord('layout');
                switch ($layout) {
                    case 'media':
                        // Get data from the model
                        $this->items = $this->get('Items');
                        $this->pagination = $this->get('Pagination');
                        $this->state	= $this->get('State');
                        break;
                    default:
                        // Get data from the model
                        $this->media = $this->get('media');
                        $this->albums = $this->get('albums');
                        $this->groups = $this->get('groups');
                        $this->users = $this->get('users');
                        $this->playlists = $this->get('playlists');
                        $this->activities = $this->get('activities');
                        break;
                }

                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                                JError::raiseError(500, implode('<br />', $errors));
                                return false;
                }

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();  
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_REPORTED_ITEMS'));
		$document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/".JRequest::getCmd('view')."/submitbutton.js");
                JText::script('COM_HWDMS_ERROR_UNACCEPTABLE');
	}
        

	function addIcon( $image, $url, $text )
	{
		$lang		=& JFactory::getLanguage();
                ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $url; ?>" target="_top">
					<?php echo JHtml::_('image', 'media/com_hwdmediashare/assets/images/icons/48/' . $image , NULL, NULL ); ?>
					<span><?php echo $text; ?></span>
                                </a>
			</div>
		</div>
                <?php
	}
        
	/**
	 * Method to get human readable report type
         * 
         * @since   0.1
	 **/
	public function getReportType($item)
	{
                switch ($item->report_id) {
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