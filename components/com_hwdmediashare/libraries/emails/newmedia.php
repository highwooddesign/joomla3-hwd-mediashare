<?php
/**
 * @version    SVN $Id: navigation.php 496 2012-08-29 13:26:32Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      05-Mar-2013 15:54:41
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * This template is used to generate the body of the email notification sent when new media
 * items are uploaded and automatically approved
 */

$owner = & JFactory::getUser($row->created_user_id);

// Load the HWDMediaShare language file
$lang =& JFactory::getLanguage();
$lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());
$lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);

hwdMediaShareFactory::load('utilities');
$utilities = hwdMediaShareUtilities::getInstance();

$linkFront = JURI::root() . "index.php?option=com_hwdmediashare&view=mediaitem&id=" . $row->id;
$linkAdmin = JURI::root() . "administrator/index.php?option=com_hwdmediashare&task=editmedia.edit&id=" . $row->id;
$linkPending = JURI::root() . "administrator/index.php?option=com_hwdmediashare&view=media&filter_status=2";
$linkConfig = JURI::root() . "administrator/index.php?option=com_hwdmediashare&view=configuration";
$linkPretty = str_replace("www.", "", JURI::root());
$linkPretty = str_replace("http://", "", $linkPretty);
$linkPretty = str_replace("https://", "", $linkPretty);
$linkPretty = rtrim($linkPretty, "/");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $app->getCfg('sitename'); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>

<table cellpadding="0" cellspacing="0" style="color:#2e3033;font-family:Arial,sans-serif;font-size:14px;margin:0;padding:0" width="100%">
	<tbody>
		<tr>
			<td align="left" valign="bottom"><a href="<?php echo JURI::root(); ?>" style="font-size:32px;color:#fff;text-decoration:none;color:#a0a6b3" target="_blank"><?php echo $app->getCfg('sitename'); ?></a></td>
			<td align="right" valign="bottom"><a href="<?php echo JURI::root(); ?>" style="font-size:12px;color:#fff;text-decoration:none;color:#a0a6b3" target="_blank"><?php echo $linkPretty; ?></a></td>
		</tr>
	</tbody>
</table>

<div style="margin-bottom:3em">
        <h1 style="font-weight:normal;line-height:1em;margin-bottom:14px;"><span style="color:#2e3033;font-size:24px">New media upload</span></h1>
        <div style="padding-bottom:1em">
                <table cellpadding="0" cellspacing="10" style="color:#2e3033;font-family:Arial,sans-serif;font-size:14px;margin:0;padding:0" width="100%">
                        <tbody>
                                <tr>
                                        <td align="left" valign="top">
                                        <div style="font-size:11px;margin-top:6px">
                                        <h4 style="font-weight:bold;margin:0 0 3px 0;padding:0"><span style="font-size:16px"><a href="<?php echo $linkFront; ?>" style="color:#293e66" target="_blank"><?php echo $row->title; ?></a></span></h4>
                                        <p style="line-height:1.3em;padding:0;margin:0 0 .7em 0"><span style="color:#a0a6b3;font-size:11px"><?php echo JText::sprintf('COM_HWDMS_CREATED_ON', JHtml::_('date', $row->created, $config->get('list_date_format'))); ?></p>

                                        <div style="margin:0.7em 0;font-size:12px;color:#444">
                                        <div>

                                        <p style="line-height:1.3em;padding:0;margin:0 0 .7em 0">
                                        <?php echo $row->description; ?>
                                        </p>
                                        </div>
                                        </div>

                                        <p style="line-height:1.3em;padding:0;margin:0 0 .7em 0;margin:0"><span style="color:#a0a6b3;font-size:11px"><img alt="<?php echo $owner->username; ?>" height="16" width="16" src="<?php echo JRoute::_($utilities->getAvatar(JFactory::getUser($owner->id))); ?>" border="0" /></span><span style="color:#a0a6b3;font-size:11px"> </span><span style="color:#a0a6b3;font-size:11px"><?php echo JText::sprintf('COM_HWDMS_CREATED_BY', $owner->username);?></span></p>

                                        <table border="0" cellpadding="4" cellspacing="0" style="color:#2e3033;font-family:Arial,sans-serif;font-size:14px;margin:0;padding:0;background-color:#f7f0f0;border:1px solid #e6b8b8;font-size:11px;margin:5px 0">
                                                <tbody>
                                                        <tr>
                                                                <td><a href="<?php echo $linkAdmin; ?>" style="color:#293e66" target="_blank">Manage</a></td>
                                                        </tr>
                                                </tbody>
                                        </table>
                                        </div>
                                        </td>
                                </tr>
                        </tbody>
                </table>
        </div>
</div>

<p style="line-height:1.3em;padding:0;margin:0 0 .7em 0"><span style="font-size:11px">You have been sent this email because you are registered at <?php echo $linkPretty; ?>, and your account is configured to received system email notifications. If you would like to stop receiving emails like this, you may </span><span style="font-size:11px"><a href="<?php echo $linkConfig; ?>" style="color:#293e66" target="_blank">set notifications to no in the hwdMediaShare configuration</a></span><span style="font-size:11px">.</span></p>

</body>
</html>