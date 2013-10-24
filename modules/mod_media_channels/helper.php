<?php
// no direct access
defined('_JEXEC') or die;

class modMediaChannelsHelper extends JObject {

	public $params 		= null;
	public $url		= null;
	public $container	= null;

	public function __construct($module, &$params)
	{
                // Load config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                $config->merge( $params );
                
                // Download links
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('utilities');
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
    
                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);

                $params = $config;
                $this->set('utilities', hwdMediaShareUtilities::getInstance());
                $this->set('params', $config);
		$this->set('url', JURI::root().'modules/mod_media_channels/');
		$this->set('container', 'modmediachannels_'.$module->id);
                $this->set('return', base64_encode(JFactory::getURI()->toString()));
                $this->set('columns', $params->get('list_columns', 3));
                $this->set('elementType', 5);
	}

	public function addHead()
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
                if ($this->params->get('load_joomla_css') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
	}

	public function getItems()
	{
                jimport( 'joomla.application.component.model' );
                //JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                //$model =& JModel::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true));
                $version = new JVersion();
                ($version->RELEASE >= 3.0 ? JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models') : JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models'));
                $model = ($version->RELEASE >= 3.0 ? JModelLegacy::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true)) : JModel::getInstance('Users', 'hwdMediaShareModel', array('ignore_request' => true)));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $this->params->get('count', 0));
		$model->setState('filter.published', 1);

		// Ordering
		$model->setState('com_hwdmediashare.users.list.ordering', $this->params->get('ordering', 'a.ordering'));
		$model->setState('com_hwdmediashare.users.list.direction', $this->params->get('ordering_direction', 'ASC'));

		// New Parameters
		$model->setState('filter.featured', $this->params->get('show_featured', 'show'));
		$excluded_articles = $this->params->get('excluded_articles', '');

		if ($excluded_articles) 
                {
			$excluded_articles = explode("\r\n", $excluded_articles);
			$model->setState('filter.article_id', $excluded_articles);
			$model->setState('filter.article_id.include', false); // Exclude
		}

		$date_filtering = $this->params->get('date_filtering', 'off');
		if ($date_filtering !== 'off') 
                {
			$model->setState('filter.date_filtering', $date_filtering);
			$model->setState('filter.date_field', $this->params->get('date_field', 'a.created'));
			$model->setState('filter.start_date_range', $this->params->get('start_date_range', '1000-01-01 00:00:00'));
			$model->setState('filter.end_date_range', $this->params->get('end_date_range', '9999-12-31 23:59:59'));
			$model->setState('filter.relative_date', $this->params->get('relative_date', 30));
		}

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

                if ($items = $model->getItems())
                {
                }

		return $items;                
	}
}