<?php
/**
 * @version    SVN $Id: view.html.php 1183 2013-02-25 15:28:47Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      14-Nov-2011 20:56:45
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewUser extends JViewLegacy {
	// Overwriting JView display method
	function display($tpl = null)
	{
                $app = & JFactory::getApplication();
                $layout = JRequest::getWord('layout', '');
                
                // Get data from the model
                if (!empty($layout))
                {
                        switch ($layout)
                        {
                            case 'albums':
                                $albums = $this->get('Albums');
                                break;
                            case 'favourites':
                                $favourites = $this->get('Favourites');
                                break;
                            case 'groups':
                                $groups = $this->get('Groups');
                                break;
                            case 'playlists':
                                $playlists = $this->get('Playlists'); 
                                break;
                            case 'subscribers':
                                $subscribers = $this->get('Subscribers');                           
                                break;
                            default:
                                $media = $this->get('Media');
                        }
                        $pagination = $this->get('Pagination');
                }
                else
                {
                        $activities = $this->get('Activities');
                        $albums = $this->get('Albums');
                        $favourites = $this->get('Favourites');
                        $groups = $this->get('Groups');
                        $media = $this->get('Media');
                        $playlists = $this->get('Playlists');
                        $subscribers = $this->get('Subscribers');                 
                }
                
		$channel = $this->get('Channel');
		$state	= $this->get('State');

                // Download links
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
		hwdMediaShareFactory::load('utilities');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                hwdMediaShareHelperNavigation::setJavascriptVars();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$params = &$state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

                $this->assign('columns',	        $params->get('list_columns', 3));
                $this->assign('display',		$state->get('media.display'));                
                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));
                
                $this->assignRef('channel',		$channel);

		$this->assignRef('activities',		$activities);
		$this->assignRef('albums',		$albums);
		$this->assignRef('favourites',		$favourites);
		$this->assignRef('groups',		$groups);
                $this->assignRef('media',		$media);
		$this->assignRef('playlists',		$playlists);
		$this->assignRef('subscribers',		$subscribers);

                $this->assignRef('pagination',		$pagination);
                $this->assignRef('state',		$state);
                $this->assignRef('params',		$params);
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
		$title	= null;

                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                if ($this->state->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');

                $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/hwd.min.js');
                                
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_HWDMS_USER_CHANNEL'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// If the menu item does not concern this item
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'user' || $id != $this->channel->id))
		{
			// If this is not a single item menu item, set the page title to the item title
			if ($this->channel->title) 
                        {
				$title = $this->channel->title;
			}      
                        
                        // Breadcrumb support
			$path = array(array('title' => $this->channel->title, 'link' => ''));
                                                
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
			$title = $this->channel->title;
		}
		$this->document->setTitle($title);

                if ($this->channel->params->get('meta_desc'))
		{
			$this->document->setDescription($this->channel->params->get('meta_desc'));
		}
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }
                else
                {                        
			$this->document->setDescription($this->escape(JHtmlString::truncate($this->channel->description, $this->params->get('list_desc_truncate'), true, false)));   
                }                

		if ($this->channel->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->channel->params->get('meta_keys'));
		}
                elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if ($this->channel->params->get('meta_rights'))
		{
			$this->document->setMetadata('keywords', $this->channel->params->get('meta_rights'));
		}
                elseif ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
                }             
                
		if ($this->channel->params->get('meta_author') == 1)
		{
			$this->document->setMetadata('author', $this->channel->title);
		}
                elseif ($this->params->get('meta_author') == 1)
                {
			$this->document->setMetadata('author', $this->channel->title);
                }            
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
                $act = hwdMediaShareActivities::getInstance();
                return $act->getActivities($item, $parent);
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
                        return;
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
}
