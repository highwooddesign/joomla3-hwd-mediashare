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

class hwdMediaShareRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the com_hwdmediashare component.
	 *
         * @access  public
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
        {
                // Register HWD library factory.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD router.
                hwdMediaShareFactory::load('routers.' . $config->get('sef_router', 'standard'));

                $router = 'hwdMediaShareRouter' . $config->get('sef_router', 'standard');
                $HWDrouter = new $router;
                return $HWDrouter->build($query);
        }

	/**
	 * Parse the segments of a URL.
	 *
         * @access  public
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
                // Register HWD library factory.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();

                // Load HWD router.
                hwdMediaShareFactory::load('routers.' . $config->get('sef_router', 'standard'));

                $router = 'hwdMediaShareRouter' . $config->get('sef_router', 'standard');
                $HWDrouter = new $router;
                return $HWDrouter->parse($segments);
        }
}

/**
 * hwdMediaShare router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function hwdMediaShareBuildRoute(&$query)
{
	$router = new hwdMediaShareRouter;

	return $router->build($query);
}

function hwdMediaShareParseRoute($segments)
{
	$router = new hwdMediaShareRouter;

	return $router->parse($segments);
}
