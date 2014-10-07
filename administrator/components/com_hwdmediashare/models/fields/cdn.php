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

class JFormFieldCdn extends JFormFieldList
{
	/**
	 * The name of the form field type.
         * 
         * @access      protected
	 * @var         string
	 */
	protected $type = 'CDN';

	/**
	 * Method to get the field options.
	 *
	 * @access	protected
	 * @return      array       The field option objects.
	 */
	protected function getOptions()
	{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('*')
                        ->from('#__extensions')
                        ->where('type = ' . $db->quote('plugin'))
                        ->where('folder = ' . $db->quote('hwdmediashare'));
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
                
                // Loop all plugins and check if a cdn plugin.
		for($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];

                        if(substr($row->element, 0, 4) == 'cdn_')
			{
                                // Load the plugin language file.
                                $lang = JFactory::getLanguage();
                                $lang->load($row->name, JPATH_SITE . '/administrator', $lang->getTag());

                                // Add option.
                                if (file_exists(JPATH_ROOT.'/plugins/hwdmediashare/' . $row->element . '/' . $row->element . '.php'))
                                {
                                        $options[] = JHtml::_('select.option', $row->element, JText::_($row->name));  
                                }
			}
		}

                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
