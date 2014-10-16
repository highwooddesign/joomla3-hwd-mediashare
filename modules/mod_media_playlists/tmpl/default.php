<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_playlists
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$menu = $app->getMenu();
?>
<div class="hwd-container">
  <div class="media-details-view">
    <?php if (empty($helper->items)) : ?>
      <div class="alert alert-no-items">
        <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
      </div>
    <?php else : ?>
      <?php echo JLayoutHelper::render('playlists_details', $helper, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    <?php endif; ?>        
  </div> 
  <?php if ($params->get('show_more_link') != 'hide') :?><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getPlaylistsRoute())); ?>" class="btn"><?php echo JText::_($params->get('more_link_text', 'MOD_MEDIA_VIEW_MORE_PLAYLISTS')); ?></a><?php endif; ?>  
</div>
