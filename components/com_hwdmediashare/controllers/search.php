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

class hwdMediaShareControllerSearch extends JControllerLegacy
{
	/**
	 * Method to validate and sanitise data from the request and redirect.
	 *
	 * @access  public
	 * @return  void
	 */
        public function processForm()
	{          
                // Initialise variables.
                $app = JFactory::getApplication();

                // Retrieve filtered jform data.
                $jform = $app->input->getArray(array(
                    'jform' => array(
                        'keyword' => 'string',
                        'ordering' => 'string',
                        'limit' => 'int',
                        'area' => 'int',
                        'catid' => 'int',
                    )
                ));
                
                $data = $jform['jform'];

                // Slashes cause errors, <> get stripped anyway later on. # causes problems.
		$badchars = array('#','>','<','\\');
                
                // Further checks.
                $data['keyword'] = trim(str_replace($badchars, '', $data['keyword']));

                // Validate custom fields.


                // Save the data in the session.
                $app->setUserState('com_hwdmediashare.search.data', $data);
                
                // Construct redirect.
		$uri = JURI::getInstance();
		$uri->setVar('option', 'com_hwdmediashare');
		$uri->setVar('view', 'search');
		$uri->setVar('type', $app->input->get('type', 'media', 'word'));
		$uri->setVar('keyword', $data['keyword']);
		$uri->setVar('uid', md5(serialize($data)));
                                
		$this->setRedirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
	}
        
	/**
	 * Method to clear the search form.
	 *
	 * @access  public
	 * @return  void
	 */
        public function clear()
	{          
                // Initialise variables.
                $app = JFactory::getApplication();
                
                // Save the data in the session.
                $app->setUserState('com_hwdmediashare.search.data', array());
                
                // Construct redirect.
		$uri = JURI::getInstance();
		$uri->setVar('option', 'com_hwdmediashare');
		$uri->setVar('view', 'search');
		$uri->setVar('type', $app->input->get('type', 'media', 'word'));
		$uri->setVar('keyword', '');
                                
		$this->setRedirect(JRoute::_('index.php'.$uri->toString(array('query', 'fragment')), false));
	}        
}
