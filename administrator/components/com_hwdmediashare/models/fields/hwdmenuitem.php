<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('groupedlist');

// Import the com_menus helper.
require_once realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

/**
 * Supports an HTML grouped select list of menu item grouped by menu
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       1.6
 */
class JFormFieldHwdMenuitem extends JFormFieldList
{
	/**
	 * The name of the form field type.
         * 
         * @access  protected
	 * @var     string
	 */
	protected $type = 'HwdMenuItem';

	/**
	 * The menu view.
	 *
         * @access  protected
	 * @var     string
	 */
	protected $menuView;
        
	/**
	 * Method to attach a JForm object to the field.
	 *
         * @access  public
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 * @return  boolean  True on success.
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		if ($result == true)
		{
			$this->menuView  = (string) $this->element['menu_view'];
		}

		return $result;
	}
        
	/**
	 * Method to get the field options.
	 *
	 * @access  protected
	 * @return  array      The field option objects.
	 */
	protected function getOptions()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__menu')
                        ->where('type = ' . $db->quote('component'))
                        ->where('link LIKE ' . $db->quote('index.php?option=com_hwdmediashare&view=' . $this->menuView . '%'))
                        ->where('client_id = ' . $db->quote(0));

                try
                {
                        $db->setQuery($query);
                        $db->execute(); 
                        $rows = $db->loadObjectList();                        
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }

                // Define empty array to hold options.
                $options = array();
                
                // Loop all plugins and check if a commenting plugin.
		for($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];

                        $options[] = JHtml::_('select.option', $row->id, JText::sprintf('COM_HWDMS_HWDMENUITEM_OPTION_MENUX_TITLEX', $row->menutype, $row->title));  
		}

                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
