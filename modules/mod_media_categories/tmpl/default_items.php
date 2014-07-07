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
          <?php if ($params->get('list_item_heading')) : ?><h<?php echo $params->get('list_item_heading')+$levelup; ?> class="contentheading"><?php else: ?><p><?php endif; ?>
            <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>">
              <?php echo $helper->get('utilities')->escape(JHtmlString::truncate($item->title, $params->get('list_title_truncate'))); ?> 
            </a>
          <?php if ($params->get('list_item_heading')) : ?></h<?php echo $params->get('list_item_heading')+$levelup; ?>><?php else: ?></p><?php endif; ?>
        <?php       
        if($params->get('show_description', 0))
        {
            echo $helper->get('utilities')->escape(JHtmlString::truncate($item->description, $params->get('list_desc_truncate'), true, false));
        }
        if($params->get('show_children', 0) && (($params->get('maxlevel', 0) == 0) || ($params->get('maxlevel') >= ($item->level - $helper->startLevel))) && count($item->getChildren()))
        {
            echo '<ul>';
            $temp = $items;
            $items = $item->getChildren();
            require JModuleHelper::getLayoutPath('mod_media_categories', $params->get('layout', 'default').'_items');
            $items = $temp;
            echo '</ul>';
        }
        ?>
    </li>
<?php endforeach; ?>
