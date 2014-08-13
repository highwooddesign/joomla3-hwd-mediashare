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
	 * Proxy view method for MVC based architecture.
	 *
	 * @access	public
	 * @param       boolean     $cachable       If true, the view output will be cached
	 * @param       array       $urlparams      An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 * @return      object      A JControllerLegacy object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Set the default view name.
		$view = $this->input->get('view', 'media');
                $this->input->set('view', $view);

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
                                       'filter_search'=>'STRING',
                                       'print'=>'BOOLEAN',
                                       'lang'=>'CMD');

		parent::display($cachable, $safeurlparams);

		return $this;
	}
}
