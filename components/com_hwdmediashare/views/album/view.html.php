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

class hwdMediaShareViewAlbum extends JViewLegacy
{
	protected $album;

	protected $items;
        
	protected $state;
        
	protected $params;
        
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
                // Get data from the model.
                $this->album = $this->get('Album');
                $this->items = $this->get('Items');
                $this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->params = $this->state->params;

                // Load libraries.
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlHwdDropdown', JPATH_COMPONENT . '/helpers/dropdown.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->columns = $this->params->get('list_columns', 3);
                $this->return = base64_encode(JFactory::getURI()->toString());
                $this->display = $state->get('media.display', 'details');

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
			$this->params->def('page_heading', JText::_('COM_HWDMS_ALBUM'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];
                
		// If the menu item does not concern this item
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'album' || $id != $this->album->id))
		{
			// If this is not a single item menu item, set the page title to the item title
			if ($this->album->title) 
                        {
				$title = $this->album->title;
			}      
                        
                        // Breadcrumb support
			$path = array(array('title' => $this->album->title, 'link' => ''));
                                                
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

                if ($this->album->params->get('meta_desc'))
		{
			$this->document->setDescription($this->album->params->get('meta_desc'));
		}
                elseif ($this->album->description)
                {                        
			$this->document->setDescription($this->escape(JHtmlString::truncate($this->album->description, 160, true, false)));   
                }                 
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }                

		if ($this->album->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->album->params->get('meta_keys'));
		}
                elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if ($this->album->params->get('meta_rights'))
		{
			$this->document->setMetadata('keywords', $this->album->params->get('meta_rights'));
		}
                elseif ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
                }             
                
		if ($this->album->params->get('meta_author') == 1)
		{
			$this->document->setMetadata('author', $this->album->author);
		}
                elseif ($this->params->get('meta_author') == 1)
                {
			$this->document->setMetadata('author', $this->album->author);
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
