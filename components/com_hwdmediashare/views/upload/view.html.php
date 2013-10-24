<?php
/**
 * @version    SVN $Id: view.html.php 1204 2013-02-28 10:48:03Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      01-Dec-2011 09:57:02
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewUpload extends JViewLegacy {
	// Overwriting JView display method
	function display($tpl = null)
	{
		$hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                if (!JFactory::getUser()->authorise('hwdmediashare.upload','com_hwdmediashare') 
                 && !JFactory::getUser()->authorise('hwdmediashare.import','com_hwdmediashare'))
                {
                        JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_ERROR_NOAUTHORISED_ADDMEDIA') );
                        JFactory::getApplication()->redirect( $config->get('no_access_redirect') > 0 ? ContentHelperRoute::getArticleRoute($config->get('no_access_redirect')) : hwdMediaShareHelperRoute::getMediaRoute() );
                }
            
		hwdMediaShareFactory::load('utilities');

                // Get data from the model
		$form = $this->get('Form');
		$script = $this->get('Script');
		$state = $this->get('State');
                $standardExtensions = $this->get('standardExtensions');
                $largeExtensions = $this->get('largeExtensions');
                $platformExtensions = $this->get('platformExtensions');
                
                // Get any binds
                $assocAlbum = $this->get('assocAlbum');
                $assocGroup = $this->get('assocGroup');
                $assocPlaylist = $this->get('assocPlaylist');
                $assocResponse = $this->get('assocResponse');
                $assocCategory = $this->get('assocCategory');

                // Download links
                hwdMediaShareFactory::load('files');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
                {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
                
                // Check for errors.
		if ($this->get('terms') === true)
		{
                        $tpl = 'terms';                        
		}
                
                if ($config->get('enable_uploads_file') == 1) 
                {
                        if ($config->get('upload_tool_fancy') == 1 && (is_array($standardExtensions) && count($standardExtensions) > 0)) 
                        {
                                $fancyUploadHtml = $this->get('FancyUploadScript');
                        }
                        if ($config->get('upload_tool_perl') == 1) 
                        {
                                $uberUploadHtml = $this->get('UberUploadScript');
                                $this->uberUploadHtml = $uberUploadHtml;
                        }
                }
                
                $params = &$state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assign('template',	        JRequest::getWord( 'tmpl', '' ));
                $this->assign('columns',	        $params->get('list_columns', 3));
                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));

                $this->assignRef('params',		$params);
                $this->assignRef('form',		$form);
                $this->assignRef('state',		$state);
                $this->assignRef('standardExtensions',	$standardExtensions);
                $this->assignRef('largeExtensions',	$largeExtensions);
                $this->assignRef('platformExtensions',	$platformExtensions);
                $this->assignRef('uberUploadHtml',	$uberUploadHtml);

                // Binds
                $this->assignRef('assocAlbum',          $assocAlbum);
                $this->assignRef('assocGroup',          $assocGroup);
                $this->assignRef('assocCategory',       $assocCategory);

                $this->assignRef('utilities',		hwdMediaShareUtilities::getInstance());

		$this->_prepareDocument();

		parent::display($tpl);
	}
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
                $menus	= $app->getMenu();
		$title	= null;
                
                JHtml::_('behavior.framework', true);
                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                if ($this->state->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');

                if ($this->params->get('upload_tool_fancy') == 1) 
                {
                        $this->document->addScript(JURI::root() . "/media/com_hwdmediashare/assets/javascript/Swiff.Uploader.js");
                        $this->document->addScript(JURI::root() . "/media/com_hwdmediashare/assets/javascript/Fx.ProgressBar.js");
                        $this->document->addScript(JURI::root() . "/media/com_hwdmediashare/assets/javascript/FancyUpload2.js");
                        $this->document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/fancy.css");
                }

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
                else
                {
			$this->params->def('page_heading', JText::_('COM_HWDMS_UPLOAD'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title))
                {
			$title = JText::_('COM_HWDMS_UPLOAD');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
                {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
                {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
                $this->document->setTitle($title);

		if ($this->params->get('meta_desc'))
		{
			$this->document->setDescription($this->params->get('meta_desc'));
		}

		if ($this->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
		}

		if ($this->params->get('meta_rights'))
		{
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
		}
         	
                if ($this->params->get('meta_author'))
		{
			//$this->document->setMetadata('author', $this->params->get('meta_author'));
		}       
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

 	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function getReadableAllowedRemotes()
	{
		hwdMediaShareFactory::load('remote');
                return hwdMediaShareRemote::getReadableAllowedRemotes();
	}        
        
 	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function getAssociated()
	{
		$buffer = null;
                $messages = array();
                
                if (isset($this->assocAlbum->id)) 
                {
                    $messages[] = 'New media will be added to '.$this->escape($this->assocAlbum->title).' album';
                    $buffer .= '<input type="hidden" name="jform[album_id]" value="'.$this->assocAlbum->id.'" />';
                    $buffer .= '<input type="hidden" name="jform_album_id" value="'.$this->assocAlbum->id.'" />';
                }
                if (isset($this->assocGroup->id)) 
                {
                    $messages[] = 'New media will be added to '.$this->escape($this->assocGroup->title).' group';
                    $buffer .= '<input type="hidden" name="jform[group_id]" value="'.$this->assocGroup->id.'" />';
                    $buffer .= '<input type="hidden" name="jform_group_id" value="'.$this->assocGroup->id.'" />';
                }
                if (isset($this->assocCategory->id)) 
                {
                    $messages[] = 'New media will be added to '.$this->escape($this->assocCategory->title).' category';
                    $buffer .= '<input type="hidden" name="jform[catid]" value="'.$this->assocCategory->id.'" />';
                    $buffer .= '<input type="hidden" name="jform_catid" value="'.$this->assocCategory->id.'" />';
                }
                
                // Build the return string. If messages exist render them
		if (is_array($messages) && count($messages))
		{
                    $buffer .= "<div id=\"system-message-container\">";
			$buffer .= "\n<dl id=\"system-message\">";
                            $buffer .= "\n<dt class=\"message\">Message</dt>";
                            $buffer .= "\n<dd class=\"message message\">";
                            $buffer .= "\n\t<ul>";
                            foreach ($messages as $message)
                            {
                                    $buffer .= "\n\t\t<li>" . $message . "</li>";
                            }
                            $buffer .= "\n\t</ul>";
                            $buffer .= "\n</dd>";
			$buffer .= "\n</dl>";
                    $buffer .= "\n</div>";
		}

		return $buffer;
	}
}
