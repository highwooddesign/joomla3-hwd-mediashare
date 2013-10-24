<?php
/**
 * @version    SVN $Id: search.php 960 2013-01-30 09:06:19Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Jan-2012 18:13:39
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerSearch extends JControllerLegacy {
	/**
	 * Method to search records
	 *
	 * @since	0.1
	 */
        function search()
	{
                // Slashes cause errors, <> get stripped anyway later on. # causes problems.
		$badchars = array('#','>','<','\\');
		// @TODO: Here we have removed the hash variable so that the searchword can be passed in the URL
                //$searchword = trim(str_replace($badchars, '', JRequest::getString('searchword', null, 'post')));
		$searchword = trim(str_replace($badchars, '', JRequest::getString('searchword', null)));
		
                // If searchword enclosed in double quotes, strip quotes and do exact match
		if (substr($searchword,0,1) == '"' && substr($searchword, -1) == '"') 
                {
			$post['searchword'] = substr($searchword,1,-1);
			JRequest::setVar('searchphrase', 'exact');
		}
		else 
                {
			$post['searchword'] = $searchword;
		}
		$post['ordering']	= JRequest::getWord('ordering', null, 'post');
		$post['searchphrase']	= JRequest::getWord('searchphrase', 'all', 'post');
		$post['limit']          = JRequest::getInt('limit', null, 'post');
		// @TODO: Here we have removed the hash variable to allow passing in the URL
		$post['ordering']	= JRequest::getWord('ordering', null);
		$post['searchphrase']	= JRequest::getWord('searchphrase', 'all');
		$post['limit']          = JRequest::getInt('limit', null);
                if ($post['limit'] === null) unset($post['limit']);
		$post['area']           = JRequest::getInt('area', null);

		// Set Itemid id for links from menu
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$items	= $menu->getItems('link', 'index.php?option=com_hwdmediashare&view=search');

		if (isset($items[0])) 
                {
			$post['Itemid'] = $items[0]->id;
		} 
                else if (JRequest::getInt('Itemid') > 0) 
                {      
                        // Use Itemid from requesting page only if there is no existing menu
			$post['Itemid'] = JRequest::getInt('Itemid');
		}

		unset($post['task']);
		unset($post['submit']);

                $data = JRequest::getVar('jform', array(), 'post', 'array');               
                $post['catid'] = (int) $data['catid'];

                // Validate custom fields
                $elementSet = array('media' => 1, 'albums' => 2, 'groups' => 3,'playlists' => 4, 'users' => 5);
                hwdMediaShareFactory::load('customfields');
                $customfields = hwdMediaShareCustomFields::get(null, $post['area']);
                
                foreach ($customfields['fields'] as $group => $groupFields)
                {
                        foreach ($groupFields as $field)
                        {
                                $field = JArrayHelper::toObject ( $field );                 
                                if ($field->searchable)
                                {
                                        $searchterm = trim(str_replace($badchars, '', JRequest::getString('field'.$field->id, null, 'post')));
		                        $post['field'.$field->id] = $searchterm;    
                                }
                        }
                }
                
                JRequest::getWord('tmpl') ? $post['tmpl'] = JRequest::getWord('tmpl') : null;
                $post['layout'] = JRequest::getWord('layout');

		$uri = JURI::getInstance();
		$uri->setQuery($post);
		$uri->setVar('option', 'com_hwdmediashare');
		$uri->setVar('view', 'search');
                                
		$this->setRedirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
	}
}
