<?php
/**
 * @version    SVN $Id: icon.php 1025 2013-02-01 10:39:08Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      23-Nov-2011 09:24:14
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare JHtmlIcon Helper
 *
 * @package	hwdMediaShare
 * @since       0.1
 */
class JHtmlHwdIcon
{
	/**
	 * Display an edit icon.
	 *
	 * This icon will not display in a popup window, nor if the item is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$article	The article in question.
	 * @param	object	$params		The article parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the item edit icon.
	 * @since	0.1
	 */
	static function edit($element, $item, $params, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri	= JFactory::getURI();
                
		// Ignore if in a popup window.
		if ($params && $params->get('popup')) {
			return;
		}

		// Ignore if the published is negative (trashed).
		if ($item->published < 0) {
			return;
		}

		$url	= 'index.php?option=com_hwdmediashare&task='.$element.'form.edit&id='.$item->id.'&return='.base64_encode($uri);
		$text	= JText::_('JGLOBAL_EDIT');

		$button = JHtml::_('link', JRoute::_($url), $text, 'class="pagenav-edit"');

		return $button;
	}
	/**
	 * Display a delete icon.
	 *
	 * This icon will not display in a popup window, nor if the article is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$article	The article in question.
	 * @param	object	$params		The article parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the item delete icon.
	 * @since	0.1
	 */
	static function delete($element, $item, $params, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri	= JFactory::getURI();

		// If we are about to redirect the user to the page that was just deleted, we set the redirect to the account page instead 
                if ($element == 'media' && in_array(JRequest::getCmd('view'), array('mediaitem')))
                {
			$uri = hwdMediaShareHelperRoute::getMyMediaRoute();
		}
                else if ($element == 'category' && in_array(JRequest::getCmd('view'), array('category')))
                {
                        $uri = hwdMediaShareHelperRoute::getCategoriesRoute();
                }                
                else if (in_array(JRequest::getCmd('view'), array('album','group','user','playlist')))
                {
			$uri = hwdMediaShareHelperRoute::getMyMediaRoute();
                }
                
                switch ($element) {
                    default:
                        $controller = $element.'s';
                        break;
                    case 'media':
                        $controller = 'media';
                        break;
                    case 'category':
                        $controller = 'categories';
                        break;
                    case 'activity':
                        $controller = 'activities';
                        break;                    
                }

		// Ignore if in a popup window.
		if ($params && $params->get('popup')) {
			return;
		}

		// Ignore if the published is negative (trashed).
		if ($item->published < 0) {
			return;
		}

		$url	= 'index.php?option=com_hwdmediashare&task='.$controller.'.delete&id='.$item->id.'&return='.base64_encode($uri);
		$text	= JText::_('COM_HWDMS_DELETE');

		$button = JHtml::_('link', JRoute::_($url), $text, 'class="pagenav-delete"');
		
                return $button;
	}
	/**
	 * Display a publish icon.
	 *
	 * This icon will not display in a popup window, nor if the article is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$article	The article in question.
	 * @param	object	$params		The article parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the publish icon.
	 * @since	0.1
	 */
	static function publish($element, $item, $params, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri	= JFactory::getURI();

                switch ($element) {
                    default:
                        $controller = $element.'s';
                        break;
                    case 'media':
                        $controller = 'media';
                        break;
                    case 'category':
                        $controller = 'categories';
                        break;
                    case 'activity':
                        $controller = 'activities';
                        break;                    
                }

		// Ignore if in a popup window.
		if ($params && $params->get('popup')) {
			return;
		}

		// Ignore if the published is negative (trashed).
		if ($item->published < 0) {
			return;
		}

		$url	= 'index.php?option=com_hwdmediashare&task='.$controller.'.publish&id='.$item->id.'&return='.base64_encode($uri);
		$text	= JText::_('COM_HWDMS_PUBLISH');

		$button = JHtml::_('link', JRoute::_($url), $text, 'class="pagenav-publish"');

		return $button;
	}
	/**
	 * Display an unpublish icon.
	 *
	 * This icon will not display in a popup window, nor if the article is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$article	The article in question.
	 * @param	object	$params		The article parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the unpublish icon.
	 * @since	0.1
	 */
	static function unpublish($element, $item, $params, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri	= JFactory::getURI();

                switch ($element) {
                    default:
                        $controller = $element.'s';
                        break;
                    case 'media':
                        $controller = 'media';
                        break;
                    case 'category':
                        $controller = 'categories';
                        break;
                    case 'activity':
                        $controller = 'activities';
                        break;                    
                }

		// Ignore if in a popup window.
		if ($params && $params->get('popup')) {
			return;
		}

		// Ignore if the published is negative (trashed).
		if ($item->published < 0) {
			return;
		}

                $url	= 'index.php?option=com_hwdmediashare&task='.$controller.'.unpublish&id='.$item->id.'&return='.base64_encode($uri);
		$text	= JText::_('COM_HWDMS_UNPUBLISH');

		$button = JHtml::_('link', JRoute::_($url), $text, 'class="pagenav-unpublish"');

		return $button;
	}
	/**
	 * Display an overlay icon.
	 *
	 * @param	object	$article	The article in question.
	 * @param	object	$params		The article parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The URL for the overlay icon.
	 * @since	0.1
	 */
	static function overlay($type, $item=null)
	{
                if (isset($item->ext))
                {
                        switch ($item->ext)
                        {
                              case "zip":
                                    return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/16/zip.png';
                                    break;
                        }
                }

                if (!isset($type)) return;

                switch ($type) {
                        case "1":
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/media.png';
                                break;
                        case "1-1":
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/audio.png';
                                break;
                        case "1-2":
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/document.png';
                                break;
                        case "1-3":
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/image.png';
                                break;
                        case "1-4":
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/video.png';
                                break;
                        case 2:
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/album.png';
                                break;
                        case 3:
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/group.png';
                                break;
                        case 4:
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/playlist.png';
                                break;
                        case 5:
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/user.png';
                                break;
                        case 6:
                                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/category.png';
                                break;
                }
                
                // Can't find any appropriate icon so this is remote media
                return JURI::root( true ).'/media/com_hwdmediashare/assets/images/icons/24/globe.png';
	}
}