<?php
/**
 * @version    SVN $Id: view.html.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      14-Feb-2012 14:42:36
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * hwdMediaShare View
 */
class hwdMediaShareViewPending extends JViewLegacy {
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null)
	{
                // get the Data
		$media = $this->get('media');
		$albums = $this->get('albums');
		$groups = $this->get('groups');
		$users = $this->get('users');
		$playlists = $this->get('playlists');
		$activities = $this->get('activities');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Assign the Data
		$this->media = $media;
		$this->albums = $albums;
		$this->groups = $groups;
		$this->users = $users;
		$this->playlists = $playlists;
		$this->activities = $activities;

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
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_PENDING_ITEMS'));
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
}