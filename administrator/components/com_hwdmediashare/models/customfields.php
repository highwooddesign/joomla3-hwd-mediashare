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

class hwdMediaShareModelCustomFields extends JModelList
{
	/**
	 * Class constructor. Defines a white list of column filters.
	 *
	 * @access	public
	 * @param       array       $config     An optional associative array of configuration settings.
         * @return      void
	 */    
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'element_type', 'a.element_type',
				'type', 'a.type',
 				'ordering', 'a.ordering',
				'published', 'a.published', 
				'min', 'a.min', 
				'max', 'a.max', 
				'name', 'a.name',
				'visible', 'a.visible',
				'required', 'a.required',
				'searchable', 'a.searchable',
				'fieldcode', 'a.fieldcode',
			);
		}

		parent::__construct($config);
	}
        
	/**
	 * Method to get the database query.
	 *
	 * @access  protected
	 * @return  JDatabaseQuery  database query
	 */
        protected function getListQuery()
        {
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

                // Get HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.element_type, a.type, a.name, a.ordering, a.published,' .
				'a.visible, a.required, a.searchable, a.fieldcode, a.tooltip'
			)
		);

                // From the fields table
                $query->from('#__hwdms_fields AS a');

		// Filter by published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published))
                {
			$query->where('a.published = '.(int) $published);
		}
                elseif ($published === '')
                {
			$query->where('(a.published IN (0, 1))');
		}
                
		// Filter by element type.
		$elementType = $this->getState('filter.element_type');
		if (is_numeric($elementType))
                {
			$query->where('a.element_type = '.(int) $elementType);
		} 
                
		// Filter by field type.
		$fieldType = $this->getState('filter.field_type');
		if (!empty($fieldType))
                {
			$fieldType = preg_replace("/[^a-z]+/", "", $fieldType);
                        $query->where('a.type = '. $db->quote($fieldType));
		}
                
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
                {
			if (stripos($search, 'id:') === 0)
                        {
				$query->where('a.id = '.(int) substr($search, 3));
			}
                        else
                        {
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('(a.name LIKE '.$search.' OR a.tooltip LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
                $listOrder = $this->state->get('list.ordering');
                $listDirn = $this->state->get('list.direction');

		$query->order($db->escape($listOrder.' '.$listDirn));

   		// Group over the ID to prevent duplicates.
                $query->group('a.id');
                
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
        }
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @access  protected
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.ordering', 'asc');
	}

	/**
	 * Get the batch form.
	 *
	 * @access      public
	 * @param       array       $data       Data for the form.
	 * @param       boolean     $loadData   True if the form is to load its own data (default case), false if not.
	 * @return      mixed       A JForm object on success, false on failure
	 */
	public function getBatchForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hwdmediashare.batch', 'batch', array('control' => 'batch', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	} 
        
	/**
	 * Get the batch form.
	 *
	 * @access      public
	 * @return      array       An array of supported profile types.
	 */
	public function getProfileTypes()
	{
		static $types = false;

		if(!$types)
		{
                        $path = JPATH_ROOT . '/components/com_hwdmediashare/libraries/fields/customfields.xml';
                        $parser = JFactory::getXML($path);                                        
                        $fields	= $parser->fields;
                        
			$data	= array();

			foreach( $fields->children() as $field )
			{
                                $data["$field->type"] = $field->name;
			}
                        
			$types = $data;
		}
               
		return $types;
	}        
}
