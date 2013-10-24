<?php
/**
 * @version    SVN $Id: editorhwd.php 858 2013-01-07 11:41:09Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Dec-2011 09:54:09
 */

// No direct access
 defined('_JEXEC') or die('Restricted access');

// Import Joomla formfield library
jimport('joomla.form.formfield');
jimport('joomla.html.editor');

JFormHelper::loadFieldClass('editor');

/**
 * Form Field class for the Joomla Platform.
 * An editarea field for content creation
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @see         JFormfieldEditors
 * @see         JEditor
 * @since       11.1
 */
class JFormFieldEditorHwd extends JFormFieldEditor
{
	/**
	 * Method to get a JEditor object based on the form field.
	 *
	 * @return  JEditor  The JEditor object.
	 *
	 * @since   11.1
	 */
	protected function &getEditor()
	{
		// Only create the HWD editor if it is not already created.
		if (empty($this->editor))
		{
                        // Load config
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig();

                        // Initialize variables.
			$editor = null;

			// Get the editor type attribute. Can be in the form of: editor="desired|alternative".
			$type = trim((string) $config->get('editor'));

			if ($type)
			{
				// Get the list of editor types.
				$types = explode('|', $type);

				// Get the database object.
				$db = JFactory::getDBO();

				// Iterate over teh types looking for an existing editor.
				foreach ($types as $element)
				{
					// Build the query.
					$query = $db->getQuery(true);
					$query->select('element');
					$query->from('#__extensions');
					$query->where('element = ' . $db->quote($element));
					$query->where('folder = ' . $db->quote('editors'));
					$query->where('enabled = 1');

					// Check of the editor exists.
					$db->setQuery($query, 0, 1);
					$editor = $db->loadResult();

					// If an editor was found stop looking.
					if ($editor)
					{
                                                // Create the JEditor instance based on the given editor.
                                                $this->editor = JFactory::getEditor($editor ? $editor : null);
						break;
					}
				}
			}
		}
                
		// Only create the editor if it is not already created.
		if (empty($this->editor))
		{
			// Initialize variables.
			$editor = null;

			// Get the editor type attribute. Can be in the form of: editor="desired|alternative".
			$type = trim((string) $this->element['editor']);

			if ($type)
			{
				// Get the list of editor types.
				$types = explode('|', $type);

				// Get the database object.
				$db = JFactory::getDBO();

				// Iterate over teh types looking for an existing editor.
				foreach ($types as $element)
				{
					// Build the query.
					$query = $db->getQuery(true);
					$query->select('element');
					$query->from('#__extensions');
					$query->where('element = ' . $db->quote($element));
					$query->where('folder = ' . $db->quote('editors'));
					$query->where('enabled = 1');

					// Check of the editor exists.
					$db->setQuery($query, 0, 1);
					$editor = $db->loadResult();

					// If an editor was found stop looking.
					if ($editor)
					{
						break;
					}
				}
			}

			// Create the JEditor instance based on the given editor.
			$this->editor = JFactory::getEditor($editor ? $editor : null);
		}

		return $this->editor;
	}
}
