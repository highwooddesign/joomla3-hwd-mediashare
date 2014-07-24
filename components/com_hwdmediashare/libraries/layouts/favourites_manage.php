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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_ROOT . '/administrator/components/com_hwdmediashare/helpers/html');

$user = JFactory::getUser();
?>
<div class="btn-toolbar">
        <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('media.unfavourite')">
                        <i class="icon-ok"></i> <?php echo JText::_('COM_HWDMS_REMOVE'); ?>
                </button>
        </div>  
</div>
<?php echo JHtml::_('grid.checkall'); ?>
<table class="category table table-striped table-bordered table-hover">
  <tbody>
    <?php foreach ($displayData->items as $id => $item) :
    $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
    $canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
    $canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.media.'.$item->id);
    $canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.media.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.media.'.$item->id) && ($item->created_user_id == $user->id)));
    ?>
    <tr class="cat-list-row<?php echo ($id % 2);?>">
      <td>       
        <?php if ($canEdit || $canDelete): ?>
        <div class="btn-group pull-right">
          <?php
            // Create dropdown items
            if ($canEdit) : 
                JHtml::_('hwddropdown.edit', $item->id, 'mediaform'); 
            endif;    
            if ($canEditState) :
                $action = $item->published ? 'unpublish' : 'publish';
                JHtml::_('hwddropdown.' . $action, $item->id, 'media'); 
            endif; 
            if ($canDelete) : 
                JHtml::_('hwddropdown.delete', $item->id, 'media'); 
            endif;         
            // Render dropdown list
            echo JHtml::_('hwddropdown.render', $this->escape($item->title), ' btn-micro');
          ?>                    
        </div>
        <?php endif; ?>
        <?php echo JHtml::_('grid.id', $id, $item->id); ?>
        <span class="label label-info pull-right"><?php echo $displayData->utilities->getReadableStatus($item); ?></span>
        <?php if ($item->featured): ?>
          <span class="label label-info pull-right"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></span>
        <?php endif; ?>
        <?php if ($displayData->params->get('list_meta_thumbnail') != '0') :?>
        <div class="media-item pull-left">
          <div class="media-aspect<?php echo $displayData->params->get('list_thumbnail_aspect'); ?>"></div>        
          <?php if ($displayData->params->get('list_meta_type_icon') != '0') :?>
          <div class="media-item-format-1-<?php echo $item->media_type; ?>">
             <img src="<?php echo JHtml::_('hwdicon.overlay', '1-'.$item->media_type, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_MEDIA_TYPE'); ?>" />
          </div>
          <?php endif; ?>
          <?php if ($displayData->params->get('list_meta_duration') != '0' && $item->duration > 0) :?>
          <div class="media-duration">
             <?php echo hwdMediaShareMedia::secondsToTime($item->duration); ?>
          </div>
          <?php endif; ?>
          <?php if ($displayData->params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug)); ?>"><?php endif; ?>
             <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item)); ?>" border="0" alt="<?php echo $displayData->escape($item->title); ?>" class="media-thumb <?php echo ($displayData->params->get('list_tooltip_location') > '2' ? 'hasTooltip' : ''); ?>" title="<?php echo $displayData->escape($item->title); ?>::<?php echo ($displayData->params->get('list_tooltip_contents') != '0' ? $displayData->escape(JHtmlString::truncate($item->description, $displayData->params->get('list_desc_truncate'), false, false)) : ''); ?>" />
          <?php if ($displayData->params->get('list_link_thumbnails') == 1) :?></a><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($displayData->params->get('list_meta_title') != '0') :?>
          <p class="contentheading <?php echo ($displayData->params->get('list_tooltip_location') > '1' ? 'hasTooltip' : ''); ?>" title="<?php echo $displayData->escape($item->title); ?>::<?php echo ($displayData->params->get('list_tooltip_contents') != '0' ? $displayData->escape(JHtmlString::truncate($item->description, $displayData->params->get('list_desc_truncate'), false, false)) : ''); ?>">
          <?php if ($displayData->params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->slug)); ?>"><?php endif; ?>
            <?php echo $displayData->escape($item->title); ?>
          <?php if ($displayData->params->get('list_link_titles') == 1) :?></a><?php endif; ?>
          </p>
        <?php endif; ?>

        <?php if ($displayData->params->get('list_meta_description') != '0' || $displayData->params->get('list_meta_category') != '0' || $displayData->params->get('list_meta_author') != '0' || $displayData->params->get('list_meta_created') != '0' || $displayData->params->get('list_meta_likes') != '0' || $displayData->params->get('list_meta_hits') != '0') : ?>
        <!-- Item Meta -->
        <dl class="media-info">
          <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
          <?php if ($displayData->params->get('list_meta_author') != '0' || $displayData->params->get('list_meta_created') != '0') : ?>
            <dd class="media-info-createdby">
              <?php if ($displayData->params->get('list_meta_author') != '0') : ?><?php echo JText::sprintf('COM_HWDMS_BY_X_USER', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($item->created_user_id)).'">'.htmlspecialchars($item->author, ENT_COMPAT, 'UTF-8').'</a>'); ?><?php endif; ?><?php if ($displayData->params->get('list_meta_created') != '0') : ?>, <?php echo JHtml::_('hwddate.relative', $item->created); ?><?php endif; ?>
            </dd>
          <?php endif; ?>
          <?php if ($displayData->params->get('list_meta_category') != '0' && $displayData->params->get('enable_categories') && (count($item->categories) > 0)) : ?>
            <dd class="media-info-category"><?php echo JText::sprintf('COM_HWDMS_IN_X_CATEGORY', hwdMediaShareCategory::renderCategories($item)); ?></dd>
          <?php endif; ?>              
          <?php if ($displayData->params->get('list_meta_hits') != '0') :?>
            <dd class="media-info-hits"><?php echo JText::sprintf('COM_HWDMS_X_VIEWS', number_format((int) $item->hits)); ?></dd>
          <?php endif; ?>
        </dl>
        <?php endif; ?> 
        
        <div class="clear"></div>
        
        <?php if ($displayData->params->get('list_meta_description') != '0' && !empty($item->description)) :?>
          <p class="media-info-description"> <?php echo $displayData->escape(JHtmlString::truncate($item->description, $displayData->params->get('list_desc_truncate'), false, false)); ?> </p>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>