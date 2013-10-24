<?php
/**
 * @version    SVN $Id: view.html.php 405 2012-05-31 12:21:35Z dhorsfall $
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
class hwdMediaShareViewEditMedia extends JViewLegacy {
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null)
	{
                $app = & JFactory::getApplication();

                // get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');
		$script = $this->get('Script');

                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                
                $isNew = $item->id == 0;
                if ($isNew)
                {
                    JFactory::getApplication()->redirect(JRoute::_('index.php?option='.JRequest::getCmd('option').'&view=addmedia', false));
                }

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;
		$this->script = $script;

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
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
		$canDo = hwdMediaShareHelper::getActions($this->item->id, 'media');
		JToolBarHelper::title(JText::_('COM_HWDMS_EDIT_MEDIA'), 'hwdmediashare');

		if ($this->item->type == 6)
		{
                        // Load the HWDMediaShare language file
                        $lang =& JFactory::getLanguage();
                        $plugin = 'plg_hwdmediashare_'.$this->item->storage;
                        $lang->load($plugin, JPATH_SITE.'/administrator', $lang->getTag());

                        // Sample data install option
                        $document = JFactory::getDocument();
                        $document->addStyleDeclaration('.icon-32-platform {background-image: url(../plugins/hwdmediashare/'.$this->item->storage.'/assets/logo-32.png);}');
                        JToolBarHelper::custom('editmedia.syncToCdn', 'platform.png', 'platform_f2.png', 'COM_HWDMS_SYNC_TO_CDN', false);
			JToolBarHelper::custom('editmedia.syncFromCdn', 'platform.png', 'platform_f2.png', 'COM_HWDMS_SYNC_FROM_CDN', false);
                        JToolBarHelper::divider();
		}
                
                // Built the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::apply('editmedia.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('editmedia.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('editmedia.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('editmedia.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('editmedia.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('editmedia.save', 'JTOOLBAR_SAVE');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create'))
				{
					JToolBarHelper::custom('editmedia.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			JToolBarHelper::cancel('editmedia.cancel', 'JTOOLBAR_CLOSE');
		}
                JToolBarHelper::divider();
                JToolBarHelper::custom('help', 'help.png', 'help.png', 'JHELP', false);
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_EDIT_MEDIA'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/".JRequest::getCmd('view')."/submitbutton.js");
                JText::script('COM_HWDMS_ERROR_UNACCEPTABLE');
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getFileType( &$item )
	{
                hwdMediaShareFactory::load('files');
                return hwdMediaShareFiles::getFileType($item);
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getType( &$item )
	{
                hwdMediaShareFactory::load('media');
                return hwdMediaShareMedia::getType($item);
	}

	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getMediaType( &$item )
	{
                hwdMediaShareFactory::load('media');
                return hwdMediaShareMedia::getMediaType($item);
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getPath( &$item )
	{
                hwdMediaShareFactory::load('files');
                hwdMediaShareFiles::getLocalStoragePath();
                $folders = hwdMediaShareFiles::getFolders($this->item->key);
                $filename = hwdMediaShareFiles::getFilename($this->item->key,$item->file_type);
                $ext = hwdMediaShareFiles::getExtension($this->item,$item->file_type);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                return str_replace(JPATH_SITE, '', $path);
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getExtension( &$item )
	{
                hwdMediaShareFactory::load('files');
                return hwdMediaShareFiles::getExtension($this->item,$item->file_type);
	}
}