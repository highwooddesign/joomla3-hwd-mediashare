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

class hwdMediaShareControllerGroupForm extends JControllerForm
{
	/**
	 * The URL view item variable to use with this controller.
	 *
         * @access  protected
	 * @var     string
	 */
	protected $view_item = 'groupform';

	/**
	 * The URL view list variable to use with this controller.
	 *
         * @access  protected
	 * @var     string
	 */
	protected $view_list = 'groups';

	/**
	 * The URL edit variable.
	 *
         * @access  protected
	 * @var     string
	 */
	protected $urlVar = 'id';

	/**
	 * Method to add a new record.
	 *
	 * @access  public
	 * @return  mixed   True if the record can be added, a error object if not.
	 */
	public function add()
	{
		if (!parent::add())
		{
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @access  protected
	 * @param   array      $data  An array of input data.
	 * @param   string     $key   The name of the key for the primary key; default is id.
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user     = JFactory::getUser();
		$userId   = $user->get('id');
		$asset    = 'com_hwdmediashare.group.' . $recordId;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', $asset))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_user_id']) ? $data['created_user_id'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_user_id;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	/**
	 * Method to cancel an edit.
	 *
         * @access  public
	 * @param   string   $key  The name of the primary key of the URL variable.
	 * @return  boolean
	 */
	public function cancel($key = 'id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
                
                return true;
	}

	/**
	 * Method to edit an existing record.
	 *
         * @access  public
	 * @param   string   $key     The name of the primary key of the URL variable.
	 * @param   string   $urlVar  The name of the URL variable if different from the primary key
	 * @return  boolean
	 */
	public function edit($key = null, $urlVar = 'id')
	{
		$result = parent::edit($key, $urlVar);

		return $result;
	}

        /**
	 * Proxy for getModel.
	 *
	 * @access  public
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.          
         * @return  object  The model.
	 */
	public function getModel($name = 'groupForm', $prefix = 'hwdMediaShareModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
         * @access  protected
	 * @param   integer    $recordId  The primary key id for the item.
	 * @param   string     $urlVar    The name of the URL variable for the id.
	 * @return  string     The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$itemId	= $this->input->getInt('Itemid');
		$return	= $this->getReturnPage();
		$tmpl   = $this->input->get('tmpl');

		$append = '&layout=edit';
                
		if ($recordId)
		{
			$append .= '&'.$urlVar.'='.$recordId;
		}
                
		if ($itemId)
		{
			$append .= '&Itemid='.$itemId;
		}

		if ($return)
		{
			$append .= '&return='.base64_encode($return);
		}
                
		if ($tmpl)
		{
			$append .= '&tmpl='.$tmpl;
		}

		return $append;
	}

	/**
	 * Get the return URL, if a "return" variable has been passed in the request.
	 *
         * @access  protected
         * @return  string     The return URL.
	 */
	protected function getReturnPage()
	{
		$return = $this->input->get('return', null, 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return)))
		{
			return JUri::base();
		}
		else
		{
			return base64_decode($return);
		}
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
         * @access  protected
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 * @return  void
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		return;
	}

	/**
	 * Method to save a record.
	 *
         * @access  public
	 * @param   string   $key     The name of the primary key of the URL variable.
	 * @param   string   $urlVar  The name of the URL variable if different from the primary key.
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = 'id')
	{
		$result = parent::save($key, $urlVar);

		// If ok, redirect to the return page.
		if ($result)
		{
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}

	/**
	 * Method to display the report view.
         * 
         * @access  public
	 * @return  void
	 */
	public function report()
	{
		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'groupForm';
		$vFormat	= 'html';

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			// Get the model for the view.
			$model = $this->getModel($vName, 'hwdMediaShareModel', array('ignore_request' => false));

			// Push the model into the view (as default).
			$view->setModel($model, true);

			// Push document object into the view.
			$view->document = $document;

			$view->report();
		}
	}
}
