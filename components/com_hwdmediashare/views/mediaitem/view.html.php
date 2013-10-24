<?php
/**
 * @version    SVN $Id: view.html.php 1568 2013-06-13 10:17:34Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewMediaItem extends JViewLegacy {
        // Overwriting JView display method
	function display($tpl = null)
	{
                $app = & JFactory::getApplication();

                $mobile = & hwdMediaShareHelperMobile::getInstance();

                // Get the Data
		$item = $this->get('Item');
		$script = $this->get('Script');
                $state = $this->get('State');
                $related = $this->get('Related');              

		// Temporary backwards compatibility
                //jimport('joomla.html.pane');
                //$pane = & JPane::getInstance('tabs');
                //$this->pane = $pane;

                // Download links
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('utilities');
                hwdMediaShareFactory::load('recaptcha.recaptchalib');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                JLoader::register('hwdMediaShareHelperModule', JPATH_COMPONENT . '/helpers/module.php');
                hwdMediaShareHelperNavigation::setJavascriptVars();
                
                // Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

                // Check for errors.
		if (isset($item->agerestricted))
		{
			$this->assign('dob', JFactory::getApplication()->getUserState( "media.dob" ));
                        $tpl = 'dob';                        
		}
                
                // Check for errors.
		if (isset($item->passwordprotected))
		{
			$tpl = 'password';                        
		}
                
		$params = &$state->params;

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));
                $this->assign('mobile',                 $mobile);
                $this->assign('columns',	        $params->get('list_columns', 3));
                $this->assign('searchword',             $state->get('related.searchword'));     
                
                $this->assignRef('params',		$params);
		$this->assignRef('parent',		$parent);
		$this->assignRef('item',		$item);
		$this->assignRef('state',		$state);
                $this->assignRef('related',		$related);
                $this->assignRef('utilities',		hwdMediaShareUtilities::getInstance());

                $model = $this->getModel();
		$model->hit();

		$this->_prepareDocument();

                // Display the view
                parent::display($tpl);
	}
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                
                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                if ($this->state->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');

                $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/hwd.min.js');
                // Add image tag
                $this->document->addCustomTag('<link rel="image_src" href="'.$utilities->relToAbs(JRoute::_(hwdMediaShareDownloads::thumbnail($this->item))).'"/>');
                // Add open graph tags (facebook support)
                hwdMediaShareFactory::load('opengraph.opengraph');
                $openGraph = hwdMediaShareOpenGraph::getInstance();
                $openGraph->get($this->item);

                if(!isset($this->item->media_type))
                {
                        $this->item->media_type = hwdMediaShareMedia::loadMediaType($this->item);
                }
                
                if ($this->item->media_type == 3)
                {
$url = 'index.php?option=com_hwdmediashare&task=get.url&id=' . $this->item->id . '&format=raw';

$ajax = <<<EOD
window.addEvent('domready', function() {

                var size = $('media-item-image').getSize();

		var a = new Request({
                        url: '{$url}&width=' + size.x,
                        method: 'get',
                        onComplete: function( response )
                        {
                                var json = JSON.decode(response);
                                var src = json['url'];

                                if(src!='')
                                {
                                        $('media-item-image').setProperty('src',src);
                                }
                        }
		}).send();
});

/*
window.addEvent('resize', function(){
  clearTimeout(window.timer);
  window.timer= setTimeout(function(){

  },500);
});
*/
EOD;

$doc = & JFactory::getDocument();
//$doc->addScriptDeclaration( $ajax );
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
			$this->params->def('page_heading', JText::_('COM_HWDMS_MEDIA'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// If the menu item does not concern this item
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'mediaitem' || $id != $this->item->id))
		{
			// If this is not a single item menu item, set the page title to the item title
			if ($this->item->title) 
                        {
				$title = $this->item->title;
			}      
                        
                        // Breadcrumb support
			$path = array(array('title' => $this->item->title, 'link' => ''));
                        
                        // Category breadcrumb support
                        if (isset($this->item->categories) && count($this->item->categories) == 1)
                        {
                                // Load JCategories
                                jimport('joomla.application.categories');
                                $category = JCategories::getInstance('hwdMediaShare')->get($this->item->categories[0]->id);                                
                                while ($category && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] == 'mediaitem' || $id != $category->id) && $category->id > 1)
                                {
                                        $path[] = array('title' => $category->title, 'link' => hwdMediaShareHelperRoute::getCategoryRoute($category->id));
                                        $category = $category->getParent();
                                }
                        }
                        
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}                    
		}

		// Check for empty title and add site name if param is set
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		if (empty($title)) {
			$title = $this->item->title;
		}
		$this->document->setTitle($title);

		if ($this->item->params->get('meta_desc'))
		{
			$this->document->setDescription($this->item->params->get('meta_desc'));
		}
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }
                else
                {                        
			$this->document->setDescription($this->escape(JHtmlString::truncate($this->item->description, $this->params->get('list_desc_truncate'), true, false)));   
                }                

		if ($this->item->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->item->params->get('meta_keys'));
		}
                elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if ($this->item->params->get('meta_rights'))
		{
			$this->document->setMetadata('copyright', $this->item->params->get('meta_rights'));
		}
                elseif ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
                }             
                
		if ($this->item->params->get('meta_author') == 1 && isset($this->item->author))
		{
			$this->document->setMetadata('author', $this->item->author);
		}
                elseif ($this->params->get('meta_author') == 1 && isset($this->item->author))
                {
			$this->document->setMetadata('author', $this->item->author);
                }  
	}
        
	/**
	 * DEPRECATED Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getChannel( &$item )
	{
                if (!isset($item))
                {
                        return;
                }

                $href = '';
                if ($this->item->created_user_id > 0)
                {
                        $href = '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->item->created_user_id)).'">'.JText::_($this->item->author).'</a>';
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }  
                
                return $href;
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getCategories( &$item )
	{
                if (!isset($item))
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }

                $href = '';
                if (count($item->categories) > 0)
                {
                        foreach ($item->categories as $value)
                        {
                                $href.= '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($value->id)).'">' . $value->title . '</a> ';
                        }
                        unset($value);
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }             

                return $href;
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getActivities( &$item, $parent = true )
	{
                hwdMediaShareFactory::load('activities');
                return hwdMediaShareActivities::getActivities($item, $parent);
        }
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getLinkedAlbums( &$item )
	{
                if (!isset($item))
                {
                        return;
                }

                $href = '';
                if (count($item->linkedalbums) > 0)
                {
                        foreach ($item->linkedalbums as $value)
                        {
                                $href.= '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($value->id)).'">' . $value->title . '</a> ';

                        }
                        unset($value);
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }             

                return $href;
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getLinkedGroups( &$item )
	{
                if (!isset($item))
                {
                        return;
                }

                $href = '';
                if (count($item->linkedgroups) > 0)
                {
                        foreach ($item->linkedgroups as $value)
                        {
                                $href.= '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getGroupRoute($value->id)).'">' . $value->title . '</a> ';

                        }
                        unset($value);
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }             

                return $href;
	}
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getLinkedPlaylists( &$item )
	{
                if (!isset($item))
                {
                        return;
                }

                $href = '';
                if (count($item->linkedplaylists) > 0)
                {
                        foreach ($item->linkedplaylists as $value)
                        {
                                $href.= '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getPlaylistRoute($value->id)).'">' . $value->title . '</a> ';

                        }
                        unset($value);
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }             

                return $href;
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getLinkedMedia( &$item )
	{
                if (!isset($item))
                {
                        return;
                }

                $href = '';
                if (count($item->linkedmedia) > 0)
                {
                        foreach ($item->linkedmedia as $value)
                        {
                                $href.= '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($value->id)).'">' . $value->title . '</a> ';

                        }
                        unset($value);
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }             

                return $href;
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getLinkedPages( &$item )
	{
                if (!isset($item))
                {
                        return;
                }

                $href = '';
                if (count($item->linkedpages) > 0)
                {
                        foreach ($item->linkedpages as $value)
                        {
                                $href.= '<a href="#">' . $value->title . '</a> ';

                        }
                        unset($value);
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }             

                return $href;
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function hasDownloads()
	{
                if ($this->item->type == 1 || $this->item->type == 5 || $this->item->type == 7)
                {
                        return true;
                }
                return false;
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function hasMeta()
	{
                if ($this->item->type == 1 && ($this->item->media_type == 1 || $this->item->media_type == 3 || $this->item->media_type == 4))
                {
                        return true;
                }
                return false;
	}
        
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function hasQualities()
	{
                if ($this->item->media_type == 4 && ($this->item->type == 1 || $this->item->type == 5))
                {
                        return true;
                }
                return false;
	}  
        
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getRecaptcha()
	{
                if ($this->params->get('recaptcha_public_key'))
                {
                        hwdMediaShareFactory::load('recaptcha.recaptchalib');
                        return recaptcha_get_html($this->params->get('recaptcha_public_key'));
                }
                return;
	}  
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getComments()
	{
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $pluginClass = 'plgHwdmediashare'.$config->get('commenting');
                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('commenting').'/'.$config->get('commenting').'.php';

                // Import hwdMediaShare plugins
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $comms = call_user_func(array($pluginClass, 'getInstance'));
                        $params = new JRegistry('{}');
                        return $comms->getComments($params);
                }
	} 
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getCustomFieldData($field)
	{
                hwdMediaShareFactory::load('customfields');                
                return hwdMediaShareCustomFields::getFieldData($field);
	}         
}