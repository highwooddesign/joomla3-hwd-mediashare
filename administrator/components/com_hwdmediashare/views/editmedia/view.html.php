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

class hwdMediaShareViewEditMedia extends JViewLegacy 
{
	protected $state;

	protected $item;

	protected $form;
        
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
                // Get data from the model.
		$this->state	= $this->get('State');
		$this->item	= $this->get('Item');
		$this->form	= $this->get('Form');

                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                
                $isNew = $this->item->id == 0;
                if ($isNew) JFactory::getApplication()->redirect(JRoute::_('index.php?option='.JRequest::getCmd('option').'&view=addmedia', false));

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolBar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		// Since we don't track these assets at the item level, use the category id.
		$canDo		= hwdMediaShareHelper::getActions($this->item->id, 'media');

		JToolBarHelper::title(JText::_('COM_HWDMS_EDIT_MEDIA'), 'video');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||(count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create')))))
		{
			JToolbarHelper::apply('editmedia.apply');
			JToolbarHelper::save('editmedia.save');
		}
		if (!$checkedOut && (count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create'))))
		{
			JToolbarHelper::save2new('editmedia.save2new');
		}
		// If CDN then show sync options.
		if ($this->item->type == 6)
		{
                        // Load the HWDMediaShare language file
                        $lang =& JFactory::getLanguage();
                        $plugin = 'plg_hwdmediashare_'.$this->item->storage;
                        $lang->load($plugin, JPATH_SITE.'/administrator', $lang->getTag());

                        JToolBarHelper::custom('editmedia.syncToCdn', 'database', 'database', 'COM_HWDMS_SYNC_TO_CDN', false);
			JToolBarHelper::custom('editmedia.syncFromCdn', 'database', 'database', 'COM_HWDMS_SYNC_FROM_CDN', false);
                        JToolBarHelper::divider();
		}                
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('editmedia.cancel');
		}
		else
		{
			JToolbarHelper::cancel('editmedia.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs'); 
	}

	/**
	 * Method to display the human readable file type.
	 * @return  void
	 */
	public function getFileType($item)
	{
                hwdMediaShareFactory::load('files');
                return hwdMediaShareFiles::getFileType($item);
	}
        
	/**
	 * Method to display the human readable media source (local, remote, etc).
	 * @return  void
	 */
	public function getType($item)
	{
                hwdMediaShareFactory::load('media');
                return hwdMediaShareMedia::getType($item);
	}

	/**
	 * Method to display the human readable media type (audio, document, image, video).
	 * @return  void
	 */
	public function getMediaType($item)
	{
                hwdMediaShareFactory::load('media');
                return hwdMediaShareMedia::getMediaType($item);
	}
        
	/**
	 * Method to display the relative path to a media file.
	 * @return  void
	 */
	public function getPath($item)
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
	 * Method to display the extension for a media file.
	 * @return  void
	 */
	public function getExtension($item)
	{
                hwdMediaShareFactory::load('files');
                return hwdMediaShareFiles::getExtension($this->item, $item->file_type);
	}
}