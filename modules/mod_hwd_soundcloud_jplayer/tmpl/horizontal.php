<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_soundcloud_jplayer
 *
 * @copyright   (C) 2014 Joomlabuzz.com
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;
?>
<div class="hwd-soundcloud<?php echo $params->get('theme', '') == 'dark' ? ' black' : ''; ?><?php echo $params->get('position', '') == 'vertical' ? ' vertical' : ''; ?>">
    <div id="jquery_jplayer_<?php echo $helper->pid; ?>" class="jp-jplayer"></div>
    <div id="jp_container_<?php echo $helper->pid; ?>" class="jp-type-playlist">
        <div class="jp-gui">
            <div class="jp-interface">
                <ul class="jp-controls">
                    <li><a href="javascript:;" class="jp-previous" tabindex="1">previous</a></li>
                    <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
                    <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
                    <li><a href="javascript:;" class="jp-next" tabindex="1">next</a></li>
                </ul>
                <div>
                    <div class="jp-time-holder">
                        <div class="jp-current-time"></div>
                        <div class="jp-progress">
                                <div class="jp-seek-bar">
                                        <div class="jp-play-bar"></div>
                                </div>
                        </div>
                        <div class="jp-duration"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="jp-playlist">
            <ul>
                <li></li>
            </ul>
        </div>
        <div class="jp-no-solution">
                <span>Update Required</span>
                To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
        </div>
    </div>                         
</div>