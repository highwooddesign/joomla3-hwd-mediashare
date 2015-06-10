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

class hwdMediaShareControllerEditMedia extends JControllerForm
{
	/**
	 * The name of the listing view to use with this controller.
         * 
         * @access  protected
	 * @var     string
	 */
    	protected $view_list = "media";

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
         * @access  protected
	 * @param   integer    $recordId  The primary key id for the item.
	 * @param   string     $urlVar    The name of the URL variable for the id.
	 * @return  string     The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
                        
                $return = $this->input->get('return', null, 'base64');

		if ($return)
		{
			$append .= '&return='.$return;
		}

		return $append;
	}
        
        /**
	 * Proxy for save.
	 *
	 * @access  public
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
         * @return  void
	 */
	public function save($key = null, $urlVar = null)
	{
		$result = parent::save($key, $urlVar);
                
                $return = base64_decode($this->input->get('return', null, 'base64'));

		if ($return && $this->task == 'save')
		{
                        $this->setRedirect($return);
		}

		return $result;
        }
        
        /**
	 * Proxy for cancel.
	 *
	 * @access  public
	 * @param   string  $key  The name of the primary key of the URL variable.
         * @return  void
	 */
	public function cancel($key = null)
	{
		$result = parent::cancel($key);
                
                $return = base64_decode($this->input->get('return', null, 'base64'));

		if ($return)
		{
                        $this->setRedirect($return);
		}

		return $result;
        }
                
	/**
	 * Method to run batch operations.
	 *
	 * @access  public
	 * @param   object   $model  The model.
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 */    
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model.
		$model = $this->getModel('EditMedia', '', array());

		// Preset the redirect.
		$this->setRedirect(JRoute::_('index.php?option=com_hwdmediashare&view=' . $this->view_list . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}    

        /**
	 * Method to syncronise data from the local gallery to a platform. 
	 *
	 * @access  public
         * @return  void
	 */
	public function syncToPlatform()
	{
		$this->task = 'apply';
                if (parent::save())
                {                      
                        // Get a table instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');

                        // Attempt to load the table row.
                        $return = $table->load($this->input->getInt('id'));

                        // Check for a table object error.
                        if ($return === false && $table->getError())
                        {
                                $this->setError($table->getError());
                                return false;
                        }

                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');

                        if ($item->type == 6 && $item->storage)
                        {
                                $pluginClass = 'plgHwdmediashare' . $item->storage;
                                $pluginPath = JPATH_ROOT . '/plugins/hwdmediashare/' . $item->storage . '/' . $item->storage . '.php';
                                if (file_exists($pluginPath))
                                {
                                        JLoader::register($pluginClass, $pluginPath);
                                        $HWDplatform = call_user_func(array($pluginClass, 'getInstance'));
                                        if (!$result = $HWDplatform->syncToPlatform($item))
                                        {
                                                JFactory::getApplication()->enqueueMessage($HWDplatform->getError());
                                        }
                                }
                        }
		}
	}
        
        /**
	 * Method to syncronise data from a platform to the local gallery. 
	 *
	 * @access  public
         * @return  void
	 */
	public function syncFromPlatform()
	{
		$this->task = 'apply';
                if (parent::save())
                {
                        // Get a table instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('media', 'hwdMediaShareTable');
                        
                        // Attempt to load the table row.
                        $return = $table->load($this->input->getInt('id'));

                        // Check for a table object error.
                        if ($return === false && $table->getError())
                        {
                                $this->setError($table->getError());
                                return false;
                        }
                
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');

                        if ($item->type == 6 && $item->storage)
                        {
                                $pluginClass = 'plgHwdmediashare' . $item->storage;
                                $pluginPath = JPATH_ROOT . '/plugins/hwdmediashare/' . $item->storage . '/' . $item->storage . '.php';
                                if (file_exists($pluginPath))
                                {
                                        JLoader::register($pluginClass, $pluginPath);
                                        $HWDplatform = call_user_func(array($pluginClass, 'getInstance'));
                                        if (!$result = $HWDplatform->syncFromPlatform($item))
                                        {
                                                JFactory::getApplication()->enqueueMessage($HWDplatform->getError());
                                        }
                                }
                        }
		}
	}        
}
