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

class JFormFieldActivity extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Activity';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
                // Initialise variables.
		$options = array();
                $options[] = JHtml::_('select.option', '1', JText::_('COM_HWDMS_OPTION_ACTIVITY_TYPE_1'));
                $options[] = JHtml::_('select.option', '2', JText::_('COM_HWDMS_OPTION_ACTIVITY_TYPE_2'));
                $options[] = JHtml::_('select.option', '3', JText::_('COM_HWDMS_OPTION_ACTIVITY_TYPE_3'));
                $options[] = JHtml::_('select.option', '4', JText::_('COM_HWDMS_OPTION_ACTIVITY_TYPE_4'));
                $options[] = JHtml::_('select.option', '5', JText::_('COM_HWDMS_OPTION_ACTIVITY_TYPE_5'));
                $options[] = JHtml::_('select.option', '6', JText::_('COM_HWDMS_OPTION_ACTIVITY_TYPE_6'));
                $options[] = JHtml::_('select.option', '7', JText::_('COM_HWDMS_OPTION_ACTIVITY_TYPE_7'));
                $options[] = JHtml::_('select.option', '8', JText::_('COM_HWDMS_OPTION_ACTIVITY_TYPE_8'));
                $options[] = JHtml::_('select.option', '9', JText::_('COM_HWDMS_OPTION_ACTIVITY_TYPE_9'));

                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
