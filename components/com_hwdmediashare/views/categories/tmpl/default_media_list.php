<?php
/**
 * @version    SVN $Id: default_media_list.php 645 2012-10-19 13:20:33Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-Oct-2011 11:48:43
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<section id="category-media">
<?php foreach ($this->media as $cid => &$media) : ?>
  <a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($media->id)); ?>"> <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($media)); ?>" border="0" alt="<?php echo $this->escape($media->title); ?>" /> </a> 
<?php endforeach; ?>
</section>


