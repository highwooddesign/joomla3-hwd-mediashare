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

class hwdMediaShareViewCategory extends JViewLegacy
{
    	public $category;

        public $subcategories;

	public $items;

	public $feature;
        
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
                // Get data from the model.
                $this->category = $this->get('Category');
                $this->subcategories = $this->get('Subcategories');
                $this->items = $this->get('Items');
                $this->pagination = $this->get('Pagination');
                $this->feature = $this->get('Feature');
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
                
                $this->category->nummedia = $this->get('numMedia');
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
                JHtml::_('hwdhead.core', $this->params);

		// Define the page title and headings. 
		$menu = $menus->getActive();
		if ($menu && $menu->query['option'] == 'com_hwdmediashare' && $menu->query['view'] == 'category' && (int) @$menu->query['id'] == $this->category->id)
		{
                        $title = $this->params->get('page_title');
                        $heading = $this->params->get('page_heading') ? $this->params->get('page_heading') : ($this->category->title ? $this->category->title : JText::_('COM_HWDMS_CATEGORY'));
		}
		else
		{
			if ($this->category->title) 
                        {
				$title = $this->category->title;
                                $heading = $this->category->title;   
			} 
                        else
                        {
                                $title = JText::_('COM_HWDMS_CATEGORY');
                                $heading = JText::_('COM_HWDMS_CATEGORY');
                        }
		}
                
		// If the menu item does not concern this view then add a breadcrumb.
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'category' || (int) @$menu->query['id'] != $this->category->id))
		{
                        // Breadcrumb support.
			$path = array(array('title' => $this->category->title, 'link' => ''));
                                                
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

                if ($this->category->metadesc)
                {
			$this->document->setDescription($this->category->metadesc);
                }
                elseif ($this->category->description)
                {                        
			$this->document->setDescription($this->escape(JHtml::_('string.truncate', $this->category->description, 160, true, false)));   
                }                 
                elseif ($menu && $menu->query['option'] == 'com_hwdmediashare' && $menu->query['view'] == 'category' && (int) @$menu->query['id'] == $this->category->id && $this->params->get('menu-meta_description'))
                {
			$this->document->setDescription($this->params->get('menu-meta_description'));
                } 
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }                

		if ($this->category->metakey)
                {
			$this->document->setMetadata('keywords', $this->category->metakey);
                }
                elseif ($menu && $menu->query['option'] == 'com_hwdmediashare' && $menu->query['view'] == 'category' && (int) @$menu->query['id'] == $this->category->id && $this->params->get('menu-meta_keywords'))
                {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
                } 
		elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }     
	}
}
