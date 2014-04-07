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

abstract class JHtmlHwdBatch
{
	/**
	 * Display a batch widget for the searchable field.
	 *
	 * @return  string  The necessary HTML for the widget.
	 */
	public static function searchable()
	{
		JHtml::_('bootstrap.tooltip');

		$options = array(
			JHtml::_('select.option', '', JText::_('COM_HWDMS_OPTION_KEEP_ORIGINAL_VALUE')),
			JHtml::_('select.option', '1', JText::_('COM_HWDMS_OPTION_SEARCHABLE')),
			JHtml::_('select.option', '0', JText::_('COM_HWDMS_OPTION_UNSEARCHABLE'))
		);
                
		$lines = array();
                $lines[] = '<label id="batch-searchable-lbl" for="batch-searchable-id" class="hasToolip" title="' . JHtml::tooltipText('COM_HWDMS_BATCH_SEARCHABLE_LABEL', 'COM_HWDMS_BATCH_SEARCHABLE_DESC') . '">';
                $lines[] = JText::_('COM_HWDMS_BATCH_SEARCHABLE_LABEL');
                $lines[] = '</label>';
                $lines[] = '<select name="batch[searchable]" class="inputbox" id="batch-searchable-id">';
                $lines[] = JHtml::_('select.options', $options, 'value', 'text');
                $lines[] = '</select>';                

		return implode("\n", $lines);
	}
        
	/**
	 * Display a batch widget for the visible field.
	 *
	 * @return  string  The necessary HTML for the widget.
	 */
	public static function visible()
	{
		JHtml::_('bootstrap.tooltip');

		$options = array(
			JHtml::_('select.option', '', JText::_('COM_HWDMS_OPTION_KEEP_ORIGINAL_VALUE')),
			JHtml::_('select.option', '1', JText::_('COM_HWDMS_OPTION_VISIBLE')),
			JHtml::_('select.option', '0', JText::_('COM_HWDMS_OPTION_UNVISIBLE'))
		);
                
		$lines = array();
                $lines[] = '<label id="batch-visible-lbl" for="batch-visible-id" class="hasToolip" title="' . JHtml::tooltipText('COM_HWDMS_BATCH_VISIBLE_LABEL', 'COM_HWDMS_BATCH_VISIBLE_DESC') . '">';
                $lines[] = JText::_('COM_HWDMS_BATCH_VISIBLE_LABEL');
                $lines[] = '</label>';
                $lines[] = '<select name="batch[visible]" class="inputbox" id="batch-visible-id">';
                $lines[] = JHtml::_('select.options', $options, 'value', 'text');
                $lines[] = '</select>';                

		return implode("\n", $lines);
	}
        
	/**
	 * Display a batch widget for the required field.
	 *
	 * @return  string  The necessary HTML for the widget.
	 */
	public static function required()
	{
		JHtml::_('bootstrap.tooltip');

		$options = array(
			JHtml::_('select.option', '', JText::_('COM_HWDMS_OPTION_KEEP_ORIGINAL_VALUE')),
			JHtml::_('select.option', '1', JText::_('COM_HWDMS_OPTION_REQUIRED')),
			JHtml::_('select.option', '0', JText::_('COM_HWDMS_OPTION_UNREQUIRED'))
		);
                
		$lines = array();
                $lines[] = '<label id="batch-required-lbl" for="batch-required-id" class="hasToolip" title="' . JHtml::tooltipText('COM_HWDMS_BATCH_REQUIRED_LABEL', 'COM_HWDMS_BATCH_REQUIRED_DESC') . '">';
                $lines[] = JText::_('COM_HWDMS_BATCH_REQUIRED_LABEL');
                $lines[] = '</label>';
                $lines[] = '<select name="batch[required]" class="inputbox" id="batch-required-id">';
                $lines[] = JHtml::_('select.options', $options, 'value', 'text');
                $lines[] = '</select>';                

		return implode("\n", $lines);
	}
        
	/**
	 * Display a batch widget for the download access field.
	 *
	 * @return  string  The necessary HTML for the widget.
	 */
	public static function downloadaccess()
	{
		JHtml::_('bootstrap.tooltip');

		// Create the batch selector to change an access level on a selection list.
		return   '<label id="batch-downloadaccess-lbl" for="batch-downloadaccess" class="hasToolip"'
			. 'title="' . JHtml::tooltipText('COM_HWDMS_BATCH_DOWNLOAD_ACCESS_LABEL', 'COM_HWDMS_BATCH_DOWNLOAD_ACCESS_DESC') . '">'
			. JText::_('COM_HWDMS_BATCH_DOWNLOAD_ACCESS_LABEL')
			. '</label>'
			. JHtml::_(
				'access.assetgrouplist',
				'batch[downloadaccess]', '',
				'class="inputbox"',
				array(
					'title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),
					'id' => 'batch-downloadaccess'
				)
			);
	}
}
