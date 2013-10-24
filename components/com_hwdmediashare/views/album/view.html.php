<?php
/**
 * @version    SVN $Id: view.html.php 1213 2013-03-05 13:30:14Z dhorsfall $
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
class hwdMediaShareViewAlbum extends JViewLegacy {
	// Overwriting JView display method
	function display($tpl = null)
	{
                $app = & JFactory::getApplication();

                // Get the album data from the model
                $album = $this->get('Album');

                // Get the media data from the model
                $items = $this->get('Items');
                $pagination = $this->get('Pagination');
		$state	= $this->get('State');

                $album->nummedia = $this->get('NumMedia');
                
                // Download links
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
		hwdMediaShareFactory::load('utilities');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
                
                if (count($items) == 0)
                {
                        JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_NO_MEDIA_IN_ALBUM') );
                }
                
		$params = &$state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

                $this->assign('columns',	        $params->get('list_columns', 3));
                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));

                $this->assignRef('params',		$params);
		$this->assignRef('album',		$album);
                $this->assignRef('parent',		$parent);
		$this->assignRef('items',		$items);
                $this->assignRef('state',		$state);
                $this->assignRef('pagination',		$pagination);
                $this->assignRef('display',		JRequest::getWord( 'display', 'details' ));
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
                if ($this->state->params->get('list_thumbnail_aspect') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/aspect.css');
                if ($this->state->params->get('list_thumbnail_aspect') != 0) $this->document->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/aspect.js');

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
			$title = $this->album->title;
		}
		$this->document->setTitle($title);

                if ($this->album->params->get('meta_desc'))
		{
			$this->document->setDescription($this->album->params->get('meta_desc'));
		}
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }
                else
                {                        
			$this->document->setDescription($this->escape(JHtmlString::truncate($this->album->description, $this->params->get('list_desc_truncate'), true, false)));   
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
