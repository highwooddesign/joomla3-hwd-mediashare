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
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function display($tpl = null)
	{
                // Get data from the model.
		$this->state	= $this->get('State');
		$this->item	= $this->get('Item');
		$this->form	= $this->get('Form');

                // Import HWD libraries.
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');

                // Redirect new upload views to the upload page.
                $isNew = $this->item->id == 0;
                if ($isNew) JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_hwdmediashare&view=addmedia', false));

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		// Display the template.
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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$canDo		= hwdMediaShareHelper::getActions($this->item->id, 'media');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

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

		// Show sync options for platform media.
		if ($this->item->type == 6)
		{
                        // Default language strings.
                        $sync2platform = 'COM_HWDMS_SYNC_TO_PLATFORM';
                        $sync2local = 'COM_HWDMS_SYNC_FROM_PLATFORM';
                        
                        $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$this->item->storage.'/'.$this->item->storage.'.php';                                
                        if (file_exists($pluginPath))
                        {
                                $lang = JFactory::getLanguage();
                                $plugin = 'plg_hwdmediashare_'.$this->item->storage;
                                $lang->load($plugin, JPATH_SITE.'/administrator', $lang->getTag());
                                $sync2platform = 'COM_HWDMS_SYNC_TO_PLATFORM_'.$this->item->storage;
                                $sync2local = 'COM_HWDMS_SYNC_FROM_PLATFORM_'.$this->item->storage;
                        }

                        JToolBarHelper::custom('editmedia.sync2cdn', 'database', 'database', $sync2platform, false);
			JToolBarHelper::custom('editmedia.sync2local', 'database', 'database', $sync2local, false);
		}                
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('editmedia.cancel');
		}
		else
		{
			JToolbarHelper::cancel('editmedia.cancel', 'JTOOLBAR_CLOSE');
		}
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs'); 
	}
        
	/**
	 * Method to display the relative path to a media file.
	 *
	 * @access  public
         * @param   object  $item   The media object
	 * @return  string  The path.
	 */
	public function getPath($item)
	{
                hwdMediaShareFiles::getLocalStoragePath();
                $folders = hwdMediaShareFiles::getFolders($this->item->key);
                $filename = hwdMediaShareFiles::getFilename($this->item->key,$item->file_type);
                $ext = hwdMediaShareFiles::getExtension($this->item,$item->file_type);
                $path = hwdMediaShareFiles::getPath($folders, $filename, $ext);
                return str_replace(JPATH_SITE, '', $path);
	}
}