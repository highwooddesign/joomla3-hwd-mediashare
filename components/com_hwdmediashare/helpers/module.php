<?php
/**
 * @version    SVN $Id: module.php 576 2012-10-15 16:57:45Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      13-Dec-2011 09:36:10
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare Module Helper
 *
 * @package	hwdMediaShare
 * @since       0.1
 */
abstract class hwdMediaShareHelperModule
{
	protected static $modules = array();
	protected static $mods = array();

	/**
	 * @module	string	The module position to be displayed
	 * @title	string	The module title
	 * @style	string	The module style
	 */
	public function _loadmod($module, $title = '', $style = 'none')
	{
		if (!isset(self::$mods[$module])) 
                {
			self::$mods[$module] = '';
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$mod		= JModuleHelper::getModule($module, $title);
			// If the module without the mod_ isn't found, try it with mod_.
			// This allows people to enter it either way in the content
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
	 * @position	string	The module position to be displayed
	 * @style	string	The module style
	 */
	public function _loadpos($position, $style = 'xhtml')
	{
                $document = &JFactory::getDocument();
                $renderer   = $document->loadRenderer('modules');
                $options   = array('style' => $style);
                echo $renderer->render($position, $options, null);
        }

	/**
	 * @position	string	The module position to be displayed
	 * @style	string	The module style
	 */
	public function _loadtab($position = 'media-tabs', $style = 'xhtml')
	{
                $document = &JFactory::getDocument();
                $renderer   = $document->loadRenderer('module');
                $params   = array('style' => $style);
                
		$buffer = '';

		foreach (JModuleHelper::getModules($position) as $mod)
		{
                        $mod->showtitle = false;
                        $buffer .= JHtml::_('tabs.panel', JText::_($mod->title), 'tab-'.$mod->title);
			$buffer .= $renderer->render($mod, $params);
		}
               
		return $buffer;
        }
}
