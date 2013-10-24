<?php
/**
 * @version    SVN $Id: default_list.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Dec-2011 17:13:47
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<ul>
<?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
	<li><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($item->id));?>"><?php echo $this->escape($item->title); ?></a> </li>
<?php endforeach; ?>
</ul>
