<?php
/**
 * @version    SVN $Id: view.html.php 792 2012-12-17 14:59:10Z dhorsfall $
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
class hwdMediaShareViewAddMedia extends JViewLegacy {
        /**
	 * display method of Hello view
	 * @return void
	 */
	function display($tpl = null)
	{
                $hwdms = hwdMediaShareFactory::getInstance();
                $this->config = $hwdms->getConfig();
		
		$app	= JFactory::getApplication();
                $lang	= JFactory::getLanguage();

                // Get data from the model
		$form = $this->get('Form');
		$script = $this->get('Script');
                $standardExtensions = $this->get('standardExtensions');
                $largeExtensions = $this->get('largeExtensions');
                $platformExtensions = $this->get('platformExtensions');
                        
                // Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
                
                // Assign data to the view
                $this->replace = (JRequest::getInt('id') > 0 ? true: false);
                
                if ($this->config->get('enable_uploads_file') == 1) 
                {
                        if ($this->config->get('upload_tool_fancy') == 1 && !$this->replace && (is_array($standardExtensions) && count($standardExtensions) > 0)) 
                        {
                                $fancyUploadHtml = $this->get('FancyUploadScript');
                        }
                        if ($this->config->get('upload_tool_perl') == 1) 
                        {
                                $uberUploadHtml = $this->get('UberUploadScript');
                                $this->uberUploadHtml = $uberUploadHtml;
                        }
                }
                
                // Assign data to the view
		$this->form = $form;
                $this->standardExtensions = $standardExtensions;
                $this->largeExtensions = $largeExtensions;
                $this->platformExtensions = $platformExtensions;
                
                // Bulk Import from server
                if (!$this->replace) 
                {
                        $style = $app->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

                        $document = JFactory::getDocument();

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

                        $session	= JFactory::getSession();
                        $state		= $this->get('state');
                        $this->assignRef('state', $state);
                        $this->assign('folders', $this->get('folderTree'));
                        $this->assign('folders_id', ' id="media-tree"');
                }
                
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
		JToolBarHelper::title(JText::_('COM_HWDMS_ADD_NEW_MEDIA'), 'hwdmediashare');
                JToolBarHelper::custom('help', 'help.png', 'help.png', 'JHELP', false);
	}
 	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
                $document = JFactory::getDocument();
                $document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/".JRequest::getCmd('view')."/submitbutton.js");
                if ($this->config->get('upload_tool_fancy') == 1) 
                {
                        $document->addScript(JURI::root() . "/media/com_hwdmediashare/assets/javascript/Swiff.Uploader.js");
                        $document->addScript(JURI::root() . "/media/com_hwdmediashare/assets/javascript/Fx.ProgressBar.js");
                        $document->addScript(JURI::root() . "/media/com_hwdmediashare/assets/javascript/FancyUpload2.js");
                        $document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/fancy.css");
                }
                $document->setTitle(JText::_('COM_HWDMS_HWDMEDIASHARE').' '.JText::_('COM_HWDMS_ADD_NEW_MEDIA'));
	}
        
 	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function getPlatformUploadForm()
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                $pluginClass = 'plgHwdmediashare'.$config->get('platform');
                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('platform').'/'.$config->get('platform').'.php';

                // Import hwdMediaShare plugins
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $player = call_user_func(array($pluginClass, 'getInstance'));
                        return $player->getUploadForm();
                }
	}
        
 	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function getReadableAllowedMediaTypes($method=null)
	{
		hwdMediaShareFactory::load('upload');
                return hwdMediaShareUpload::getReadableAllowedMediaTypes($method);
	}
        
 	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function getReadableAllowedExtensions($extensions)
	{
		hwdMediaShareFactory::load('upload');
                return hwdMediaShareUpload::getReadableAllowedExtensions($extensions);
	}
        
	function getFolderLevel($folder)
	{
		$this->folders_id = null;
		$txt = null;
		if (isset($folder['children']) && count($folder['children'])) {
			$tmp = $this->folders;
			$this->folders = $folder;
			$txt = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}
		return $txt;
	}
        
	// Overwriting JView display method
	function scan($tpl = null)
	{
                $document = JFactory::getDocument();
                $document->addScript(JURI::root() . "/administrator/components/com_hwdmediashare/views/addmedia/submitbutton.js");

		// Required to initiate the MooTree functionality
                $document->addScriptDeclaration("
		window.addEvent('domready', function() {
			window.parent.document.updateUploader();
		});");
                
                $folder = JRequest::getVar('folder', '', '', 'path');

		// Get some paths from the request
		$base = JPATH_SITE.'/media/'.$folder;

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$files = JFolder::files($base, '.', false, true);

		$count = 0;

		foreach ($files as $file)
		{
                        //Import filesystem libraries. Perhaps not necessary, but does not hurt
                        jimport('joomla.filesystem.file');
                        
                        //Retrieve file details
                        $ext = strtolower(JFile::getExt($file));

                        //First check if the file has the right extension, we need jpg only
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

                $this->assign('count', $count);
                $this->assign('folder', $folder);

                // Display the view
                parent::display('scan');
	}        
}
