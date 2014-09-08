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

class JHtmlHwdDropdown
{
	/**
	 * Array of elements containing HTML markup for the dropdown list.
         * 
         * @access      protected
         * @static
	 * @var         array
	 */
	protected static $dropDownList = array();

	/**
	 * Method to render current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $item   An item to render.
	 * @param   string  $class  The button class. 
	 * @return  string  HTML markup for the dropdown list.
	 */
	public static function render($item = '', $class = '')
	{
		$html = array();
    
		$html[] = '<button data-toggle="dropdown" class="dropdown-toggle btn' . $class . '">';
		$html[] = '<span class="caret"></span>';

		if ($item)
		{
			$html[] = '<span class="element-invisible">' . JText::sprintf('JACTIONS', $item) . '</span>';
		}

		$html[] = '</button>';
		$html[] = '<ul class="dropdown-menu">';
		$html[] = implode('', static::$dropDownList);
		$html[] = '</ul>';

		static::$dropDownList = null;

		return implode('', $html);
	}

	/**
	 * Method to render current dropdown menu for video quality.
	 *
         * @access  public
         * @static
	 * @param   string  $item   An item to render.
	 * @param   string  $class  The button class. 
	 * @return  string  HTML markup for the dropdown list.
	 */
	public static function renderQualities($item = '', $class = '')
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                
		$html = array();
    
		$html[] = '<button data-toggle="dropdown" class="dropdown-toggle btn' . $class . '">';
		$html[] = '<span class="caret"></span> ';

		if ($item)
		{
			$html[] = '<span>' . JText::sprintf('COM_HWDMS_QUALITY_WITH_DEFAULT', $app->getUserState('media.quality', '360')) . '</span>';
		}

		$html[] = '</button>';
		$html[] = '<ul class="dropdown-menu pull-right">';
		$html[] = implode('', static::$dropDownList);
		$html[] = '</ul>';

		static::$dropDownList = null;

		return implode('', $html);
	}
        
	/**
	 * Append a publish item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function publish($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'publish';
		static::addCustomItem(JText::_('COM_HWDMS_PUBLISH'), 'publish', $id, $task);
	}

	/**
	 * Append an unpublish item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function unpublish($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'unpublish';
		static::addCustomItem(JText::_('COM_HWDMS_UNPUBLISH'), 'unpublish', $id, $task);
	}

	/**
	 * Append a feature item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function feature($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'featured';
		static::addCustomItem(JText::_('JFEATURE'), 'featured', $id, $task);
	}

	/**
	 * Append an unfeature item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function unfeature($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'unfeatured';
		static::addCustomItem(JText::_('JUNFEATURE'), 'unfeatured', $id, $task);
	}

	/**
	 * Append an archive item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function archive($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'archive';
		static::addCustomItem(JText::_('JTOOLBAR_ARCHIVE'), 'archive', $id, $task);
	}

	/**
	 * Append an unarchive item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function unarchive($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'unpublish';
		static::addCustomItem(JText::_('JTOOLBAR_UNARCHIVE'), 'unarchive', $id, $task);
	}

	/**
	 * Append a trash item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function trash($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'trash';
		static::addCustomItem(JText::_('JTOOLBAR_TRASH'), 'trash', $id, $task);
	}

	/**
	 * Append an untrash item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function untrash($id, $prefix = '')
	{
		self::publish($id, $prefix);
	}

	/**
	 * Append an edit item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function edit($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'edit';
		static::addCustomItem(JText::_('JGLOBAL_EDIT'), 'edit', $id, $task);
	}

	/**
	 * Append an add item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of element type.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function add($id, $prefix = '')
	{
                switch ($id)
                {
                        case 1: // Media
                                $text = JText::_('COM_HWDMS_ADD_MEDIA');
                        break;
                        case 2: // Album
                                $text = JText::_('COM_HWDMS_ADD_ALBUM');
                        break;
                        case 3: // Group
                                $text = JText::_('COM_HWDMS_ADD_GROUP');
                        break;
                        case 4: // Playlist
                                $text = JText::_('COM_HWDMS_ADD_PLAYLIST');
                        break;
                        case 5: // Channel
                        break;
                }            
		$task = ($prefix ? $prefix . '.' : '') . 'edit';
		static::addCustomItem($text, 'plus', 0, $task);
	}
        
	/**
	 * Append a delete item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function delete($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'delete';
		static::addCustomItem(JText::_('COM_HWDMS_DELETE'), 'delete', $id, $task);
	}

	/**
	 * Append a metadata item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function meta($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'meta';
		static::addCustomModalItem(JText::_('COM_HWDMS_VIEW_META_DATA'), 'info', $id, $task, 'media-popup-iframe-form');
	}

	/**
	 * Append a download item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $id      ID of corresponding checkbox of the record.
	 * @param   string  $prefix  The task prefix.
	 * @return  void
	 */
	public static function downloads($id, $prefix = '')
	{
		$task = ($prefix ? $prefix . '.' : '') . 'download';
		static::addCustomModalItem(JText::_('COM_HWDMS_VIEW_ALL_SIZES'), 'download', $id, $task, 'media-popup-iframe-form');
	}

        /**
	 * Append a quality item to the current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   object  $item       The media item.
	 * @param   string  $quality    The quality value.
	 * @return  void
	 */
	public static function quality($item, $quality)
	{
		static::addCustomQualityItem($item, $quality);
	}
        
	/**
	 * Writes a divider between dropdown items.
	 *
         * @access  public
         * @static
	 * @return  void
	 */
	public static function divider()
	{
		static::$dropDownList[] = '<li class="divider"></li>';
	}

	/**
	 * Append a custom item to current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $label  The label of the item.
	 * @param   string  $icon   The icon classname.
	 * @param   string  $id     The item id.
	 * @param   string  $task   The task.
	 * @return  void
	 */
	public static function addCustomItem($label, $icon = '', $id = '', $task = '')
	{
		$uri	= JFactory::getURI();
		$url	= 'index.php?option=com_hwdmediashare&task=' . $task . '&id=' . $id . '&return=' . base64_encode($uri) . '&' . JSession::getFormToken() . '=1';

		static::$dropDownList[] = '<li>'
			. '<a href="' . JRoute::_($url) . '">'
			. ($icon ? '<span class="icon-' . $icon . '"></span> ' : '')
			. $label
			. '</a>'
			. '</li>';
	}
        
	/**
	 * Append a custom modal item to current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   string  $label  The label of the item.
	 * @param   string  $icon   The icon classname.
	 * @param   string  $id     The item id.
	 * @param   string  $task   The task.
	 * @return  void
	 */
	public static function addCustomModalItem($label, $icon = '', $id = '', $task = '')
	{
		$uri	= JFactory::getURI();
		$url	= 'index.php?option=com_hwdmediashare&task=' . $task . '&id=' . $id . '&return=' . base64_encode($uri) . '&' . JSession::getFormToken() . '=1&tmpl=component';

                // Include the component HTML helpers.
                JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
                JHtml::_('HwdPopup.iframe', 'form');
                
		static::$dropDownList[] = '<li>'
			. '<a href="' . JRoute::_($url) . '" class="media-popup-iframe-form">'
			. ($icon ? '<span class="icon-' . $icon . '"></span> ' : '')
			. $label
			. '</a>'
			. '</li>';
	} 
        
	/**
	 * Append a custom quality item to current dropdown menu.
	 *
         * @access  public
         * @static
	 * @param   object  $item       The media item.
	 * @param   string  $quality    The quality value.
	 * @return  void
	 */
	public static function addCustomQualityItem($item = '', $quality = '')
	{
                // Initialise variables.
                $app = JFactory::getApplication();
                
		$url = hwdMediaShareHelperRoute::getMediaItemRoute($item->id, array('quality' => $quality));
                $class = '';
                $default = $app->getUserState('media.quality', '360');
                
		static::$dropDownList[] = '<li>'
			. '<a href="' . JRoute::_($url) . '" class="' . $class . '">'
			. ($default == $quality ? '<span class="icon-ok"></span> ' : '<span class="icon-dummy"></span> ')
			. JText::_('COM_HWDMS_'.$quality.'P')
			. '</a>'
			. '</li>';
	}         
}
