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

class hwdMediaShareModelConfiguration extends JModelAdmin
{
	/**
	 * Method to get the configuration item.
	 *
	 * @access  public
         * @param   integer     The id of the primary key.
	 * @return  mixed       Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
                $hwdms = hwdMediaShareFactory::getInstance();          
                $item = $hwdms->getConfig();
		return $item;
	}
        
	/**
	 * Method to get a table object, and load it if necessary.
	 *
	 * @access  public
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 * @return  JTable  A JTable object
	 */
	public function getTable($name = 'Configuration', $prefix = 'hwdMediaShareTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @access  public
	 * @param   array       $data      Data for the form.
	 * @param   boolean     $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed       A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hwdmediashare.configuration', 'configuration', array('control' => 'jform', 'load_data' => $loadData));

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
         * @return  mixed       The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_hwdmediashare.edit.configuration.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the configuration form data.
	 *
	 * @access  public
         * @param   array       $data  The form data.
	 * @return  boolean     True on success, False on error.
	 */
	public function save($data)
	{
 		// Initialise variables.
		$data = JFactory::getApplication()->input->post->get('jform', array(), 'array');
                $date = JFactory::getDate();

                jimport('joomla.filesystem.file');
                $ini = JPATH_ROOT.'/administrator/components/com_hwdmediashare/config.ini';
                $defaultConfig = JFile::read($ini);

                // Load default configuration.
                $config	= new JRegistry($defaultConfig);

                $dataWithoutRules = $data;
                unset($dataWithoutRules['rules']);

                // Bind the user saved configuration.
                $config->loadArray($dataWithoutRules);

                $configObject = JRegistryFormatJSON::stringToObject($config);
                $configJson = JRegistryFormatJSON::objectToString($configObject);

                $array['id'] = 1;
                $array['name'] = 'config';
                $array['date'] = $date->toSql();
                $array['params'] = (string)$configJson;
                $array['rules'] = $data['rules'];
                        
                if (parent::save($array))
                {
                        return true;
                }   

		return false;
	}
}
