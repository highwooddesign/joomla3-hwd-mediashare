<?php
/**
 * @version    SVN $Id: processes.php 1457 2013-04-30 10:48:55Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * hwdMediaShare framework processes class
 *
 * @package hwdMediaShare
 * @since   0.1
 */
class hwdMediaShareProcesses extends JObject
{
	var $_total;
	var $_complete;
        
        /**
	 * Class constructor.
	 *
	 * @param   array  $config  A configuration array including optional elements.
	 *
	 * @since   0.1
	 */
	public function __construct($config = array())
	{
	}

	/**
	 * Returns the hwdMediaShareProcesses object, only creating it if it
	 * doesn't already exist.
	 *
	 * @return  hwdMediaShareProcesses A hwdMediaShareProcesses object.
	 * @since   0.1
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
	 * Method to add a process
         * 
         * @since   0.1
	 **/
	public function add( $media , $processType = null )
	{
                $date =& JFactory::getDate();

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $row =& JTable::getInstance('Process', 'hwdMediaShareTable');

                $post                    = array();
                $post['process_type']    = $processType;
                $post['media_id']        = $media->id;
                $post['status']          = '1';
                $post['attempts']        = '0';
                $post['created_user_id'] = '';
                $post['created']         = $date->format('Y-m-d H:i:s');

                // Bind it to the table
                if (!$row->bind( $post ))
                {
                        return JError::raiseWarning( 500, $row->getError() );
                }

                // Store it in the db
                if (!$row->store())
                {
                        return JError::raiseError(500, $row->getError() );
                }
                
                return $row->id;
	}
        
	/**
	 * Method to select and run a queued process
         * 
         * @since   0.1
	 **/
	public function run($cids = array())
	{
                // Create a new query object.
                $db = JFactory::getDBO();
                
                // Get total queued tasks
                $this->_total = hwdMediaShareProcesses::getQueue();
                
                // Get next task
                $task = hwdMediaShareProcesses::getTask($cids);

                if ($task)
                {
                        if (isset($task->process_type))
                        {
                                if (method_exists('hwdMediaShareProcesses', 'process'.$task->process_type))
                                {
                                        $method = "process$task->process_type";
                                        $result = hwdMediaShareProcesses::$method($task);

                                        // Ping the SQL database to check connection hasn't timed out.
                                        $db->connected();
        
                                        // Update task
                                        hwdMediaShareProcesses::update($task, $result);

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
	 * Method to get a queued task
         * 
         * @since   0.1
	 **/
	public function getTask($cids = array())
	{
                // Create a new query object.
                $db = JFactory::getDBO();

                // Attempt to increase MySQL timeout
                $query = 'SET SESSION wait_timeout = 28800';
                $db->setQuery($query);
                $db->query($query);
                                
                if (count($cids) > 0)
                {
                        foreach($cids as $key => $cid)
                        {
                                // Setup query
                                $query = $db->getQuery(true);

                                // Select the required fields from the table.
                                $query->select('a.*');
                                $query->select('ext.media_type');
                                $query->from('#__hwdms_processes AS a');
                                $query->join('LEFT', '`#__hwdms_media` AS media ON media.id = a.media_id');
                                $query->join('LEFT', '`#__hwdms_ext` AS ext ON ext.id = media.ext_id');
                                $query->where('(a.status = 1 || a.status = 3)');
                                $query->where('a.attempts < 5');
                                $query->where('a.id = '.$cid);
                                
                                // If we are running over CLI then don't allow multiple executions
                                $args = @$GLOBALS['argv'];
                                if ($args[1] == 'process')
                                {
                                        $query->where('a.modified < DATE_SUB(SYSDATE(), INTERVAL 1 MINUTE)');
                                }
                                
                                $db->setQuery($query);
                                $task = $db->loadObject();
                                if (isset($task->id))
                                {
                                        return $task;
                                }
                        }
                        return false;
                }
                    
                // Setup query
                $query = $db->getQuery(true);

                // Select the required fields from the table.
                $query->select('a.*');
                $query->select('ext.media_type');

                $query->from('#__hwdms_processes AS a');
                $query->join('LEFT', '`#__hwdms_media` AS media ON media.id = a.media_id');
                $query->join('LEFT', '`#__hwdms_ext` AS ext ON ext.id = media.ext_id');

                $query->where('(a.status = 1 || a.status = 3)');
                $query->where('a.attempts < 5');
                $query->order('a.modified ASC');
                
                // If we are running over CLI then don't allow multiple executions
                $args = @$GLOBALS['argv'];
                if ($args[1] == 'process')
                {
                        $query->where('a.modified < DATE_SUB(SYSDATE(), INTERVAL 1 MINUTE)');
                }
                
                $db->setQuery($query);
                return $db->loadObject();
	}
        
	/**
	 * Method to get all queued tasks
         * 
         * @since   0.1
	 **/
	public function getQueue()
	{
                // Create a new query object.
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('COUNT(*)');
                $query->from('#__hwdms_processes AS a');
		$query->where('a.status = 1 || a.status = 3');
                $query->where('a.attempts < 5');

                $db->setQuery($query);
                return $db->loadResult();
	}
        
	/**
	 * Method to update a process (post-run)
         * 
         * @since   0.1
	 **/
	public function update($task, $result)
	{
                $date =& JFactory::getDate();
                $user = & JFactory::getUser();

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('Process', 'hwdMediaShareTable');
                $table->load( $task->id );

                $properties = $table->getProperties(1);
                $row = JArrayHelper::toObject($properties, 'JObject');

                $data = array();
                $data['id'] = $row->id;
                $data['status'] = isset($result->status) ? $result->status : 3;
                $data['modified'] = $date->format('Y-m-d H:i:s');
                $data['modified_user_id'] = $user->id;
                $data['attempts'] = $row->attempts+1;

                if (!$table->bind( $data )) {
                        return JError::raiseWarning( 500, $row->getError() );
                }
                if (!$table->store()) {
                        JError::raiseError(500, $row->getError() );
                }
	}

	/**
	 * Method to add a log
         * 
         * @since   0.1
	 **/
	public function addLog($item)
	{
                $date =& JFactory::getDate();
                $user = & JFactory::getUser();

                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                $table =& JTable::getInstance('ProcessLog', 'hwdMediaShareTable');

                jimport( 'joomla.filter.filterinput' );
                $safeFilter = JFilterInput::getInstance();
                
                $input = $safeFilter->clean($item->input);
                $output = is_array($item->output) ? implode("\n",$item->output) : $item->output;
                $output = $safeFilter->clean($output);
  
                $data = array();
                $data['process_id'] = $item->process_id;
                $data['input'] = $input;
                $data['output'] = $output;
                $data['status'] = $item->status;
                $data['created_user_id'] = $user->id;
                $data['created'] = $date->format('Y-m-d H:i:s');

                if (!$table->bind( $data ))
                {
                        return JError::raiseWarning( 500, $table->getError() );
                }
                if (!$table->store())
                {
                        JError::raiseError(500, $table->getError() );
                }             
	}
        
	/**
	 * Method to get human readable process type
         * 
         * @since   0.1
	 **/
	public function getType($item)
	{
                switch ($item->process_type) {
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
	 * Method to get human readable process status
         * 
         * @since   0.1
	 **/
	public function getStatus($item)
	{
                switch ($item->status) {
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
         * @since   0.1
	 **/
	public function process1($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::processImage($process, 2, 75, true);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        return hwdMediaShareImages::processImage($process, 2, 75, true);
                }
	}
        
	/**
	 * Method to run process type 2:
         * Create thumbnail image (100px maximum)
         * 
         * @since   0.1
	 **/
	public function process2($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::processImage($process, 3, 100);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        return hwdMediaShareImages::processImage($process, 3, 100);
                }
	}

	/**
	 * Method to run process type 3:
         * Create small image (240px maximum)
         * 
         * @since   0.1
	 **/
	public function process3($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::processImage($process, 4, 240);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        return hwdMediaShareImages::processImage($process, 4, 240);
                }
	}

	/**
	 * Method to run process type 4:
         * Create medium (500) image (500px maximum)
         * 
         * @since   0.1
	 **/
	public function process4($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::processImage($process, 5, 500);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        return hwdMediaShareImages::processImage($process, 5, 500);
                }
	}

	/**
	 * Method to run process type 5:
         * Create medium (640) image (640px maximum)
         * 
         * @since   0.1
	 **/
	public function process5($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::processImage($process, 6, 640);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        return hwdMediaShareImages::processImage($process, 6, 640);
                }
	}

	/**
	 * Method to run process type 6:
         * Create large image (1024px maximum)
         * 
         * @since   0.1
	 **/
	public function process6($process)
	{
                if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::processImage($process, 7, 1024);
                }
                else
                {
                        hwdMediaShareFactory::load('images');
                        return hwdMediaShareImages::processImage($process, 7, 1024);
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
                return hwdMediaShareAudio::processMp3($process, 8);
	}
        
	/**
	 * Method to run process type 8:
         * Create ogg audio
         * 
         * @since   0.1
	 **/
	public function process8($process)
	{
                hwdMediaShareFactory::load('audio');
                return hwdMediaShareAudio::processOgg($process, 9);
	}       

	/**
	 * Method to run process type 9, 10 & 11:
         * Create flv video
         * 
         * @since   0.1
	 **/
	public function process9($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processFlv($process, 11, 240);
	}   
	public function process10($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processFlv($process, 12, 360);
	}  
	public function process11($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processFlv($process, 13, 480);
	} 
        
	/**
	 * Method to run process type 12, 13, 14 & 15:
         * Create mp4 video
         * 
         * @since   0.1
	 **/
	public function process12($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processMp4($process, 14, 360);
	}   
	public function process13($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processMp4($process, 15, 480);
	}   
	public function process14($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processMp4($process, 16, 720);
	}   
	public function process15($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processMp4($process, 17, 1080);
	}   
        
	/**
	 * Method to run process type 16, 17, 18 & 19:
         * Create webm video
         * 
         * @since   0.1
	 **/
	public function process16($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processWebm($process, 18, 360);
	}   
	public function process17($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processWebm($process, 19, 480);
	}   
	public function process18($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processWebm($process, 20, 720);
	}   
	public function process19($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processWebm($process, 21, 1080);
	} 
        
        /**
	 * Method to run process type 20:
         * Inject metadata
         * 
         * @since   0.1
	 **/
	public function process20($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::injectMetaData($process);
	} 
        
        /**
	 * Method to run process type 21:
         * Move moov atom
         * 
         * @since   0.1
	 **/
	public function process21($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::checkMoovAtoms($process);
	} 
        
	/**
	 * Method to run process type 22:
         * Get duration
         * 
         * @since   0.1
	 **/
	public function process22($process)
	{
                if ($process->media_type == 1)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::getDuration($process);
                }
                else if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::getDuration($process);
                }
	}
        
	/**
	 * Method to run process type 23:
         * Get title
         * 
         * @since   0.1
	 **/
	public function process23($process)
	{
                if ($process->media_type == 1)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::getTitle($process);
                }
                else if ($process->media_type == 4)
                {
                        hwdMediaShareFactory::load('videos');
                        return hwdMediaShareVideos::getTitle($process);
                }
	}
        
	/**
	 * Method to run process type 23, 24, 25 & 26:
         * Create ogg video
         * 
         * @since   0.1
	 **/
	public function process24($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processOgg($process, 22, 360);
	}   
	public function process25($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processOgg($process, 23, 480);
	}   
	public function process26($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processOgg($process, 24, 720);
	}   
	public function process27($process)
	{
                hwdMediaShareFactory::load('videos');
                return hwdMediaShareVideos::processOgg($process, 25, 1080);
	} 
}