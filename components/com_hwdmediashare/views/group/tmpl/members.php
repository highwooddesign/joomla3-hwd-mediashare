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
      <?php echo JLayoutHelper::render('members_list', $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    </div>    
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
</form>
