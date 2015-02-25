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

class hwdMediaShareViewGroupForm extends JViewLegacy
{
    	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$user = JFactory::getUser();
                
                // Get data from the model.
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
                $this->return_page = $this->get('ReturnPage');
		$this->params = $this->state->params;

                // Include JHtml helpers.
                JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
                JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->isNew = $this->item->id == 0;
                
                // Check access.
		if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_hwdmediashare') || (count($user->getAuthorisedCategories('com_hwdmediashare', 'core.create')));
		}
		else
		{
			$authorised = $this->item->attributes->get('access-edit');
		}
		if ($authorised !== true)
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
                
                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->_prepareDocument();
                
		// Display the template.
		parent::display($tpl);
	}
        
	/**
	 * Prepares the document.
	 *
         * @access  protected
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		// Initialise variables.
                $app = JFactory::getApplication();
                            
                // Add page assets.
                JHtml::_('hwdhead.core', $this->params);

		// Define the page heading.                
                if ($app->input->get('view', '', 'word') == 'groupform')
                {
                        if ($this->isNew)
                        {
                                $this->params->set('page_heading', JText::_('COM_HWDMS_NEW_GROUP'));
                        }
                        else
                        {
                                $this->params->set('page_heading', JText::sprintf('COM_HWDMS_EDIT_GROUPX', $this->escape($this->item->title)));
                        }
                }
                else
                {
                        $this->params->set('page_heading', JText::_('COM_HWDMS_GROUP'));
                } 
                
		// Define the page title.                
                $this->document->setTitle($this->params->get('page_heading'));                  
	}

	/**
	 * Display the report view.
	 *
	 * @access  public
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function report($tpl = null)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
                
                // Get data from the model.
                $this->form = $this->get('ReportForm');
                $this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->params = $this->state->params;
                
                // Include JHtml helpers.
                JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
                JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
                
                // Import HWD libraries.                
		hwdMediaShareFactory::load('utilities');
                
                $this->utilities = hwdMediaShareUtilities::getInstance();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
                $this->return = base64_encode(JFactory::getURI()->toString());

                if (!$user->authorise('hwdmediashare.report', 'com_hwdmediashare'))
                {
                        $this->utilities->printModalNotice('COM_HWDMS_NOTICE_NO_FEATURE_ACCESS', 'COM_HWDMS_NOTICE_NO_FEATURE_ACCESS_DESC'); 
                        return;
                }
                
                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }

		$this->_prepareDocument();
                
		// Display the template.
		parent::display('report');                
	}
}