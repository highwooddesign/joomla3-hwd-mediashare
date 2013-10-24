<?php
/**
 * @version    SVN $Id: integer.php 164 2012-01-29 15:40:23Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Oct-2011 13:06:42
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla formrule library
jimport('joomla.form.formrule');

/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleInteger extends JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @access	protected
	 * @var		string
	 * @since	0.1
	 */
	protected $regex = '^[0-9]+$';
}
