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

class hwdMediaShareControllerAccount extends JControllerForm
{
	/**
	 * The prefix to use with controller messages.
         * 
         * @access      protected
	 * @var         string
	 */
	protected $text_prefix = 'COM_HWDMS';
        
	/**
	 * The URL view item variable to use with this controller.
	 *
         * @access      protected
	 * @var         string
	 */
	protected $view_item = 'account';

	/**
	 * The URL view list variable to use with this controller.
	 *
         * @access      protected
	 * @var         string
	 */
	protected $view_list = 'users';
}
