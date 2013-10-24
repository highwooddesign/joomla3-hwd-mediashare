<?php
/**
 * @version    $Id: default_items.php 1383 2013-04-23 12:53:48Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

foreach ($items as $item) : ?>
    <li <?php if ($_SERVER['PHP_SELF'] == JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->id))) echo ' class="active"';?>> <?php $levelup=$item->level-$startLevel -1; ?>
        <?php //if ($params->get('list_meta_title') != 'hide') :?>
          <h<?php echo $params->get('list_item_heading')+$levelup; ?> class="contentheading">
            <?php if ($params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->slug)); ?>"><?php endif; ?>
              <?php echo $helper->get('utilities')->escape(JHtmlString::truncate($item->title, $params->get('list_title_truncate'))); ?> 
            <?php if ($params->get('list_link_titles') == 1) :?></a><?php endif; ?>
          </h<?php echo $params->get('list_item_heading')+$levelup; ?>>
        <?php //endif; ?>
        <?php       
        if($params->get('show_description', 0))
        {
            echo $helper->get('utilities')->escape(JHtmlString::truncate($item->description, $params->get('list_desc_truncate'), true, false));
        }
        if($params->get('show_children', 0) && (($params->get('maxlevel', 0) == 0) || ($params->get('maxlevel') >= ($item->level - $startLevel))) && count($item->getChildren()))
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
