<?php
/**
 * @version    SVN $Id: categoryform.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Nov-2011 12:02:06
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_categories/models/category.php';

// Import Joomla modelitem library
jimport('joomla.application.component.modelform');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelCategoryForm extends CategoriesModelCategory
{
        /**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	0.1
	 */
	public function getTable($type = 'Category', $prefix = 'hwdMediaShareTable', $config = array())
	{
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                return JTable::getInstance($type, $prefix, $config);
	}
        /**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	0.1
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		// Load state from the request.
		$id = JRequest::getInt('id');
		$this->setState('category.id', $id);

		$return = JRequest::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		parent::populateState();
	}
	/**
	 * Method to get article data.
	 *
	 * @param	integer	The id of the article.
	 *
	 * @return	mixed	Content item data object on success, false on failure.
	 */
	public function getItem($itemId = null)
	{
		// Initialise variables.
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('category.id');

		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');

		// Convert attrib field to Registry.
		$value->params = new JRegistry;
                $value->params->loadString(@$value->params);

		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_hwdmediashare.category.'.$value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) 
                {
			$value->params->set('access-edit', true);
		}
		// Now check if edit.own is available.
		else if (!empty($userId) && $user->authorise('core.edit.own', $asset)) 
                {
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_user_id)
                        {
				$value->params->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($itemId) 
                {
			// Existing item
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else 
                {
                        // New item    
			$value->params->set('access-change', $user->authorise('core.edit.state', 'com_hwdmediashare'));
		}

		return $value;
	}
	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 * @since	0.1
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
        
       /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getReportForm($data = array(), $loadData = true)
	{
                $form = & JForm::getInstance('report', JPATH_SITE.'/components/com_hwdmediashare/models/forms/report.xml');

                if (empty($form))
		{
			$this->setError(JText::_('COM_HWDMS_ERROR_FAILED_TO_LOAD_FORM'));
                        return false;
		}
		return $form;
	}        
}