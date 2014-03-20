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

class hwdMediaShareViewAddMedia extends JViewLegacy
{
	/**
	 * The show form view variable.
	 * @var    boolean
	 */
	public $show_form = true;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
                // Load HWD config
                $hwdms = hwdMediaShareFactory::getInstance();
		$app = JFactory::getApplication();
                $lang = JFactory::getLanguage();
                $document = JFactory::getDocument();

                // Get data from the model.
		$this->config = $hwdms->getConfig();
                $this->state = $this->get('State');
		$this->form = $this->get('Form');
                $this->replace = (JFactory::getApplication()->input->get('id', '', 'int') > 0 ? true: false);

		// Determine if we need to show the form
		if ($this->config->get('upload_workflow') == 0 && $this->show_form && !$this->replace) 
		{
			$this->setLayout('form');
		}
                else
                {              
                        $this->jformdata = $this->get('processedUploadData'); 
                        $this->standardExtensions = $this->get('standardExtensions');
                        $this->largeExtensions = $this->get('largeExtensions');
                        $this->platformExtensions = $this->get('platformExtensions');

                        if ($this->config->get('enable_uploads_file') == 1) 
                        {
                                if ($this->config->get('upload_tool_fancy') == 1 && !$this->replace && (is_array($this->standardExtensions) && count($this->standardExtensions) > 0)) 
                                {
                                        // Add assets to the document.
                                        $this->get('FancyUploadScript');
                                }
                                if ($this->config->get('upload_tool_perl') == 1) 
                                {
                                        $this->uberUploadHtml = $this->get('UberUploadScript');
                                }
                        }

                        // Bulk import from server (unless we are updating an existing media)
                        if (!$this->replace) 
                        {
                                $style = $app->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

                                JHtml::_('behavior.framework', true);

                                $document->addScript(JURI::root() . "/media/com_hwdmediashare/assets/javascript/MooTree.js");

                                JHtml::_('script', 'system/mootree.js', true, true, false, false);
                                JHtml::_('stylesheet', 'system/mootree.css', array(), true);
                                if ($lang->isRTL()) :
                                        JHtml::_('stylesheet', 'media/mootree_rtl.css', array(), true);
                                endif;

                                $base = JPATH_SITE.'/media';

                                $js = "
                                        var basepath = '';
                                        var viewstyle = 'thumbs';
                                " ;
                                $document->addScriptDeclaration($js);

                                $session = JFactory::getSession();
                                $state = $this->get('state');
                                $this->state = $state;
                                $this->folders = $this->get('folderTree');
                                $this->folders_id = ' id="media-tree"';
                        } 
                }
                
                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}
                
		// Display the template.
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
                $canDo = hwdMediaShareHelper::getActions();
		$user  = JFactory::getUser();
                
		JToolBarHelper::title(JText::_('COM_HWDMS_ADD_NEW_MEDIA'), 'upload');

                if ($this->config->get('upload_workflow') == 0 && $this->show_form)
		{
                        if ($canDo->get('core.create'))
                        {
				JToolBarHelper::custom('addmedia.processform', 'save-new', 'save-new', 'COM_HWDMS_TOOLBAR_SAVE_AND_UPLOAD', false);
				JToolBarHelper::custom('addmedia.processform', 'arrow-right', 'arrow-right', 'COM_HWDMS_TOOLBAR_SKIP', false);
                        }
		}

                JToolBarHelper::divider();
                JToolbarHelper::cancel('addmedia.cancel');
                JToolBarHelper::divider();

		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs');
	}
                
 	/**
	 * Method to render the platform upload form
	 * @return void
	 */
	protected function getPlatformUploadForm()
	{
                // Load HWD config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $pluginClass = 'plgHwdmediashare'.$config->get('platform');
                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('platform').'/'.$config->get('platform').'.php';
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $player = call_user_func(array($pluginClass, 'getInstance'));
                        return $player->getUploadForm();
                }
	}
        
 	/**
	 * Method to get the human readable allowed media types
	 * @return void
	 */
	protected function getReadableAllowedMediaTypes($method=null)
	{
		hwdMediaShareFactory::load('upload');
                return hwdMediaShareUpload::getReadableAllowedMediaTypes($method);
	}
        
 	/**
	 * Method to get the human readable allowed media extensions
	 * @return void
	 */
	protected function getReadableAllowedExtensions($extensions)
	{
		hwdMediaShareFactory::load('upload');
                return hwdMediaShareUpload::getReadableAllowedExtensions($extensions);
	}
        
 	/**
	 * Method to get the folder level for the server driectory scan tool
	 * @return void
	 */        
	protected function getFolderLevel($folder)
	{
		$this->folders_id = null;
		$txt = null;
		if (isset($folder['children']) && count($folder['children'])) 
                {
			$tmp = $this->folders;
			$this->folders = $folder;
			$txt = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}
		return $txt;
	}
        
	/**
	 * Display the scan view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function scan($tpl = null)
	{
                $document = JFactory::getDocument();

		// Required to initiate the MooTree functionality
                $document->addScriptDeclaration("
		window.addEvent('domready', function() {
			window.parent.document.updateUploader();
		});");
                
                $folder = JFactory::getApplication()->input->get('folder', '', 'path');
                        
		// Get some paths from the request
		$base = JPATH_SITE.'/media/'.$folder;

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$files = JFolder::files($base, '.', false, true);

		$count = 0;

		foreach ($files as $file)
		{
                        // Import filesystem libraries. Perhaps not necessary, but does not hurt
                        jimport('joomla.filesystem.file');
                        
                        // Retrieve file details
                        $ext = strtolower(JFile::getExt($file));

                        // First check if the file has the right extension, we need jpg only
                        $db = JFactory::getDBO();
                        $query = $db->getQuery(true);
                        $query->select('id');
                        $query->from('#__hwdms_ext');
                        $query->where($db->quoteName('ext').' = '.$db->quote($ext));

                        $db->setQuery($query);
                        $ext_id = $db->loadResult();
                        if ( $ext_id > 0 )
                        {
                                $count++;
                        }
		}

                $this->count = $count;
                $this->folder = $folder;

                // Display the view
                parent::display('scan');
	}        
}
