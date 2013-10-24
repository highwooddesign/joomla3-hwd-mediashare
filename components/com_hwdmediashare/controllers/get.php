<?php
/**
 * @version    SVN $Id: get.php 1591 2013-06-14 13:31:30Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-May-2011 16:53:20
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerGet extends JControllerForm
{
	/**
	 * Method to render the display html of a media item
	 * @since	0.1
	 */
        function embed()
        {
                // Load hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $document = JFactory::getDocument();

                $config->set('mediaitem_size', JRequest::getInt('width', 560));
                $config->set('mediaitem_width', JRequest::getInt('width', 560));
                $config->set('mediaitem_height', JRequest::getInt('height', 315));
                //$config->set('mediaitem_size', '100%');
                //$config->set('mediaitem_width', '100%');
                //$config->set('mediaitem_height', '100%');

                $document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');

                $document->addStyleDeclaration('
#main, .contentpane {
  margin: 0!important;
  padding: 0!important;
}
.embed-container {
  width:100%;
  height:'.JRequest::getInt('height', 315).'px;
  overflow:hidden;
  text-align:center;
  color:#fff!important;
  background:#000000; /* Old browsers */
  position:fixed;
  top:0;
  left:0;
}
#hwd-container, #hwd-container .media-item-container .media-item-full {
  margin:0;
}
#hwd-container .media-respond {
  max-height:'.JRequest::getInt('height', 315).'px;    
}
');
                // Start output
                ob_start();
                ?>
<div id="hwd-container" class="embed-container">
  <div id="media-item-container" class="media-item-container">
    <div class="media-item-full" id="media-item" style="width:100%;">    
                <?php
                JRequest::setVar('tmpl','component');
                $id = JRequest::getInt( 'id' , '' );
                if ($id > 0)
                {
                        jimport( 'joomla.application.component.model' );
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $model =& JModelLegacy::getInstance('MediaItem', 'hwdMediaShareModel', array('ignore_request' => true));

                        // Set application parameters in model
                        $app = JFactory::getApplication();
                        $appParams = $app->getParams();
                        $model->setState('params', $appParams);

                        $user = JFactory::getUser();
                        if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) &&  (!$user->authorise('core.edit', 'com_hwdmediashare')))
                        {
                                // Limit to published for people who can't edit or edit.state.
                                $model->setState('filter.published',	1);
                                $model->setState('filter.status',	1);

                                // Filter by start and end dates.
                                $model->setState('filter.publish_date', true);
                        }
                        else
                        {
                                // Limit to published for people who can't edit or edit.state.
                                $model->setState('filter.published',	array(0,1));
                                $model->setState('filter.status',	1);
                        }

                        // Load the object state.
                        $model->setState('media.id', $id);

                        if ($item = $model->getItem())
                        {
                                // Check for errors.
                                if (count($errors = $model->getErrors()))
                                {
                                        echo '<h3>'.implode('<br />', $errors).'</h3>';
                                        echo '<p>Try viewing the <a href="'.JRoute::_(hwdMediaShareHelperRoute::getMediaItemRoute($item->id)).'" target="_blank" style="color:#fff;">original media</a>.</p>';
                                        return false;
                                }
                                else
                                {
                                        hwdMediaShareFactory::load('media');
                                        hwdMediaShareFactory::load('downloads');

                                        $item->media_type = hwdMediaShareMedia::loadMediaType($item);

                                        // Print the media
                                        echo hwdMediaShareMedia::get($item);
                                }
                        }
                }
                ?>
    <div style="clear:both;"></div>
    </div>
  </div>            
</div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                print $html;
                return true;
        }
}
