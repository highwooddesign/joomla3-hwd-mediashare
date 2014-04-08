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

class hwdMediaShareViewUser extends JViewLegacy
{
	public $user;

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
        
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                $this->layout = $app->input->get('layout', 'media', 'word');

                // Get data from the model.
                // User is called afterwards so we have data from the items.
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

                $this->user = $this->get('User');
		$this->state = $this->get('State');
		$this->params = $this->state->params;
                $this->filterForm = $this->get('FilterForm');

                // Load libraries.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
		hwdMediaShareFactory::load('utilities');
                hwdMediaShareFactory::load('activities');
                hwdMediaShareHelperNavigation::setJavascriptVars();

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
	 * Prepares the document
	 *
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
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'user' || $id != $this->user->id))
		{
			// If this is not a single item menu item, set the page title to the item title
			if ($this->user->title) 
                        {
				$title = $this->user->title;
			}      
                        
                        // Breadcrumb support
			$path = array(array('title' => $this->user->title, 'link' => ''));
                                                
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}                    
		}

		// Check for empty title and add site name if param is set
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
                
		if (empty($title))
		{
			$title = $this->item->title;
		}
		$this->document->setTitle($title);

                if ($this->user->params->get('meta_desc'))
		{
			$this->document->setDescription($this->user->params->get('meta_desc'));
		}
                elseif ($this->user->description)
                {                        
			$this->document->setDescription($this->escape(JHtmlString::truncate($this->user->description, 160, true, false)));   
                }                 
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }                

		if ($this->user->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->user->params->get('meta_keys'));
		}
                elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if ($this->user->params->get('meta_rights'))
		{
			$this->document->setMetadata('keywords', $this->user->params->get('meta_rights'));
		}
                elseif ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
                }             
                
		if ($this->user->params->get('meta_author') == 1)
		{
			$this->document->setMetadata('author', $this->user->title);
		}
                elseif ($this->params->get('meta_author') == 1)
                {
			$this->document->setMetadata('author', $this->user->title);
                }      
	}
        
	/**
	 * Prepares the category list
	 *
	 * @return  void
	 */
	public function getCategories($item)
	{            
                hwdMediaShareFactory::load('category');
                $cat = hwdMediaShareCategory::getInstance();
                $cat->elementType = 1;
                return $cat->getCategories($item);
	}
}
