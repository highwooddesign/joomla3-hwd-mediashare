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

class JFormFieldDuration extends JFormField
{
	/**
	 * The name of the form field type.
         * 
         * @access      protected
	 * @var         string
	 */
	protected $type = 'Duration';

	/**
	 * Method to get the field options.
	 *
	 * @access	protected
	 * @return      array       The field option objects.
	 */
	protected function getInput()
	{
                // Initialise variables.
		$doc = JFactory::getDocument();            

                // Add page assets.
                JHtml::_('bootstrap.framework');
                $doc->addStyleSheet(JURI::root(true).'/media/com_hwdmediashare/assets/css/bootstrap-timepicker.min.css');
                $doc->addScript(JURI::root(true).'/media/com_hwdmediashare/assets/javascript/bootstrap-timepicker.min.js');
                
                // Convert seconds into time object.
                $duration = hwdMediaShareMedia::secondsToTime($this->value, true); 

                // Start capturing output into a buffer.
                ob_start();
                ?>
                <input type="hidden" value="<?php echo $this->value; ?>" id="jform_duration" name="jform[duration]">
                <div class="input-append bootstrap-timepicker">
                    <input id="durationpicker" data-show-seconds="true" data-show-meridian="false" data-default-time="<?php echo $duration->h . ':' . $duration->m . ':' . $duration->s; ?>" data-minute-step="1" data-second-step="1" data-show-inputs="false" type="text" class="input-small">
                    <span class="add-on"><i class="icon-clock"></i></span>
                </div>
                <script type="text/javascript">
                jQuery( document ).ready(function( $ ) {
                    $('#durationpicker').timepicker().on('changeTime.timepicker', function(e) {
                        var duration = (60*60*e.time.hours) + (60*e.time.minutes) + (e.time.seconds);
                        console.log('The duration is ' + duration);
                        $('#jform_duration').val(duration);
                    })
                    $('#durationpicker').timepicker().on('hide.timepicker', function(e) {
                        $('#durationpicker').css('display' , 'inline-block');
                    });
                });           
                </script>
                <?php
                $html = ob_get_contents();
                ob_end_clean();

                return $html;
	}
}
