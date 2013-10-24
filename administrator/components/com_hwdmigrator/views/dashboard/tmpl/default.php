<?php
/**
 * @version    SVN $Id: default.php 481 2012-08-21 16:28:14Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmigrator'); ?>" method="post" name="adminForm" id="adminForm">
        <table class="adminlist">
                <thead>
                        <tr>
                                <th>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_RESOURCE' ); ?>
                                </th>
                                <th width="5%">
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_TOTAL' ); ?>
                                </th>
                                <th width="10%">
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_PROGRESS' ); ?>
                                </th>
                                <th width="5%">
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_STATUS' ); ?>
                                </th>
                        </tr>
                </thead>
                <tbody>
                        <tr class="row0">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_VIDEO_ITEMS' ); ?>
                                </td>
                                <td align="center">
                                        <?php echo $this->video_items->count; ?>
                                </td>                                
                                <td align="center">
                                        <?php echo $this->video_items->migrated; ?>/<?php echo $this->video_items->count; ?>
                                </td>
                                <td align="center">
                                        <div id="ajax-container-videoitems"></div>
                                </td>
                        </tr>
                        <tr class="row1">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_VIDEO_CATEGORIES' ); ?>
                                </td>
                                <td align="center">
                                        <?php echo $this->video_categories->count; ?>
                                </td>  
                                <td align="center">
                                        <?php echo $this->video_categories->migrated; ?>/<?php echo $this->video_categories->count; ?>
                                </td>
                                <td align="center">
                                        <div id="ajax-container-videocategories"></div>
                                </td>
                        </tr>
                        <tr class="row0">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_VIDEO_GROUPS' ); ?>
                                </td>
                                <td align="center">
                                        <?php echo $this->video_groups->count; ?>
                                </td>  
                                <td align="center">
                                        <?php echo $this->video_groups->migrated; ?>/<?php echo $this->video_groups->count; ?>
                                </td>
                                <td align="center">
                                        <div id="ajax-container-videogroups"></div>
                                </td>
                        </tr>
                        <tr class="row1">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_VIDEO_PLAYLISTS' ); ?>
                                </td>
                                <td align="center">
                                        <?php echo $this->video_playlists->count; ?>
                                </td>  
                                <td align="center">
                                        <?php echo $this->video_playlists->migrated; ?>/<?php echo $this->video_playlists->count; ?>
                                </td>
                                <td align="center">
                                        <div id="ajax-container-videoplaylists"></div>
                                </td>
                        </tr>                        
                        <tr class="row0">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_PHOTO_ITEMS' ); ?>
                                </td>
                                <td align="center">
                                        <?php echo $this->photo_items->count; ?>
                                </td>  
                                <td align="center">
                                        <?php echo $this->photo_items->migrated; ?>/<?php echo $this->photo_items->count; ?>
                                </td>
                                <td align="center">
                                        <div id="ajax-container-photoitems"></div>
                                </td>
                        </tr>   
                        <tr class="row1">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_PHOTO_CATEGORIES' ); ?>
                                </td>
                                <td align="center">
                                        <?php echo $this->photo_categories->count; ?>
                                </td>  
                                <td align="center">
                                        <?php echo $this->photo_categories->migrated; ?>/<?php echo $this->photo_categories->count; ?>
                                </td>
                                <td align="center">
                                        <div id="ajax-container-photocategories"></div>
                                </td>
                        </tr> 
                        <tr class="row0">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_PHOTO_GROUPS' ); ?>
                                </td>
                                <td align="center">
                                        <?php echo $this->photo_groups->count; ?>
                                </td>  
                                <td align="center">
                                        <?php echo $this->photo_groups->migrated; ?>/<?php echo $this->photo_groups->count; ?>
                                </td>
                                <td align="center">
                                        <div id="ajax-container-photogroups"></div>
                                </td>
                        </tr> 
                        <tr class="row1">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_PHOTO_ALBUMS' ); ?>
                                </td>
                                <td align="center">
                                        <?php echo $this->photo_albums->count; ?>
                                </td>  
                                <td align="center">
                                        <?php echo $this->photo_albums->migrated; ?>/<?php echo $this->photo_albums->count; ?>
                                </td>
                                <td align="center">
                                        <div id="ajax-container-photoalbums"></div>
                                </td>
                        </tr>   
                        <tr class="row0">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_MATCH_VIDEO_CATEGORIES' ); ?>
                                </td>
                                <td align="center">
                                        N/A
                                </td>  
                                <td align="center">
                                        N/A
                                </td>
                                <td align="center">
                                        <div id="ajax-container-matchvideocategories"></div>
                                </td>
                        </tr> 
                        <tr class="row1">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_MATCH_VIDEO_TAGS' ); ?>
                                </td>
                                <td align="center">
                                        N/A
                                </td>  
                                <td align="center">
                                        N/A
                                </td>
                                <td align="center">
                                        <div id="ajax-container-matchvideotags"></div>
                                </td>
                        </tr> 
                        <tr class="row0">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_MATCH_VIDEO_GROUPS' ); ?>
                                </td>
                                <td align="center">
                                        N/A
                                </td>  
                                <td align="center">
                                        N/A
                                </td>
                                <td align="center">
                                        <div id="ajax-container-matchvideogroups"></div>
                                </td>
                        </tr> 
                        <tr class="row1">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_MATCH_VIDEO_PLAYLISTS' ); ?>
                                </td>
                                <td align="center">
                                        N/A
                                </td>  
                                <td align="center">
                                        N/A
                                </td>
                                <td align="center">
                                        <div id="ajax-container-matchvideoplaylists"></div>
                                </td>
                        </tr> 
                        <tr class="row0">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_MATCH_PHOTO_CATEGORIES' ); ?>
                                </td>
                                <td align="center">
                                        N/A
                                </td>  
                                <td align="center">
                                        N/A
                                </td>
                                <td align="center">
                                        <div id="ajax-container-matchphotocategories"></div>
                                </td>
                        </tr> 
                        <tr class="row1">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_MATCH_PHOTO_TAGS' ); ?>
                                </td>
                                <td align="center">
                                        N/A
                                </td>  
                                <td align="center">
                                        N/A
                                </td>
                                <td align="center">
                                        <div id="ajax-container-matchphototags"></div>
                                </td>
                        </tr>
                        <tr class="row0">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_MATCH_PHOTO_GROUPS' ); ?>
                                </td>
                                <td align="center">
                                        N/A
                                </td>  
                                <td align="center">
                                        N/A
                                </td>
                                <td align="center">
                                        <div id="ajax-container-matchphotogroups"></div>
                                </td>
                        </tr> 
                        <tr class="row1">
                                <td>
                                        <?php echo JText::_( 'COM_HWDMIGRATOR_MATCH_PHOTO_ALBUMS' ); ?>
                                </td>
                                <td align="center">
                                        N/A
                                </td>  
                                <td align="center">
                                        N/A
                                </td>
                                <td align="center">
                                        <div id="ajax-container-matchphotoalbums"></div>
                                </td>
                        </tr>       
                </tbody>
        </table>
	<input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="maintenance" />
        <?php echo JHtml::_('form.token'); ?>
</form>
