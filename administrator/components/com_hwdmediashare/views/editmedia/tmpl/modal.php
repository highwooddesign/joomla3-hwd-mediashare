<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . "media/com_hwdmediashare/assets/css/hwd.css");

// Load HWD config.
$hwdms = hwdMediaShareFactory::getInstance();
$config = $hwdms->getConfig();
$config->set('mediaitem_size', 900);
?>  
<div id="hwd-container">
  <?php echo hwdMediaShareMedia::get($this->item); ?>
</div>
