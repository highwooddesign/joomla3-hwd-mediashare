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

class JFormFieldPlaylist extends JFormField
{
	/**
	 * The name of the form field type.
         * 
         * @access  protected
	 * @var     string
	 */
 	protected $type = 'Playlist';

	/**
	 * The name of the form field.
         * 
         * @access  protected
	 * @var     string
	 */
 	protected $name = 'playlist';

	/**
	 * The id of the form field.
         * 
         * @access  protected
	 * @var     string
	 */
 	protected $id = 'playlist';

	/**
	 * Method to get the field input markup.
	 *
	 * @access  public
	 * @return  string  The field input markup.
	 */
        public function getInput()
        {
		$allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

		// Load language.
		JFactory::getLanguage()->load('com_hwdmediashare', JPATH_ADMINISTRATOR);

		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();

		// Select button script.
		$script[] = '	function jSelectPlaylist_'.$this->id.'(id, title, catid, object) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '		document.getElementById("'.$this->id.'_name").innerHTML = title;';

		if ($allowClear)
		{
			$script[] = '		jQuery("#'.$this->id.'_clear").removeClass("hidden");';
		}

		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Clear button script.
		static $scriptClear;

		if ($allowClear && !$scriptClear)
		{
			$scriptClear = true;

			$script[] = '	function jClearPlaylist(id) {';
			$script[] = '		document.getElementById(id + "_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").innerHTML = "'.htmlspecialchars(JText::_('COM_HWDMS_SELECT_PLAYLIST', true), ENT_COMPAT, 'UTF-8').'";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
			$script[] = '		return false;';
			$script[] = '	}';
		}

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html	= array();
                $link   = 'index.php?option=com_hwdmediashare&amp;view=playlists&amp;layout=modal&amp;tmpl=component&amp;function=jSelectPlaylist_'.$this->id;

		if (isset($this->element['language']))
		{
			$link .= '&amp;forcedLanguage='.$this->element['language'];
		}

		$db	= JFactory::getDbo();
		$db->setQuery(
                  'SELECT title' .
                  ' FROM #__hwdms_playlists' .
                  ' WHERE id = '.(int) $this->value
		);

		try
		{
			$title = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		if (empty($title))
		{
			$title = JText::_('COM_HWDMS_SELECT_PLAYLIST');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The active id field.
		if (0 == (int) $this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int) $this->value;
		}

		// The current display field.
		$html[] = '<span class="input-append">';
		$html[] = '<span class="input-medium uneditable-input" id="'.$this->id.'_name">'.$title.'</span>';
		$html[] = '<a class="modal btn hasTooltip" title="'.JHtml::tooltipText('COM_HWDMS_CHANGE_PLAYLIST').'"  href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JSELECT').'</a>';

		// Clear button.
		if ($allowClear)
		{
			$html[] = '<button id="'.$this->id.'_clear" class="btn'.($value ? '' : ' hidden').'" onclick="return jClearPlaylist(\''.$this->id.'\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</button>';
		}

		$html[] = '</span>';

		// class='required' for client side validation
		$class = '';
		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
        }
}
