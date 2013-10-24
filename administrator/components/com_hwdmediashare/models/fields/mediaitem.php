<?php
/**
 * @version    SVN $Id: mediaitem.php 283 2012-03-29 16:34:10Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      18-Feb-2012 15:24:35
 */

// No direct access
 defined('_JEXEC') or die('Restricted access');

// Import Joomla formfield library
jimport('joomla.form.formfield');

 /**
  * Media field class
  */
 class JFormFieldMediaItem extends JFormField
 {
        /**
 	 * Field type
 	 * @var string
 	 */
 	protected $type = 'Media';

        /**
 	 * Field name
 	 * @var string
 	 */
 	protected $name = 'media';

        /**
 	 * Field id
 	 * @var string
 	 */
 	protected $id = 'media';

        /**
         * Method to get the field input markup
         */
        public function getInput()
        {
              // Load modal behavior
              JHtml::_('behavior.modal', 'a.modal');

              // Build the script
              $script = array();
              $script[] = '    function jSelectMedia_'.$this->id.'(id, title, object) {';
              $script[] = '        document.id("'.$this->id.'_id").value = id;';
              $script[] = '        document.id("'.$this->id.'_name").value = title;';
              $script[] = '        SqueezeBox.close();';
              $script[] = '    }';

              // Add to document head
              JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

              // Setup variables for display
              $html = array();
              $link = 'index.php?option=com_hwdmediashare&amp;view=media&amp;layout=modal&amp;tmpl=component&amp;function=jSelectMedia_'.$this->id;

              // The active item id field
              if (0 == (int)$this->value) 
              {
                $title = JText::_('COM_HWDMS_SELECT_MEDIA');
              } 
              else 
              {
                $db = JFactory::getDBO();
                $db->setQuery(
                  'SELECT title' .
                  ' FROM #__hwdms_media' .
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
              $html[] = '    <a class="modal" title="'.JText::_('COM_HWDMS_SELECT_MEDIA').'" href="'.$link.
                             '" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
                             JText::_('COM_HWDMS_SELECT_MEDIA').'</a>';
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