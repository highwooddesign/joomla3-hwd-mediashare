<?php
// no direct access
defined('_JEXEC') or die;

class modMediaImagesHelper extends JObject
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
    
                // Need Mootools more loaded
                JHtml::_('behavior.framework', true);

                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);

                $params = $config;
                $this->set('params', $config);
		$this->set('url', JURI::root().'modules/mod_media_images/');
		$this->set('container', 'modmediaimages_'.$module->id);
                $this->set('return', base64_encode(JFactory::getURI()->toString()));
	}

	public function addHead()
	{
		$doc = JFactory::getDocument();
                
                $autostart = ($this->get('params')->get('auto_interval',0) > 0 ? "autostart:true,interval:".$this->get('params')->get('auto_interval',4)."," : "autostart:false,");
$js = <<<EOD
	window.addEvent('load', function () {

		var duration = 300,

			div = document.getElement('div.slider-tabs');
			links = div.getElements('a'),

			carousel = new Carousel.Extra({
				$autostart
				activeClass: 'selected',
				container: 'slide',
				scroll: 1,
				circular: true,
				current: 0,
				previous: links.shift(),
				next: links.pop(),
				tabs: links,
				/* mode: 'horizontal', */
				fx: {
					duration: duration
				}
			}),
			removed = 0;

		function change() {

			var panel = this.retrieve('panel');

			if(this.checked) {

				if(!panel) {

					if(carousel.running) {

						carousel.addEvent('complete:once', change.bind(this));
						return
					}

					panel = carousel.remove(Math.max(0, this.value - removed));

					if(panel) {

						this.store('panel', panel);
						removed++;
					}

					this.checked = !!panel
				}

			} else {

				if(panel) {

					this.eliminate('panel');
					removed--;
					carousel.add(panel.panel, panel.tab.inject(div.getFirst(), 'after'), this.value)
				}
			}
		}

		$$('input.remove').addEvents({click: change, change: change})
	})
EOD;

		$doc->addScriptDeclaration($js);

$bgc_top = $this->get('params')->get('bgc_top');
$bgc_bottom = $this->get('params')->get('bgc_bottom');
$height = $this->get('params')->get('mediaitem_height', 350).'px';
$css = <<<EOD

#slide {
 height: $height;
 background: #$bgc_top; /* Old browsers */
 background: -moz-linear-gradient(top, #$bgc_top 0%, #$bgc_bottom 100%); /* FF3.6+ */
 background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #$bgc_top), color-stop(100%, #$bgc_bottom)); /* Chrome,Safari4+ */
 background: -webkit-linear-gradient(top, #$bgc_top 0%, #$bgc_bottom 100%); /* Chrome10+,Safari5.1+ */
 background: -o-linear-gradient(top, #$bgc_top 0%, #$bgc_bottom 100%); /* Opera 11.10+ */
 background: -ms-linear-gradient(top, #$bgc_top 0%, #$bgc_bottom 100%); /* IE10+ */
 background: linear-gradient(top, #$bgc_top 0%, #$bgc_bottom 100%); /* W3C */
 filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#$bgc_top', endColorstr='#$bgc_bottom', GradientType=0 ); /* IE6-9 */
}

#slide div.slide-container {
 height: $height;
}

EOD;

		$doc->addStyleDeclaration($css);

		$doc->addStylesheet($this->get('url').'css/slideshow.css');
		$doc->addScript($this->get('url').'js/PeriodicalExecuter.js');
		$doc->addScript($this->get('url').'js/Carousel.js');
		$doc->addScript($this->get('url').'js/Carousel.Extra.js');
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

		$model->setState('filter.mediaType', 3);
                
                $catids = $this->params->get('catid');
                $model->setState('filter.category_id.include', (bool) $this->params->get('category_filtering_type', 1));

		// Category filter
                if (is_array($catids)) $catids = array_filter($catids);
		if ($catids) {
			if ($this->params->get('show_child_category_articles', 0) && (int) $this->params->get('levels', 0) > 0) {
				// Get an instance of the generic categories model
				//$categories = JModel::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true));
                                ($version->RELEASE >= 3.0 ? JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models') : JModel::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models'));
                                if ($categories = ($version->RELEASE >= 3.0 ? JModelLegacy::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true)) : JModel::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true))))
                                {
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