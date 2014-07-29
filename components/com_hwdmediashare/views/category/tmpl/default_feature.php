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
<?php if ($this->feature) : ?>
<div id="media-item-container" class="media-item-container">
  <div class="media-item-full" id="media-item">
    <?php echo hwdMediaShareMedia::get($this->feature); ?>
  </div>
</div>
<?php endif; ?> 