<?php
/**
 * @version    SVN $Id: limits.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Jan-2012 14:03:17
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import the list field type
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('rules');

 /**
  * Upload limits field class
  */
class JFormFieldLimits extends JFormFieldRules
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  0.1
	 */
	public $type = 'Limits';

	/**
	 * Method to get the field input markup for upload limits
         *
	 * @return  string  The field input markup.
	 *
	 * @since	0.1
	 */
	protected function getInput()
	{
		JHtml::_('behavior.tooltip');

		// Define objects for custom limits
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

		// Build the form control.
		$curLevel = 0;

		// Prepare output
		$html = array();
                $html[] = '<div id="limits-sliders" class="pane-sliders">';
		//$html[] = '<p class="rule-desc">&#160;</p>';
		$html[] = '<ul id="rules">';

		// Start a row for each user group.
		foreach ($groups as $group)
		{
			$difLevel = $group->level - $curLevel;

			if ($difLevel > 0) {
				$html[] = '<li><ul>';
			}
			else if ($difLevel < 0) {
				$html[] = str_repeat('</ul></li>', -$difLevel);
			}

			$html[] = '<li>';

			$html[] = '<div class="panel">';
			$html[] =	'<h3 class="pane-toggler title"><a href="javascript:void(0);"><span>';
			$html[] =	str_repeat('<span class="level">|&ndash;</span> ', $curLevel = $group->level) . $group->text;
			$html[] =	'</span></a></h3>';
			$html[] =	'<div class="pane-slider content pane-hide">';
			$html[] =		'<div class="mypanel">';
			$html[] =			'<table class="group-rules">';
			$html[] =				'<thead>';
			$html[] =					'<tr>';

			$html[] =						'<th class="actions" id="actions-th' . $group->value . '">';
			$html[] =							'<span class="acl-action">' . JText::_('COM_HWDMS_LIMIT_METHOD') . '</span>';
			$html[] =						'</th>';

			$html[] =						'<th class="settings" id="settings-th' . $group->value . '">';
			$html[] =							'<span class="acl-action">' . JText::_('COM_HWDMS_LIMIT_VALUE') . '</span>';
			$html[] =						'</th>';

			$html[] =					'</tr>';
			$html[] =				'</thead>';
			$html[] =				'<tbody>';

			foreach ($actions as $action)
			{
				$html[] =				'<tr>';
				$html[] =					'<td headers="actions-th' . $group->value . '">';
				$html[] =						'<label class="hasTip" for="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="'.htmlspecialchars(JText::_($action->title).'::'.JText::_($action->description), ENT_COMPAT, 'UTF-8').'">';
				$html[] =						JText::_($action->title);
				$html[] =						'</label>';
				$html[] =					'</td>';

				$html[] =					'<td headers="settings-th' . $group->value . '">';

				$html[] = '<input name="' . $this->name . '[' . $action->name . '][' . $group->value . ']" id="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="' . JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', JText::_($action->title), trim($group->text)) . '" type="text">';

				$html[] = '&#160; ';

				$html[] = '</td>';

				$html[] = '</tr>';
			}

			$html[] = '</tbody>';
			$html[] = '</table></div>';

			$html[] = '</div></div>';
			$html[] = '</li>';

		}

		$html[] = str_repeat('</ul></li>', $curLevel);
		$html[] = '</ul></div>';

		$js = "window.addEvent('domready', function(){ new Fx.Accordion($$('div#limits-sliders.pane-sliders .panel h3.pane-toggler'), $$('div#limits-sliders.pane-sliders .panel div.pane-slider'), {onActive: function(toggler, i) {toggler.addClass('pane-toggler-down');toggler.removeClass('pane-toggler');i.addClass('pane-down');i.removeClass('pane-hide');Cookie.write('jpanesliders_limits-sliders',$$('div#limits-sliders.pane-sliders .panel h3').indexOf(toggler));},onBackground: function(toggler, i) {toggler.addClass('pane-toggler');toggler.removeClass('pane-toggler-down');i.addClass('pane-hide');i.removeClass('pane-down');},duration: 300,display: ".JRequest::getInt('jpanesliders_limits-sliders', 0, 'cookie').",show: ".JRequest::getInt('jpanesliders_limits-sliders', 0, 'cookie').", alwaysHide:true, opacity: false}); });";

		JFactory::getDocument()->addScriptDeclaration($js);

		return implode("\n", $html);
	}
}