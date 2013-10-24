<?php
/**
 * @version    SVN $Id: group.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      06-Dec-2011 09:54:09
 */

// No direct access
 defined('_JEXEC') or die('Restricted access');

// Import Joomla formfield library
jimport('joomla.form.formfield');

 /**
  * Group field class
  */
 class JFormFieldGroup extends JFormField
 {
        /**
 	 * Field type
 	 * @var string
 	 */
 	protected $type = 'Group';

        /**
 	 * Field name
 	 * @var string
 	 */
 	protected $name = 'group';

        /**
 	 * Field id
 	 * @var string
 	 */
 	protected $id = 'group';

        /**
         * Method to get the field input markup
         */
        public function getInput()
        {
              // Load modal behavior
              JHtml::_('behavior.modal', 'a.modal');

              // Build the script
              $script = array();
              $script[] = '    function jSelectGroup_'.$this->id.'(id, title, object) {';
              $script[] = '        document.id("'.$this->id.'_id").value = id;';
              $script[] = '        document.id("'.$this->id.'_name").value = title;';
              $script[] = '        SqueezeBox.close();';
              $script[] = '    }';

              // Add to document head
              JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

              // Setup variables for display
              $html = array();
              $link = 'index.php?option=com_hwdmediashare&amp;view=groups&amp;layout=modal&amp;tmpl=component&amp;function=jSelectGroup_'.$this->id;

              // The active item id field
              if (0 == (int)$this->value) 
              {
                $title = JText::_('COM_HWDMS_SELECT_GROUP');
              } 
              else 
              {
                $db = JFactory::getDBO();
                $db->setQuery(
                  'SELECT title' .
                  ' FROM #__hwdms_groups' .
                  ' WHERE id = '.(int) $this->value
                );
                $title = $db->loadResult();
              }              
              $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

              // The current item input field
              $html[] = '<div class="fltlft">';
              $html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" />';
              $html[] = '</div>';

              // The item select button
              $html[] = '<div class="button2-left">';
              $html[] = '  <div class="blank">';
              $html[] = '    <a class="modal" title="'.JText::_('COM_HWDMS_SELECT_GROUP').'" href="'.$link.
                             '" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
                             JText::_('COM_HWDMS_SELECT_GROUP').'</a>';
              $html[] = '  </div>';
              $html[] = '</div>';

              // The active item id field
              if (0 == (int)$this->value) {
                      $value = '';
              } else {
                      $value = (int)$this->value;
              }

              // class='required' for client side validation
              $class = '';
              if ($this->required) {
                      $class = ' class="required modal-value"';
              }

              $html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

              return implode("\n", $html);
        }
}