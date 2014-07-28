<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareHelperModule
{
	/**
	 * An array of module data.
         * 
         * @access      protected
         * @static
	 * @var         array
	 */     
	protected static $mods = array();

	/**
	 * This is always going to get the first instance of the module type unless
	 * there is a title.
	 *
         * @access  public
         * @static 
	 * @param   string  $module  The module name.
	 * @param   string  $title   The title of the module.
	 * @param   string  $style   The style of the module.
	 * @return  mixed
	 */
	public static function _loadmod($module, $title = '', $style = 'none')
	{
		if (!isset(self::$mods[$module])) 
                {
			self::$mods[$module] = '';
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$mod		= JModuleHelper::getModule($module, $title);
                        
			// If the module without the mod_ isn't found, try it with mod_ prefix.
			// This allows people to enter it either way.
			if (!isset($mod))
                        {
				$name = 'mod_'.$module;
				$mod  = JModuleHelper::getModule($name, $title);
			}
			$params = array('style' => $style);
			ob_start();

			echo $renderer->render($mod, $params);

			self::$mods[$module] = ob_get_clean();
		}
                
		return self::$mods[$module];
	}
        
	/**
	 * Method to load a module position.
	 *
         * @access  public
         * @static 
	 * @param   string  $position  The position assigned to the module
	 * @param   string  $style     The style assigned to the module
	 * @return  mixed
	 */
	public static function _loadpos($position, $style = 'xhtml')
	{
                $document = JFactory::getDocument();
                $renderer = $document->loadRenderer('modules');
                $options = array('style' => $style);
                echo $renderer->render($position, $options, null);
        }

	/**
	 * Method to load a module position (media-tabs) and display each module
         * in a (Bootstrap) tab.
	 *
         * @access  public
         * @static 
	 * @param   string  $position  The position assigned to the module
	 * @param   string  $style     The style assigned to the module
	 * @return  mixed
	 */
	public static function _loadtab($position = 'media-tabs', $style = 'xhtml')
	{
                $document = JFactory::getDocument();
                $renderer = $document->loadRenderer('module');
                $params = array('style' => $style);
                
		$buffer = '';
          
		foreach (JModuleHelper::getModules($position) as $mod)
		{
                        $mod->showtitle = false;
                        $buffer .= JHtml::_('bootstrap.addTab', 'pane', 'tab-'.$mod->id, JText::_($mod->title));
			$buffer .= $renderer->render($mod, $params);
			$buffer .= JHtml::_('bootstrap.endTab');
		}
               
		return $buffer;
        }
}
