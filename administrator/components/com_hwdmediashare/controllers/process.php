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

class hwdMediaShareControllerProcess extends JControllerForm
{
	/**
	 * The prefix to use with controller messages.
	 * @var    string
	 */
	protected $text_prefix = 'COM_HWDMS';
        
	/**
	 * Constructor.
	 * @return	void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
                
		// Define standard task mappings.                
                $this->registerTask('runall', 'run');
	}
        
        /**
	 * Method to run processes sequentially using ajax.
	 * @return	void
	 */
        function run()
        {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		// Initialise variables.
		$values	= array('runall' => 1, 'run' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');
                
                // If we want to process everything, then reset the $cid array
                if ($value)
                {
                        $cid = array();
                }

		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'processes';
		$vFormat	= 'html';

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			// Get the model for the view.
			$model = $this->getModel($vName);

			// Push the model into the view (as default).
			$view->setModel($model, true);

			// Push document object into the view.
			$view->document = $document;

			$view->run($cid);
		}
        }
}
