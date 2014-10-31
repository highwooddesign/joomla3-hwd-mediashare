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

class modHwdSpotifyAudioBoxHelper
{
        /**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   array   $module  The module object.
	 * @param   array   $params  The module parameters object.
         * @return  void
	 */       
	public function __construct($module, $params)
	{
                // Get data.
                $this->module = $module;                
                $this->params = $params;                
	}
}