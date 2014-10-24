<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.comprofiler.media
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

/**
 * Basic tab extender. Any plugin that needs to display a tab in the user profile
 * needs to have such a class. Also, currently, even plugins that do not display tabs (e.g., auto-welcome plugin)
 * need to have such a class if they are to access plugin parameters (see $this->params statement).
 */
class getMediaTab extends cbTabHandler 
{
	/**
	 * Method to generate a profile view tab title.
         * 
         * @access  public
	 * @param   TabTable   $tab       The tab database entry.
	 * @param   UserTable  $user      The user being displayed.
	 * @param   int        $ui        1 for front-end, 2 for back-end.
	 * @param   array      $postdata  The _POST data for saving edited tab content as generated with getEditTab.
	 * @return  mixed      Either string HTML for tab content, or false if ErrorMSG generated.
	 */
	public function getTabTitle($tab, $user, $ui, $postdata)
	{
		$plugin	= cbarticlesClass::getPlugin();
		$viewer	= CBuser::getMyUserDataInstance();
		$total = cbarticlesModel::getArticlesTotal( null, $viewer, $user, $plugin );

		return parent::getTabTitle( $tab, $user, $ui, $postdata ) . ' <span class="badge badge-default">' . (int) $total . '</span>';
	}
        
	/**
	 * Generates the HTML to display the user profile tab.
	 *
         * @access  public
	 * @param   \CB\Database\Table\TabTable   $tab   The tab database entry.
	 * @param   \CB\Database\Table\UserTable  $user  The user being displayed.
	 * @param   int                           $ui    1 for front-end, 2 for back-end.
	 * @return  mixed                         Either string HTML for tab content, or false if ErrorMSG generated.
	 */
	public function getDisplayTab($tab, $user, $ui)
        {                    
                // Load HWD assets.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');

                // Include JHtml helpers.
                JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_hwdmediashare/helpers/html');
                JHtml::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/helpers/html');
                
                // Import HWD libraries.                
                hwdMediaShareFactory::load('activities');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('thumbnails');
		hwdMediaShareFactory::load('utilities');

                // Load HWD config (and force reset).
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig(null, true);
                
                // Merge with plugin parameters.
                $params = new JRegistry($tab->params);
                $config->merge($params);

                // Load the HWD language file.
                $lang = JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);
                
                // Get data.
                $helper = new JViewLegacy;
                $helper->params = $config;                
                $helper->items = $this->getItems();
                $helper->utilities = hwdMediaShareUtilities::getInstance();
                $helper->columns = $config->get('list_columns', 3);
                $helper->return = base64_encode(JFactory::getURI()->toString());

                // Add assets to the head tag.
                JHtml::_('hwdhead.core', $config);
                
		// Get the user.
                $this->user = $user;
                
                ob_start();
                ?>
<div id="hwd-container">
  <div class="media-details-view">
    <?php if (empty($helper->items)): ?>
      <div class="alert alert-no-items">
        <?php echo JText::_('COM_HWDMS_NOTHING_TO_SHOW'); ?>
      </div>
    <?php else: ?>
      <?php echo JLayoutHelper::render('media_details', $helper, JPATH_ROOT.'/components/com_hwdmediashare/libraries/layouts'); ?>
    <?php endif; ?>
  </div> 
</div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                
                return $html;
	}
        
	/**
	 * Method to get a list of items.
	 *
	 * @access  public
	 * @return  mixed  An array of data items on success, false on failure.
	 */        
	public function getItems()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
                $doc = JFactory::getDocument();

                JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                $model = JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));               

                // Populate state (and set the context).
                $model->context = 'cb_media';
		$model->populateState();

		// Set the start and limit states.
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $this->params->get('count', 6));

		// Set the ordering states.
                $model->setState('list.ordering', 'a.created');
                $model->setState('list.direction', 'DESC');

                // Set the author states.
                $model->setState('filter.author_id', $this->user->id);
                $model->setState('filter.author_id.include', 1);

                $this->items = $model->getItems();

                return $this->items;               
	}
}
