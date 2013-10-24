<?php
/**
 * @version    SVN $Id: default_link.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Nov-2011 10:01:56
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
$user = JFactory::getUser();

?>
<div class="edit">
  <form action="<?php echo JRoute::_('index.php'); ?>" method="post" id="adminForm" class="formelm">
    <fieldset>
      <legend><?php echo JText::_( 'COM_HWDMS_ADD_MEDIA_TO' ); ?></legend>
      <?php if ($this->params->get('enable_categories') && $this->item->created_user_id == $user->id): ?>
      <div class="formelm">
        <?php echo $this->form->getLabel('category_id'); ?>
        <?php echo $this->form->getInput('category_id'); ?>
      </div>
      <?php endif; ?>
      <?php if ($this->params->get('enable_playlists')): ?>
      <div class="formelm">
        <?php echo $this->form->getLabel('playlist_id'); ?>
        <?php echo $this->form->getInput('playlist_id'); ?>
      </div>
      <?php endif; ?>
      <?php if ($this->params->get('enable_albums') && $this->item->created_user_id == $user->id): ?>
      <div class="formelm">
        <?php echo $this->form->getLabel('album_id'); ?>
        <?php echo $this->form->getInput('album_id'); ?>
      </div>
      <?php endif; ?>
      <?php if ($this->params->get('enable_groups') && $this->item->created_user_id == $user->id): ?>
      <div class="formelm">
        <?php echo $this->form->getLabel('group_id'); ?>
        <?php echo $this->form->getInput('group_id'); ?>
      </div>
      <?php endif; ?>
    </fieldset>
    <div class="formelm-buttons">
      <button onclick="Joomla.submitbutton('mediaitem.link')" type="button" class="button"><?php echo JText::_( 'COM_HWDMS_ADD' ); ?></button>
      <button onclick="window.parent.SqueezeBox.close();" type="button" class="button"><?php echo JText::_( 'COM_HWDMS_CANCEL' ); ?></button>
    </div>
    <div>
      <input type="hidden" name="option" value="com_hwdmediashare" />       
      <input type="hidden" name="task" value="mediaitem.link" />
      <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
      <input type="hidden" name="tmpl" value="component" />
      <?php echo JHtml::_('form.token'); ?> </div>
  </form>
</div>