<?php
/**
 * @package     Joomla.site
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class hwdMediaShareProcesses extends JObject
{
	/**
	 * The total number of queued processes.
         * 
         * @access      public
	 * @var         integer
	 */    
	public $_total;
        
	/**
	 * The flag for completion.
         * 
         * @access      public
	 * @var         boolean
	 */    
	public $_complete;
        
	/**
	 * The output from the process.
         * 
         * @access      public
	 * @var         string
	 */    
	public $_output;
        
	/**
	 * Class constructor.
	 *
	 * @access  public
	 * @param   mixed  $properties  Either and associative array or another
	 *                              object to set the initial properties of the object.
         * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);
	}

	/**
	 * Returns the hwdMediaShareProcesses object, only creating it if it
	 * doesn't already exist.
         * 
	 * @access  public
         * @static
	 * @return  hwdMediaShareProcesses Object.
	 */  
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance))
                {
			$c = 'hwdMediaShareProcesses';
                        $instance = new $c;
		}

		return $instance;
	}
        
	/**
	 * Method to add a process for a media.
         * 
         * @access  public
         * @param   object   $media        The associated media.
         * @param   integer  $processType  The API value for the type of process.
         * @return  mixed    Process ID on true, false on fail.
	 */
	public function addProcess($media, $processType = null)
	{
                // Initialise variables.                        
                $date = JFactory::getDate();
                $user =  JFactory::getUser();

                // Load the process table.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('Process', 'hwdMediaShareTable');
                
                // Define a new entry.
                $post                    = array();
                $post['process_type']    = $processType;
                $post['media_id']        = $media->id;
                $post['status']          = 1;
                $post['attempts']        = 0;
                $post['created_user_id'] = $user->id;
                $post['created']         = $date->toSql();

                // Attempt to save the details to the database.
                if (!$table->save($post))
                {
                        $this->setError($table->getError());
                        return false;
                }
                
                return $table->id;
	}

	/**
	 * Method to select and run a queued process.
         * 
         * @access  public
         * @param   array   $array      An array of processes.
         * @return  boolean True on success.
	 */
	public function run($cids = array())
	{
                // Initialise variables. 
                $db = JFactory::getDBO();
                
                // Attempt to increase MySQL timeout.
                $db->setQuery('SET SESSION wait_timeout = 28800');
                $db->execute();
                
                // Get total number of queued processes.
                $this->_total = $this->countQueue();
                
                // Select the next process from the queue.
                if ($process = $this->nextProcess($cids))
                {
                        if (isset($process->process_type))
                        {
                                $method = 'process' . $process->process_type;
                                if (method_exists($this, $method))
                                {
                                        // Set the modified date for the process.
                                        $this->setModified($process);

                                        // Run the process.
                                        $result = $this->$method($process);

                                        // Ping the database to check connection hasn't timed out.
                                        $db->connected();
        
                                        // Update process.
                                        $this->update($process, $result);

                                        if ($result->status == 3)
                                        {
                                                $this->setError($result->output);
                                                return false;
                                        }
                                }
                                else
                                {
                                        $this->setError(JText::_('COM_HWDMS_PROCESS_FUNCTION_NOT_EXIST'));
                                        return false;
                                } 
                        }
                        else
                        {
                                $this->setError(JText::_('COM_HWDMS_PROCESS_NOT_EXIST'));
                                return false;
                        }   
                }
                else
                {
                        $this->_complete = true;
                }
                        
                return true;
	}
        
	/**
	 * Method to select the next queued process.
         * 
         * @access  public
         * @param   array   $cids   An array of processes.
         * @return  object  The process.
	 */
	public function nextProcess($cids = array())
	{
                // Initialise variables. 
                $db = JFactory::getDBO();
    
                $query = $db->getQuery(true)
                        ->select('a.*, e.media_type')
                        ->from('#__hwdms_processes AS a')
                        ->join('LEFT', '#__hwdms_media AS m ON m.id = a.media_id')
                        ->join('LEFT', '#__hwdms_ext AS e ON e.id = m.ext_id')
                        ->where('(a.status = ' . $db->quote(1) . ' || a.status = ' . $db->quote(3) . ')')
                        ->where('a.attempts < ' . $db->quote(5))
                        ->order('a.modified ASC');
                
                // When passed an array of IDs, then we can only select one of these.
                if (count($cids) > 0)
                {
                        $query->where('a.id IN ('.implode(', ', $cids).')');
                }

                // If we are running over CLI then try to prevent multiple executions of the same process.
                $args = @$GLOBALS['argv'];
                if ($args[1] == 'process')
                {
                        $query->where('a.modified < DATE_SUB(SYSDATE(), INTERVAL 1 MINUTE)');
                }                

                try
                {      
                        $db->setQuery($query);
                        return $db->loadObject();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }
	}
        
	/**
	 * Method to count all queued processes.
         * 
         * @access  public
         * @return  integer The number of processes.
	 */
	public function countQueue()
	{
                // Initialise variables. 
                $db = JFactory::getDBO();
                
                $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from('#__hwdms_processes')
                        ->where('(status = ' . $db->quote(1) . ' || status = ' . $db->quote(3) . ')')
                        ->where('attempts < ' . $db->quote(5));
                try
                {                
                        $db->setQuery($query);
                        return $db->loadResult();
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }
	}
        
	/**
	 * Method to set the modified date for a process.
         * 
         * @access  public
         * @param   object  $process    The process being updated.
         * @return  boolean True on success.
	 */
	public function setModified($process)
	{
                // Initialise variables. 
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();

                // Create an object for updating the record.
                $object = new stdClass();
                $object->id = $process->id;
                $object->modified = $date->toSql();
                $object->modified_user_id = $user->id;
                
                try
                {                
                        $result = $db->updateObject('#__hwdms_processes', $object, 'id');
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }
                
                return true;
	}        
        
	/**
	 * Method to update a process, after execution.
         * 
         * @access  public
         * @param   object  $process    The process being updated.
         * @param   object  $result     The result of the execution.
         * @return  boolean True on success.
	 */
	public function update($process, $result)
	{   
                // Initialise variables. 
                $db = JFactory::getDBO();
                $date = JFactory::getDate();
                $user = JFactory::getUser();

                // Create an object for updating the record.
                $object = new stdClass();
                $object->id = $process->id;
                $object->status = isset($result->status) ? $result->status : 3;
                $object->modified = $date->toSql();
                $object->modified_user_id = $user->id;
                $object->attempts = $process->attempts + 1;

                try
                {                
                        $result = $db->updateObject('#__hwdms_processes', $object, 'id');
                }
                catch (Exception $e)
                {
                        $this->setError($e->getMessage());
                        return false;
                }
	}

	/**
	 * Method to add a process log.
         * 
         * @access  public
         * @param   object  $item   The log data.
         * @return  boolean True on success.
	 */
	public function addLog($item)
	{
                // Initialise variables.                        
                $date = JFactory::getDate();
                $user = JFactory::getUser();

                // Load the processlog table.
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table = JTable::getInstance('ProcessLog', 'hwdMediaShareTable');
                
                // Load the Joomla filters.
                jimport('joomla.filter.filterinput');
                $safeFilter = JFilterInput::getInstance();
                
                // Clean, trim and truncate the log data.
                $input = $safeFilter->clean(trim($item->input));
                $output = is_array($item->output) ? implode("\n", $item->output) : $item->output;
                $output = $safeFilter->clean(trim($output));
                $output = JHtml::_('string.truncate', $output, 5120, false, false);
                
                // Define a new entry.
                $post                       = array();
                $post['process_id']         = $item->process_id;
                $post['input']              = $input;
                $post['output']             = $output;
                $post['status']             = $item->status;
                $post['created_user_id']    = $user->id;
                $post['created']            = $date->toSql();

                // Attempt to save the details to the database.
                if (!$table->save($post))
                {
                        $this->setError($table->getError());
                        return false;
                }
                
                return true;
	}
        
	/**
	 * Method to reset a log entry.
         * 
         * @access  public
         * @param   object  $process    The process being updated.
         * @return  object  An empty log.
	 */
	public function resetLog($process)
	{
                // Setup log.
                $log = new StdClass;
                $log->process_id = $process->id;
                $log->input = '';
                $log->output = '';
                $log->status = 3; // Default to fail.
                return $log;
	}
        
	/**
	 * Method to get human readable process type.
         * 
         * @access  public
         * @static
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public static function getType($process)
	{
                switch ($process->process_type)
                {
                        case 1:
                                return JText::_('COM_HWDMS_GENERATE_JPG_75_LABEL');
                        break; 
                        case 2:
                                return JText::_('COM_HWDMS_GENERATE_JPG_100_LABEL');
                        break; 
                        case 3:
                                return JText::_('COM_HWDMS_GENERATE_JPG_240_LABEL');
                        break; 
                        case 4:
                                return JText::_('COM_HWDMS_GENERATE_JPG_500_LABEL');
                        break; 
                        case 5:
                                return JText::_('COM_HWDMS_GENERATE_JPG_640_LABEL');
                        break; 
                        case 6:
                                return JText::_('COM_HWDMS_GENERATE_JPG_1024_LABEL');
                        break; 
                        case 7:
                                return JText::_('COM_HWDMS_GENERATE_AUDIO_MP3_LABEL');
                        break; 
                        case 8:
                                return JText::_('COM_HWDMS_GENERATE_AUDIO_OGG_LABEL');
                        break; 
                        case 9:
                                return JText::_('COM_HWDMS_GENERATE_FLV_240_LABEL');
                        break; 
                        case 10:
                                return JText::_('COM_HWDMS_GENERATE_FLV_360_LABEL');
                        break; 
                        case 11:
                                return JText::_('COM_HWDMS_GENERATE_FLV_480_LABEL');
                        break; 
                        case 12:
                                return JText::_('COM_HWDMS_GENERATE_MP4_360_LABEL');
                        break; 
                        case 13:
                                return JText::_('COM_HWDMS_GENERATE_MP4_480_LABEL');
                        break; 
                        case 14:
                                return JText::_('COM_HWDMS_GENERATE_MP4_720_LABEL');
                        break; 
                        case 15:
                                return JText::_('COM_HWDMS_GENERATE_MP4_1080_LABEL');
                        break; 
                        case 16:
                                return JText::_('COM_HWDMS_GENERATE_WEBM_360_LABEL');
                        break; 
                        case 17:
                                return JText::_('COM_HWDMS_GENERATE_WEBM_480_LABEL');
                        break; 
                        case 18:
                                return JText::_('COM_HWDMS_GENERATE_WEBM_720_LABEL');
                        break; 
                        case 19:
                                return JText::_('COM_HWDMS_GENERATE_WEBM_1080_LABEL');
                        break; 
                        case 20:
                                return JText::_('COM_HWDMS_INJECT_METADATA_LABEL');
                        break; 
                        case 21:
                                return JText::_('COM_HWDMS_MOVE_MOOV_ATOM_LABEL');
                        break; 
                        case 22:
                                return JText::_('COM_HWDMS_GET_DURATION_LABEL');
                        break; 
                        case 23:
                                return JText::_('COM_HWDMS_GET_TITLE_LABEL');
                        break; 
                        case 24:
                                return JText::_('COM_HWDMS_GENERATE_OGG_360_LABEL');
                        break; 
                        case 25:
                                return JText::_('COM_HWDMS_GENERATE_OGG_480_LABEL');
                        break; 
                        case 26:
                                return JText::_('COM_HWDMS_GENERATE_OGG_720_LABEL');
                        break; 
                        case 27:
                                return JText::_('COM_HWDMS_GENERATE_OGG_1080_LABEL');
                        break; 
                }
	}
        
	/**
	 * Method to get human readable process status.
         * 
         * @access  public
         * @static
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public static function getStatus($process)
	{
                switch ($process->status) 
                {
                        case 1:
                                return JText::_('COM_HWDMS_QUEUED');
                        break;
                        case 2:
                                return JText::_('COM_HWDMS_SUCCESSFUL');
                        break;
                        case 3:
                                return JText::_('COM_HWDMS_FAILED');
                        break;
                        case 4:
                                return JText::_('COM_HWDMS_UNNECESSARY');
                        break;
                }
                return ;
	}
        
	/**
	 * Method to run process type 1:
         * Create square image (75x75)
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process1($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->processImage($process, 2, 75, true);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        $HWDimages = hwdMediaShareImages::getInstance();
                        return $HWDimages->processImage($process, 2, 75, true);
                }
	}
        
	/**
	 * Method to run process type 2:
         * Create thumbnail image (100px maximum)
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process2($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->processImage($process, 3, 100);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        $HWDimages = hwdMediaShareImages::getInstance();
                        return $HWDimages->processImage($process, 3, 100);
                }
	}

	/**
	 * Method to run process type 3:
         * Create small image (240px maximum)
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process3($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->processImage($process, 4, 240);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        $HWDimages = hwdMediaShareImages::getInstance();
                        return $HWDimages->processImage($process, 4, 240);
                }
	}

	/**
	 * Method to run process type 4:
         * Create medium (500) image (500px maximum)
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process4($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->processImage($process, 5, 500);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        $HWDimages = hwdMediaShareImages::getInstance();
                        return $HWDimages->processImage($process, 5, 500);
                }
	}

	/**
	 * Method to run process type 5:
         * Create medium (640) image (640px maximum)
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process5($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->processImage($process, 6, 640);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        $HWDimages = hwdMediaShareImages::getInstance();
                        return $HWDimages->processImage($process, 6, 640);
                }
	}

	/**
	 * Method to run process type 6:
         * Create large image (1024px maximum)
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process6($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->processImage($process, 7, 1024);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        $HWDimages = hwdMediaShareImages::getInstance();
                        return $HWDimages->processImage($process, 7, 1024);
                }
	}

	/**
	 * Method to run process type 7:
         * Create mp3 audio
         * 
         * @since   0.1
	 **/
	public function process7($process)
	{
                hwdMediaShareFactory::load('audio');
                $HWDaudio = hwdMediaShareAudio::getInstance();
                return $HWDaudio->processMp3($process, 8);
	}
        
	/**
	 * Method to run process type 8:
         * Create ogg audio
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process8($process)
	{
                hwdMediaShareFactory::load('audio');
                $HWDaudio = hwdMediaShareAudio::getInstance();
                return $HWDaudio->processOgg($process, 9);
	}       

	/**
	 * Method to run process type 9, 10 & 11:
         * Create flv video
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process9($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processFlv($process, 11, 240);
	}   
	public function process10($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processFlv($process, 12, 360);
	}  
	public function process11($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processFlv($process, 13, 480);
	} 
        
	/**
	 * Method to run process type 12, 13, 14 & 15:
         * Create mp4 video
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process12($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processMp4($process, 14, 360);
	}   
	public function process13($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processMp4($process, 15, 480);
	}   
	public function process14($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processMp4($process, 16, 720);
	}   
	public function process15($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processMp4($process, 17, 1080);
	}   
        
	/**
	 * Method to run process type 16, 17, 18 & 19:
         * Create webm video
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process16($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processWebm($process, 18, 360);
	}   
	public function process17($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processWebm($process, 19, 480);
	}   
	public function process18($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processWebm($process, 20, 720);
	}   
	public function process19($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processWebm($process, 21, 1080);
	} 
        
        /**
	 * Method to run process type 20:
         * Inject metadata
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process20($process)
	{
                $log = $this->resetLog($process);
                $log->status = 4;
                return $log;
	} 
        
        /**
	 * Method to run process type 21:
         * Move moov atom
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process21($process)
	{
                $log = $this->resetLog($process);
                $log->status = 4;
                return $log;
	} 
        
	/**
	 * Method to run process type 22:
         * Get duration
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process22($process)
	{
                if ($process->media_type == 1)
                {
                        // We can use the video method for audio too.                 
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->getDuration($process);
                }
                elseif ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->getDuration($process);
                }
	}
        
	/**
	 * Method to run process type 23:
         * Get title
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process23($process)
	{
                if ($process->media_type == 1)
                {
                        // We can use the video method for audio too.                 
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->getTitle($process);
                }
                elseif ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        $HWDvideos = hwdMediaShareVideos::getInstance();
                        return $HWDvideos->getTitle($process);
                }
	}
        
	/**
	 * Method to run process type 23, 24, 25 & 26:
         * Create ogg video
         * 
         * @access  public
         * @param   object  $process   The process.
         * @return  boolean True on success.
	 */
	public function process24($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processOgg($process, 22, 360);
	}   
	public function process25($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processOgg($process, 23, 480);
	}   
	public function process26($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processOgg($process, 24, 720);
	}   
	public function process27($process)
	{
                hwdMediaShareFactory::load('videos');
                $HWDvideos = hwdMediaShareVideos::getInstance();
                return $HWDvideos->processOgg($process, 25, 1080);
	} 
}