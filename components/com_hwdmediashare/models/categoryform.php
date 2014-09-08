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

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_categories/models/category.php';

class hwdMediaShareModelCategoryForm extends CategoriesModelCategory
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
	public function getTable($name = 'Category', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($name, $prefix, $config);
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
		// Initialise variables.
		$app = JFactory::getApplication();
                $user = JFactory::getUser();
                
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('category.id', $id);

		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));

		parent::populateState();               
	}

	/**
	 * Method to get a single item.
	 *
         * @access  public
	 * @param   integer     $pk     The id of the primary key.
	 * @return  mixed       Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (int) (!empty($pk)) ? $pk : $this->getState('category.id');

		// Get a table instance.
		$table = $this->getTable();

		// Attempt to load the table row.
		$return = $table->load($pk);

		// Check for a table object error.
		if ($return === false && $table->getError())
                {
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');

		// Convert params field to array. We don't convert to registry to avoid 
                // SimpleXMLElement warnings when binding the data to the form
		if (property_exists($value, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($value->params);
			$value->params = $registry->toArray();
		}
                
		// Define access registry.
 		$value->attributes = new JRegistry;

		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_hwdmediashare.category.'.$value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			$value->attributes->set('access-edit', true);
		}

		// Now check if edit.own is available.
		elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
		{
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_user_id)
			{
				$value->attributes->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($pk)
		{
			// Existing item.
			$value->attributes->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else
		{
			// New item.
			$value->attributes->set('access-change', $user->authorise('core.edit.state', 'com_hwdmediashare'));
		}

		if ($pk)
		{
                        // Add the tags.
                        $value->tags = new JHelperTags;
                        $value->tags->getTagIds($value->id, 'com_hwdmediashare.category');
		}

		return $value;
	}

	/**
	 * Get the return URL.
	 *
         * @access  public
	 * @return  string  The return URL.
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}
