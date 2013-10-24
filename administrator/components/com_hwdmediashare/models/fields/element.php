<?php
/**
 * @version    SVN $Id: element.php 219 2012-02-29 14:22:40Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Oct-2011 17:54:20
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

 /**
  * Element field class
  */
class JFormFieldElement extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Element';

        /**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	public function getOptions()
	{
		// Initialise variables.
		$options	= array();

                $options[] = JHtml::_('select.option', '', JText::_('COM_HWDMS_LIST_SELECT_TYPE'));
                $options[] = JHtml::_('select.option', '1', JText::_('COM_HWDMS_MEDIA'));
                $options[] = JHtml::_('select.option', '2', JText::_('COM_HWDMS_ALBUM'));
                $options[] = JHtml::_('select.option', '3', JText::_('COM_HWDMS_GROUP'));
                $options[] = JHtml::_('select.option', '4', JText::_('COM_HWDMS_PLAYLIST'));
                $options[] = JHtml::_('select.option', '5', JText::_('COM_HWDMS_USER_CHANNEL'));

		return $options;
	}
}
