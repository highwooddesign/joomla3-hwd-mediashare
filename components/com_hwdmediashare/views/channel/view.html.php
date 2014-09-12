<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareViewChannel extends JViewLegacy
{
	public $channel;

	public $items;        

	public $albums;

	public $favourites;

	public $groups;

	public $media;

	public $playlists;

	public $subscribers;

	public $activities;
        
	public $state;
        
	public $params;
         
	public $filterForm;
        
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
                
                // Get the layout from the request.                
                $this->layout = $app->input->get('layout', 'media', 'word');

                // Update the filterFormName state from the request.
                $this->get('FilterFormName');
                
                // Get data from the model.
                $this->channel = $this->get('Channel');
                $this->albums = $this->get('Albums');
                $this->favourites = $this->get('Favourites');
                $this->groups = $this->get('Groups');
                $this->media = $this->get('Media');
                $this->playlists = $this->get('Playlists');
                $this->subscribers = $this->get('Subscribers');
                $this->activities = $this->get('Activities');

                switch ($this->layout)
                {
                        case 'albums':
                                $this->items = $this->get('Albums');
                                $this->pagination = $this->get('Pagination');
                        break;
                        case 'favourites':
                                $this->items = $this->get('Favourites');
                                $this->pagination = $this->get('Pagination');
                        break;
                        case 'groups':
                                $this->items = $this->get('Groups');
                                $this->pagination = $this->get('Pagination');
                        break;
                        case 'playlists':
                                $this->items = $this->get('Playlists');
                                $this->pagination = $this->get('Pagination');
                        break;
                        case 'subscribers':
                                $this->items = $this->get('Subscribers'); 
                                $this->pagination = $this->get('Pagination');
                        break;
                        default:
                                $this->items = $this->get('Media');
                                $this->pagination = $this->get('Pagination');
                        break;
                }

		$this->state = $this->get('State');
		$this->params = $this->state->params;
                $this->filterForm = $this->get('FilterForm');

                // Load libraries.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

                // Import HWD libraries.  
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');
                hwdMediaShareHelperNavigation::setJavascriptVars();

                $this->channel->numalbums = $this->get('numAlbums');
                $this->channel->numfavourites = $this->get('numFavourites');
                $this->channel->numgroups = $this->get('numGroups');
                $this->channel->nummedia = $this->get('numMedia');
                $this->channel->numplaylists = $this->get('numPlaylists');
                $this->channel->numsubscribers = $this->get('numSubscribers');     
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->columns = (int) $this->params->get('list_columns', 3) - 1; // This view has columns, so we reduce the number of columns
                $this->return = base64_encode(JFactory::getURI()->toString());
                $this->display = $this->state->get('media.display', 'details');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

                $model = $this->getModel();
		$model->hit();

		$this->_prepareDocument();
                
		// Display the template.
		parent::display($tpl);
	}
        
	/**
	 * Prepares the document.
	 *
         * @access  protected
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;

                // Add page assets.
                JHtml::_('bootstrap.framework');
                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                if ($this->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/aspect.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/aspect.js');

		// Define the page title and headings. 
		$menu = $menus->getActive();
		if ($menu)
		{
                        $title = $this->params->get('page_title');
                        $heading = $this->params->get('page_heading', JText::_('COM_HWDMS_CHANNEL'));
		}
		else
		{
                        $title = JText::_('COM_HWDMS_CHANNEL');
                        $heading = JText::_('COM_HWDMS_CHANNEL');
		}

		// If the menu item does not concern this view then add a breadcrumb.
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'channel' || (int) @$menu->query['id'] != $this->channel->id))
		{
			// Reset title and heading if menu item doesn't point 
                        // directly to this item.
			if ($this->channel->title) 
                        {
				$title = $this->channel->title;
                                $heading = $this->channel->title;                           
			}   
                        
                        // Breadcrumb support.
			$path = array(array('title' => $this->channel->title, 'link' => ''));
                                                
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}                    
		}

		// Redefine the page title and headings. 
                $this->params->set('page_title', $title);
                $this->params->set('page_heading', $heading); 
                
		// Check for empty title and add site name when configured.
		if (empty($title))
                {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
                {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
                {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
                
                // Set metadata.
		$this->document->setTitle($title);
                
                if ($this->channel->params->get('meta_desc'))
		{
			$this->document->setDescription($this->channel->params->get('meta_desc'));
		}
                elseif ($this->channel->description)
                {                        
			$this->document->setDescription($this->escape(JHtmlString::truncate($this->channel->description, 160, true, false)));   
                }                 
                elseif ($menu && $menu->query['option'] == 'com_hwdmediashare' && $menu->query['view'] == 'channel' && (int) @$menu->query['id'] == $this->channel->id && $this->params->get('menu-meta_description'))
                {
			$this->document->setDescription($this->params->get('menu-meta_description'));
                } 
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }                

		if ($this->channel->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->channel->params->get('meta_keys'));
		}
                elseif ($menu && $menu->query['option'] == 'com_hwdmediashare' && $menu->query['view'] == 'channel' && (int) @$menu->query['id'] == $this->channel->id && $this->params->get('menu-meta_keywords'))
                {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
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
}
