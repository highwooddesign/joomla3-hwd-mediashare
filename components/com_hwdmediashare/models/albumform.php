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

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/album.php';

class hwdMediaShareModelAlbumForm extends hwdMediaShareModelAlbum
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState()
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
		$this->setState('album.id', $id);

		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));

		$this->setState('layout', $app->input->getString('layout'));                

		parent::populateState();               
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $itemId  The id of the primary key.
         * 
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($itemId = null)
	{
		// Initialise variables.
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('album.id');

		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

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
 		$value->access = new JRegistry;

		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_hwdmediashare.album.' . $value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			$value->access->set('access-edit', true);
		}

		// Now check if edit.own is available.
		elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
		{
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_user_id)
			{
				$value->access->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($itemId)
		{
			// Existing item
			$value->access->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else
		{
			// New item.
			$value->access->set('access-change', $user->authorise('core.edit.state', 'com_hwdmediashare'));
		}

		if ($itemId)
		{
			//$value->tags = new JHelperTags;
			//$value->tags->getTagIds($value->id, 'com_content.article');
                        hwdMediaShareFactory::load('customfields');
                        $cf = hwdMediaShareCustomFields::getInstance();
                        $cf->elementType = 2;
                        $value->customfields = $cf->get($value);
                        $value->thumbnail = $this->getThumbnail($value);
		}

		return $value;
	}

	/**
	 * Get the return URL.
	 *
	 * @return  string  The return URL.
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
        
	/**
	 * Method for getting the report form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getReportForm()
	{
		// Get the form.
		$form = JForm::getInstance('report', JPATH_SITE.'/components/com_hwdmediashare/models/forms/report.xml');

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
}