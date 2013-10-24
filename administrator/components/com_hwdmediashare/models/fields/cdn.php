<?php
/**
 * @version    SVN $Id: cdn.php 1249 2013-03-08 14:24:48Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Mar-2013 09:18:21
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

 /**
  * Process field class
  */
class JFormFieldCdn extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'CDN';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{
                // Initialise variables.
		$options	= array();    
                $options[]      = JHtml::_('select.option', '', JText::_('COM_HWDMS_LIST_SELECT_CDN'));

                // Load all hwdMediaShare plugins
                $db =& JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__extensions AS a');
                $query->where('a.type = '.$db->quote('plugin'));
                $query->where('a.folder = '.$db->quote('hwdmediashare'));
                $db->setQuery($query);
                $rows = $db->loadObjectList();            

                // Loop all plugins and check if a cdn plugin
		for( $i = 0; $i < count($rows); $i++ )
		{
			$row = $rows[$i];

                        if( substr($row->element, 0, 4) == 'cdn_' )
			{
                                // Load the HWDMediaShare language file
                                $lang =& JFactory::getLanguage();
                                $lang->load($row->name, JPATH_SITE.'/administrator', $lang->getTag());

                                $pluginClass = 'plgHwdmediashare'.$row->element;
                                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$row->element.'/'.$row->element.'.php';

                                $insert = true;

                                // Import hwdMediaShare plugins
                                if (file_exists($pluginPath))
                                {
                                        JLoader::register($pluginClass, $pluginPath);
                                        
                                        $player = call_user_func(array($pluginClass, 'getInstance'));
                                        if (method_exists($player,'checkInstalled'))
                                        {
                                                $insert = call_user_func(array($pluginClass, 'checkInstalled'));
                                        }
                                }
                                
                                if ($insert)
                                {
                                        $options[] = JHtml::_('select.option', $row->element, JText::_($row->name));     
                                }                              
			}
		}

		return $options;
	}
}