<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_categories
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;
?>
<ul id="<?php echo $params->get('list_id'); ?>" class="hwd-container categories-module<?php echo $params->get('list_class', ' nav menu'); ?>">
<?php require JModuleHelper::getLayoutPath('mod_media_categories', $params->get('layout', 'default').'_items'); ?>
</ul>