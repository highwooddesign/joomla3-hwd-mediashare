<?php
/**
 * @version    SVN $Id: process.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      09-Nov-2011 09:17:39
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * hwdMediaShare Controller
 */
class hwdMediaShareControllerProcess extends JControllerForm
{
        /**
	 * Method to run processes from a modal window periodically.
	 * @since	0.1
	 */
        function run()
        {
                $document = JFactory::getDocument();
                $document->addStyleSheet(JURI::root() . 'media/com_hwdmediashare/assets/css/administrator.css');
                JHtml::_('behavior.framework', true);
                $cids	    = JRequest::getVar('cid', array(), '', 'array');
                $cidsString = implode(",", $cids);
                $cidsPost   = (count($cids) > 0 ? '&cid[]='.implode('&cid[]=', $cids) : '');                
                ?>
<table class="adminlist">
<tbody>        
    <tr class="row0">
        <td>
            <?php echo JText::_('COM_HWDMS_TOTAL_QUEUED_PROCESSES'); ?>
        </td>
        <td>
            <div id="ajax-remaining"></div>      
        </td>
    </tr>
    <tr class="row1">
        <td>
            <?php echo JText::_('COM_HWDMS_QUEUED_PROCESSES'); ?>
        </td>
        <td>
            <?php echo (count($cids) > 0 ? $cidsString : JText::_('COM_HWDMS_ALL')); ?>            
        </td>
    </tr>
    <tr class="row0">
        <td colspan="2">
            <div id="ajax-loader"></div>  
        </td>
    </tr> 
</tbody>
</table>
                <?php
$ajax = <<<EOD
window.addEvent( 'domready', function() {
        var a = new Request({
                url: 'index.php?option=com_hwdmediashare&task=process.run&format=raw$cidsPost',
                method: 'get',
                initialDelay: 1000,
                delay: 2000,
                limit: 100000,
                onRequest: function()
                {
                        $('ajax-loader').empty().addClass('ajax-loading');
                },
                onComplete: function( response )
                {
                        $('ajax-loader').removeClass('ajax-loading');
                        var object = JSON.decode(response);
                        if(object.complete)
                        {
                                $('ajax-loader').set('html', '<strong>Processing complete</strong>');
                                a.stopTimer();
                        }
                        else
                        {
                                $('ajax-remaining').set('html', object['data']['total']);
                                if (object['data']['error_msg'])
                                {
                                    $('ajax-loader').set('html', object['data']['error_msg']);
                                }
                                else
                                {
                                    $('ajax-loader').set('html', 'No errors');
                                }
                                
                        }
                }
        }).startTimer();
});
EOD;
                $document->addScriptDeclaration( $ajax );
        }
}
