<?php
/**
 * @version    SVN $Id: default_reply.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      20-Nov-2011 16:55:49
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);

?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare&task=activity.comment&id=' . $this->id . '&return=' . $this->return . '&tmpl=component&reply=' . $this->reply); ?>" method="post">
    <fieldset>
    <legend><strong>Make a Comment</strong></legend>
    <dl>
    <dt>
    <label id="jform_comment-lbl" for="jform_comment" class="hasTip required" title="">
    <?php echo (empty(JFactory::getUser()->username) ? 'Guest' : JFactory::getUser()->username); ?>:<span class="star">&nbsp;*</span>
    </label>
    </dt>
    <dd>
    <input type="text" name="jform[comment]" id="jform_comment" value="" class="required" size="30" aria-required="true" required="required">
    </dd>
    </dl>
    </fieldset>
    <?php echo JHtml::_('form.token'); ?>
    <input class="button" comment="" type="submit" value="Comment" />
</form>
