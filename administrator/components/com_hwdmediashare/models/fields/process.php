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

class JFormFieldProcess extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Process';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
                // Get HWD config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Initialise variables.
		$options = array();
                $options[] = JHtml::_('select.option', '1', JText::_('COM_HWDMS_GENERATE_JPG_75_LABEL'));
                $options[] = JHtml::_('select.option', '2', JText::_('COM_HWDMS_GENERATE_JPG_100_LABEL'));
                $options[] = JHtml::_('select.option', '3', JText::_('COM_HWDMS_GENERATE_JPG_240_LABEL'));
                $options[] = JHtml::_('select.option', '4', JText::_('COM_HWDMS_GENERATE_JPG_500_LABEL'));
                $options[] = JHtml::_('select.option', '5', JText::_('COM_HWDMS_GENERATE_JPG_640_LABEL'));
                $options[] = JHtml::_('select.option', '6', JText::_('COM_HWDMS_GENERATE_JPG_1024_LABEL'));
                $options[] = JHtml::_('select.option', '7', JText::_('COM_HWDMS_GENERATE_AUDIO_MP3_LABEL'));
                $options[] = JHtml::_('select.option', '8', JText::_('COM_HWDMS_GENERATE_AUDIO_OGG_LABEL'));
                $options[] = JHtml::_('select.option', '9', JText::_('COM_HWDMS_GENERATE_FLV_240_LABEL'));
                $options[] = JHtml::_('select.option', '10', JText::_('COM_HWDMS_GENERATE_FLV_360_LABEL'));
                $options[] = JHtml::_('select.option', '11', JText::_('COM_HWDMS_GENERATE_FLV_480_LABEL'));
                $options[] = JHtml::_('select.option', '12', JText::_('COM_HWDMS_GENERATE_MP4_360_LABEL'));
                $options[] = JHtml::_('select.option', '13', JText::_('COM_HWDMS_GENERATE_MP4_480_LABEL'));
                $options[] = JHtml::_('select.option', '14', JText::_('COM_HWDMS_GENERATE_MP4_720_LABEL'));
                $options[] = JHtml::_('select.option', '15', JText::_('COM_HWDMS_GENERATE_MP4_1080_LABEL'));
                $options[] = JHtml::_('select.option', '16', JText::_('COM_HWDMS_GENERATE_WEBM_360_LABEL'));
                $options[] = JHtml::_('select.option', '17', JText::_('COM_HWDMS_GENERATE_WEBM_480_LABEL'));
                $options[] = JHtml::_('select.option', '18', JText::_('COM_HWDMS_GENERATE_WEBM_720_LABEL'));
                $options[] = JHtml::_('select.option', '19', JText::_('COM_HWDMS_GENERATE_WEBM_1080_LABEL'));
                $options[] = JHtml::_('select.option', '24', JText::_('COM_HWDMS_GENERATE_OGG_360_LABEL'));
                $options[] = JHtml::_('select.option', '25', JText::_('COM_HWDMS_GENERATE_OGG_480_LABEL'));
                $options[] = JHtml::_('select.option', '26', JText::_('COM_HWDMS_GENERATE_OGG_720_LABEL'));
                $options[] = JHtml::_('select.option', '27', JText::_('COM_HWDMS_GENERATE_OGG_1080_LABEL'));
                $options[] = JHtml::_('select.option', '20', JText::_('COM_HWDMS_INJECT_METADATA_LABEL'));
                $options[] = JHtml::_('select.option', '21', JText::_('COM_HWDMS_MOVE_MOOV_ATOM_LABEL'));
                $options[] = JHtml::_('select.option', '22', JText::_('COM_HWDMS_GET_DURATION_LABEL'));
                $options[] = JHtml::_('select.option', '23', JText::_('COM_HWDMS_GET_TITLE_LABEL'));
                
                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
