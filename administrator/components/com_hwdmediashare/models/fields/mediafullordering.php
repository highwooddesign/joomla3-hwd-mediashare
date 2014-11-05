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

class JFormFieldMediaFullOrdering extends JFormFieldList
{
	/**
	 * The name of the form field type.
         * 
         * @access  protected
	 * @var     string
	 */
	protected $type = 'MediaFullOrdering';

	/**
	 * Method to get the field options.
	 *
	 * @access  protected
	 * @return  array      The field option objects.
	 */
	protected function getOptions()
	{
                // Initialise variables.
		$options = array();
                $options[] = JHtml::_('select.option', 'a.created DESC', JText::_('COM_HWDMS_OPTION_MOST_RECENT'));
                $options[] = JHtml::_('select.option', 'a.hits DESC', JText::_('COM_HWDMS_OPTION_MOST_HITS'));
                $options[] = JHtml::_('select.option', 'a.likes DESC', JText::_('COM_HWDMS_OPTION_MOST_LIKES'));
                $options[] = JHtml::_('select.option', 'a.dislikes DESC', JText::_('COM_HWDMS_OPTION_MOST_DISLIKES'));
                $options[] = JHtml::_('select.option', 'a.title ASC', JText::_('COM_HWDMS_OPTION_TITLE_ALPHABETICAL'));
                $options[] = JHtml::_('select.option', 'a.modified DESC', JText::_('COM_HWDMS_OPTION_RECENTLY_MODIFIED'));
                $options[] = JHtml::_('select.option', 'a.viewed DESC', JText::_('COM_HWDMS_OPTION_RECENTLY_VIEWED'));
                $options[] = JHtml::_('select.option', 'author ASC', JText::_('COM_HWDMS_OPTION_AUTHOR_ALPHABETICAL'));
                $options[] = JHtml::_('select.option', 'random ASC', JText::_('COM_HWDMS_OPTION_RANDOM'));
                                
                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
