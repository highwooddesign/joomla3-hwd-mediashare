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

class hwdMediaShareViewAccount extends JViewLegacy 
{
	public $user;

	public $items;        

	public $albums;

	public $favourites;

	public $groups;

	public $memberships;
        
	public $media;

	public $playlists;

	public $subscriptions;

	public $activities;
        
	public $state;
        
	public $params;
        
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
                $user = JFactory::getUser();   

                // Get the layout from the request.                
                $this->layout = $app->input->get('layout', 'media', 'word');

                // Account access check. 
                if (!$user->id)
                {
                        $return = base64_encode(JFactory::getURI()->toString());
                        $app->enqueueMessage(JText::_('COM_HWDMS_PLEASE_LOGIN_TO_VIEW_YOUR_ACCOUNT'));
                        $app->redirect('index.php?option=com_users&view=login&return='.$return);
                }
                
                // Get data from the model.
                $this->user = $this->get('Channel');
                $this->albums = $this->get('Albums');
                $this->favourites = $this->get('Favourites');
                $this->groups = $this->get('Groups');
                $this->media = $this->get('Media');
                $this->memberships = $this->get('Memberships');
                $this->playlists = $this->get('Playlists');
                $this->subscriptions = $this->get('Subscriptions');
                $this->activities = $this->get('Activities');

                switch ($this->layout)
                {
                        case 'albums':
                                $this->items = $this->get('Albums');
                                $this->pagination = $this->get('Pagination');
                                $this->page_heading = JText::_('COM_HWDMS_MY_ALBUMS');
                        break;
                        case 'favourites':
                                $this->items = $this->get('Favourites');
                                $this->pagination = $this->get('Pagination');
                                $this->page_heading = JText::_('COM_HWDMS_MY_FAVOURITES');
                        break;
                        case 'groups':
                                $this->items = $this->get('Groups');
                                $this->pagination = $this->get('Pagination');
                                $this->page_heading = JText::_('COM_HWDMS_MY_GROUPS');
                        break;
                        case 'memberships':
                                $this->items = $this->get('Memberships'); 
                                $this->pagination = $this->get('Pagination');
                                $this->page_heading = JText::_('COM_HWDMS_MY_MEMBERSHIPS');
                        break;
                        case 'playlists':
                                $this->items = $this->get('Playlists');
                                $this->pagination = $this->get('Pagination');
                                $this->page_heading = JText::_('COM_HWDMS_MY_PLAYLISTS');
                        break;
                        case 'subscriptions':
                                $this->items = $this->get('Subscriptions'); 
                                $this->pagination = $this->get('Pagination');
                                $this->page_heading = JText::_('COM_HWDMS_MY_SUBSCRIPTIONS');
                        break;
                        default:
                                $this->items = $this->get('Media');
                                $this->pagination = $this->get('Pagination');
                                $this->page_heading = JText::_('COM_HWDMS_MY_MEDIA');
                        break;
                }

                // Update the filterFormName state from the layout data.
                $this->get('FilterFormName');
                
		$this->state = $this->get('State');
		$this->params = $this->state->params;
                $this->filterForm = $this->get('FilterForm');

                // Include JHtml helpers.
                JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
                JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');

                $this->user->numalbums = $this->get('numAlbums');
                $this->user->numfavourites = $this->get('numFavourites');
                $this->user->numgroups = $this->get('numGroups');
                $this->user->nummedia = $this->get('numMedia');
                $this->user->nummemberships = $this->get('numMemberships');
                $this->user->numplaylists = $this->get('numPlaylists');
                $this->user->numsubscriptions = $this->get('numSubscriptions');
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->columns = $this->params->get('list_columns', 3);
                $this->return = base64_encode(JFactory::getURI()->toString());
                $this->display = $this->state->get('media.display', 'details');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

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
                JHtml::_('hwdhead.core', $this->params);

		// Define the page title and headings. 
		$menu = $menus->getActive();
		if ($menu)
		{
                        $title = $this->params->get('page_title');
                        $heading = $this->page_heading;
		}
		else
		{
                        $title = JText::_('COM_HWDMS_MY_ACCOUNT');
                        $heading = $this->page_heading;
		}

                $this->params->set('page_title', $title);
                $this->params->set('page_heading', $heading);
                
		// If the menu item does not concern this view then add a breadcrumb.
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'account'))
		{       
                        // Breadcrumb support.
			$path = array(array('title' => JText::_('COM_HWDMS_MY_ACCOUNT'), 'link' => ''));
                                               
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}                    
		}
                
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

                if ($menu && $menu->query['option'] == 'com_hwdmediashare' && $menu->query['view'] == 'account' && $this->params->get('menu-meta_description'))
                {
			$this->document->setDescription($this->params->get('menu-meta_description'));
                } 
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }              

		if ($menu && $menu->query['option'] == 'com_hwdmediashare' && $menu->query['view'] == 'account' && $this->params->get('menu-meta_keywords'))
                {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
                } 
		elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
                }   
	}
}
