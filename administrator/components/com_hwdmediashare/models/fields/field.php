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

JFormHelper::loadFieldClass('list');

class JFormFieldField extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Field';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
                // Get list of field types
                $path = JPATH_ROOT . '/components/com_hwdmediashare/libraries/fields/customfields.xml';
                $parser = JFactory::getXML($path);                                        
                $fields	= $parser->fields;
                
                // Initialise variables.
		$options = array();
                
                foreach($fields->children() as $field)
                {
                        $options[] = JHtml::_('select.option', $field->type, JText::_($field->name));
                }

                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
