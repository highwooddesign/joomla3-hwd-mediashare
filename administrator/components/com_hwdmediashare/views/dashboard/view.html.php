<?php
/**
 * @version    SVN $Id: view.html.php 492 2012-08-24 15:11:58Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * hwdMediaShare View
 */
class hwdMediaShareViewDashboard extends JViewLegacy {
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null)
	{
                $media = $this->get('Media');
                $version = $this->get('Version');
                $nummedia = $this->get('CountMedia');
                $numcategories = $this->get('CountCategories');
                $numalbums = $this->get('CountAlbums');
                $numgroups = $this->get('CountGroups');
                $numchannels = $this->get('CountChannels');
                $numplaylists = $this->get('CountPlaylists');

                // Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

                $this->assign( 'version', $version );
                $this->assign( 'nummedia', $nummedia );
                $this->assign( 'numcategories', $numcategories );
                $this->assign( 'numalbums', $numalbums );
                $this->assign( 'numgroups', $numgroups );
                $this->assign( 'numchannels', $numchannels );
                $this->assign( 'numplaylists', $numplaylists );
                
		jimport( 'joomla.html.html.sliders' );

                $this->assignRef( 'media', $media );

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
                JToolBarHelper::title(JText::_('COM_HWDMS_DASHBOARD'), 'hwdmediashare');
                JToolBarHelper::custom('help', 'help.png', 'help.png', 'JHELP', false);
                
                // Sample data install option
                if ($this->nummedia == 0 && $this->numcategories == 0 && $this->numalbums == 0 && $this->numgroups == 0 && $this->numchannels == 0 && $this->numplaylists == 0) 
                {
                        $document = JFactory::getDocument();
                        $document->addStyleDeclaration('.icon-32-sample {background-image: url(../media/com_hwdmediashare/assets/images/icons/32/sample-data.png);width:98px!important;}');
                        JToolBarHelper::custom('sample.install', 'sample.png', 'sample_f2.png', JText::_('COM_HWDMS_INSTALL_SAMPLE_DATA'), true);
                }
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
                $document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/dashboard/submitbutton.js");
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_DASHBOARD'));
        }

	function addIcon( $image, $url, $text, $newWindow = false, $modal = false )
	{
		$lang		=& JFactory::getLanguage();

		$newWindow	= ( $newWindow ) ? ' target="_blank"' : '';
		$modal          = ( $modal ) ? ' class="modal" rel="{handler: \'iframe\', size: {x: 415, y: 250}}" ' : '';
                ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $url; ?>"<?php echo $newWindow; ?><?php echo $modal; ?>>
					<?php echo JHtml::_('image', 'media/com_hwdmediashare/assets/images/icons/48/' . $image , NULL, NULL ); ?>
					<span><?php echo $text; ?></span>
                                </a>
			</div>
		</div>
                <?php
	}
}
