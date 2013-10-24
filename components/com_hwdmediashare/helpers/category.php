<?php
/**
 * @version    SVN $Id: category.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Nov-2011 09:00:03
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla categories library
jimport('joomla.application.categories');

/**
 * hwdMediaShare Component Category Tree
 *
 * @package	hwdMediaShare
 * @since       0.1
 */
class hwdMediaShareCategories extends JCategories
{
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	hwdMediaShareCategories
	 * @see		JCategories
	 * @since	0.1
	 */
        public function __construct($options = array())
	{
		$options['table'] = '#__hwdms_category_map';
		$options['extension'] = 'com_hwdmediashare';
		$options['field'] = 'category_id';
                $options['published'] = '0';
                parent::__construct($options);
	}
}
