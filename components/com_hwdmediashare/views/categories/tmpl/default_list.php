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
?>
<ul>
<?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
	<li><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->id));?>"><?php echo $this->escape($item->title); ?></a> </li>
<?php endforeach; ?>
</ul>
