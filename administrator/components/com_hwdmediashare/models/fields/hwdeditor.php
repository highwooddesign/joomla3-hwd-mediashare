<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('editor');

class JFormFieldHwdEditor extends JFormFieldEditor
{
	/**
	 * The name of the form field type.
         * 
         * @access  public
	 * @var     string
	 */
	public $type = 'HwdEditor';
        
	/**
	 * Method to get a JEditor object based on the form field.
	 *
	 * @access  protected
	 * @return  object     The JEditor object.
	 */
	protected function getEditor()
	{
                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Set the local editor.
		$this->editorType = array($config->get('editor'));
                
                return parent::getEditor();
	}      
}
