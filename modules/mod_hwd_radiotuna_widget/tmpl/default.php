<?php
/**
 * @package     Joomla.site
 * @subpackage  Module.mod_hwd_radiotuna_widget
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

?>
<a id="onlineRadioLink" href="http://radiotuna.com/">online radio</a>
<script type="text/javascript" src="http://radiotuna.com/OnlineRadioPlayer/EmbedRadio?playerParams=<?php echo urlencode($helper->getCode()); ?>&width=<?php echo $helper->params->get('playerSize', 240); ?>&height=<?php echo $helper->params->get('playerHeight', 292) ?>"></script>