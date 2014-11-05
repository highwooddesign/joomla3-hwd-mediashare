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

class JFormFieldElement extends JFormFieldList
{
	/**
	 * The name of the form field type.
         * 
         * @access  protected
	 * @var     string
	 */
	protected $type = 'Element';

	/**
	 * Method to get the field options.
	 *
	 * @access  protected
	 * @return  array      The field option objects.
	 */
	protected function getOptions()
	{
                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Initialise variables.
		$options = array();
                $options[] = JHtml::_('select.option', '1', JText::_('COM_HWDMS_MEDIA'));
                
                if ($config->get('enable_albums'))          $options[] = JHtml::_('select.option', '2', JText::_('COM_HWDMS_ALBUM'));
                if ($config->get('enable_groups'))          $options[] = JHtml::_('select.option', '3', JText::_('COM_HWDMS_GROUP'));
                if ($config->get('enable_playlists'))       $options[] = JHtml::_('select.option', '4', JText::_('COM_HWDMS_PLAYLIST'));
                if ($config->get('enable_channels'))        $options[] = JHtml::_('select.option', '5', JText::_('COM_HWDMS_CHANNEL'));

                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
