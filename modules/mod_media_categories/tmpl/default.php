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
<ul class="categories-module<?php echo $helper->moduleclass_sfx; ?>">
<?php require JModuleHelper::getLayoutPath('mod_media_categories', $params->get('layout', 'default').'_items'); ?>
</ul>