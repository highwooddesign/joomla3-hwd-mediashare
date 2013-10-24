<?php
/**
 * @version    SVN $Id: view.html.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      20-Nov-2011 16:54:04
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewActivity extends JViewLegacy {
	// Overwriting JView display method
	function display($tpl = null)
	{
	}
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
	}
	// Overwriting JView display method
	function reply($tpl = null)
	{
                $id	= JRequest::getInt( 'id' , '' );
                $reply	= JRequest::getInt( 'reply' , '' );
                $return	= JRequest::getVar( 'return' , '' );

                $this->assign('id', $id);
                $this->assign('reply', $reply);
                $this->assign('return', $return);

                $this->form = $this->get('ReportForm');
                $this->assignRef('form', $this->form);

                // Display the view
                parent::display('reply');
	}
}
