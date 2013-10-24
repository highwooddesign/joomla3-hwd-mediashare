<?php
/**
 * @version    SVN $Id: album.php 936 2013-01-25 15:48:20Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      05-Dec-2011 16:56:06
 */

// No direct access
 defined('_JEXEC') or die('Restricted access');

// Import Joomla formfield library
jimport('joomla.form.formfield');

 /**
  * Album field class
  */
 class JFormFieldAlbum extends JFormField
 {
        /**
 	 * Field type
 	 * @var string
 	 */
 	protected $type = 'Album';

        /**
 	 * Field name
 	 * @var string
 	 */
 	protected $name = 'album';

        /**
 	 * Field id
 	 * @var string
 	 */
 	protected $id = 'album';

        /**
         * Method to get the field input markup
         */
        public function getInput()
        {
              // Load modal behavior
              JHtml::_('behavior.modal', 'a.modal');

              // Build the script
              $script = array();
              $script[] = '    function jSelectAlbum_'.$this->id.'(id, title, object) {';
              $script[] = '        document.id("'.$this->id.'_id").value = id;';
              $script[] = '        document.id("'.$this->id.'_name").value = title;';
              $script[] = '        SqueezeBox.close();';
              $script[] = '    }';

              // Add to document head
              JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

              // Setup variables for display
              $html = array();
              $link = 'index.php?option=com_hwdmediashare&amp;view=albums&amp;layout=modal&amp;tmpl=component&amp;function=jSelectAlbum_'.$this->id;
              
              // The active item id field
              if (0 == (int)$this->value) 
              {
                $title = JText::_('COM_HWDMS_SELECT_ALBUM');
              } 
              else 
              {
                $db = JFactory::getDBO();
                $db->setQuery(
                  'SELECT title' .
                  ' FROM #__hwdms_albums' .
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
              $html[] = '    <a class="modal" title="'.JText::_('COM_HWDMS_SELECT_ALBUM').'" href="'.$link.
                             '" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
                             JText::_('COM_HWDMS_SELECT_ALBUM').'</a>';
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