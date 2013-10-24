<?php
/**
 * @version    SVN $Id: upload.php 474 2012-08-17 09:19:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      01-Dec-2011 09:57:49
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/models/addmedia.php';

// Import Joomla modelitem library
jimport('joomla.application.component.modelform');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelUpload extends hwdMediaShareModelAddMedia 
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.upload';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_hwdmediashare';

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

		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $this->setState('params', $config);

		parent::populateState();
	}

      	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
        }
        
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getStandardExtensions()
	{
                hwdMediaShareFactory::load('upload');
                $standardExtensions = hwdMediaShareUpload::getAllowedExtensions('standard');
                $this->setState('standardExtensions', $standardExtensions);
                return $standardExtensions;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getLargeExtensions()
	{
		hwdMediaShareFactory::load('upload');
                $largeExtensions = hwdMediaShareUpload::getAllowedExtensions('large');
                $this->setState('largeExtensions', $largeExtensions);
                return $largeExtensions;
	}
        
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getPlatformExtensions()
	{
		hwdMediaShareFactory::load('upload');
                $platformExtensions = hwdMediaShareUpload::getAllowedExtensions('platform');
                $this->setState('platformExtensions', $platformExtensions);
                return $platformExtensions;
	}
        
        
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getTerms()
	{
		// Load the parameters.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                if ($config->get('upload_terms') == 0) return false;
                if (JFactory::getApplication()->getUserState( "media.terms" ) == 1) return false;
                return true;    
	}
        
        /**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function terms($pk = null)
	{
                $data = JRequest::getVar('jform', array(), 'post', 'array');
                if (isset($data['terms']))
                {
                        JFactory::getApplication()->setUserState( "media.terms", "1" );

                }    
                return true;
	}
        
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	0.1
	 */
	public function getAssocAlbum()
	{
                $albumId = JRequest::getInt( 'album_id', '0' );
                if ($albumId > 0)
                {                    
                        // Load album
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Album', 'hwdMediaShareTable');
                        $table->load( $albumId );

                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');

                        return $row;
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
	public function getAssocGroup()
	{
                $groupId = JRequest::getInt( 'group_id', '0' );
                if ($groupId > 0)
                {                    
                        // Load album
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Group', 'hwdMediaShareTable');
                        $table->load( $groupId );

                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');

                        return $row;
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
	public function getAssocCategory()
	{
                $categoryId = JRequest::getInt( 'category_id', '0' );                
                if ($categoryId > 0)
                {                    
                        // Load album
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Category', 'hwdMediaShareTable');
                        $table->load( $categoryId );

                        $properties = $table->getProperties(1);
                        $row = JArrayHelper::toObject($properties, 'JObject');

                        return $row;
                }
                return false;
	} 
}
