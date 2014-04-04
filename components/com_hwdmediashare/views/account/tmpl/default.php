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

$user = JFactory::getUser();
$uri = JFactory::getURI();
$canEdit = ($user->authorise('core.edit', 'com_hwdmediashare.user.'.$this->user->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.user.'.$this->user->id) && ($this->user->id == $user->id)));
$canEditState = $user->authorise('core.edit.state', 'com_hwdmediashare.user.'.$this->user->id);
$canDelete = ($user->authorise('core.delete', 'com_hwdmediashare.user.'.$this->user->id) || ($user->authorise('core.edit.own', 'com_hwdmediashare.user.'.$this->user->id) && ($this->user->id == $user->id)));
$canAddMedia = ($user->authorise('hwdmediashare.upload', 'com_hwdmediashare') || $user->authorise('hwdmediashare.import', 'com_hwdmediashare'));
$canAddAlbum = $user->authorise('core.create', 'com_hwdmediashare');
$canAddGroup = $user->authorise('core.create', 'com_hwdmediashare');
$canAddPlaylist = $user->authorise('core.create', 'com_hwdmediashare');
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container"> <a name="top" id="top"></a>
    <!-- Media Navigation -->
    <?php echo hwdMediaShareHelperNavigation::getInternalNavigation(); ?>
    <!-- Media Main Navigation -->
    <div class="media-accountmenu">
      <ul class="media-accountnav">
        <li class="media-accountnav-media"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=media'); ?>"><?php echo JText::_('COM_HWDMS_MY_MEDIA'); ?> (<?php echo $this->user->nummedia; ?>)</a></li>
        <li class="media-accountnav-favourites"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=favourites'); ?>"><?php echo JText::_('COM_HWDMS_MY_FAVOURITES'); ?> (<?php echo $this->user->numfavourites; ?>)</a></li>
        <?php if ($this->params->get('enable_albums')): ?><li class="media-accountnav-albums"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=albums'); ?>"><?php echo JText::_('COM_HWDMS_MY_ALBUMS'); ?> (<?php echo $this->user->numalbums; ?>)</a></li><?php endif; ?>
        <?php if ($this->params->get('enable_groups')): ?><li class="media-accountnav-groups"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=groups'); ?>"><?php echo JText::_('COM_HWDMS_MY_GROUPS'); ?> (<?php echo $this->user->numgroups; ?>)</a></li><?php endif; ?>
        <?php if ($this->params->get('enable_playlists')): ?><li class="media-accountnav-playlists"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=playlists'); ?>"><?php echo JText::_('COM_HWDMS_MY_PLAYLISTS'); ?> (<?php echo $this->user->numplaylists; ?>)</a></li><?php endif; ?>
        <?php if ($this->params->get('enable_subscriptions')): ?><li class="media-accountnav-subscriptions"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=subscriptions'); ?>"><?php echo JText::_('COM_HWDMS_MY_SUBSCRIPTIONS'); ?> (<?php echo $this->user->numsubscriptions; ?>)</a></li><?php endif; ?>
        <?php if ($this->params->get('enable_groups')): ?><li class="media-accountnav-memberships"><a href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&view=account&layout=memberships'); ?>"><?php echo JText::_('COM_HWDMS_MY_MEMBERSHIPS'); ?> (<?php echo $this->user->nummemberships; ?>)</a></li><?php endif; ?>
      </ul>
    </div>      
    <!-- Media Header -->
    <div class="media-header">
      <?php if ($this->params->get('item_meta_title') != 'hide') :?>
        <h2 class="media-user-title"><?php echo $this->escape($this->page_title); ?></h2>
      <?php endif; ?>        
      <!-- Buttons -->
      <div class="btn-group pull-right">
        <a title="<?php echo JText::_('COM_HWDMS_MY_CHANNEL'); ?>" href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getUserRoute($user->id)); ?>" class="btn"><i class="icon-user"></i> <?php echo JText::_('COM_HWDMS_MY_CHANNEL'); ?></a>
        <a title="<?php echo JText::_('COM_HWDMS_EDIT_PROFILE'); ?>" href="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=userform.edit&id='.$user->id.'&return='.base64_encode($uri)); ?>" class="btn"><i class="icon-edit"></i> <?php echo JText::_('COM_HWDMS_EDIT_PROFILE'); ?></a> 
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
      <!-- Search Filters -->
      <?php echo JLayoutHelper::render('search_tools', array('view' => $this), JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
      <div class="clear"></div>
    </div>
    <div class="media-details-view">
        <?php echo JLayoutHelper::render($this->layout . '_manage', $this, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    </div>    
    <!-- Pagination -->
    <div class="pagination"> <?php echo $this->pagination->getPagesLinks(); ?> </div>
  </div>
</form>
