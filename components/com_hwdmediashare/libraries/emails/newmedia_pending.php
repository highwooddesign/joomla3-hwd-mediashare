<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<font face="calibri" size="4">
<br>
This is an automated message from <?php echo $app->getCfg('sitename'); ?>. 
<br>
<br>
A new media item was <?php echo JText::sprintf('COM_HWDMS_CREATED_ON', JHtml::_('date', $row->created, $config->get('list_date_format'))); ?>.<br>
<br>
Media name: <?php echo $row->title; ?><br>
Description: <?php echo $row->description; ?><br>
<?php echo JText::sprintf('COM_HWDMS_CREATED_BY', $user->username);?><br>
<br>
This media is pending. You need to check this item for approval:<br>
<?php echo $linkPending; ?><br>
<br>
View this media in the administrator:<br>
<?php echo $linkAdmin; ?><br>
<br>
<br>
<br>
<i><small>To stop receiving these notifications set notifications to no
in the hwdMediaShare configuration.</small></i>
</font> 
