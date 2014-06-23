<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_soundcloud_audiobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;
?>
<iframe width="100%" height="<?php echo $helper->height; ?>" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo urlencode($helper->item); ?>&amp;color=<?php echo $helper->params->get('colour'); ?>&amp;auto_play=<?php echo $helper->params->get('autoplay'); ?>&amp;show_artwork=<?php echo $helper->params->get('artwork'); ?>"></iframe>
