<?php
/**
 * @version    SVN $Id: default_related.php 795 2012-12-17 15:03:50Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      09-Nov-2011 16:21:17
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<dl class="search-results">
<?php foreach($this->related as $result) : ?>
    <dt class="result-title">
		<?php if ($result->href) :?>
			<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_top"<?php endif;?>>
                                <?php echo $this->escape(JHtmlString::truncate($result->title, $this->params->get('list_title_truncate'))); ?> 
			</a>
		<?php else:?>
			<?php echo $this->escape(JHtmlString::truncate($result->title, $this->params->get('list_title_truncate'))); ?> 
		<?php endif; ?>
	</dt>
	<?php if ($result->section) : ?>
		<dd class="result-category">
			<span class="small<?php echo $this->pageclass_sfx; ?>">
				(<?php echo $this->escape($result->section); ?>)
			</span>
		</dd>
	<?php endif; ?>
	<dd class="result-text">
		<div class="image-left" style="max-width:100px;">
		<a href="<?php echo JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($result->id)); ?>">
		<img src="<?php echo JRoute::_(hwdMediaShareDownloads::thumbnail($result)); ?>" border="0" alt="<?php echo $this->escape($result->title); ?>" style="max-width:100%;" />
		</a>
		</div>           
                <?php echo JHtmlString::truncate($result->text, $this->params->get('list_desc_truncate'), true, false); ?>
		<div class="clear"></div>
	</dd>
	<?php if ($this->params->get('show_date')) : ?>
		<dd class="result-created<?php echo $this->pageclass_sfx; ?>">
			<?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?>
		</dd>
	<?php endif; ?>
<?php endforeach; ?>
</dl>