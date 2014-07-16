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

class hwdMediaShareTableFileExtension extends JTable
{
	/**
	 * Class constructor. Overridden to explicitly set the table and key fields.
	 *
	 * @access	public
	 * @param       JDatabaseDriver  $db     JDatabaseDriver object.
         * @return      void
	 */ 
	public function __construct($db)
	{
		parent::__construct('#__hwdms_ext', 'id', $db);
	}
        
        
	/**
	 * Method to store a row in the database from the JTable instance properties.
	 *
         * @access  public
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 * @return  boolean  True on success.
	 * @link    http://docs.joomla.org/JTable/store
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		if ($this->id)
		{
			// Existing item.
			$this->modified		= $date->toSql();
			$this->modified_user_id	= $user->get('id'); 
		}
		else
		{
			// New record. The created and created_by fields can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date->toSql();
			}
			if (empty($this->created_user_id))
			{
				$this->created_user_id = $user->get('id');
			}  
		}

		// Set publish_up to null date if not set.
		if (!$this->publish_up)
		{
			$this->publish_up = $this->_db->getNullDate();
		}

		// Set publish_down to null date if not set.
		if (!$this->publish_down)
		{
			$this->publish_down = $this->_db->getNullDate();
		}

		return parent::store($updateNulls);
	}
        
	/**
	 * Method to perform sanity checks on the JTable instance properties to ensure
	 * they are safe to store in the database.
	 *
         * @access  public
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 * @link    http://docs.joomla.org/JTable/check
	 */
	public function check()
	{
		// Check for valid name.
		if (trim($this->ext) == '')
		{
			$this->setError(JText::_('COM_HWDMS_ERROR_SAVE_NO_EXT'));
			return false;
		}
                
		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

		return true;
	}        
}
