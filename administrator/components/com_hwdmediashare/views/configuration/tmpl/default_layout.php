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
?>
<div class="row-fluid">
        <div class="span6">
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_GLOBAL_LIST_LAYOUT'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_item_heading'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_item_heading'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_thumbnail_size'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_thumbnail_size'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_thumbnail_aspect'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_thumbnail_aspect'); ?></div>
                        </div>                    
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_default_display'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_default_display'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_columns'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_columns'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_limit'); ?></div> 
                                <div class="controls"><?php echo $this->form->getInput('list_limit'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_date_format'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_date_format'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_date_field'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_date_field'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_link_titles'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_link_titles'); ?></div>
                        </div>
                        <div class="control-group"> 
                                <div class="control-label"><?php echo $this->form->getLabel('list_link_thumbnails'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_link_thumbnails'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_title_truncate'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_title_truncate'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_desc_truncate'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_desc_truncate'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_tooltip_location'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_tooltip_location'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_tooltip_contents'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_tooltip_contents'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_details_button'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_details_button'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_gallery_button'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_gallery_button'); ?></div>
                        </div> 
                        <div class="control-group">  
                                <div class="control-label"><?php echo $this->form->getLabel('list_list_button'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_list_button'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_tree_button'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_tree_button'); ?></div>
                        </div>                     
                        <div class="control-group"> 
                                <div class="control-label"><?php echo $this->form->getLabel('list_filter_search'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_filter_search'); ?></div>
                        </div>                                       
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_filter_media'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_filter_media'); ?></div>
                        </div>                      
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_filter_ordering'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_filter_ordering'); ?></div>
                        </div>                      
                        <div class="control-group"> 
                                <div class="control-label"><?php echo $this->form->getLabel('list_filter_pagination'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_filter_pagination'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_title'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_title'); ?></div>
                        </div>
                        <div class="control-group"> 
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_thumbnail'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_thumbnail'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_type_icon'); ?></div> 
                                <div class="controls"><?php echo $this->form->getInput('list_meta_type_icon'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_duration'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_duration'); ?></div>
                        </div>
                        <div class="control-group"> 
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_description'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_description'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_category'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_category'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_author'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_author'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_likes'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_likes'); ?></div>
                        </div>
                        <div class="control-group"> 
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_hits'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_hits'); ?></div> 
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_created'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_created'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_meta_media_count'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_meta_media_count'); ?></div>
                        </div>                     
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_CATEGORY_LIST_LAYOUT'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('category_list_default_display'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('category_list_default_display'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('category_list_quick_view'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('category_list_quick_view'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('category_list_meta_category_desc'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('category_list_meta_category_desc'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('category_list_meta_subcategory_count'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('category_list_meta_subcategory_count'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('category_list_media_tooltip'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('category_list_media_tooltip'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_GLOBAL_ORDERING'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_order_media'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_order_media'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_order_album'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_order_album'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_order_group'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_order_group'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_order_playlist'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_order_playlist'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('list_order_channel'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('list_order_channel'); ?></div>
                        </div> 
                </fieldset>
        </div>
        <div class="span6">
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_GLOBAL_ITEM_LAYOUT'); ?></legend>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_title'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_title'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_thumbnail'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_thumbnail'); ?></div>
                        </div>
                        <div class="control-group"> 
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_type_icon'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_type_icon'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_media_count'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_media_count'); ?></div>
                        </div> 
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_description'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_description'); ?></div>
                        </div> 
                        <div class="control-group"> 
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_author'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_author'); ?></div>
                        </div>  
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_created'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_created'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_hits'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_hits'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_likes'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_likes'); ?></div>
                        </div>
                        <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('item_meta_report'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('item_meta_report'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_MEDIA_ITEM_LAYOUT'); ?></legend>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_size'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_size'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_meta_title'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_meta_title'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_navigation'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_navigation'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_subscribe_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_subscribe_button'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_like_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_like_button'); ?></div>
                        </div>  
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_dislike_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_dislike_button'); ?></div>
                        </div>                    
                        <div class="control-group"> 
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_favourite_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_favourite_button'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_add_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_add_button'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_share_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_share_button'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_report_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_report_button'); ?></div>
                        </div>  
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_download_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_download_button'); ?></div>
                        </div> 
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_quality_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_quality_button'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_meta_hits'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_meta_hits'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_meta_author'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_meta_author'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_meta_created'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_meta_created'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_meta_likes'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_meta_likes'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_description_tab'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_description_tab'); ?></div>
                        </div>    
                        <div class="control-group"> 
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_location_tab'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_location_tab'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_tags_tab'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_tags_tab'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_associations_tab'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_associations_tab'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('mediaitem_activity'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('mediaitem_activity'); ?></div>
                        </div>
                </fieldset>
                <fieldset class="form-horizontal">
                        <legend><?php echo JText::_('COM_HWDMS_GROUP_ITEM_LAYOUT'); ?></legend>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('groupitem_media_count'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('groupitem_media_count'); ?></div>
                        </div>                     
                        <div class="control-group"> 
                            <div class="control-label"><?php echo $this->form->getLabel('groupitem_member_count'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('groupitem_member_count'); ?></div>
                        </div>   
                        <div class="control-group"> 
                            <div class="control-label"><?php echo $this->form->getLabel('groupitem_join_button'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('groupitem_join_button'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('groupitem_media_map'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('groupitem_media_map'); ?></div>
                        </div>  
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('groupitem_group_activity'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('groupitem_group_activity'); ?></div>
                        </div> 
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('groupitem_group_members'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('groupitem_group_members'); ?></div>
                        </div>   
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('groupitem_group_media'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('groupitem_group_media'); ?></div>
                        </div>   
                </fieldset>
        </div>
</div>
