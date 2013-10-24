<?php
// no direct access
defined('_JEXEC') or die;

class modMediaAudioHelper extends JObject
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
                hwdMediaShareFactory::load('utilities');
                JLoader::register('hwdMediaShareHelperNavigation', JPATH_ROOT.'/components/com_hwdmediashare/helpers/navigation.php');
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                JLoader::register('plgHwdmediasharePlayer_mejs', JPATH_ROOT.'/plugins/hwdmediashare/player_mejs/player_mejs.php');
                hwdMediaShareHelperNavigation::setJavascriptVars();

                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);

                // Load the player 
                $pluginClass = 'plgHwdmediashare'.$config->get('media_player');
                $pluginPath = JPATH_ROOT.'/plugins/hwdmediashare/'.$config->get('media_player').'/'.$config->get('media_player').'.php';

                // Import hwdMediaShare plugins
                if (file_exists($pluginPath))
                {
                        JLoader::register($pluginClass, $pluginPath);
                        $player = call_user_func(array($pluginClass, 'getInstance'));
                        $player->preloadAssets();
                }
  
                $this->set('player', $player);
                
                $params = $config;
                $this->set('utilities', hwdMediaShareUtilities::getInstance());
                $this->set('params', $config);
		$this->set('url', JURI::root().'modules/mod_media_audio/');
		$this->set('container', 'modmediaaudio_'.$module->id);
                $this->set('return', base64_encode(JFactory::getURI()->toString()));
	}

	public function addHead()
	{
                JHtml::_('behavior.framework', true);
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
		$doc->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/hwd.min.js');
                if ($this->params->get('load_joomla_css') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
	}

	public function getItems()
	{
                jimport( 'joomla.application.component.model' );
                //JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                //$model =& JModel::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true));
                $version = new JVersion();
                ($version->RELEASE >= 3.0 ? JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models') : JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models'));
                $model = ($version->RELEASE >= 3.0 ? JModelLegacy::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true)) : JModel::getInstance('Media', 'hwdMediaShareModel', array('ignore_request' => true)));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $this->params->get('count', 0));

		// Ordering
		$model->setState('com_hwdmediashare.media.list.ordering', $this->params->get('ordering', 'a.ordering'));
		$model->setState('com_hwdmediashare.media.list.direction', $this->params->get('ordering_direction', 'ASC'));

		$model->setState('filter.mediaType', 1);

                $catids = $this->params->get('catid');
                $model->setState('filter.category_id.include', (bool) $this->params->get('category_filtering_type', 1));

		// Category filter
                if (is_array($catids)) $catids = array_filter($catids);
		if ($catids) {
			if ($this->params->get('show_child_category_articles', 0) && (int) $this->params->get('levels', 0) > 0) {
				// Get an instance of the generic categories model
				$categories = JModel::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true));
				$categories->setState('params', $appParams);
				$levels = $this->params->get('levels', 1) ? $this->params->get('levels', 1) : 9999;
				$categories->setState('filter.get_children', $levels);
				$categories->setState('filter.published', 1);
				$additional_catids = array();

				foreach($catids as $catid)
				{
					$categories->setState('filter.parentId', $catid);
					$recursive = true;
					$items = $categories->getItems($recursive);

					if ($items)
					{
						foreach($items as $category)
						{
							$condition = (($category->level - $categories->getParent()->level) <= $levels);
                                                        if ($condition) {
								$additional_catids[] = $category->id;
							}

						}
					}
				}

				$catids = array_unique(array_merge($catids, $additional_catids));
			}

			$model->setState('filter.category_id', $catids);
		}
 
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

                $user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_hwdmediashare')) &&  (!$user->authorise('core.edit', 'com_hwdmediashare')))
                {
			// Limit to published for people who can't edit or edit.state.
			$model->setState('filter.published',	1);
			$model->setState('filter.status',	1);

			// Filter by start and end dates.
			$model->setState('filter.publish_date', true);
		}
                else
                {
			// Limit to published for people who can't edit or edit.state.
			$model->setState('filter.published',	array(0,1));
			$model->setState('filter.status',	1);
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