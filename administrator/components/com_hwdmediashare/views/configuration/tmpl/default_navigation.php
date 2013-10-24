<?php
/**
 * @version    SVN $Id: default_navigation.php 411 2012-06-12 17:44:12Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access
defined('_JEXEC') or die;
?>
<div id="submenu-box">
	<div class="submenu-box">
		<div class="submenu-pad">
			<ul id="submenu" class="hwdmediashare-configuration">
				<li><a href="#" onclick="return false;" id="site"><?php echo JText::_('COM_HWDMS_SITE'); ?></a></li>
				<li><a href="#" onclick="return false;" id="media"><?php echo JText::_('COM_HWDMS_MEDIA'); ?></a></li>
				<li><a href="#" onclick="return false;" id="processing"><?php echo JText::_('COM_HWDMS_PROCESSING'); ?></a></li>
				<li><a href="#" onclick="return false;" id="permissions"><?php echo JText::_('COM_HWDMS_PERMISSIONS'); ?></a></li>
                                <li><a href="#" onclick="return false;" id="layout"><?php echo JText::_('COM_HWDMS_LAYOUT'); ?></a></li>
                                <li><a href="#" onclick="return false;" id="integrations"><?php echo JText::_('COM_HWDMS_INTEGRATIONS'); ?></a></li>
                                <li><a href="#" onclick="return false;" id="uploads"><?php echo JText::_('COM_HWDMS_UPLOADS'); ?></a></li>
                        </ul>
			<div class="clr"></div>
		</div>
	</div>
	<div class="clr"></div>
</div>