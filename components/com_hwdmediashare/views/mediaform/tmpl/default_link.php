<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
$user = JFactory::getUser();
?>
<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
  <div id="hwd-container" class="hwd-modal <?php echo $this->pageclass_sfx;?>"> <a name="top" id="top"></a>
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
      <?php if ($this->params->get('enable_groups')): ?>
      <div class="formelm">
        <?php echo $this->form->getLabel('group_id'); ?>
        <?php echo $this->form->getInput('group_id'); ?>
      </div>
      <?php endif; ?>
    </fieldset>
    <div class="formelm-buttons">
      <button onclick="Joomla.submitbutton('media.link')" type="button" class="btn"><?php echo JText::_('COM_HWDMS_ADD'); ?></button>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>
