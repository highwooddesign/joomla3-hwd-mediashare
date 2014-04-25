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

class hwdMediaShareController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean         If true, the view output will be cached
	 * @param   array           An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController     This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Set the default view name.
		$view = $this->input->get('view', 'media');
                $this->input->set('view', $view);

                // Override caching if set.
                if (!$cachable)
                {
                        // Get HWD config.
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

		parent::display($cachable, $safeurlparams);

		return $this;
	}
}
