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

class hwdMediaShareCustomFields extends JObject
{
	/**
	 * The element type to use with this library.
         * 
         * @access  public
	 * @var     string
	 */
	public $elementType = 1;

	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed   $properties  Associative array to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareCustomFields object, only creating it if it
	 * doesn't already exist.
	 *
	 * @access  public
         * @static
	 * @return  hwdMediaShareCustomFields Object.
	 */ 
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareCustomFields';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to save custom fields from a form submission.
	 *
	 * @access  public
	 * @param   object  $item  The item to save.
	 * @return  boolean True on success, false on fail.
	 */    
	public function save($item)
	{
                // Initialise variables.
                $app            = JFactory::getApplication();
                $db             = JFactory::getDBO();
		$custom         = $this->load(null);
                $values         = array();
                
                // Loop through each field and validate associated inputs.
		foreach($custom->fields as $group => $fields)
		{
			foreach($fields as $field)
			{
                                // Skip fields that don't belong to this type of element.
                                if ($field->element_type != $this->elementType) continue;

				// Get the request filter type for this field.
				$filter = $this->getFilter($field);

				// Get the field value from the form submission.
				$input = $app->input->get('field' . $field->id, '', $filter);
 
                                // Format the data, and assign it ot the $values array.
				$values[$field->id] = $this->formatData($field, $input);

				// Validate custom field if necessary.
				if(!$this->validateField($field, $values[$field->id]))
				{
					// If there are errors on the form, remove the value, and display to the user.
                                        $app->enqueueMessage(JText::sprintf('COM_HWDMS_ERROR_CUSTOM_FIELD_IMPROPER_VALUE', $field->name));
                                        unset($values[$field->id]);
				}
			}
		}
                
                // Load the HWD table path.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
 
                // Loop through each validated input and save.
		foreach($values as $id => $value)
		{
                        // Check if the field exists, and get the ID to bind it during the save.
                        $query = $db->getQuery(true)
                                ->select('id')
                                ->from('#__hwdms_fields_values')
                                ->where('element_type = ' . $db->quote($this->elementType))
                                ->where('element_id = ' . $db->quote($item->id))
                                ->where('field_id = ' . $db->quote($id));
                        try
                        {                
                                $db->setQuery($query);
                                $result = $db->loadResult();
                        }
                        catch (Exception $e)
                        {
                                $this->setError($e->getMessage());
                                return false;
                        }

                        // Load the table.
                        $table = JTable::getInstance('FieldValue', 'hwdMediaShareTable');
                        $table->reset();

                        // Create an object to bind to the database.
                        $object = new StdClass;
                        $object->id = (int) $result;
                        $object->element_type = $this->elementType;
                        $object->element_id = $item->id;
                        $object->field_id = $id;
                        $object->value = isset($value) ? $value : '';
                        $object->access = 1;

                        // Attempt to save the details to the database.
                        if (!$table->save($object))
                        {
                                $this->setError($table->getError());
                                return false;
                        }
		}
        }

	/**
	 * Method to get custom fields of an item.
	 *
	 * @access  public
	 * @param   object  $item  The item to check.
	 * @return  array   An array of categories assigned to the item.
	 */       
	public function load($item = null)
	{
		// Initialiase variables.
                $db = JFactory::getDBO();
                
                $query = $db->getQuery(true)
                        ->select('field.*')
                        ->from('#__hwdms_fields AS field')
                        ->where($db->quoteName('field.published'). '=' . $db->Quote('1'))
                        ->where($db->quoteName('field.element_type') . '=' . $db->Quote($this->elementType))
                        ->order($db->quoteName('field.ordering'));

                // Only bind values $item exists.
                if (isset($item->id))
                {
                        $query->select('value.'.$db->quoteName('value').' , value.'.$db->quoteName('access'));
                        $query->join('LEFT', '#__hwdms_fields_values AS value ON field.'.$db->quoteName('id').'=value.'.$db->quoteName('field_id').' AND value.'.$db->quoteName('element_id').'=' . $db->Quote($item->id));
                }
                else 
                {
                        $query->select('"" as value , "" as access');
                }

                try
                {                
                        $db->setQuery($query);
                        // $result = $db->loadAssocList();
                        $result = $db->loadObjectList();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }

                // Define new properties to hold the field values.
                $this->values = new JRegistry;
                $this->fields = array();

		// We have loaded the fields in the correct ordering. 
		foreach($result as $field)
		{
			if ($field->type == 'group')
			{
				$group	= $field->name;

				// Initialize groups.
				if(!isset($this->fields[$group]))
				{
					$this->fields[$group] = array();
				}
			}
                        else
                        {                  
                                $this->values->set($field->fieldcode, $field->value); 
                        }

			// Convert options field to array.
			if (isset($field->options) && $field->options != '')
			{
				$field->options = explode("\n", $field->options);         
			}

			// Convert params field to registry.
			if (isset($field->params) && $field->params != '')
			{
                                $registry = new JRegistry;
                                $registry->loadString($field->params);
                                $field->params = $registry;             
			}
                        
			// Add fields to groups.
			if($field->type != 'group')
                        {
				if(!isset($group))
                                {
                                        $this->fields[JText::_('COM_HWDMS_UNGROUPED')][] = $field;
                                }
                                else
                                {
					$this->fields[$group][] = $field;
                                }
			}
		}

		return $this;
	}
        
	/**
	 * Method to generate the label markup for a custom field.
	 *
	 * @access  public
	 * @param   object  $field  The field to show.
	 * @return  string  The HTML markup.
	 */ 
	public function getLabel($field)
	{
		$label = '';

		// Get the label name.
		$text = (string) $field->name;

		// Build the class for the label.
		$class = !empty($field->tooltip) ? 'hasTooltip' : '';
		$class = $field->required == true ? $class . ' required' : $class;

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $field->id . '-lbl" for="field' . $field->id . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($field->tooltip))
		{
			JHtml::_('bootstrap.tooltip');
			$label .= ' title="' . JHtml::tooltipText($text, $field->tooltip, 0) . '"';
		}

		// Add the label text and closing tag.
		if ($field->required)
		{
			$label .= '>' . $text . '<span class="star">&#160;*</span></label>';
		}
		else
		{
			$label .= '>' . $text . '</label>';
		}

		return $label;
	}
        
	/**
	 * Method to generate the input markup for a custom field.
	 *
	 * @access  public
	 * @param   object  $field  The field to show.
	 * @return  string  The HTML markup.
	 */ 
	public function getInput($field)
	{
		// Attempt to load the field library.
                hwdMediaShareFactory::load('fields.' . strtolower($field->type));

		$class = 'hwdMediaShareFields' . ucfirst($field->type);

		if(class_exists($class))
		{
			$HWDfield = new $class();
			if(method_exists($HWDfield, 'getInput'))
			{
				return $HWDfield->getInput($field);
			}
		}

		return JText::sprintf('COM_HWD_UNKNOWN_FIELD_TYPE', $field->type);
	}
                
	/**
	 * Method to validate any custom field in PHP. Javascript validation is not sufficient enough.
	 * We also need to validate fields in PHP since if the user knows how to send POST data, then they
	 * will bypass javascript validations.
         *
	 * @access  public
	 * @param   object  $field  The field to validate.
	 * @param   mixed   $value  The value to check.
	 * @return  boolean True on success, false on fail.
	 */ 
	public function validateField($field, $value)
	{
		// Attempt to load the field library.
                hwdMediaShareFactory::load('fields.' . strtolower($field->type));

		$class = 'hwdMediaShareFields' . ucfirst($field->type);

		if(class_exists($class))
		{
			$HWDfield = new $class();
			if(method_exists($HWDfield, 'isValid'))
			{
				return $HWDfield->isValid($field, $value);
			}
		}
                
		// If no method exists, then there is no requirement for validation.
		return true;
	}

	/**
	 * Method to format field data in preparation for saving.
         *
	 * @access  public
	 * @param   object  $field  The field to validate.
	 * @param   mixed   $value  The value to check.
	 * @return  mixed   The formatted input value.
	 */ 
	public function formatData($field, $value)
	{
		// Attempt to load the field library.
                hwdMediaShareFactory::load('fields.' . strtolower($field->type));

		$class = 'hwdMediaShareFields' . ucfirst($field->type);

		if(class_exists($class))
		{
			$HWDfield = new $class();
			if(method_exists($HWDfield, 'formatData'))
			{
				return $HWDfield->formatData($value);
			}
		}
                
		// If no method exists, then there is no requirement for formatting.
		return $value;
	}

	/**
	 * Method to get the filter for a field type.
         *
	 * @access  public
	 * @param   object  $field  The field to validate.
	 * @return  mixed   The formatted input value.
	 */ 
	public function getFilter($field)
	{
		// Attempt to load the field library.
                hwdMediaShareFactory::load('fields.' . strtolower($field->type));

		$class = 'hwdMediaShareFields' . ucfirst($field->type);

		if(class_exists($class))
		{
			$HWDfield = new $class();
			if(method_exists($HWDfield, 'getFilter'))
			{
				return $HWDfield->getFilter();
			}
		}
                
		// If no method exists, then we will apply a string filter.
		return 'string';
	}
        
	/**
	 * Method to display a field value.
         *
	 * @access  public
	 * @param   object  $field  The field to validate.
	 * @return  string  The markup to display the field value.
	 */ 
	public function display($field)
	{
		// Attempt to load the field library.
                hwdMediaShareFactory::load('fields.' . strtolower($field->type));

		$class = 'hwdMediaShareFields' . ucfirst($field->type);

		if(class_exists($class))
		{
			$HWDfield = new $class();
			if(method_exists($HWDfield, 'display'))
			{
				return $HWDfield->display($field);
			}
		}
                
		// If no method exists, then we simply return the field value.
		return $field->value;
	}        
}
