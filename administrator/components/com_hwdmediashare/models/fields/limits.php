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

JFormHelper::loadFieldClass('rules');

class JFormFieldLimits extends JFormFieldRules
{
	/**
	 * The name of the form field type.
         * 
         * @access  public
	 * @var     string
	 */
	public $type = 'Limits';

	/**
	 * Method to get the field input markup for upload limits.
	 *
	 * @access  protected
	 * @return  string     The field input markup.
	 */
	protected function getInput()
	{
		JHtml::_('behavior.tooltip');

		// Define objects for custom limits.
                $space = new StdClass;
                $space->name = 'space'; 
                $space->title = JText::_('COM_HWDMS_LIMIT_SPACE_LABEL'); 
                $space->description = JText::_('COM_HWDMS_LIMIT_SPACE_DESC'); 
                
                $number = new StdClass;
                $number->name = 'number'; 
                $number->title = JText::_('COM_HWDMS_LIMIT_NUMBER_LABEL'); 
                $number->description = JText::_('COM_HWDMS_LIMIT_NUMBER_DESC'); 

                // Get the actions for the asset.
		$actions = array($space,$number);

		// Get the available user groups.
		$groups = $this->getUserGroups();

		// Decode the limit value.
                $this->value = is_array($this->value) ? $this->value : json_decode($this->value, true);
                
		// Build the form control.
		$curLevel = 0;

		// Prepare output
		$html = array();

		// Begin tabs
		$html[] = '<div id="limits-sliders" class="tabbable tabs-left">';

		// Building tab nav
		$html[] = '<ul class="nav nav-tabs">';

		foreach ($groups as $group)
		{
			// Initial active tab.
			$active = "";

			if ($group->value == 1)
			{
				$active = "active";
			}

			$html[] = '<li class="' . $active . '">';
			$html[] = '<a href="#limit-' . $group->value . '" data-toggle="tab">';
			$html[] = str_repeat('<span class="level">&ndash;</span> ', $curLevel = $group->level) . $group->text;
			$html[] = '</a>';
			$html[] = '</li>';
		}

		$html[] = '</ul>';

		$html[] = '<div class="tab-content">';

		// Start a row for each user group.
		foreach ($groups as $group)
		{
			// Initial active pane.
			$active = "";

			if ($group->value == 1)
			{
				$active = " active";
			}

			$html[] = '<div class="tab-pane' . $active . '" id="limit-' . $group->value . '">';
			$html[] = '<table class="table table-striped">';
			$html[] = '<thead>';
			$html[] = '<tr>';

			$html[] = '<th class="actions" id="actions-th' . $group->value . '">';
			$html[] = '<span class="acl-action">' . JText::_('COM_HWDMS_LIMIT_METHOD') . '</span>';
			$html[] = '</th>';

			$html[] = '<th class="settings" id="settings-th' . $group->value . '">';
			$html[] = '<span class="acl-action">' . JText::_('COM_HWDMS_LIMIT_VALUE') . '</span>';
			$html[] = '</th>';

			$html[] = '</tr>';
			$html[] = '</thead>';
			$html[] = '<tbody>';

			foreach ($actions as $action)
			{
                                // Define the value for this group, and this action.
                                $value = null;
                                if (isset($this->value[$action->name][$group->value])) { $value = intval($this->value[$action->name][$group->value]); }

				$html[] = '<tr>';
				$html[] =  '<td headers="actions-th' . $group->value . '">';
				$html[] =   '<label class="hasTip" for="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="'.htmlspecialchars(JText::_($action->title).'::'.JText::_($action->description), ENT_COMPAT, 'UTF-8').'">';
				$html[] =    JText::_($action->title);
				$html[] =   '</label>';
				$html[] =  '</td>';
				$html[] =  '<td headers="settings-th' . $group->value . '">';
				$html[] =   '<input name="' . $this->name . '[' . $action->name . '][' . $group->value . ']" id="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="' . JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', JText::_($action->title), trim($group->text)) . '" type="text" value="' . $value .'">';
				$html[] =  '</td>';
				$html[] = '</tr>';
                                
                                unset($value);
			}

			$html[] = '</tbody>';
			$html[] = '</table>';
			$html[] = '</div>';
		}

		$html[] = '</div></div>';

		return implode("\n", $html);
	}
}