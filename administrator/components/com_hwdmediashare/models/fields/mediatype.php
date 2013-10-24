<?php
/**
 * @version    SVN $Id: mediatype.php 460 2012-08-13 13:07:10Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Oct-2011 12:54:55
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * hwdMediaShare Form Field class for the hwdMediaShare component
 */
class JFormFieldMediaType extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'MediaType';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                // Initialise variables.
		$options	= array();

                $this->none = (isset($this->none) && ($this->none)) ? (string) $this->none : JText::_('COM_HWDMS_LIST_SELECT_MEDIA_TYPE');
                                
                $options[] = JHtml::_('select.option', '', $this->none);
                if ($config->get('enable_audio')) $options[] = JHtml::_('select.option', '1', JText::_('COM_HWDMS_AUDIO'));
                if ($config->get('enable_documents')) $options[] = JHtml::_('select.option', '2', JText::_('COM_HWDMS_DOCUMENT'));
                if ($config->get('enable_images')) $options[] = JHtml::_('select.option', '3', JText::_('COM_HWDMS_IMAGE'));
                if ($config->get('enable_videos')) $options[] = JHtml::_('select.option', '4', JText::_('COM_HWDMS_VIDEO'));
 
		return $options;
	}
        
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	public function getPublicInput($params)
	{
                // Setup options object
		$this->element['class']    = (isset($params['class']) && ($params['class'])) ? (string) $params['class'] : '';
		$this->element['size']     = (isset($params['size']) && ($params['size'])) ? (int) $params['size'] : '';
                $this->element['readonly'] = (isset($params['readonly']) && ($params['readonly'])) ? (boolean) $params['readonly'] : false;
                $this->element['disabled'] = (isset($params['disabled']) && ($params['disabled'])) ? (boolean) $params['disabled'] : false;
                $this->multiple            = (isset($params['multiple']) && ($params['multiple'])) ? (boolean) $params['multiple'] : false;
                $this->element['onchange'] = (isset($params['onchange']) && ($params['onchange'])) ? (string) $params['onchange'] : '';
                $this->value               = (isset($params['value']) && ($params['value'])) ? (string) $params['value'] : '';
                $this->id                  = (isset($params['id']) && ($params['id'])) ? (string) $params['id'] : '';
                $this->name                = (isset($params['name']) && ($params['name'])) ? (string) $params['name'] : '';
                $this->none                = (isset($params['none']) && ($params['none'])) ? (string) $params['none'] : '';

                // Return nothing if only one active media type 
                if (count($this->getOptions()) <= 2) return null;
                
		return $this->getInput();
	}
}