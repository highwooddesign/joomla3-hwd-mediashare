<?php
/**
 * @version    SVN $Id: hwdflowplayer.php 1587 2013-06-14 07:44:36Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      27-Feb-2012 16:29:22
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class plgContentHwdFlowPlayer extends JPlugin
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
		if (strpos($article->text, 'hwdflow') === false) {
			return true;
		}
                
                //$this->config = new JRegistry( $this->params );
                $this->config = $this->params;

		// Expression to search for (positions)
		$regex	= '/{hwdflowplayer\s+(.*?)}/i';

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

                                $output = '';
				$output.= (($this->config->get('wrap', '0') == '0') ? '<div style="clear:both;"></div>' : '');
                                $output.= '<div class="media-content" style="width:'.$this->config->get('width', 640).'px;'.$align.'">';
                                $output.= $this->_load();
                                $output.= '</div>';
				$output.= (($this->config->get('wrap', '0') == '0') ? '<div style="clear:both;"></div>' : '');
                                
                                // We prepare the match to put into preg_replace 
                                $match[0] = str_replace("?", "\?", $match[0]);

                                // We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\'), $article->text, 1);
				
                                // Shall we replace a license?
                                $license = $this->config->get('licensekey');
                                if (!empty($license)) $article->text = preg_replace("|flowlicensecontainer|", '\\'.$this->config->get('licensekey'), $article->text, 1);
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

                $id = rand();
                ob_start();
                if ($this->config->get('provider') == 'video') 
                {
                        $doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js');
                        $doc->addScript(JURI::base( true ).'/plugins/content/hwdflowplayer/assets/flowplayer-commercial.js');
                        $doc->addStyleDeclaration('.flowplayer .fp-logo {display: block;opacity: 1;}');
                        if ($this->config->get('skin') == 'custom')
                        {
                                $doc->addStyleSheet($this->config->get('customskin'));
                        }
                        else
                        {
                                $doc->addStyleSheet(JURI::base( true ).'/plugins/content/hwdflowplayer/assets/skin/'.$this->config->get('skin', 'minimalist').'.css');
                        }
                        ?>
<div class="hwdflow is-splash" style="background-image:url('<?php echo ($this->config->get('image') ?  $this->config->get('image') : ''); ?>');width:<?php echo intval ($this->config->get('width')); ?>px;">
   <video<?php echo ($this->config->get('autostart', '1') == '1' ? ' autostart' : ''); ?><?php echo ($this->config->get('loop', '1') == '1' ? ' loop' : ''); ?>>
        <?php echo ($this->config->get('mp4') ?  '<source type="video/mp4" src="'.$this->config->get('mp4').'"/>' : ''); ?>
        <?php echo ($this->config->get('webm') ? '<source type="video/webm" src="'.$this->config->get('webm').'"/>' : ''); ?>
        <?php echo ($this->config->get('ogg') ?  '<source type="video/ogg" src="'.$this->config->get('ogg').'"/>' : ''); ?>
        <?php echo ($this->config->get('flv') ?  '<source type="video/flash" src="'.$this->config->get('flv').'"/>' : ''); ?>
   </video>
</div>
<script>
jQuery.noConflict();
jQuery(document).ready(function($) {
   // Install flowplayer to an element with CSS class "hwdflow"
   $(".hwdflow").flowplayer({ 
      swf: '<?php echo JURI::base( true ); ?>/plugins/content/hwdflowplayer/assets/flowplayer-commercial.swf'
      , ratio: <?php echo $this->config->get('video_aspect', 0.56); ?>
      , engine: '<?php echo ($this->config->get('fallback', '3') == '3' ? 'flash' : 'html5'); ?>'
      , debug: <?php echo ($this->config->get('debug', '1') == '1' ? 'true' : 'false'); ?>
      , key: 'flowlicensecontainer'
      , volume: '<?php echo $this->config->get('volume'); ?>'
      , muted: <?php echo ($this->config->get('muted', '1') == '1' ? 'true' : 'false'); ?>
      , logo: '<?php echo $this->config->get('logofile'); ?>'
   });     
});
</script>
                        <?php
                } 
                elseif ($this->config->get('provider') == 'audio')
                {
                        $doc->addScript(JURI::base( true ).'/plugins/content/hwdflowplayer/assets/flash/flowplayer-3.2.12.min.js');
                        ?>
<a href="#" 
   style="display:block;width:<?php echo intval ($this->config->get('width')); ?>px;height:<?php echo intval ($this->config->get('width')); ?>px;"
   id="player">
</a>
<script language="JavaScript">
flowplayer("player", "<?php echo JURI::base( true ); ?>/plugins/content/hwdflowplayer/assets/flash/flowplayer-3.2.16.swf", {
     clip: {
       url: "<?php echo ($this->config->get('mp3') ?  $this->config->get('mp3') : ''); ?>",
 
       // this style of configuring the cover image was added in audio
       // plugin version 3.2.3
       coverImage: { url: "<?php echo ($this->config->get('image') ?  $this->config->get('image') : ''); ?>",
                     scaling: 'orig' }
    }
});
</script>
                        <?php 
                }
                elseif ($this->config->get('provider') == 'rtmp' || $this->config->get('provider') == 'hls')
                {
                        $doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js');
                        $doc->addScript(JURI::base( true ).'/plugins/content/hwdflowplayer/assets/flowplayer.js');
                        if ($this->config->get('skin') == 'custom')
                        {
                                $doc->addStyleSheet($this->config->get('customskin'));
                        }
                        else
                        {
                                $doc->addStyleSheet(JURI::base( true ).'/plugins/content/hwdflowplayer/assets/skin/'.$this->config->get('skin', 'minimalist').'.css');
                        }
                        ?>
<div class="hwdflow is-splash minimalist" data-rtmp="<?php echo $this->config->get('streamer'); ?>" data-ratio="<?php echo $this->config->get('video_aspect', 0.56); ?>" style="background-image:url('<?php echo ($this->config->get('image') ?  $this->config->get('image') : ''); ?>');width:<?php echo intval ($this->config->get('width')); ?>px;">
   <video>
        <?php echo ($this->config->get('hls') ?  '<source type="application/x-mpegurl" src="'.$this->config->get('hls').'"/>' : ''); ?>
        <?php echo ($this->config->get('file') ?  '<source type="video/flash" src="'.$this->config->get('file').'"/>' : '<source type="video/flash" src="mp4:stsp">'); ?>
   </video>
</div>
<script>
jQuery.noConflict();
jQuery(document).ready(function($) {
   // install flowplayer to an element with CSS class "player"
   $(".hwdflow").flowplayer({
      swf: '//releases.flowplayer.org/5.4.3/commercial/flowplayer.swf'
      , ratio: <?php echo $this->config->get('video_aspect', 0.56); ?>
      , engine: 'flash'
      , debug: true});
});
</script>
                        <?php
                }
                $retval = ob_get_contents();
                ob_end_clean();

		return $retval;
	}
}
