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

class hwdMediaShareCategories extends JCategories
{
	/**
	 * Class constructor
	 *
	 * @param   array  $options  Array of options
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
