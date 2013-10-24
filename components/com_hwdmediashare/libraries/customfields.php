<?php
/**
 * @version    SVN $Id: customfields.php 289 2012-03-31 19:02:58Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework custom fields class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareCustomFields
{
	/**
	 * Method to save custom fields from a form submission
         * 
         * @since   0.1
	 **/
	public function save($params)
	{
                $app         = & JFactory::getApplication();

                // Process and save custom fields
		$customField    = new hwdMediaShareCustomFields;
		$profile	= $customField->get(null, $this->elementType);
                $values = array();
                
		foreach( $profile['fields'] as $group => $fields )
		{
			foreach( $fields as $data )
			{
				// Get value from posted data and map it to the field.
				// Here we need to prepend the 'field' before the id because in the form, the 'field' is prepended to the id.
				$postData = JRequest::getVar( 'field' . $data['id'] , '' , 'POST' );

				$values[ $data['id'] ]	= hwdMediaShareCustomFields::formatData( $data['type']  , $postData );

				// @rule: Validate custom profile if necessary
				if( !hwdMediaShareCustomFields::validateField( $data['id'], $data['type'] , $values[ $data['id'] ] , $data['required'] ) )
				{
					// If there are errors on the form, display to the user.
					$message	= JText::sprintf('The field "%1$s" contain improper values' ,  $data['name'] );
		
                                        $return = JRequest::getVar('return', null, 'default', 'base64');
                                        if (empty($return) || !JUri::isInternal(base64_decode($return))) 
                                        {
                                                $app->redirect( 'index.php?option=com_hwdmediashare&view=editmedia&layout=edit&id=' . $params->elementId , $message , 'error' );
                                        }
                                        else 
                                        {
                                                $app->redirect( base64_decode($return) , $message , 'error' );
                                        }
					return;
				}
			}
		}

                hwdMediaShareCustomFields::saveFields($params, $values);
        }

	/**
	 * Method to get custom fields of an item
         * 
         * @since   0.1
	 **/        
	public function get($item = null , $elementType = null)
	{
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
		$data		= array();

                if (empty($elementType))
                    $elementType = $this->elementType;

		// Attach custom fields into the user object
                $query->select('field.*');
                $query->from('#__hwdms_fields AS field');

                // Only bind values if $item exists
                if (isset($item->id))
                {
                        $query->select('value.'.$db->quoteName('value').' , value.'.$db->quoteName('access'));
                        $query->join('LEFT', '#__hwdms_fields_values AS value ON field.'.$db->quoteName('id').'=value.'.$db->quoteName('field_id').' AND value.'.$db->quoteName('element_id').'=' . $db->Quote($item->id));
                }
                else 
                {
                        $query->select('"" as value , "" as access');
                }

 		// Build proper query for multiple profile types.
		$query->where('field.'.$db->quoteName('published').'=' . $db->Quote('1'));
                $query->where('field.'.$db->quoteName('element_type').'=' . $db->Quote($elementType));
		$query->order('field.'.$db->quoteName('ordering'));

                //$query.= ' GROUP BY field.'.$db->quoteName('id');

		$db->setQuery( $query );

		$result	= $db->loadAssocList();

                if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		$data['fields']	= array();
		for($i = 0; $i < count($result); $i++)
		{
			// We know that the groups will definitely be correct in ordering.
			if($result[$i]['type'] == 'group')
			{
				$group	= $result[$i]['name'];

				// Group them up
				if(!isset($data['fields'][$group]))
				{
					// Initialize the groups.
					$data['fields'][$group]	= array();
				}
			}

			// Re-arrange options to be an array by splitting them into an array
			if(isset($result[$i]['options']) && $result[$i]['options'] != '')
			{
				$options	= $result[$i]['options'];
				$options	= explode("\n", $options);

				array_walk($options, array( 'JString' , 'trim' ) );

				$result[$i]['options']	= $options;

			}

			// Only append non group type into the returning data as we don't
			// allow users to edit or change the group stuffs.
			if($result[$i]['type'] != 'group'){
				if(!isset($group))
                                        $data['fields'][JText::_('COM_HWDMS_UNGROUPED')][]   = $result[$i];
				else
					$data['fields'][$group][]                           = $result[$i];
			}
		}

                //$this->_dump($data);
		return $data;
	}
        
	/**
	 * Method to render html for a field
         * 
         * @since   0.1
	 **/
	public function getFieldHTML( $field , $showRequired = '&nbsp; *' )
	{
		$fieldType	= strtolower( $field->type);

		if(is_array($field))
		{
			jimport( 'joomla.utilities.arrayhelper');
			$field = JArrayHelper::toObject($field);
		}

		hwdMediaShareFactory::load('fields.'.$fieldType);

		$class	= 'hwdMediaShareFields' . ucfirst( $fieldType );

		if(is_object($field->options))
		{
			$field->options = JArrayHelper::fromObject($field->options);
		}

		// Clean the options
		if( !empty( $field->options ) && !is_array( $field->options ) )
		{
			array_walk( $field->options , array( 'JString' , 'trim' ) );
		}

		// Escape the field name
		$field->name	= $this->escape($field->name);

		if( !isset($field->value) )
		{
			$field->value	= '';
		}

		if( class_exists( $class ) )
		{
			$object	= new $class();

			if( method_exists( $object, 'getFieldHTML' ) )
			{
				$html	= $object->getFieldHTML( $field , $showRequired );
				return $html;
			}
		}

		return JText::sprintf('COM_COMMUNITY_UNKNOWN_USER_PROFILE_TYPE' , $class , $fieldType );
	}
        
	public function getFieldData( $field )
	{
		$fieldType	= strtolower( $field->type );

		if(is_array($field))
		{
			jimport( 'joomla.utilities.arrayhelper');
			$field = JArrayHelper::toObject($field);
		}

		hwdMediaShareFactory::load('fields.'.$fieldType);

		$class	= 'hwdMediaShareFields' . ucfirst( $fieldType );

		if(is_object($field->options))
		{
			$field->options = JArrayHelper::fromObject($field->options);
		}

		if( class_exists( $class ) )
		{
			$object	= new $class();
			
			if( method_exists( $object , 'getFieldData' ) )
			{
				return $object->getFieldData( JArrayHelper::fromObject($field) );
			}
		}
		if($fieldType == 'select' || $fieldType == 'singleselect' || $fieldType == 'radio')
		{
			return JText::_($field->value);
		}
		else if($fieldType == 'textarea')
		{
			return nl2br($field->value);
		}
		else
		{		
			return $field->value;
		}
	}
        
	/**
	 * Method to validate any custom field in PHP. Javascript validation is not sufficient enough.
	 * We also need to validate fields in PHP since if the user knows how to send POST data, then they
	 * will bypass javascript validations.
	 **/
	public function validateField( $fieldId, $fieldType , $value , $required )
	{
		$fieldType	= strtolower( $fieldType );

                hwdMediaShareFactory::load('fields.'.$fieldType);

		$class	= 'hwdMediaShareFields' . ucfirst( $fieldType );

		if( class_exists( $class ) )
		{
			$object	= new $class();
			$object->fieldId = $fieldId;
			if( method_exists( $object, 'isValid' ) )
			{
				return $object->isValid( $value , $required );
			}
		}
		// Assuming there is no need for validation in these subclasses.
		return true;
	}

        /**
	 * Method to format field data in preparation for saving
         * 
         * @since   0.1
	 **/
	public function formatData( $fieldType , $value )
	{
		$fieldType = strtolower( $fieldType );

                hwdMediaShareFactory::load('fields.'.$fieldType);

		$class	= 'hwdMediaShareFields' . ucfirst( $fieldType );

		if( class_exists( $class ) )
		{
			$object	= new $class();

			if( method_exists( $object, 'formatData' ) )
			{
				return $object->formatData( $value );
			}
		}
		// Assuming there is no need for formatting in subclasses.
		return $value;
	}
        
	/**
	 * Method to save custom fields after a form submission
         * 
         * @since   0.1
	 **/
	public function saveFields($params, $fields)
	{
		jimport('joomla.utilities.date');
		$db = & JFactory::getDBO();

		foreach($fields as $id => $value)
		{
			$table	=& JTable::getInstance( 'FieldValue' , 'hwdMediaShareTable' );

                        if( !$table->load( $this->elementType, $params->elementId, $id ) )
			{
				$table->element_type	= $this->elementType;
                                $table->element_id	= $params->elementId;
				$table->field_id	= $id;
			}

			if( is_object( $value ) )
			{
				$table->value	= $value->value;
				$table->access	= $value->access;
			}

			if( is_string( $value ) )
			{
				$table->value	= $value;
			}

                        if (!$table->store())
                        {
                                JError::raiseError(500, $table->getError() );
                        }
		}
	}
}
