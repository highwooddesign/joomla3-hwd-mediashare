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

class JFormFieldExtension extends JFormFieldList
{
	/**
	 * The name of the form field type.
         * 
         * @access  protected
	 * @var     string
	 */
	protected $type = 'Extension';

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
                        ->from('#__hwdms_ext')
                        ->where('published = ' . $db->quote(1));
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

                // Loop all valid extension types and add option.
		for($i = 0; $i < count($rows); $i++)
		{
                        $options[] = JHtml::_('select.option', $rows[$i]->id, $rows[$i]->ext);     
		}

                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
