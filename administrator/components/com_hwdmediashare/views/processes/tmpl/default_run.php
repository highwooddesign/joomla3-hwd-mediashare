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
                
JHtml::_('behavior.framework', true);
?>
<form action="<?php echo JRoute::_('index.php?option=com_hwdmediashare'); ?>" method="post" name="adminForm" id="adminForm">
        <table class="table">
                <tbody>
                        <tr>
                                <td width="90%">
                                        <div><?php echo JText::_('COM_HWDMS_TOTAL_QUEUED_PROCESSES'); ?></div>
                                </td>
                                <td width="10%">
                                        <div id="ajax-process-total-queued" class="fltrt"></div>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div><?php echo JText::_('COM_HWDMS_CURRENTLY_PROCESSING'); ?></div>
                                </td>
                                <td>
                                        <div id="ajax-container-cleantagmap" class="fltrt"><?php echo (count($this->cid) > 0 ? JText::sprintf('COM_HWDMS_N_PROCESSES', count($this->cid)) : JText::_('COM_HWDMS_ALL')); ?></div>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <div id="ajax-process-job"><?php echo JText::_('COM_HWDMS_PROCESSING_INITIALISING'); ?></div>
                                </td>
                                <td>
                                        <div id="ajax-process-result" class="fltrt"></div>
                                </td>
                        </tr>
                        <tr>
                                <td colspan="2">
                                        <div id="ajax-process-error"></div>
                                </td>
                        </tr>
                </tbody>
        </table>
	<input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
</form>
<?php
$cidsPost   = (count($this->cid) > 0 ? '&cid[]='.implode('&cid[]=', $this->cid) : '');
$textPorcessing = JText::_('COM_HWDMS_PROCESSING_PROCESSING');
$textError = JText::_('COM_HWDMS_PROCESSING_ERROR');
$textResult = JText::_('COM_HWDMS_PROCESSING_RESULT');
$textSuccess = JText::_('COM_HWDMS_PROCESSING_SUCCESS');
$textFailed = JText::_('COM_HWDMS_PROCESSING_FAILED');
$textComplete = JText::_('COM_HWDMS_PROCESSING_COMPLETE');
$textStopped = JText::_('COM_HWDMS_PROCESSING_STOPPED');
$textServerResponse = JText::_('COM_HWDMS_PROCESSING_SERVER_RESPONSE');
$textNotUnderstood = JText::_('COM_HWDMS_PROCESSING_RESPONSE_NOT_UNDERSTOOD');
$ajax = <<<EOD
window.addEvent( 'domready', function() {
        var a = new Request({
                url: 'index.php?option=com_hwdmediashare&task=process.run&format=raw$cidsPost',
                method: 'get',
                initialDelay: 1000,
                delay: 5000,
                limit: 100000,
                onRequest: function()
                {
                        $('ajax-process-error').set('html', '');        
                        $('ajax-process-result').empty().addClass('ajax-loading');
                        $('ajax-process-job').set('html', '$textPorcessing');        
                },
                onComplete: function( response )
                {
                        $('ajax-process-result').removeClass('ajax-loading');
                        $('ajax-process-result').empty();       

                        // Try to decode the response from AJAX request, and check for JSON parse errors
                        try {
                                var object = JSON.decode(response);
                        } catch(e) {
                                $('ajax-process-error').set('html', '<h3>$textError</h3><p>$textNotUnderstood</p><h3>$textServerResponse</h3><pre>' + response + '</pre>');
                                $('ajax-process-result').set('html', '<span class="label label-danger">$textError</span>');       
                                $('ajax-process-job').set('html', '$textResult');  
                        }

                        try {
                                if(object['data']['complete'])
                                {
                                        $('ajax-process-error').set('html', '');
                                        $('ajax-process-result').set('html', '<span class="label label-success">$textComplete</span>');       
                                        $('ajax-process-job').set('html', '$textStopped'); 
                                        a.stopTimer();
                                }
                                else
                                {
                                        $('ajax-process-total-queued').set('html', object['data']['total']);
                                        if (object['data']['error_msg'])
                                        {
                                                $('ajax-process-error').set('html', '<h3>$textError</h3><code>' + object['data']['error_msg'] + '</code>');
                                                $('ajax-process-result').set('html', '<span class="label label-danger">$textFailed</span>');       
                                                $('ajax-process-job').set('html', '$textResult');        
                                        }
                                        else
                                        {
                                                $('ajax-process-error').set('html', '');
                                                $('ajax-process-result').set('html', '<span class="label label-success">$textSuccess</span>'); 
                                                $('ajax-process-job').set('html', '$textResult');        
                                        }
                                }
                        } catch(e) {
                                $('ajax-process-error').set('html', '<h3>$textError</h3><p>$textNotUnderstood</p><h3>$textServerResponse</h3><pre>' + response + '</pre>');
                                $('ajax-process-result').set('html', '<span class="label label-danger">$textError</span>');       
                                $('ajax-process-job').set('html', '$textResult');  
                        }    
                }
        }).startTimer();
});
EOD;
$this->document->addScriptDeclaration($ajax);
                
