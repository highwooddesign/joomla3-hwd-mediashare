<?php
/**
 * @version    $Id: default.php 1331 2013-03-20 10:43:13Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$application = JFactory::getApplication();
$user = JFactory::getUser();
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

hwdMediaShareFactory::load('activities');
$act = hwdMediaShareActivities::getInstance();
?>
<!-- Module Container -->
<div class="hwd-module">
    <div class="categories-list"> <?php echo $act->getActivities($items); ?> </div>            
</div>
