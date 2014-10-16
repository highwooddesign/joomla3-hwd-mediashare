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

foreach ($helper->items as $item) : 
$levelup = $item->level - $helper->startLevel - 1; ?>
    <li<?php if ($_SERVER['PHP_SELF'] == JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->id))) echo ' class="active"';?>>
        <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>">
          <?php echo $item->title; ?> <?php if ($params->get('show_media_count', 1)) : ?><span class="label"><?php echo (int) $item->numitems; ?></span><?php endif; ?>
        </a>
        <?php if ($params->get('show_description', 0)) : ?>
          <p><?php echo JHtml::_('string.truncate', $item->description, $params->get('list_desc_truncate'), true, false); ?>
        <?php endif; ?>
        <?php if ($params->get('show_children', 1) && (($params->get('maxlevel', 0) == 0) || ($params->get('maxlevel') >= ($item->level - $helper->startLevel))) && count($item->getChildren())) : ?>
            <ul>
            <?php 
            $temp = $helper->items;
            $helper->items = $item->getChildren();
            if ($params->get('count', 0) > 0 && count($helper->items) > $params->get('count', 0))
              $helper->items = array_slice($helper->items, 0, $params->get('count', 0));
            require JModuleHelper::getLayoutPath('mod_media_categories', $params->get('layout', 'default').'_items');
            $helper->items = $temp;
            ?>
            </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
