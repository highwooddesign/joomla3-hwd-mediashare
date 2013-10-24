<?php
/**
 * @version    $Id: default.php 517 2012-09-26 15:59:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();
// @TODO: Add escape function to tag??
?>
<div class="media-tags-view">
<?php foreach ($items as $id => &$item) : ?>                  
  <span><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaRoute(array('filter_tag'=>$item->tag))); ?>"><?php echo $item->tag; ?></a></span>
  <?php endforeach; ?>
</div>