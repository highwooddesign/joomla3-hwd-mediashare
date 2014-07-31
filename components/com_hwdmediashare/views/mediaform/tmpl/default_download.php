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
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <div class="media-list-view">
      <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
          <?php echo JText::_('COM_HWDMS_MSG_NO_DOWNLOADS'); ?>
        </div>
      <?php else : ?>
        <table class="table">
          <thead>
            <tr>
              <th><?php echo JText::_('COM_HWDMS_TYPE'); ?></th>     
              <th width="15%"><?php echo JText::_('COM_HWDMS_EXTENSION'); ?></th> 
              <th width="15%"><?php echo JText::_('COM_HWDMS_SIZE'); ?> </th> 
            </tr>
          </thead>
          <tbody>
            <?php foreach ($this->downloads as $id => &$item) : ?>
            <tr>
              <td><a href="<?php echo hwdMediaShareDownloads::url($this->item, $item->file_type, 1); ?>"><?php echo hwdMediaShareFiles::getFileType($item); ?></a></td>
              <td><?php echo $item->ext; ?></td>
              <td><?php echo JHtml::_('number.bytes', $item->size); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>  
    </div>
  </div>
</form>