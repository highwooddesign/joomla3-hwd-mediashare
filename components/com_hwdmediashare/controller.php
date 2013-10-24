<?php
/**
 * @version    SVN $Id: controller.php 1559 2013-06-13 09:56:17Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * hwdMediaShare Component Controller
 */
class hwdMediaShareController extends JControllerLegacy {
	/**
	 * Display task
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'media'));
                
                if (!$cachable)
                {
                        // Get hwdMediaShare config
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig();
                        $cachable = $config->get('caching', JFactory::getConfig()->get( 'caching' ));
                }

                $safeurlparams = array('catid'=>'INT',
                                       'id'=>'INT',
                                       'cid'=>'ARRAY',
                                       'year'=>'INT',
                                       'month'=>'INT',
                                       'limit'=>'INT',
                                       'limitstart'=>'INT',
                                       'display'=>'STRING',
                                       'showall'=>'INT',
                                       'return'=>'BASE64',
                                       'filter_search'=>'STRING',
                                       'filter_order'=>'STRING',
                                       'filter_tag'=>'STRING',
                                       'filter_order_Dir'=>'CMD',
                                       'filter-search'=>'STRING',
                                       'print'=>'BOOLEAN',
                                       'lang'=>'CMD');

                // Call parent behavior
		parent::display($cachable, $safeurlparams);
	}
}
