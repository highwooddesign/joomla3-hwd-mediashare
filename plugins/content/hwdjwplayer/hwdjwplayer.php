<?php
/**
 * @version    SVN $Id: hwdjwplayer.php 1584 2013-06-14 07:40:25Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      27-Feb-2012 16:29:22
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class plgContentHwdJwPlayer extends JPlugin
{
	/**
	 * An item.
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected $item = null;

        /**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int	The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		$app = JFactory::getApplication();
                
                // Simple admin check to determine whether plugin should process further
		// if ($app->isAdmin()) return true;
                
                // Simple performance check to determine whether plugin should process further
		if (strpos($article->text, 'hwdjw') === false) {
			return true;
		}
                
                //$this->config = new JRegistry( $this->params );
                $this->config = $this->params;

		// Expression to search for (positions)
		$regex	= '/{hwdjwplayer\s+(.*?)}/i';

		// Find all instances of plugin and put in $matches for loadposition
		// $matches[0] is full pattern match, $matches[1] is the position
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

                // No matches, skip this
		if ($matches)
                {
			foreach ($matches as $match)
                        {
                                $matcheslist =  explode(',', $match[1]);

                                $options = array();
                                $data = '';

                                foreach ($matcheslist as $list)
                                {
                                        $data.= "$list\n";
                                }

                                // Load default configuration
                                jimport( 'joomla.html.parameter' );
                                $this->config->merge( new JRegistry( $data ) );

				$align = (($this->config->get('align', 'left') == 'center') ? 'margin: 0 auto;' : 'float:'.$this->config->get('align', 'left').';');

                                // Check if the width is an integer value of something else - i.e. percentage
                                $width = (is_int($this->config->get('width', 640)) ? $this->config->get('width', 640).'px' : $this->config->get('width', 640));

                                $output = '';
				$output.= (($this->config->get('wrap', '0') == '0') ? '<div style="clear:both;"></div>' : '');
                                $output.= '<div class="media-content" style="width:'.$width.';'.$align.'">';
                                $output.= $this->_load();
                                $output.= '</div>';
				$output.= (($this->config->get('wrap', '0') == '0') ? '<div style="clear:both;"></div>' : '');
                                
                                // We prepare the match to put into preg_replace 
                                $match[0] = str_replace("?", "\?", $match[0]);

                                // We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\'), $article->text, 1);
			}
		}
	}

	protected function _load()
	{                
                if (!$this->config->get('provider'))
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }

                $doc =& JFactory::getDocument();
                
                // Version 6
                if ($this->config->get('version') == 'version6')
                {
                        $doc->addScript(JURI::root( true ).'/plugins/content/hwdjwplayer/assets/6/jwplayer.js');
                        if ($this->config->get('licensekey')) $doc->addScriptDeclaration('jwplayer.key="'.$this->config->get('licensekey').'";');
                        $id = rand();
                        ob_start();
                        ?>
<div id='mediaspace<?php echo $id; ?>'>This text will be replaced</div>
<script type='text/javascript'>
jwplayer('mediaspace<?php echo $id; ?>').setup({
    'width': '<?php echo $this->config->get('width', '640'); ?>',
    'height': '<?php echo $this->config->get('height', '480'); ?>',
    'image': '<?php echo $this->config->get('image'); ?>',
    'controls': '<?php echo $this->config->get('controls', 'true'); ?>',
    <?php if ($this->config->get('skin6') == 'custom'):
    echo ($this->config->get('customskin') ? "'skin': '".$this->config->get('customskin')."'," : '');
    elseif ($this->config->get('skin6')):
    echo ($this->config->get('skin6') ? "'skin': '".$this->config->get('skin6')."'," : '');
    endif; ?>
    'aspectratio': '<?php echo $this->config->get('aspectratio', '16:9'); ?>',
    <?php echo ($this->config->get('autostart', 'false') ? "'autostart': '".$this->config->get('autostart', 'false')."'," : ''); ?>
    'fallback': 'true',
    'mute': '<?php echo ($this->config->get('mute') ? 'true' : 'false'); ?>',
    'primary': '<?php echo (($this->config->get('fallback') == 3) ? 'flash' : 'html5'); ?>',
    'repeat': '<?php echo ($this->config->get('repeat') == 'none' ? 'false' : 'true'); ?>',
    'stretching': '<?php echo $this->config->get('stretching', 'uniform'); ?>',
    'flashplayer': '<?php echo JURI::root( true ).'/plugins/content/hwdjwplayer/assets/6/jwplayer.flash.swf'; ?>',
    'html5player': '<?php echo JURI::root( true ).'/plugins/content/hwdjwplayer/assets/6/jwplayer.html5.js'; ?>',
    'logo': {
        'file': '<?php echo $this->config->get('logofile'); ?>',
        'link': '<?php echo $this->config->get('logolink'); ?>',
        'hide': '<?php echo $this->config->get('logohide'); ?>',
        'margin': '<?php echo $this->config->get('logomargin'); ?>',
        'position': '<?php echo $this->config->get('logoposition'); ?>'
    },
    'abouttext': '<?php echo $this->config->get('abouttext', 'HWDMediaShare'); ?>',
    'aboutlink': '<?php echo $this->config->get('aboutlink', 'http://hwdmediashare.co.uk/'); ?>',
<?php if ($this->config->get('plugfacebook') == 1 || $this->config->get('plugtwitter') == 1 || $this->config->get('plugviral') == 1) : ?>
    'sharing': {}, 
<?php endif; ?>
<?php if ($this->config->get('videoadsclient') == 1) : ?>
  advertising: {
    client: '<?php echo $this->config->get('videoadsclient', 'vast'); ?>',
    tag: '<?php echo $this->config->get('videoadstag', ''); ?>'
  },
<?php endif; ?>
<?php if ($this->config->get('provider') == "rtmp") : ?>
    <?php // Need checking
    $streamer = $this->config->get('streamer');
    $length = strlen($streamer)-1;
    if($streamer{$length} == '/'){
    $file = $this->config->get('streamer').$this->config->get('file');
    }
    else{
    $file = $this->config->get('streamer').'/'.$this->config->get('file');
    }
    ?>
    'provider': 'rtmp',
    'file': '<?php echo $file; ?>',
<?php elseif ($this->config->get('provider') == "playlist") : ?>
    'provider': 'http',
    'playlist': '<?php echo $this->config->get('file'); ?>',
    'primary': 'flash'
<?php elseif ($this->config->get('provider') == "youtube") : ?>
    'provider': 'youtube',
    'file': '<?php echo $this->config->get('file'); ?>',
<?php elseif ($this->config->get('provider') == "sound") : ?>
    'provider': 'sound',
    'levels': [
        <?php echo ($this->config->get('mp3') ? "{ 'file': '".$this->config->get('mp3')."', type: 'mp3' }, // MP3 version\n" : ''); ?>
        <?php echo ($this->config->get('ogg') ? "{ 'file': '".$this->config->get('ogg')."', type: 'vorbis' }, // OGG version\n" : ''); ?>
    ],
<?php else : ?>
    'sources': [
        <?php echo ($this->config->get('mp4') ? "{ 'file': '".$this->config->get('mp4')."', type: 'mp4', label: 'HD MP4' }, // H.264 version\n" : ''); ?>
        <?php echo ($this->config->get('webm') ? "{ 'file': '".$this->config->get('webm')."', type: 'webm', label: 'HD WEBM' }, // WebM version\n" : ''); ?>
        <?php echo ($this->config->get('ogg') ? "{ 'file': '".$this->config->get('ogg')."', type: 'vorbis', label: 'HD OGG' }, // Ogg Theora version\n" : ''); ?>
        <?php echo ($this->config->get('flv') ? "{ 'file': '".$this->config->get('flv')."', type: 'flv', label: 'HD FLV' } // Flash version\n" : ''); ?>
    ],
<?php endif; ?>
});
</script>
                        <?php
                        $retval = ob_get_contents();
                        ob_end_clean();
                }
                // Version 5
                else
                {
                        $doc->addScript(JURI::root( true ).'/plugins/content/hwdjwplayer/assets/jwplayer.js');
                        $id = rand();
                        $player = ($this->config->get('plugviral', '0') == 1 ? 'player-viral.swf' : 'player.swf'); 
                        ob_start();
                        ?>
<div id='mediaspace<?php echo $id; ?>'>This text will be replaced</div>
<script type='text/javascript'>
jwplayer('mediaspace<?php echo $id; ?>').setup({
    'flashplayer': '<?php echo JURI::root( true ); ?>/plugins/content/hwdjwplayer/assets/<?php echo $player; ?>',
    'width': '<?php echo $this->config->get('width', '640'); ?>',
    'height': '<?php echo $this->config->get('height', '480'); ?>',
    'controlbar.position': '<?php echo $this->config->get('controlbarposition', 'over'); ?>',
    'controlbar.idlehide': <?php echo $this->config->get('controlbaridlehide', 'false'); ?>,
    'display.showmute': <?php echo $this->config->get('displayshowmute', 'false'); ?>,
    'dock': <?php echo ($this->config->get('dock', 'true') == 1 ? 'true' : 'false'); ?>,
    'icons': <?php echo ($this->config->get('icons', 'true') == 1 ? 'true' : 'false'); ?>,
    <?php if ($this->config->get('skin') == 'custom'):
    echo ($this->config->get('customskin') ? "'skin': '".$this->config->get('customskin')."'," : '');    
    elseif ($this->config->get('skin')):
    echo ($this->config->get('skin') ? "'skin': '".JURI::root( true )."/plugins/content/hwdjwplayer/assets/skins/".$this->config->get('skin')."/".$this->config->get('skin').".zip'," : '');    
    endif; ?>
    <?php echo ($this->config->get('autostart', 'false') ? "'autostart': '".$this->config->get('autostart', 'false')."'," : ''); ?>
    'bufferlength': <?php echo $this->config->get('bufferlength', 1); ?>,
    'mute': <?php echo $this->config->get('mute', 'false'); ?>,
    <?php echo ($this->config->get('skin') ? "'playerready': '".$this->config->get('playerready')."'," : ''); ?>
    'repeat': '<?php echo $this->config->get('repeat', 'none'); ?>',
    'shuffle': <?php echo $this->config->get('shuffle', 'false'); ?>,
    'smoothing': <?php echo $this->config->get('smoothing', 'true'); ?>,
    'stretching': '<?php echo $this->config->get('stretching', 'uniform'); ?>',
    'volume': <?php echo $this->config->get('volume', '90'); ?>,
    'logo.file': '<?php echo $this->config->get('logofile'); ?>',
    'logo.link': '<?php echo $this->config->get('logolink'); ?>',
    'logo.linktarget': '<?php echo $this->config->get('logolinktarget'); ?>',
    'logo.hide': '<?php echo $this->config->get('logohide'); ?>',
    'logo.margin': '<?php echo $this->config->get('logomargin'); ?>',
    'logo.position': '<?php echo $this->config->get('logoposition'); ?>',
    'logo.timeout': '<?php echo $this->config->get('logotimeout'); ?>',
    'logo.over': '<?php echo $this->config->get('logoover'); ?>',
    'logo.out': '<?php echo $this->config->get('logoout'); ?>',
    'image': '<?php echo $this->config->get('image'); ?>',
<?php if ($this->config->get('provider') == "rtmp") : ?>
    'provider': 'rtmp',
    'file': '<?php echo $this->config->get('file'); ?>',
    'streamer': '<?php echo $this->config->get('streamer'); ?>',
<?php elseif ($this->config->get('provider') == "playlist") : ?>
    'provider': 'http',
    'playlistfile': '<?php echo $this->config->get('file'); ?>',
    'playlist.position': '<?php echo $this->config->get('playlistposition', 'none'); ?>',
    'playlist.size': '<?php echo $this->config->get('playlistsize', 180); ?>',
<?php elseif ($this->config->get('provider') == "youtube") : ?>
    'provider': 'youtube',
    'file': '<?php echo $this->config->get('file'); ?>',
<?php elseif ($this->config->get('provider') == "sound") : ?>
    'provider': 'sound',
    'levels': [
        <?php echo ($this->config->get('mp3') ? "{ 'file': '".$this->config->get('mp3')."', type: 'audio/mpeg' }, // MP3 version\n" : ''); ?>
        <?php echo ($this->config->get('ogg') ? "{ 'file': '".$this->config->get('ogg')."', type: 'audio/ogg' }, // OGG version\n" : ''); ?>
    ],   
<?php else : ?>
    'provider':'http',    
    'levels': [
        <?php echo ($this->config->get('mp4') ? "{ 'file': '".$this->config->get('mp4')."', type: 'video/mp4' }, // H.264 version\n" : ''); ?>
        <?php echo ($this->config->get('webm') ? "{ 'file': '".$this->config->get('webm')."', type: 'video/webm' }, // WebM version\n" : ''); ?>
        <?php echo ($this->config->get('ogg') ? "{ 'file': '".$this->config->get('ogg')."', type: 'video/ogg' }, // Ogg Theora version\n" : ''); ?>
        <?php echo ($this->config->get('flv') ? "{ 'file': '".$this->config->get('flv')."', type: 'video/flv' } // Flash version\n" : ''); ?>
    ],
<?php endif; ?>
    'modes': [
        <?php if ($this->config->get('fallback', '3') == "3") : ?>
        { 'type': 'flash', src: '<?php echo JURI::root( true ); ?>/plugins/content/hwdjwplayer/assets/<?php echo $player; ?>' },
        { 'type': 'html5' }
        <?php else : ?>
        { 'type': 'html5' },
        { 'type': 'flash', src: '<?php echo JURI::root( true ); ?>/plugins/content/hwdjwplayer/assets/<?php echo $player; ?>' }
        <?php endif; ?>
    ],
    'plugins': {
    '': '' // This is a dummy plugin to allow us more easily to insert correct syntax
    <?php if ($this->config->get('plugfacebook') == 1 && (!is_int($this->config->get('width', 640)) || $this->config->get('width', 640) > 200) && (!is_int($this->config->get('height', 300)) || $this->config->get('height', 300) > 200)) : ?>
    ,'fbit-1': {}
    <?php endif; ?>
    <?php if ($this->config->get('plugtwitter') == 1 && (!is_int($this->config->get('width', 640)) || $this->config->get('width', 640) > 200) && (!is_int($this->config->get('height', 300)) || $this->config->get('height', 300) > 200)) : ?>
    ,'tweetit-1': {}
    <?php endif; ?>
    <?php if ($this->config->get('plughd') == 1 && (!is_int($this->config->get('width', 640)) || $this->config->get('width', 640) > 200)) : ?>
    ,'hd-2': {
        <?php echo ($this->config->get('hdfile')       ? "'file': '".$this->config->get('hdfile')."',\n" : ""); ?>
        <?php echo ($this->config->get('hdstate')      ? "'state': true,\n" : "'state': false,\n"); ?>
        <?php echo ($this->config->get('hdfullscreen') ? "'fullscreen': true,\n" : "'fullscreen': false,\n"); ?>
    }
    <?php endif; ?> 
    <?php if ($this->config->get('plugltas') == 1) : ?>
    ,'ltas': {
        <?php echo ($this->config->get('ltas_channel')? "'cc': '".$this->config->get('ltas_channel')."'\n" : '' ); ?>
    }
    <?php endif; ?>   
    <?php if ($this->config->get('pluginvideous') == 1) : ?>
    ,'http://plugin.invideous.com/v5/invideous.swf': {
        <?php echo ($this->config->get('invideouspid')? "'pid': '".$this->config->get('invideouspid')."'\n" : '' ); ?>
    }
    <?php endif; ?>  
    }    
});
</script>
                        <?php
                        $retval = ob_get_contents();
                        ob_end_clean();
                }
                
		return $retval;
	}
}
