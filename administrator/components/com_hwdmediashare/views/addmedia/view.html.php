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
         * 
         * @access  public
	 * @var     boolean
	 */
	public $show_form = true;

	/**
	 * The upload method view variable.
         * 
         * @access  public
	 * @var     mixed
	 */
	public $method = false;
        
	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function display($tpl = null)
	{
                // Initialise variables.
		$app = JFactory::getApplication();
                $lang = JFactory::getLanguage();
                $document = JFactory::getDocument();

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Import HWD libraries.
		hwdMediaShareFactory::load('upload');
		hwdMediaShareFactory::load('remote');
                
                // Get data from the model.
		$this->config = $config;
                $this->state = $this->get('State');
		$this->form = $this->get('Form');
                $this->replace = ($app->input->get('id', '', 'int') > 0 ? $app->input->get('id', '0', 'int'): false);
                $this->method = ($app->input->get('method', '', 'word') ? $app->input->get('method', '', 'word') : false);
                $this->return = base64_encode(JFactory::getURI()->toString());

		// Determine if we need to show the form.
		if ($this->config->get('upload_workflow') == 0 && $this->show_form && !$this->replace && $this->method != 'remote') 
		{
			$tpl = 'form';
		}             
                else
                {
                        $this->jformdata = hwdMediaShareUpload::getProcessedUploadData(); 
                        $this->jformreg = new JRegistry($this->jformdata);

                        if ($this->method == 'remote' && $this->config->get('enable_uploads_remote') == 1 && !$this->replace) 
                        {
                                $tpl = 'remote';
                        }  
                        else
                        {
                                $this->localExtensions = $this->get('localExtensions');

                                if ($this->config->get('enable_uploads_file') == 1) 
                                {
                                        if ($this->config->get('upload_tool_perl') == 1) 
                                        {
                                                $this->get('UberUploadScript');
                                        }
                                        elseif ($this->config->get('upload_workflow') == 1) 
                                        {
                                                $this->get('FancyUploadScript');
                                        }
                                }

                                // Bulk import from server (unless we are updating an existing media).
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
                
		$this->document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/administrator.css");
                
                // Load file input script.
		$this->document->addScript(JURI::root() . "media/com_hwdmediashare/assets/javascript/bootstrap-file-input.js");
		$this->document->addScriptDeclaration("
                    var buttonWord = 'Select File...';
                    jQuery(document).ready(function(){
                            jQuery('.hwd-form-filedata').bootstrapFileInput();
                    });
                ");        
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
                $canDo = hwdMediaShareHelper::getActions();
		$user  = JFactory::getUser();
                
		JToolBarHelper::title($this->method == 'remote' && $this->config->get('enable_uploads_remote') == 1 && !$this->replace ? JText::_('COM_HWDMS_ADD_REMOTE_MEDIA') : JText::_('COM_HWDMS_ADD_NEW_MEDIA'), 'upload');

                if ($this->config->get('upload_workflow') == 0 && $this->show_form && $this->method != 'remote' && $canDo->get('core.create'))
		{
                        JToolBarHelper::custom('addmedia.processform', 'save-new', 'save-new', 'COM_HWDMS_TOOLBAR_SAVE_AND_UPLOAD', false);
		}
                JToolbarHelper::cancel('addmedia.cancel');
		JToolbarHelper::help('HWD', false, 'http://hwdmediashare.co.uk/learn/docs');
	}

 	/**
	 * Method to render the platform upload title.
	 *
	 * @access  protected
	 * @return  void
	 */
	protected function getPlatformUploadTitle()
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $pluginClass = 'plgHwdmediashare' . $config->get('platform');
                $pluginPath = JPATH_ROOT . '/plugins/hwdmediashare/' . $config->get('platform') . '/' . $config->get('platform') . '.php';
                if (file_exists($pluginPath))
                {
                        // Load the language file.
                        $lang = JFactory::getLanguage();
                        $lang->load('plg_hwdmediashare_' .  $config->get('platform'), JPATH_ADMINISTRATOR, $lang->getTag());

                        return JText::_('PLG_HWDMEDIASHARE_' . $config->get('platform') . '_UPLOAD_TO_PLATFORM');
                }
                else
                {
                        return JText::_('COM_HWDMS_UPLOAD_TO_PLATFORM');
                }
	}
        
 	/**
	 * Method to render the platform upload form.
	 *
	 * @access  protected
	 * @return  void
	 */
	protected function getPlatformUploadForm()
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $pluginClass = 'plgHwdmediashare' . $config->get('platform');
                $pluginPath = JPATH_ROOT . '/plugins/hwdmediashare/' . $config->get('platform') . '/' . $config->get('platform') . '.php';
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $HWDplatform = call_user_func(array($pluginClass, 'getInstance'));
                        return $HWDplatform->getUploadForm();
                }
	}
        
 	/**
	 * Method to get the folder level for the server driectory scan tool.
	 *
	 * @access  protected
	 * @return  void
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
	 * Display the scan view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	function scan($tpl = null)
	{
                // Initialise variables.
                $app = JFactory::getApplication();            
                $document = JFactory::getDocument();
                $db = JFactory::getDBO();

                // Import Joomla libraries.
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
                
		// Required to initiate the MooTree functionality.
                $document->addScriptDeclaration("
		window.addEvent('domready', function() {
			window.parent.document.updateUploader();
		});");
                
                $folder = $app->input->get('folder', '', 'path');
                        
		// Get some paths from the request.
		$base = JPATH_SITE.'/media/'.$folder;

		// Get the list of folders.
		$files = JFolder::files($base, '.', false, true);

		$count = 0;

		foreach ($files as $file)
		{
                        // Retrieve file details.
                        $ext = strtolower(JFile::getExt($file));

                        // Check if the file has an allowed extension.
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_ext')
                                ->where($db->quoteName('ext') . ' = ' . $db->quote($ext))
                                ->where($db->quoteName('published') . ' = ' . $db->quote(1));
                        $db->setQuery($query);
                        try
                        {
                                $db->execute(); 
                                $ext_id = $db->loadResult();                   
                        }
                        catch (RuntimeException $e)
                        {
                                $this->setError($e->getMessage());
                                return false;                            
                        }

                        // If the extension is allowed, then count it.
                        if ($ext_id > 0)
                        {
                                $count++;
                        }
		}

                $this->count = $count;
                $this->folder = $folder;

		// Display the template.
                parent::display('scan');
	}        
}
