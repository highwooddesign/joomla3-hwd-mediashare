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

class hwdMediaShareModelSubscription extends JModelAdmin
{
	/**
	 * Method to get a table object, and load it if necessary.
	 *
	 * @access  public
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Subscription', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}
        
	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @access  public
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed    A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hwdmediashare.subscription', 'subscription', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @access  protected
         * @return  mixed      The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.subscription.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}
                
                // We define a dummy variable which is used to collect the element_id.                
                if (is_array($data) && isset($data['element_type']) && isset($data['element_id']))
                {
                        $variable = 'element_id_' . $data->element_type;
                        $data[$variable] = $data['element_id'];   
                }
                elseif(is_object($data) && isset($data->element_type) && isset($data->element_id))
                {
                        $variable = 'element_id_' . $data->element_type;
                        $data->$variable = $data->element_id;  
                }

		return $data;
	}

	/**
	 * Method to save the subscription form data.
	 *
	 * @access  public
         * @param   array    $data  The form data.
	 * @return  boolean  True on success, False on error.
	 */
	public function save($data)
	{
                // We define the element_id using the dummy variable value.
                $data['element_id'] = (int) $data['element_id_' . $data['element_type']];
                unset($data['element_id_' . $data['element_type']]);
                
		if ($data['element_id'] == 0)
		{
			$this->setError(JText::_('COM_HWDMS_NO_ITEM_SELECTED'));
			return false;
		}

                if (parent::save($data))
                {
                        return true;
                }   

		return false;
	}        
}
