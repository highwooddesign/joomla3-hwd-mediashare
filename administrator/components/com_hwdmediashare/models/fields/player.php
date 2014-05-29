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

class JFormFieldPlayer extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Player';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
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

                // Loop all plugins and check if a cdn plugin
		for($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];

                        if(substr($row->element, 0, 7) == 'player_')
			{
                                // Load the language file.
                                $lang = JFactory::getLanguage();
                                $lang->load('plg_hwdmediashare_' . $row->element, JPATH_SITE . '/administrator');

                                // Add option.
                                if (file_exists(JPATH_ROOT.'/plugins/hwdmediashare/' . $row->element . '/' . $row->element . '.php'))
                                {
                                        $options[] = JHtml::_('select.option', $row->element, JText::_('PLG_HWDMEDIASHARE_' . strtoupper($row->element) . '_OPTION_NAME'));  
                                }
			}
		}

                // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
