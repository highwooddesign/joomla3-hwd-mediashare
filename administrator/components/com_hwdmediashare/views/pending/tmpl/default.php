<?php
/**
 * @version    SVN $Id: default.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      14-Feb-2012 14:43:29
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<div id="cpanel">
        <?php echo $this->addIcon('icon-48-media.png','index.php?option=com_hwdmediashare&view=media&filter_status=2', JText::sprintf('COM_HWDMS_N_PENDING_MEDIA', $this->media)) ;?>
        <?php echo $this->addIcon('icon-48-album.png','index.php?option=com_hwdmediashare&view=albums&filter_status=2', JText::sprintf('COM_HWDMS_N_PENDING_ALBUMS', $this->albums)) ;?>
        <?php echo $this->addIcon('icon-48-groups.png','index.php?option=com_hwdmediashare&view=groups&filter_status=2', JText::sprintf('COM_HWDMS_N_PENDING_GROUPS', $this->groups)) ;?>
        <?php echo $this->addIcon('icon-48-channels.png','index.php?option=com_hwdmediashare&view=users&filter_status=2', JText::sprintf('COM_HWDMS_N_PENDING_USERS', $this->users)) ;?>
        <?php echo $this->addIcon('icon-48-playlist.png','index.php?option=com_hwdmediashare&view=playlists&filter_status=2', JText::sprintf('COM_HWDMS_N_PENDING_PLAYLISTS', $this->playlists)) ;?>
        <?php echo $this->addIcon('icon-48-activities.png','index.php?option=com_hwdmediashare&view=activities&filter_status=2', JText::sprintf('COM_HWDMS_N_PENDING_ACTIVITIES', $this->activities)) ;?>
</div>
<div class="clr"></div>
