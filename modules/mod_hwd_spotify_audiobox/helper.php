<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_spotify_audiobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class modHwdSpotifyAudioBoxHelper extends JObject
{
	/**
	 * Class data
	 * @var array
	 */    
	public $params;
	public $module;

	public function __construct($module, $params)
	{
                // Get data.
                $this->module = $module;                
                $this->params = $params;                
	}
}