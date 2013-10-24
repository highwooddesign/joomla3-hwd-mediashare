<?php
/**
 * @version    SVN $Id: account.php 710 2012-10-26 09:07:51Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      16-Nov-2011 19:38:56
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modellist');

// Base this model on the user model.
require_once JPATH_SITE.'/components/com_hwdmediashare/models/user.php';

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelAccount extends hwdMediaShareModelUser
{              
        /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
                jimport('joomla.form.form');
                $form = & JForm::getInstance('link', JPATH_SITE.'/components/com_hwdmediashare/models/forms/filter.xml');
                        
                if (empty($form))
		{
			$this->setError(JText::_('COM_HWDMS_ERROR_FAILED_TO_LOAD_FORM'));
                        return false;
		}

                $object = new StdClass;
                
                $object->filter_playlist_id = $this->getState('filter.playlist_id');
                $object->filter_group_id = $this->getState('filter.group_id');
                $object->filter_album_id = $this->getState('filter.album_id');
                $object->category_id = $this->getState('filter.category_id');

                $form->bind($object);                
                
                return $form;
	}

        /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getBatchForm($data = array(), $loadData = true)
	{
		// Get the form.
                jimport('joomla.form.form');
                $form = & JForm::getInstance('batch', JPATH_SITE.'/administrator/components/com_hwdmediashare/models/forms/batch.xml');
                        
                if (empty($form))
		{
			$this->setError(JText::_('COM_HWDMS_ERROR_FAILED_TO_LOAD_FORM'));
                        return false;
		}              
                
                return $form;
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
                $user = & JFactory::getUser();
                $userId = (int) $user->id;
                
                if ($userId > 0)
                {
                        // Load album
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('UserChannel', 'hwdMediaShareTable');
                        $table->load( $userId );

                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');

                        hwdMediaShareFactory::load('tags');
                        $row->tags = hwdMediaShareTags::getInput($row);
                        hwdMediaShareFactory::load('customfields');
                        $row->customfields = hwdMediaShareCustomFields::get($row);

                        // Get hwdMediaShare config
                        $hwdms = hwdMediaShareFactory::getInstance();
                        $config = $hwdms->getConfig();

                        // Add data to object
                        if (!empty($row->alias))
                        {
                            $row->title = ( $row->alias );
                        }
                        else
                        {
                                $user = & JFactory::getUser($row->id);
                                if ($config->get('author') == 0)
                                {
                                        $row->title = $user->username;
                                }
                                else
                                {
                                        $row->title = $user->name;
                                }
                        }
                        // We select the email so that gravatar will work correctly.
                        $row->email = $user->name;

                        $this->getMedia();
                        $row->nummedia = $this->_numMedia;

                        $this->getFavourites();
                        $row->numfavourites = $this->_numFavourites;

                        $this->getAlbums();
                        $row->numalbums = $this->_numAlbums;

                        $this->getGroups();
                        $row->numgroups = $this->_numGroups;

                        $this->getPlaylists();
                        $row->numplaylists = $this->_numPlaylists;

                        $this->getSubscriptions();
                        $row->numsubscriptions = $this->_numSubscriptions;

                        return $row;
                }
                else
                {
                        $return = base64_encode(JFactory::getURI()->toString());
                        JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_PLEASE_LOGIN_TO_VIEW_YOUR_ACCOUNT') );
                        JFactory::getApplication()->redirect('index.php?option=com_users&view=login&return='.$return);
			return false;
                }
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function getItems()
	{
                $layout = JRequest::getWord('layout', '');
                $items = null;
                
                switch ($layout)
                {
                    case 'albums':
                        $items = $this->getAlbums();
                        break;
                    case 'favourites':
                        $items = $this->getFavourites();
                        break;
                    case 'groups':
                        $items = $this->getGroups();
                        break;
                    case 'playlists':
                        $items = $this->getPlaylists();
                        break;
                    case 'subscriptions':
                        $items = $this->getSubscriptions();                      
                        break;
                    default:
                        $items = $this->getMedia();
                }

		return $items;
	}
        
	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function removefavourite($pks)
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

                $db =& JFactory::getDBO();
                $query = "
                  DELETE
                    FROM  ".$db->quoteName('#__hwdms_favourites')."
                    WHERE ".$db->quoteName('element_id')." = ".implode(" OR ".$db->quoteName('element_id')." = ", $pks)."
                    AND ".$db->quoteName('element_type')." = ".$db->quote(1)."
                    AND ".$db->quoteName('user_id')." = ".$db->quote($user->id)."
                  ";
                $db->setQuery($query);               
                
                // Check for a database error.
		if (!$db->query())
                {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

                // Clear the component's cache
		$this->cleanCache();

		return true;
	}    
        
	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function unsubscribe($pks)
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

                $db =& JFactory::getDBO();
                $query = "
                  DELETE
                    FROM  ".$db->quoteName('#__hwdms_subscriptions')."
                    WHERE ".$db->quoteName('element_id')." = ".implode(" OR ".$db->quoteName('element_id')." = ", $pks)."
                    AND ".$db->quoteName('element_type')." = ".$db->quote(5)."
                    AND ".$db->quoteName('user_id')." = ".$db->quote($user->id)."
                  ";
                $db->setQuery($query);               
                
                // Check for a database error.
		if (!$db->query())
                {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

                // Clear the component's cache
		$this->cleanCache();

		return true;
	}        
}
