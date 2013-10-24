<?php
/**
 * @version    SVN $Id: activity.php 1571 2013-06-13 10:27:17Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      20-Nov-2011 16:46:24
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * hwdMediaShare Model
 */
class hwdMediaShareModelActivity extends JModelItem
{
        /**
	 * @since	0.1
	 */
        public $elementType = 7;
        
        /**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_hwdmediashare.activity';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_hwdmediashare';

        /**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	0.1
	 */
	public function getTable($type = 'Activity', $prefix = 'hwdMediaShareTable', $config = array())
	{
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                return JTable::getInstance($type, $prefix, $config);
	}
        
	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function like()
	{
            $app = JFactory::getApplication();
                
            if (!JFactory::getUser()->authorise('hwdmediashare.like','com_hwdmediashare'))
            {
                    return JError::raiseWarning(404, JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
            }
            
            // Initialise variables.
            $pk = (!empty($pk)) ? $pk : (int) JRequest::getInt('id');
            $db = $this->getDbo();

            $db->setQuery(
                    'UPDATE #__hwdms_activities' .
                    ' SET likes = likes + 1' .
                    ' WHERE id = '.(int) $pk
            );

            if (!$db->query()) {
                    $this->setError($db->getErrorMsg());
                    return false;
            }

            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_ACTIVITY_LIKED') );
            return true;
	}

	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function dislike()
	{
            $app = JFactory::getApplication();
            $hitcount = JRequest::getInt('hitcount', 1);

            if ($hitcount)
            {
                // Initialise variables.
                $pk = (!empty($pk)) ? $pk : (int) JRequest::getInt('id');
                $db = $this->getDbo();

                $db->setQuery(
                        'UPDATE #__hwdms_activities' .
                        ' SET dislikes = dislikes + 1' .
                        ' WHERE id = '.(int) $pk
                );

                if (!$db->query()) {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
            }

            JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_ACTIVITY_DISLIKED') );
            return true;
	}

        /**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function publish($pks, $value = 0)
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

                $db =& JFactory::getDBO();
                $query = "
                  UPDATE ".$db->quoteName('#__hwdms_activities')."
                    SET ".$db->quoteName('published')." = ".$db->quote($value)."
                    WHERE ".$db->quoteName('id')." = ".implode(" OR ".$db->quoteName('id')." = ", $pks)."
                  ";
                $db->setQuery($query);

                // Check for a database error.
		if (!$db->query())
                {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

                // Clear the component's cache
		$this->cleanCache();

		return true;
	}
        
	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function delete($pks)
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

                $db =& JFactory::getDBO();
                $query = "
                  UPDATE ".$db->quoteName('#__hwdms_activities')."
                    SET ".$db->quoteName('published')." = ".$db->quote('-2')."
                    WHERE ".$db->quoteName('id')." = ".implode(" OR ".$db->quoteName('id')." = ", $pks)."
                  ";
                $db->setQuery($query);

                // Check for a database error.
		if (!$db->query())
                {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);

			return false;
		}

                // Clear the component's cache
		$this->cleanCache();

		return true;
	}
        
        /**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function report()
	{
                $app = JFactory::getApplication();
                
                if (!JFactory::getUser()->authorise('hwdmediashare.report','com_hwdmediashare'))
                {
                        return JError::raiseWarning(404, JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                }
                
                $array = JRequest::get( 'post' );
                $user = JFactory::getUser();

                $params = new StdClass;
                $params->elementType = 7;
                $params->elementId = JRequest::getInt('id');
                $params->reportId = JRequest::getInt('report_id');
                $params->description = JRequest::getVar('description');
                $params->userId = $user->id;

                hwdMediaShareFactory::load('reports');
                hwdMediaShareReports::add($params);

                hwdMediaShareFactory::load('utilities');
                $utilities = hwdMediaShareUtilities::getInstance();
                $utilities->printModalNotice('COM_HWDMS_NOTICE_ACTIVITY_REPORTED', 'COM_HWDMS_NOTICE_ACTIVITY_REPORTED_DESC'); 
                return;
	}        
        
        /**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function comment()
	{
                // Get hwdMediaShare config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                
                $app = JFactory::getApplication();
                
                if (!JFactory::getUser()->authorise('hwdmediashare.comment','com_hwdmediashare'))
                {
                        return JError::raiseWarning(404, JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                }

                # Shall we check the reCAPTCHA response?
                if ($config->get('recaptcha_public_key')) 
                {
                        # the response from reCAPTCHA
                        $resp = null;
                        # the error code from reCAPTCHA, if any
                        $error = null;
                        
                        hwdMediaShareFactory::load('recaptcha.recaptchalib');
                        $resp = recaptcha_check_answer ($config->get('recaptcha_private_key'),
                                                        $_SERVER["REMOTE_ADDR"],
                                                        $_POST["recaptcha_challenge_field"],
                                                        $_POST["recaptcha_response_field"]);

                        if (!$resp->is_valid)
                        {
                                # set the error code so that we can display it
                                $error = $resp->error; 
                                JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_INCORRECT_RECAPTCHA') );
                                return false;
                        }
                }
                
                $array = JRequest::get( 'post' );
                $user = JFactory::getUser();

                $params = new StdClass;
                $params->activityType = 1;
                $params->elementType = JRequest::getInt('element_type', '1');
                $params->elementId = JRequest::getInt('id');
                $params->replyId = JRequest::getInt('reply');
                $params->description = $array['jform']['comment'];;
                $params->userId = $user->id;

                hwdMediaShareFactory::load('activities');
                if (hwdMediaShareActivities::save($params))
                {
                        JFactory::getApplication()->enqueueMessage( JText::_('COM_HWDMS_NOTICE_COMMENT_ADDED') );
                        return true;
                }
                                
                return false;  
	}
	/**
	 * Increment the hit counter for the media.
	 *
	 * @param	int		Optional primary key of the article to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function reply()
	{
                $app = JFactory::getApplication();
                
                if (!JFactory::getUser()->authorise('hwdmediashare.comment','com_hwdmediashare'))
                {
                        return JError::raiseWarning(404, JText::_('COM_HWDMS_ERROR_NOAUTHORISED'));
                }
                
                $array = JRequest::get( 'post' );
                $user = JFactory::getUser();

                $params = new StdClass;
                $params->activityType = 1;
                $params->elementType = JRequest::getInt('element_type', '1');
                $params->elementId = JRequest::getInt('element_id');
                $params->replyId = JRequest::getInt('reply_id');
                $params->description = JRequest::getVar('comment');
                $params->userId = $user->id;

                hwdMediaShareFactory::load('activities');
                if (hwdMediaShareActivities::save($params))
                {
                        hwdMediaShareFactory::load('utilities');
                        $utilities = hwdMediaShareUtilities::getInstance();
                        $utilities->printModalNotice('COM_HWDMS_NOTICE_REPLY_ADDED', 'COM_HWDMS_NOTICE_REPLY_ADDED_DESC'); 
                        return;
                }
                
                return false;      
        }
}
