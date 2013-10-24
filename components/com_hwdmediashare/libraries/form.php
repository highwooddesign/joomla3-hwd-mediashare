<?php
/**
 * @version    SVN $Id: form.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// @TODO: Move this to form field?

require(JPATH_ROOT.'/libraries/joomla/form/fields/user.php');
jimport( 'joomla.form.fields.user' );

/**
 * hwdMediaShare framework form class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdmsFormFieldUser extends JFormFieldUser
{
	/**
	 * Method to call protected Joomla user field 
         *
	 * @since   0.1
	 */
        function getUserFieldInput($name = 'user', $id = 'user')
        {
                $this->name = $name;
                $this->id = $id;
                return $this->getInput();
        }
}
