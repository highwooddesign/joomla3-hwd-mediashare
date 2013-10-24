<?php
/**
 * @version    SVN $Id: default_layout.php 1612 2013-07-15 12:55:50Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      03-Jan-2012 15:20:20
 */

// No direct access
defined('_JEXEC') or die;

?>
<div class="width-50 fltlft">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_GLOBAL_LIST_LAYOUT'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('list_item_heading'); ?>
                    <?php echo $this->form->getInput('list_item_heading'); ?></li>
                    <li><?php echo $this->form->getLabel('list_thumbnail_size'); ?>
                    <?php echo $this->form->getInput('list_thumbnail_size'); ?></li>
                    <li><?php echo $this->form->getLabel('list_thumbnail_aspect'); ?>
                    <?php echo $this->form->getInput('list_thumbnail_aspect'); ?></li>                    
                    <li><?php echo $this->form->getLabel('list_default_display'); ?>
                    <?php echo $this->form->getInput('list_default_display'); ?></li>
                    <li><?php echo $this->form->getLabel('list_columns'); ?>
                    <?php echo $this->form->getInput('list_columns'); ?></li>
                    <li><?php echo $this->form->getLabel('list_limit'); ?>
                    <?php echo $this->form->getInput('list_limit'); ?></li>
                    <li><?php echo $this->form->getLabel('list_date_format'); ?>
                    <?php echo $this->form->getInput('list_date_format'); ?></li>
                    <li><?php echo $this->form->getLabel('list_date_field'); ?>
                    <?php echo $this->form->getInput('list_date_field'); ?></li>
                    <li><?php echo $this->form->getLabel('list_link_titles'); ?>
                    <?php echo $this->form->getInput('list_link_titles'); ?></li>
                    <li><?php echo $this->form->getLabel('list_link_thumbnails'); ?>
                    <?php echo $this->form->getInput('list_link_thumbnails'); ?></li>
                    <li><?php echo $this->form->getLabel('list_title_truncate'); ?>
                    <?php echo $this->form->getInput('list_title_truncate'); ?></li>
                    <li><?php echo $this->form->getLabel('list_desc_truncate'); ?>
                    <?php echo $this->form->getInput('list_desc_truncate'); ?></li>
                    <li><?php echo $this->form->getLabel('list_tooltip_location'); ?>
                    <?php echo $this->form->getInput('list_tooltip_location'); ?></li>
                    <li><?php echo $this->form->getLabel('list_tooltip_contents'); ?>
                    <?php echo $this->form->getInput('list_tooltip_contents'); ?></li>
                    <li><?php echo $this->form->getLabel('list_details_button'); ?>
                    <?php echo $this->form->getInput('list_details_button'); ?></li> 
                    <li><?php echo $this->form->getLabel('list_gallery_button'); ?>
                    <?php echo $this->form->getInput('list_gallery_button'); ?></li> 
                    <li><?php echo $this->form->getLabel('list_list_button'); ?>
                    <?php echo $this->form->getInput('list_list_button'); ?></li> 
                    <li><?php echo $this->form->getLabel('list_tree_button'); ?>
                    <?php echo $this->form->getInput('list_tree_button'); ?></li>                     
                    <li><?php echo $this->form->getLabel('list_filter_search'); ?>
                    <?php echo $this->form->getInput('list_filter_search'); ?></li>                                       
                    <li><?php echo $this->form->getLabel('list_filter_media'); ?>
                    <?php echo $this->form->getInput('list_filter_media'); ?></li>                      
                    <li><?php echo $this->form->getLabel('list_filter_ordering'); ?>
                    <?php echo $this->form->getInput('list_filter_ordering'); ?></li>                      
                    <li><?php echo $this->form->getLabel('list_filter_pagination'); ?>
                    <?php echo $this->form->getInput('list_filter_pagination'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_title'); ?>
                    <?php echo $this->form->getInput('list_meta_title'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_thumbnail'); ?>
                    <?php echo $this->form->getInput('list_meta_thumbnail'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_type_icon'); ?>
                    <?php echo $this->form->getInput('list_meta_type_icon'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_duration'); ?>
                    <?php echo $this->form->getInput('list_meta_duration'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_description'); ?>
                    <?php echo $this->form->getInput('list_meta_description'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_category'); ?>
                    <?php echo $this->form->getInput('list_meta_category'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_author'); ?>
                    <?php echo $this->form->getInput('list_meta_author'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_likes'); ?>
                    <?php echo $this->form->getInput('list_meta_likes'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_hits'); ?>
                    <?php echo $this->form->getInput('list_meta_hits'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_created'); ?>
                    <?php echo $this->form->getInput('list_meta_created'); ?></li>
                    <li><?php echo $this->form->getLabel('list_meta_media_count'); ?>
                    <?php echo $this->form->getInput('list_meta_media_count'); ?></li> 
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_CATEGORY_LIST_LAYOUT'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('category_list_default_display'); ?>
                    <?php echo $this->form->getInput('category_list_default_display'); ?></li> 
                    <li><?php echo $this->form->getLabel('category_list_quick_view'); ?>
                    <?php echo $this->form->getInput('category_list_quick_view'); ?></li> 
                    <li><?php echo $this->form->getLabel('category_list_meta_category_desc'); ?>
                    <?php echo $this->form->getInput('category_list_meta_category_desc'); ?></li> 
                    <li><?php echo $this->form->getLabel('category_list_meta_subcategory_count'); ?>
                    <?php echo $this->form->getInput('category_list_meta_subcategory_count'); ?></li> 
                    <li><?php echo $this->form->getLabel('category_list_media_tooltip'); ?>
                    <?php echo $this->form->getInput('category_list_media_tooltip'); ?></li>
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_GLOBAL_ORDERING'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('list_order_media'); ?>
                    <?php echo $this->form->getInput('list_order_media'); ?></li> 
                    <li><?php echo $this->form->getLabel('list_order_category'); ?>
                    <?php echo $this->form->getInput('list_order_category'); ?></li> 
                    <li><?php echo $this->form->getLabel('list_order_album'); ?>
                    <?php echo $this->form->getInput('list_order_album'); ?></li>
                    <li><?php echo $this->form->getLabel('list_order_group'); ?>
                    <?php echo $this->form->getInput('list_order_group'); ?></li> 
                    <li><?php echo $this->form->getLabel('list_order_playlist'); ?>
                    <?php echo $this->form->getInput('list_order_playlist'); ?></li> 
                    <li><?php echo $this->form->getLabel('list_order_channel'); ?>
                    <?php echo $this->form->getInput('list_order_channel'); ?></li>     
                </ul>
        </fieldset>
</div>
<div class="width-50 fltrt">
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_GLOBAL_ITEM_LAYOUT'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('item_meta_title'); ?>
                    <?php echo $this->form->getInput('item_meta_title'); ?></li> 
                    <li><?php echo $this->form->getLabel('item_meta_thumbnail'); ?>
                    <?php echo $this->form->getInput('item_meta_thumbnail'); ?></li>
                    <li><?php echo $this->form->getLabel('item_meta_type_icon'); ?>
                    <?php echo $this->form->getInput('item_meta_type_icon'); ?></li> 
                    <li><?php echo $this->form->getLabel('item_meta_media_count'); ?>
                    <?php echo $this->form->getInput('item_meta_media_count'); ?></li> 
                    <li><?php echo $this->form->getLabel('item_meta_description'); ?>
                    <?php echo $this->form->getInput('item_meta_description'); ?></li> 
                    <li><?php echo $this->form->getLabel('item_meta_author'); ?>
                    <?php echo $this->form->getInput('item_meta_author'); ?></li>  
                    <li><?php echo $this->form->getLabel('item_meta_created'); ?>
                    <?php echo $this->form->getInput('item_meta_created'); ?></li>
                    <li><?php echo $this->form->getLabel('item_meta_hits'); ?>
                    <?php echo $this->form->getInput('item_meta_hits'); ?></li>
                    <li><?php echo $this->form->getLabel('item_meta_likes'); ?>
                    <?php echo $this->form->getInput('item_meta_likes'); ?></li>
                    <li><?php echo $this->form->getLabel('item_meta_report'); ?>
                    <?php echo $this->form->getInput('item_meta_report'); ?></li>
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_MEDIA_ITEM_LAYOUT'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('mediaitem_size'); ?>
                    <?php echo $this->form->getInput('mediaitem_size'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_meta_title'); ?>
                    <?php echo $this->form->getInput('mediaitem_meta_title'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_navigation'); ?>
                    <?php echo $this->form->getInput('mediaitem_navigation'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_subscribe_button'); ?>
                    <?php echo $this->form->getInput('mediaitem_subscribe_button'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_like_button'); ?>
                    <?php echo $this->form->getInput('mediaitem_like_button'); ?></li>  
                    <li><?php echo $this->form->getLabel('mediaitem_dislike_button'); ?>
                    <?php echo $this->form->getInput('mediaitem_dislike_button'); ?></li>                    
                    <li><?php echo $this->form->getLabel('mediaitem_favourite_button'); ?>
                    <?php echo $this->form->getInput('mediaitem_favourite_button'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_add_button'); ?>
                    <?php echo $this->form->getInput('mediaitem_add_button'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_share_button'); ?>
                    <?php echo $this->form->getInput('mediaitem_share_button'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_report_button'); ?>
                    <?php echo $this->form->getInput('mediaitem_report_button'); ?></li>  
                    <li><?php echo $this->form->getLabel('mediaitem_download_button'); ?>
                    <?php echo $this->form->getInput('mediaitem_download_button'); ?></li> 
                    <li><?php echo $this->form->getLabel('mediaitem_quality_button'); ?>
                    <?php echo $this->form->getInput('mediaitem_quality_button'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_meta_hits'); ?>
                    <?php echo $this->form->getInput('mediaitem_meta_hits'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_meta_author'); ?>
                    <?php echo $this->form->getInput('mediaitem_meta_author'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_meta_created'); ?>
                    <?php echo $this->form->getInput('mediaitem_meta_created'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_meta_likes'); ?>
                    <?php echo $this->form->getInput('mediaitem_meta_likes'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_description_tab'); ?>
                    <?php echo $this->form->getInput('mediaitem_description_tab'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_related_tab'); ?>
                    <?php echo $this->form->getInput('mediaitem_related_tab'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_location_tab'); ?>
                    <?php echo $this->form->getInput('mediaitem_location_tab'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_tags_tab'); ?>
                    <?php echo $this->form->getInput('mediaitem_tags_tab'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_associations_tab'); ?>
                    <?php echo $this->form->getInput('mediaitem_associations_tab'); ?></li>   
                    <li><?php echo $this->form->getLabel('mediaitem_activity'); ?>
                    <?php echo $this->form->getInput('mediaitem_activity'); ?></li>   
                </ul>
        </fieldset>
        <fieldset class="adminform">
                <legend><?php echo JText::_('COM_HWDMS_GROUP_ITEM_LAYOUT'); ?></legend>
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('groupitem_media_count'); ?>
                    <?php echo $this->form->getInput('groupitem_media_count'); ?></li>                     
                    <li><?php echo $this->form->getLabel('groupitem_member_count'); ?>
                    <?php echo $this->form->getInput('groupitem_member_count'); ?></li>   
                    <li><?php echo $this->form->getLabel('groupitem_join_button'); ?>
                    <?php echo $this->form->getInput('groupitem_join_button'); ?></li>   
                    <li><?php echo $this->form->getLabel('groupitem_media_map'); ?>
                    <?php echo $this->form->getInput('groupitem_media_map'); ?></li>  
                    <li><?php echo $this->form->getLabel('groupitem_group_activity'); ?>
                    <?php echo $this->form->getInput('groupitem_group_activity'); ?></li> 
                    <li><?php echo $this->form->getLabel('groupitem_group_members'); ?>
                    <?php echo $this->form->getInput('groupitem_group_members'); ?></li>   
                    <li><?php echo $this->form->getLabel('groupitem_group_media'); ?>
                    <?php echo $this->form->getInput('groupitem_group_media'); ?></li>   
                </ul>
        </fieldset>    
</div>