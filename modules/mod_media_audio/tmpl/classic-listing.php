<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_media_audio
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
  <div class="media-audio-view">
    <?php if (empty($helper->items)): ?>
      <div class="alert alert-no-items">
        <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
      </div>
    <?php else: ?>
      <?php foreach ($helper->items as $id => $item) :
      $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
      $canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
      $canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id);
      $canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
        ob_start(); ?>
        <div class="hasTooltip" title="<?php echo JHtml::tooltipText($item->title, ($helper->params->get('list_tooltip_contents') != '0' ? JHtml::_('string.truncate', $item->description, $helper->params->get('list_desc_truncate'), false, false) : '')); ?>">
          <div class="pull-right">
            <?php echo hwdMediaShareMedia::secondsToTime($item->duration); ?>                    
          </div>              
          <span><i class="icon-play"></i><?php echo $item->title; ?></span>                                      
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo JHtml::_('HwdPopup.link', $item, $html, array('class' => 'media-track')); ?>                                      
      <?php endforeach; ?>
    <?php endif; ?>
  </div> 
  <?php if ($params->get('show_more_link') != 'hide') :?><p><a href="<?php echo ((intval($params->get('show_more_link')) > 0) ? JRoute::_($menu->getItem($params->get('show_more_link'))->link.'&Itemid='.$params->get('show_more_link')) : JRoute::_(hwdMediaShareHelperRoute::getMediaRoute())); ?>" class="btn"><?php echo JText::_($params->get('more_link_text', 'MOD_MEDIA_AUDIO_VIEW_MORE')); ?></a></p><?php endif; ?>  
</div>
