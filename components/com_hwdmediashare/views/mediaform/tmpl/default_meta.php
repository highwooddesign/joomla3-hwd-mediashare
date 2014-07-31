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
      <table class="table">
        <tbody>
        <?php if (!is_array($this->meta) || count($this->meta) == 0) : ?>
          <tr class="cat-list-row<?php echo ($counter % 2);?>">
            <td><?php echo JText::_('COM_HWDMS_NO_DATA'); ?></td>
          </tr>   
        <?php else : ?>
          <?php foreach ($this->meta as $id => $item) : ?>
            <tr>
              <td><?php echo ucwords($id); ?></a></td>
              <td><?php echo ucwords($item); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>   
</form>