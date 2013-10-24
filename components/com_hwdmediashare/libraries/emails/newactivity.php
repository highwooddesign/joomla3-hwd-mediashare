<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div style="max-width:700px">
    <font face="Verdana" size="3">
        <br>
        <font color="#d44b38" size="6">Hi <?php echo $app->getCfg('sitename'); ?>! </font> 
        <br>
        <br>
        A user you subscribe to on <?php echo $app->getCfg('sitename'); ?> has new activity for <?php echo JHtml::_('date', $row->created, $config->get('list_date_format')); ?>.<br>
        <br>
        <br>
        <div style="padding:15px 30px;text-align:left;background-color:#E9E9E9">
            <font color="#d44b38" size="4">
                <img width="120" border="0" alt="User" src="/J25/media/com_hwdmediashare/files/70/26/c4/b62ae425a9df71fc5f041c7550d8b56b.jpg">
                <?php echo $user->username;?>'s new activity:</font> 
            <a target="_blank" href="<?php echo $linkFront; ?>" style="float:right;display:inline-block;padding:7px 15px;background-color:#d44b38;color:#fff;font-size:13px;font-weight:bold;border-radius:2px;border:solid 1px #c43b28;white-space:nowrap;text-decoration:none">View all activity</a>
        </div>        
        <div style="padding:15px 30px;text-align:left;background-color:#E9E9E9">
            <?php echo JText::sprintf('COM_HWDMS_CREATED');?> an album named: <a href="<?php echo $linkFront; ?>"><?php echo $row->title; ?></a><hr COLOR="#FFF">
            Added new media named: <a href="<?php echo $linkFront; ?>"><?php echo $row->title; ?></a><hr COLOR="#FFF">
            Added new media named: <a href="<?php echo $linkFront; ?>"><?php echo $row->title; ?></a><hr COLOR="#FFF">
            <?php echo JText::sprintf('COM_HWDMS_CREATED');?> a playlist named: <a href="<?php echo $linkFront; ?>"><?php echo $row->title; ?></a><hr COLOR="#FFF">
            Added new media named <a href="<?php echo $linkFront; ?>"><?php echo $row->title; ?></a> to the group <a href="<?php echo $linkFront; ?>"><?php echo $row->title; ?></a><hr COLOR="#FFF">
        </div>
        <br>
        <br>
        <br>
        <br>
        <font color="#777" size="2"><i>To stop receiving these notifications unsubscribe to this user at <?php echo $app->getCfg('sitename'); ?>.</i></font> 
    </font> 
</div>