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

/**
 * This template is used to generate the body of the email notification sent when new groups
 * are added and automatically approved. The group object is contained in $group.
 */

// Load the HWD language file.
$lang = JFactory::getLanguage();
$lang->load('com_hwdmediashare', JPATH_SITE);

// Load HWD utilities.
hwdMediaShareFactory::load('utilities');
$utilities = hwdMediaShareUtilities::getInstance();
                
// Load the author.
$author = JFactory::getUser($group->created_user_id);

$link_site = $utilities->relToAbs(JRoute::_(hwdMediaShareHelperRoute::getGroupRoute($group->id)));
$link_admin = JURI::root() . "administrator/index.php?option=com_hwdmediashare&task=group.edit&id=" . $group->id;
$link_pending = JURI::root() . "administrator/index.php?option=com_hwdmediashare&view=groups&filter[status]=2";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title><?php echo $app->getCfg('sitename'); ?></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <style type="text/css">
        body {
            margin:5px;
            padding:0;
            font-family:Arial,sans-serif;
            font-size:14px;
            color:#333333;
        }
        h1 {
            margin:20px 0 10px 0;
            padding:0;
            font-size:30px;
        }          
        a {
            color:#468aca;
            text-decoration:none;
        } 
        a:hover,
        a:focus {
            color:#468aca;
        } 
        .meta {
            color:#a0a6b3;font-size:11px;line-height:100%;
        }
        .footer {
            clear:both;
            margin-top:30px;
            font-size:11px;
            color:#222;
        }
        .btn,
        .btn:hover {
            margin:0;
            padding:7px;
            background-color:#f7f0f0;
            border:1px solid #e6b8b8;
            display:inline-block;
            text-decoration:none;
            color:#2e3033;
            -webkit-border-radius:3px;
            -moz-border-radius:3px;
            border-radius:3px;            
        }         
    </style>
  </head>
  <body>
    <p><?php echo JText::sprintf('COM_HWDMS_EMAIL_NEWGROUP_INTRO', '<a href="' . JURI::root() . '" target="_blank">' . $app->getCfg('sitename') . '</a>'); ?></p>
    <h1><a href="<?php echo $link_site; ?>" target="_blank"><?php echo $group->title; ?></a></h1>
    <p class="meta"><?php echo JText::sprintf('COM_HWDMS_CREATED_ON', JHtml::_('date', $group->created, $config->get('list_date_format'))); ?></p>
    <p class="meta"><?php echo JText::sprintf('COM_HWDMS_CREATED_BY', $author->name);?></p>
    <p><?php echo JHtml::_('string.truncate', $group->description, 200, false, false); ?></p>
    <div>
      <a href="<?php echo $link_admin; ?>" class="btn" target="_blank"><?php echo JText::_('COM_HWDMS_EMAIL_BUTTON_MANAGE'); ?></a>
    </div>
    <p class="footer"><?php echo JText::sprintf('COM_HWDMS_EMAIL_FOOTER', '<a href="' . JURI::root() . '" target="_blank">' . $app->getCfg('sitename') . '</a>'); ?></p>
  </body>
</html>