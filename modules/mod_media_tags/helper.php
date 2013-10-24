<?php
// no direct access
defined('_JEXEC') or die;

class modMediaTagsHelper extends JObject
{
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
                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
    
                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);

                $params = $config;
                $this->set('params', $config);
		$this->set('url', JURI::root().'modules/mod_media_tags/');
		$this->set('container', 'modmediatags_'.$module->id);
                $this->set('return', base64_encode(JFactory::getURI()->toString()));
	}

	public function addHead()
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
	}

	public function getItems()
	{
                jimport( 'joomla.application.component.model' );
                //JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                //$model =& JModel::getInstance('Tags', 'hwdMediaShareModel', array('ignore_request' => true));
                $version = new JVersion();
                ($version->RELEASE >= 3.0 ? JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models') : JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models'));
                $model = ($version->RELEASE >= 3.0 ? JModelLegacy::getInstance('Tags', 'hwdMediaShareModel', array('ignore_request' => true)) : JModel::getInstance('Tags', 'hwdMediaShareModel', array('ignore_request' => true)));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $this->params->get('count', 0));
		$model->setState('filter.published', 1);

		// Ordering
		$model->setState('list.ordering', $this->params->get('article_ordering', 'a.tag'));
		$model->setState('list.direction', $this->params->get('article_ordering_direction', 'ASC'));

		// New Parameters
		$model->setState('filter.featured', $this->params->get('show_featured', 'show'));
		$model->setState('filter.author_id', $this->params->get('created_by', ""));
		$model->setState('filter.author_id.include', $this->params->get('author_filtering_type', 1));
		$model->setState('filter.author_alias', $this->params->get('created_by_alias', ""));
		$model->setState('filter.author_alias.include', $this->params->get('author_alias_filtering_type', 1));
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
                        for ($i=0, $n=count($items); $i < $n; $i++)
                        {
                        }
                }

		return $items;                
	}
}