<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_videos
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$menu = $app->getMenu();
$user = JFactory::getUser();

?>
<div class="hwd-container">
  <div class="media-details-view thumbnail-grid">
    <?php if (empty($helper->items)): ?>
      <div class="alert alert-no-items">
        <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
      </div>
    <?php else: ?>
      <?php foreach ($helper->items as $id => $item) :
      $rowcount = (((int)$id) % (int) 3) + 1;
      $rowspan = $rowcount == 1 ? 12 : 6;
      $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
      $canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
      $canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id);
      $canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id))); 
      ?>
        <?php if ($rowcount == 1 || $rowcount == 2) : ?>
          <div class="row-fluid">
        <?php endif; ?>
          <div class="span<?php echo intval($rowspan); ?>">
            <?php 
            ob_start();
            ?>
            <div class="media-transform-container">
              <div class="media-item">
              <div class="media-aspect<?php echo $helper->params->get('list_thumbnail_aspect'); ?>"></div>        
              <div class="media-thumbnail-grid-overlay">
                <div class="media-title">
                  <?php echo JHtml::_('string.truncate', $item->title, 100, false, false); ?> 
                </div>
              </div>
              <img src="<?php echo JRoute::_(hwdMediaShareThumbnails::thumbnail($item)); ?>" border="0" alt="<?php echo $helper->escape($item->title); ?>" class="media-thumb" />
              </div>
            </div>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo JHtml::_('HwdPopup.link', $item, $html, array('class' => 'media-video')); 
            ?> 
          </div>
        <?php if (($rowcount == 1 || $rowcount == 3) or (($id + 1) == count($helper->items))): ?>
          </div>
        <?php endif; ?>  
      <?php endforeach; ?>
    <?php endif; ?>
  </div> 
  <?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute())); ?>" class="btn"><?php echo JText::_($params->get('more_link_text', 'MOD_MEDIA_VIEW_MORE_AUDIO')); ?></a></p><?php endif; ?>  
</div>
