<?php
/**
 * @version    SVN $Id: view.html.php 1179 2013-02-25 15:20:53Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Nov-2011 19:45:14
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewGroup extends JViewLegacy {
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
                            case 'members':
                                $members = $this->get('Members');
                                break;
                            default:
                                $media = $this->get('Media');
                       }
                        $pagination = $this->get('Pagination');
                }
                else
                {
                        $members = $this->get('Members');
                        $media = $this->get('Media');
                }

                // Get the group data from the model
                $group = $this->get('Group');
		$state	= $this->get('State');
       
                // Download links
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('utilities');
                hwdMediaShareFactory::load('activities');
                JLoader::register('JHtmlHwdIcon', JPATH_COMPONENT . '/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
                
		$params = &$state->params;

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

                if (empty($layout))
                {
                        $this->assign('columns',        2);
                }
                else
                {
                        $this->assign('columns',        $params->get('list_columns', 3));
                }

                $this->assign('display',		$state->get('media.display'));                
                $this->assign('return',                 base64_encode(JFactory::getURI()->toString()));

                $this->assignRef('group',		$group);

                $this->assignRef('members',		$members);
		$this->assignRef('media',		$media);

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

                $this->document->addScript('http://maps.googleapis.com/maps/api/js?key='.$this->params->get('google_maps_api_v3_key').'&sensor=false');

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_HWDMS_GROUP'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// If the menu item does not concern this item
		if ($menu && ($menu->query['option'] != 'com_hwdmediashare' || $menu->query['view'] != 'group' || $id != $this->group->id))
		{
			// If this is not a single item menu item, set the page title to the item title
			if ($this->group->title) 
                        {
				$title = $this->group->title;
			}      
                        
                        // Breadcrumb support
			$path = array(array('title' => $this->group->title, 'link' => ''));
                                                
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
			$title = $this->group->title;
		}
		$this->document->setTitle($title);

                if ($this->group->params->get('meta_desc'))
		{
			$this->document->setDescription($this->group->params->get('meta_desc'));
		}
                elseif ($this->params->get('meta_desc'))
                {
			$this->document->setDescription($this->params->get('meta_desc'));
                }
                else
                {                        
			$this->document->setDescription($this->escape(JHtmlString::truncate($this->group->description, $this->params->get('list_desc_truncate'), true, false)));   
                }                

		if ($this->group->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->group->params->get('meta_keys'));
		}
                elseif ($this->params->get('meta_keys'))
                {
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
                }

		if ($this->group->params->get('meta_rights'))
		{
			$this->document->setMetadata('keywords', $this->group->params->get('meta_rights'));
		}
                elseif ($this->params->get('meta_rights'))
                {
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
                }             
                
		if ($this->group->params->get('meta_author') == 1)
		{
			$this->document->setMetadata('author', $this->group->author);
		}
                elseif ($this->params->get('meta_author') == 1)
                {
			$this->document->setMetadata('author', $this->group->author);
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
}
