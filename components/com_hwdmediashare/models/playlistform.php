<?php
/**
 * @version    SVN $Id: playlistform.php 829 2012-12-21 16:45:44Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      26-Nov-2011 11:45:59
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/playlist.php';

// Import Joomla modelitem library
jimport('joomla.application.component.modelform');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelPlaylistForm extends hwdMediaShareModelPlaylist
{
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
		$this->setState('playlist.id', $id);

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
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('playlist.id');

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

                hwdMediaShareFactory::load('tags');
                $value->tags = hwdMediaShareTags::getInput($value);
                hwdMediaShareFactory::load('customfields');
                $value->customfields = hwdMediaShareCustomFields::get($value);
                $value->thumbnail = $this->getThumbnail($value);

		// Convert params field to array, so it will bind correctly to form
		if (property_exists($value, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($value->params);
			$value->params = $registry->toArray();
		}
                
                // Create controls Registry
		$value->controls = new JRegistry;
                
		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_hwdmediashare.playlist.'.$value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) 
                {
			$value->controls->set('access-edit', true);
		}
		// Now check if edit.own is available.
		else if (!empty($userId) && $user->authorise('core.edit.own', $asset)) 
                {
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_user_id) 
                        {
				$value->controls->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($itemId)
                {
			// Existing item
			$value->controls->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else 
                {
                        // New item    
			$value->controls->set('access-change', $user->authorise('core.edit.state', 'com_hwdmediashare'));
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
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 * @since	0.1
	 */
	public function save($data)
	{
                $form = parent::save($data);
		if ($form) 
                {                        
                        return true;
		}            
                return false;
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