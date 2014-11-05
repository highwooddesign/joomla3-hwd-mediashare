<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

// Import formrule library
jimport('joomla.form.formrule');

class JFormRuleWord extends JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @access  protected
	 * @var     string
	 */
	protected $regex = '^[a-zA-Z]+$';
}
