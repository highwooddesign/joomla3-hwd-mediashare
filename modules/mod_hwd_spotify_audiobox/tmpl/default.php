<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_spotify_audiobox
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;
?>
<div class="mod_hwd_spotify_audiobox">
    <iframe src="https://embed.spotify.com/?uri=<?php echo $helper->params->get('url','spotify:album:0O82niJ0NpcptYRxogeEZu'); ?>&view=<?php echo $helper->params->get('view', 'list'); ?>&theme=<?php echo $helper->params->get('theme', 'dark'); ?>" width="100%" height="<?php echo $helper->params->get('player') == "compact" ? 80: 380; ?>" frameborder="0" allowtransparency="true"></iframe>
</div>