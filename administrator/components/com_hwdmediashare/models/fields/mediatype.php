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

class JFormFieldMediaType extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'MediaType';

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
                
                if ($config->get('enable_audio'))               $options[] = JHtml::_('select.option', '1', JText::_('COM_HWDMS_AUDIO'));
                if ($config->get('enable_documents'))           $options[] = JHtml::_('select.option', '2', JText::_('COM_HWDMS_DOCUMENT'));
                if ($config->get('enable_images'))              $options[] = JHtml::_('select.option', '3', JText::_('COM_HWDMS_IMAGE'));
                if ($config->get('enable_videos'))              $options[] = JHtml::_('select.option', '4', JText::_('COM_HWDMS_VIDEO'));
                
                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
