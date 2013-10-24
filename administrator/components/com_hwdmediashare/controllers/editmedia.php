<?php
/**
 * @version    SVN $Id: editmedia.php 920 2013-01-16 11:07:08Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerEditMedia extends JControllerForm
{
	var $view_list = "media";
        var $elementType = 1;
        
	/**
	 * Method to view media
	 * @since	0.1
	 */
	public function view()
	{
                $mediaId = JRequest::getInt('id');
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
                $table->load( $mediaId );
                $properties = $table->getProperties(1);
                $item = JArrayHelper::toObject($properties, 'JObject');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('downloads');
                // Why was this added? It converts the integer media_type value to a string!
                // $item->media_type = hwdMediaShareMedia::getMediaType($item);
                echo hwdMediaShareMedia::get($item);
	}
        
	/**
	 * Method to view media
	 * @since	0.1
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
	 * Method to view media
	 * @since	0.1
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
