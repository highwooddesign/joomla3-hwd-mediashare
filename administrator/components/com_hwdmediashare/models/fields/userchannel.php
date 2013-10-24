<?php
/**
 * @version    SVN $Id: userchannel.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      08-Dec-2011 10:06:08
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla formfield library
jimport('joomla.form.formfield');

 /**
  * User channel field class
  */
 class JFormFieldUserChannel extends JFormField
 {
        /**
 	 * Field type
 	 * @var string
 	 */
 	protected $type = 'UserChannel';

        /**
 	 * Field name
 	 * @var string
 	 */
 	protected $name = 'userchannel';

        /**
 	 * Field id
 	 * @var string
 	 */
 	protected $id = 'userchannel';

        /**
         * Method to get the field input markup
         */
        public function getInput()
        {
              // Load modal behavior
              JHtml::_('behavior.modal', 'a.modal');

              // Build the script
              $script = array();
              $script[] = '    function jSelectUserChannel_'.$this->id.'(id, title, object) {';
              $script[] = '        document.id("'.$this->id.'_id").value = id;';
              $script[] = '        document.id("'.$this->id.'_name").value = title;';
              $script[] = '        SqueezeBox.close();';
              $script[] = '    }';

              // Add to document head
              JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

              // Setup variables for display
              $html = array();
              $link = 'index.php?option=com_hwdmediashare&amp;view=users&amp;layout=modal&amp;tmpl=component';

              $title = JText::_('COM_HWDMS_SELECT_USER_CHANNEL');
              $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

              // The current item input field
              $html[] = '<div class="fltlft">';
              $html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" />';
              $html[] = '</div>';

              // The item select button
              $html[] = '<div class="button2-left">';
              $html[] = '  <div class="blank">';
              $html[] = '    <a class="modal" title="'.JText::_('COM_HWDMS_SELECT_USER_CHANNEL').'" href="'.$link.
                             '" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
                             JText::_('COM_HWDMS_SELECT_USER_CHANNEL').'</a>';
              $html[] = '  </div>';
              $html[] = '</div>';

              // The active item id field
              if (0 == (int)$this->value) {
                      $value = '';
              } else {
                      $value = (int)$this->value;
              }

              // Set class='required' for client side validation
              $class = '';
              if ($this->required) {
                      $class = ' class="required modal-value"';
              }

              $html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

              return implode("\n", $html);
        }
}