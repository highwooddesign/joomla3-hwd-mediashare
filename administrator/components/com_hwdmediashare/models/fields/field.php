<?php
/**
 * @version    SVN $Id: field.php 1133 2013-02-21 11:00:22Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Feb-2012 16:16:04
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * hwdMediaShare Form Field class for the hwdMediaShare component
 */
class JFormFieldField extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Field';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{
                // Get name of field type
                $path = JPATH_ROOT . '/components/com_hwdmediashare/libraries/fields/customfields.xml';
                $parser =& JFactory::getXML($path);                                        
                $fields	= $parser->fields;
                
                // Initialise variables.
		$options   = array();
                $options[] = JHtml::_('select.option', 'group', JText::_('COM_HWDMS_GROUP'));

                foreach( $fields->children() as $field )
                {
                        $options[] = JHtml::_('select.option', $field->type, JText::_($field->name));
                }

                return $options;
	}
}