<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */
/**
 * UNFINISHED
 */
defined('_JEXEC') or die;

class hwdMediaShareControllerEditMedia extends JControllerForm
{
    	/**
	 * The URL view list variable.
	 * @var    string
	 */
    	protected $view_list = "media";
        
	/**
	 * The ID of this element type.
	 * @var    string
	 */
    	protected $elementType = 1;
                
        /**
	 * Method to syncronise data from the local gallery to a remote CDN.
	 * @return	void
	 */
	public function syncToCdn()
	{
		$this->task = 'apply';
                if (parent::save())
                {                      
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                        $table->load(JRequest::getVar('id'));
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');

                        if ($item->type == 6)
                        {
                                $pluginClass = 'plgHwdmediashare'.$item->storage;
                                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$item->storage.'/'.$item->storage.'.php';

                                // Import hwdMediaShare plugins
                                if (file_exists($pluginPath))
                                {
                                        JLoader::register($pluginClass, $pluginPath);
                                        $platform = call_user_func(array($pluginClass, 'getInstance'));
                                        $platform->syncToCdn();
                                }
                        }
			return true;
		}
	}
        
        /**
	 * Method to syncronise data from a remote CDN to the local gallery.
	 * @return	void
	 */
	public function syncFromCdn()
	{
		$this->task = 'apply';
                if (parent::save())
                {                      
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                        $table->load(JRequest::getVar('id'));
                        $properties = $table->getProperties(1);
                        $item = JArrayHelper::toObject($properties, 'JObject');

                        if ($item->type == 6)
                        {
                                $pluginClass = 'plgHwdmediashare'.$item->storage;
                                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$item->storage.'/'.$item->storage.'.php';

                                // Import hwdMediaShare plugins
                                if (file_exists($pluginPath))
                                {
                                        JLoader::register($pluginClass, $pluginPath);
                                        $platform = call_user_func(array($pluginClass, 'getInstance'));
                                        $platform->syncFromCdn();
                                }
                        }
			return true;
		}
	}
}
