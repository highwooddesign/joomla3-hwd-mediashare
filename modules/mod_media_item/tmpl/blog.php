<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_item
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;
?>
<div class="hwd-container">
  <div class="media-details-view">
    <?php if (empty($helper->item)): ?>
      <div class="alert alert-no-items">
        <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
      </div>
    <?php else: ?>
      <?php echo JLayoutHelper::render('mediaitem_layout_blog', $helper, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    <?php endif; ?>
  </div> 
</div>
