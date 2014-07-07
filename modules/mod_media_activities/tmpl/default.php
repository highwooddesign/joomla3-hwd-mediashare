<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_activities
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;
?>
<div class="hwd-module">
  <?php echo JLayoutHelper::render('activities_list', $helper, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
</div>
