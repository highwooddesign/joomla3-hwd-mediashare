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

$app = JFactory::getApplication();
$user = JFactory::getUser();

JHtml::addIncludePath(JPATH_ROOT . '/administrator/components/com_hwdmediashare/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework', true);

$function  = $app->input->getCmd('function', 'jSelectMedia');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=media&layout=modal&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm" class="form-inline">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Header -->
    <div class="media-header">
      <!-- Search Filters -->
      <?php echo JLayoutHelper::render('search_tools', array('view' => $this), JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
      <div class="clear"></div>
    </div>
    <div class="media-<?php echo $this->display; ?>-view">
      <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
          <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
        </div>
      <?php else : ?>
        <table class="table table-striped table-hover">  
          <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
            <tr>
              <td>
                <div class="row-fluid">
                  <div class="span2">
                    <?php if ($this->params->get('list_meta_thumbnail') != '0') :?>
                    <div class="media-item">
                      <div class="media-aspect<?php echo $this->params->get('list_thumbnail_aspect'); ?>"></div>        
                      <?php if ($this->params->get('list_meta_type_icon') != '0') :?>
                      <div class="media-item-format-1-<?php echo $item->media_type; ?>">
                         <img src="<?php echo JHtml::_('hwdicon.overlay', '1-'.$item->media_type, $item); ?>" alt="<?php echo JText::_('COM_HWDMS_MEDIA_TYPE'); ?>" />
                      </div>
                      <?php endif; ?>
                      <?php if ($this->params->get('list_meta_duration') != '0' && $item->duration > 0) :?>
                      <div class="media-duration">
                         <?php echo hwdMediaShareMedia::secondsToTime($item->duration); ?>
                      </div>
                      <?php endif; ?>
                      <a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>');">
                         <img src="<?php echo JRoute::_(hwdMediaShareThumbnails::thumbnail($item)); ?>" border="0" alt="<?php echo $this->escape($item->title); ?>" class="media-thumb <?php echo ($this->params->get('list_tooltip_location') > '2' ? 'hasTooltip' : ''); ?>" title="<?php echo $this->escape($item->title); ?>::<?php echo ($this->params->get('list_tooltip_contents') != '0' ? $this->escape(JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false)) : ''); ?>" />
                      </a>
                    </div>
                    <?php endif; ?>
                  </div>
                  <div class="span10">
                    <?php if ($item->featured): ?>
                      <span class="label label-info pull-right"><?php echo JText::_('COM_HWDMS_FEATURED'); ?></span>
                    <?php endif; ?>
                    <?php if ($item->status != 1) : ?>
                      <span class="label pull-right"><?php echo $this->utilities->getReadableStatus($item); ?></span>
                    <?php endif; ?>
                    <?php if ($item->published != 1) : ?>
                      <span class="label pull-right"><?php echo JText::_('COM_HWDMS_UNPUBLISHED'); ?></span>
                    <?php endif; ?> 
                    <!-- Title -->
                    <?php if ($this->params->get('list_meta_title') != '0') :?>
                      <h<?php echo $this->params->get('list_item_heading'); ?> class="contentheading<?php echo ($this->params->get('list_tooltip_location') > '1' ? ' hasTooltip' : ''); ?>" title="<?php echo JHtml::tooltipText($item->title, ($this->params->get('list_tooltip_contents') != '0' ? JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false) : '')); ?>">
                        <a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>');">
                          <?php echo $this->escape(JHtmlString::truncate($item->title, $this->params->get('list_title_truncate'), false, false)); ?> 
                        </a>
                      </h<?php echo $this->params->get('list_item_heading'); ?>>
                    <?php endif; ?> 
                    <div class="clearfix"></div>
                    <?php if ($this->params->get('list_meta_hits') != '0') :?>
                    <div class="pull-right">
                      <?php if ($this->params->get('list_meta_hits') != '0') :?>
                        <div class="media-info-hits pull-right"><?php echo JText::sprintf('COM_HWDMS_X_VIEWS', number_format((int) $item->hits)); ?></div>
                      <?php endif; ?>
                      <?php if ($this->params->get('list_meta_likes') != '0') :?>
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
                      <?php if ($this->params->get('list_meta_author') != '0' || $this->params->get('list_meta_created') != '0') : ?>
                      <dd class="media-info-meta">
                        <?php if ($this->params->get('list_meta_author') != '0') : ?>
                          <span class="media-info-createdby">
                            <?php echo JText::sprintf('COM_HWDMS_BY_X_USER', $item->author); ?>
                          </span>
                        <?php endif; ?>
                        <?php if ($this->params->get('list_meta_created') != '0') : ?>
                          <span class="media-info-created">
                            <?php echo JHtml::_('hwddate.relative', $item->created); ?>
                          </span>
                        <?php endif; ?>
                      </dd>
                      <?php endif; ?>      
                      <?php if ($this->params->get('list_meta_description') != '0') :?>
                        <dd class="media-info-description"><?php echo $this->escape(JHtmlString::truncate($item->description, $this->params->get('list_desc_truncate'), false, false)); ?></dd>
                      <?php endif; ?>      
                    </dl>
                  </div>
                </div>  
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody> 
        </table>
      <?php endif; ?>        
    </div>  
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
</form>
