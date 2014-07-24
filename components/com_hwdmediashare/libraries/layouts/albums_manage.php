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
    <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('albums.delete')">
      <i class="icon-ok"></i> <?php echo JText::_('COM_HWDMS_REMOVE'); ?>
    </button>
  </div>
  <div class="btn-group">
    <button type="button" class="btn" onclick="Joomla.submitbutton('albums.publish')">
      <i class="icon-cancel"></i> <?php echo JText::_('COM_HWDMS_PUBLISH'); ?>
    </button>
  </div>
  <div class="btn-group">
    <button type="button" class="btn" onclick="Joomla.submitbutton('albums.unpublish')">
      <i class="icon-cancel"></i> <?php echo JText::_('COM_HWDMS_UNPUBLISH'); ?>
    </button>
  </div>    
</div>
<table class="table table-striped table-hover">
  <thead>
    <th><?php echo JHtml::_('grid.checkall'); ?></th>
  </thead>       
  <tbody>
    <?php foreach ($displayData->items as $id => $item) :
    $rowcount = (((int)$id) % (int) $displayData->columns) + 1;
    $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
    $canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.album.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.album.'.$item->id) && ($item->created_user_id == $user->id)));
    $canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.album.'.$item->id);
    $canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.album.'.$item->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.album.'.$item->id) && ($item->created_user_id == $user->id)));
    ?>
    <tr>
      <td>
        <div class="row-fluid">
          <div class="span1">
            <?php echo JHtml::_('grid.id', $id, $item->id); ?>  
          </div>              
          <div class="span2">
            <?php if ($displayData->params->get('list_meta_thumbnail') != '0') :?>
            <div class="media-item">
              <div class="media-aspect<?php echo $displayData->params->get('list_thumbnail_aspect'); ?>"></div>        
              <!-- Media Type -->
              <?php if ($displayData->params->get('list_meta_thumbnail') != '0') :?>
              <?php if ($displayData->params->get('list_meta_type_icon') != '0') :?>
              <div class="media-item-format-2">
                 <img src="<?php echo JHtml::_('hwdicon.overlay', 2, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_ALBUM'); ?>" />
              </div>
              <?php endif; ?>
              <?php if ($displayData->params->get('list_link_thumbnails') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($item->slug)); ?>"><?php endif; ?>
                 <img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($item, 2)); ?>" border="0" alt="<?php echo $displayData->escape($item->title); ?>" class="media-thumb<?php echo ($displayData->params->get('list_tooltip_location') > '2' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($displayData->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $displayData->params->get('list_desc_truncate'), false, false) : '')); ?>" />
              <?php if ($displayData->params->get('list_link_thumbnails') == 1) :?></a><?php endif; ?>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
          <div class="span9">
            <?php if ($canEdit || $canDelete): ?>
            <div class="btn-group pull-right">
              <?php
              // Create dropdown items
              if ($canEdit) : 
                JHtml::_('hwddropdown.edit', $item->id, 'albumform'); 
              endif;    
              if ($canEditState) :
                $action = $item->published ? 'unpublish' : 'publish';
                JHtml::_('hwddropdown.' . $action, $item->id, 'albums'); 
              endif; 
              if ($canDelete) : 
                JHtml::_('hwddropdown.delete', $item->id, 'albums'); 
              endif;         
              // Render dropdown list
              echo JHtml::_('hwddropdown.render', $this->escape($item->title), ' btn-micro');
              ?>                    
            </div>
            <?php endif; ?>
            <?php if ($item->featured): ?>
              <span class="label label-info pull-right"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></span>
            <?php endif; ?>
            <?php if ($item->status != 1) : ?>
              <span class="label pull-right"><?php echo $displayData->utilities->getReadableStatus($item); ?></span>
            <?php endif; ?>
            <?php if ($item->published != 1) : ?>
              <span class="label pull-right"><?php echo JText::_('COM_HWDMS_UNPUBLISHED'); ?></span>
            <?php endif; ?> 
            <!-- Title -->
            <?php if ($displayData->params->get('list_meta_title') != '0') :?>
              <h<?php echo $displayData->params->get('list_item_heading'); ?> class="contentheading<?php echo ($displayData->params->get('list_tooltip_location') > '1' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($displayData->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $displayData->params->get('list_desc_truncate'), false, false) : '')); ?>">
                <?php if ($displayData->params->get('list_link_titles') == 1) :?><a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getAlbumRoute($item->slug)); ?>"><?php endif; ?>
                  <?php echo $displayData->escape(JHtmlString::truncate($item->title, $displayData->params->get('list_title_truncate'), false, false)); ?> 
                <?php if ($displayData->params->get('list_link_titles') == 1) :?></a><?php endif; ?>
              </h<?php echo $displayData->params->get('list_item_heading'); ?>>
            <?php endif; ?> 
            <div class="clearfix"></div>
            <?php if ($displayData->params->get('list_meta_hits') != '0') :?>
            <div class="pull-right">
              <?php if ($displayData->params->get('list_meta_hits') != '0') :?>
                <div class="media-info-hits pull-right"><?php echo JText::sprintf('COM_HWDMS_X_VIEWS', number_format((int) $item->hits)); ?></div>
              <?php endif; ?>
              <?php if ($displayData->params->get('list_meta_likes') != '0') :?>
                <div class="media-info-likes pull-right">      
                  <i class="icon-thumbs-up"></i> <span id="media-likes"><?php echo (int) $item->likes; ?></span>
                  <i class="icon-thumbs-down"></i> <span id="media-dislikes"><?php echo (int) $item->dislikes; ?></span>
                </div>
              <?php endif; ?>
            </div>       
            <?php endif; ?>    
            <!-- Item Meta -->
            <dl class="media-info">
              <dt class="media-info-term"><?php echo JText::_('COM_HWDMS_DETAILS'); ?> </dt>
              <?php if ($displayData->params->get('list_meta_author') != '0' || $displayData->params->get('list_meta_created') != '0') : ?>
              <dd class="media-info-meta">
                <?php if ($displayData->params->get('list_meta_author') != '0') : ?>
                  <span class="media-info-createdby">
                    <?php echo JText::sprintf('COM_HWDMS_BY_X_USER', '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getUserRoute($item->created_user_id)).'">'.htmlspecialchars($item->author, ENT_COMPAT, 'UTF-8').'</a>'); ?>
                  </span>
                <?php endif; ?>
                <?php if ($displayData->params->get('list_meta_created') != '0') : ?>
                  <span class="media-info-created">
                    <?php echo JHtml::_('hwddate.relative', $item->created); ?>
                  </span>
                <?php endif; ?>
              </dd>
              <?php endif; ?>      
              <?php if ($displayData->params->get('list_meta_description') != '0') :?>
                <dd class="media-info-description"><?php echo $displayData->escape(JHtmlString::truncate($item->description, $displayData->params->get('list_desc_truncate'), false, false)); ?></dd>
              <?php endif; ?>      
            </dl>
          </div>
        </div>  
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
