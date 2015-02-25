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

class hwdMediaShareControllerConfiguration extends JControllerForm
{
	/**
	 * The name of the listing view to use with this controller.
         * 
         * @access  protected
	 * @var     string
	 */
    	protected $view_list = "dashboard";
        
	/**
	 * Method to test background execution and display results.
	 *
	 * @access  public
         * @return  void
	 */
	public function background()
	{
                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $result = false;
                
                // Delete test file if already exists.
                jimport('joomla.filesystem.file' );
                $filename = JPATH_SITE.'/tmp/hwdms.background';
		if (JFile::exists($filename)) JFile::delete($filename);

                $cli = JPATH_SITE.'/administrator/components/com_hwdmediashare/cli.php';

                // Try to create test file in background.
		if(substr(PHP_OS, 0, 3) != "WIN") 
                {
			exec("env -i ".$config->get('path_php')." " . $cli . " test > /dev/null 2>&1");
		} 
                else 
                {
                        pclose(popen("start /B ". $config->get('path_php')." " . $cli . " test", "r"));  
		}
                
                // Sleep for 2 seconds.
		usleep(1000000);
                
                // Check if file exists.
                if (JFile::exists($filename)) 
                {
                        JFile::delete($filename);
                        $result = true;
                }  
                
                if ($result)
                {
                        echo "<h2>" . JText::_('COM_HWDMS_GOOD_NEWS') . "</h2>";
                        echo "<p>" . JText::_('COM_HWDMS_AUTO_PROCESS_SUCCESS') . "</p>";
                } 
                else
                {
                        echo "<h2>" . JText::_('COM_HWDMS_BAD_NEWS') . "</h2>";
                        echo "<p>" . JText::_('COM_HWDMS_AUTO_PROCESS_FAIL') . "</p>";
                }
	}
}
