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

class hwdMediaShareModelMaintenance extends JModelLegacy
{
        /**
	 * Method to clean the category map.
	 * @return	void
	 */
	public function cleanCategoryMap()
	{
                $db = JFactory::getDbo();

                $query = $db->getQuery(true);

                // Delete zero entries
                $conditions = array(
                    $db->quoteName('element_type') . ' = ' . $db->quote(0),
                    $db->quoteName('element_id') . ' = ' . $db->quote(0),
                    $db->quoteName('category_id') . ' = ' . $db->quote(0)
                );

                $query->delete($db->quoteName('#__hwdms_category_map'));
                $query->where($conditions, 'OR');

                $db->setQuery($query);
                try
                {
                        $result = $db->query();                 
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $result;
        }
                
        /**
	 * Method to empty old upload tokens
	 * @return	void
	 */
	public function emptyUploadTokens()
	{
                $db = JFactory::getDbo();

                $query = $db->getQuery(true);

                // Delete zero entries
                $conditions = array(
                    $db->quoteName('datetime') . ' < (NOW() - INTERVAL 10 MINUTE)'
                );

                $query->delete($db->quoteName('#__hwdms_upload_tokens'));
                $query->where($conditions);

                $db->setQuery($query);
                try
                {
                        $result = $db->query();                 
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $result;
        }    
        
        /**
	 * Method to purge old processes
	 * @return	void
	 */
	public function purgeOldProcesses()
	{
                $db = JFactory::getDbo();

                $query = $db->getQuery(true);

                // Delete zero entries
                $conditions = array(
                    $db->quoteName('created') . ' < (NOW() - INTERVAL 90 DAY)'
                );

                $query->delete($db->quoteName('#__hwdms_process_log'));
                $query->where($conditions);

                $db->setQuery($query);
                try
                {
                        $result = $db->query();                 
                }
                catch (RuntimeException $e)
                {
                        $this->setError($e->getMessage());
                        return false;                            
                }
                return $result;
        }    
}
