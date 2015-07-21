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

// Load Mootools JavaScript Framework.
JHtml::_('behavior.framework');
JHtml::_('behavior.core');

$user = JFactory::getUser();
$uri = JFactory::getURI();
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.channel.'.$this->user->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.channel.'.$this->user->id) && ($this->user->id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.channel.'.$this->user->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.channel.'.$this->user->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.channel.'.$this->user->id) && ($this->user->id == $user->id)));
$canAddMedia = ($user->authorise('hwdmediashare.upload', 'com_hwdmediashare') || $user->authorise('hwdmediashare.import', 'com_hwdmediashare'));
$canAddAlbum = $user->authorise('core.create', 'com_hwdmediashare');
$canAddGroup = $user->authorise('core.create', 'com_hwdmediashare');
$canAddPlaylist = $user->authorise('core.create', 'com_hwdmediashare');
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="<?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>    
    <!-- Media Header -->
    <div class="media-header">
      <h2 class="media-account-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h2>
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <?php if ($this->params->get('enable_channels') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_MY_CHANNEL'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getChannelRoute($user->id)); ?>" class="btn"><i class="icon-user"></i> <?php echo JText::_('COM_HWDMS_MY_CHANNEL'); ?></a>
        <?php endif; ?>
        <?php if ($canEdit && $this->params->get('enable_channels') != '0') : ?>
          <a title="<?php echo JText::_('COM_HWDMS_EDIT_PROFILE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=channelform.edit&id='.$user->id.'&return=' . $this->return); ?>" class="btn"><i class="icon-edit"></i> <?php echo JText::_('COM_HWDMS_EDIT_PROFILE'); ?></a>
        <?php endif; ?>             
        <?php if ($canAddMedia) : ?>
        <a title="<?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUploadRoute()); ?>" class="btn"><i class="icon-plus"></i> <?php echo JText::_('COM_HWDMS_ADD_MEDIA'); ?></a>
        <?php endif; ?>
        <?php
        // Create dropdown items 
        if ($canAddAlbum) : 
          JHtml::_('hwddropdown.add', 2, 'albumform'); 
        endif;                   
        if ($canAddGroup) : 
          JHtml::_('hwddropdown.add', 3, 'groupform'); 
        endif;  
        if ($canAddPlaylist) : 
          JHtml::_('hwddropdown.add', 4, 'playlistform'); 
        endif;  
        // Render dropdown list
        echo JHtml::_('hwddropdown.render', $this->escape($this->user->title));
        ?>                    
      </div>        
      <div class="clear"></div>
      <!-- Navigation -->
      <div class="media-tabmenu">
        <ul>
          <li class="<?php echo ($this->layout == 'media' ? 'active ' : false); ?>media-tabmenu-media"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=media'); ?>"><?php echo JText::_('COM_HWDMS_MY_MEDIA'); ?> (<?php echo $this->user->nummedia; ?>)</a></li>
          <li class="<?php echo ($this->layout == 'favourites' ? 'active ' : false); ?>media-tabmenu-favourites"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=favourites'); ?>"><?php echo JText::_('COM_HWDMS_MY_FAVOURITES'); ?> (<?php echo $this->user->numfavourites; ?>)</a></li>
          <?php if ($this->params->get('enable_albums')): ?><li class="<?php echo ($this->layout == 'albums' ? 'active ' : false); ?>media-tabmenu-albums"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=albums'); ?>"><?php echo JText::_('COM_HWDMS_MY_ALBUMS'); ?> (<?php echo $this->user->numalbums; ?>)</a></li><?php endif; ?>
          <?php if ($this->params->get('enable_groups')): ?><li class="<?php echo ($this->layout == 'groups' ? 'active ' : false); ?>media-tabmenu-groups"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=groups'); ?>"><?php echo JText::_('COM_HWDMS_MY_GROUPS'); ?> (<?php echo $this->user->numgroups; ?>)</a></li><?php endif; ?>
          <?php if ($this->params->get('enable_playlists')): ?><li class="<?php echo ($this->layout == 'playlists' ? 'active ' : false); ?>media-tabmenu-playlists"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=playlists'); ?>"><?php echo JText::_('COM_HWDMS_MY_PLAYLISTS'); ?> (<?php echo $this->user->numplaylists; ?>)</a></li><?php endif; ?>
          <?php if ($this->params->get('enable_subscriptions')): ?><li class="<?php echo ($this->layout == 'subscriptions' ? 'active ' : false); ?>media-tabmenu-subscriptions"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=subscriptions'); ?>"><?php echo JText::_('COM_HWDMS_MY_SUBSCRIPTIONS'); ?> (<?php echo $this->user->numsubscriptions; ?>)</a></li><?php endif; ?>
          <?php if ($this->params->get('enable_groups')): ?><li class="<?php echo ($this->layout == 'memberships' ? 'active ' : false); ?>media-tabmenu-memberships"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=memberships'); ?>"><?php echo JText::_('COM_HWDMS_MY_MEMBERSHIPS'); ?> (<?php echo $this->user->nummemberships; ?>)</a></li><?php endif; ?>
        </ul>
      </div>      
      <!-- Search Filters -->
      <?php echo JLayoutHelper::render('search_tools', array('view' => $this), JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
      <div class="clear"></div>
    </div>
    <div class="media-list-view">
      <?php echo JLayoutHelper::render($this->layout . '_manage', $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    </div>    
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
  <input type="hidden" name="return" value="<?php echo $this->return;?>" />
  <?php echo JHtml::_( 'form.token' ); ?>   
</form>
