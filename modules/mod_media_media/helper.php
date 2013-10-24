<?php
// no direct access
defined('_JEXEC') or die;

class modMediaMediaHelper extends JObject {

	public $params 		= null;
	public $url		= null;
	public $container	= null;

	public function __construct($module, &$params)
	{
                // Load config
                $hwdms = hwdMediaShareFactory::getInstance();
                $config = $hwdms->getConfig();
                // We need to reset this varaible to avoid issues where other modules set this value in earlier position
                $config->set('list_default_media_type', '');
                $config->merge( $params );
                
                // Download links
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('utilities');
                hwdMediaShareFactory::load('media');
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
    
                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);
                
                $params = $config;
                $this->set('utilities', hwdMediaShareUtilities::getInstance());
                $this->set('params', $config);
		$this->set('url', JURI::root().'modules/mod_media_media/');
		$this->set('container', 'modmediamedia_'.$module->id);
                $this->set('return', base64_encode(JFactory::getURI()->toString()));
                $this->set('columns', $config->get('list_columns', 3));
                $this->set('elementType', 1);
	}

	public function addHead()
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
                if ($this->params->get('load_joomla_css') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/aspect.css');
                if ($this->params->get('list_thumbnail_aspect') != 0) $doc->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/aspect.js');
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

                // Check if the module is configured to show media from the current categories or current authors.
                // If yes, then add those categories and authors to the parameters, using function caching.
                $cache = & JFactory::getCache();
                $cache->setCaching( 1 );
                $this->params->set('catid', $cache->call( array( $this, 'setCategories' ) )); 
                $this->params->set('created_by', $cache->call( array( $this, 'setAuthors' ) )); 

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $this->params->get('count', 0));

		// Ordering
		$model->setState('com_hwdmediashare.media.list.ordering', $this->params->get('ordering', 'a.ordering'));
		$model->setState('com_hwdmediashare.media.list.direction', $this->params->get('ordering_direction', 'ASC'));

                $model->setState('filter.mediaType', $this->params->get('list_default_media_type', ''));
                
                $catids = $this->params->get('catid');
                $model->setState('filter.category_id.include', (bool) $this->params->get('category_filtering_type', 1));

                // Category filter
                if (is_array($catids)) $catids = array_filter($catids);
                if ($catids) {
			if ($this->params->get('show_child_category_articles', 0) && (int) $this->params->get('levels', 0) > 0) {
				// Get an instance of the generic categories model
				//$categories = JModel::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true));
                                $categories = ($version->RELEASE >= 3.0 ? JModelLegacy::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true)) : JModel::getInstance('Categories', 'hwdMediaShareModel', array('ignore_request' => true)));
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
		$model->setState('filter.author_id', $this->params->get('created_by', ''));
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
                }

		return $items;                
	}
        
        /**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function getCategories( &$item )
	{            
                if (!isset($item))
                {
                        return;
                }

                $href = '';
                if (count($item->categories) > 0)
                {
                        foreach ($item->categories as $value)
                        {
                                $href.= '<a href="'.JRoute::_(hwdMediaShareHelperRoute::getCategoryRoute($value->id)).'">' . $value->title . '</a> ';
                        }
                        unset($value);
                }
                else
                {
                        $href = JText::_('COM_HWDMS_NONE');
                }             

                return $href;
	}
        
        /**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function setCategories()
	{      
                $catids = $this->params->get('catid', array());
                if (is_array($catids) && in_array(-1, $catids))
                {
                        // When searching for related media, use the relation method
                        if (JRequest::getWord('option') == 'com_hwdmediashare' && JRequest::getWord('view') == 'mediaitem' && JRequest::getInt('id') > 0)
                        {
                                // Get a media row instance.
                                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                                $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                                if ($table->load(JRequest::getInt('id')))
                                {                               
                                        // Convert the JTable to a clean JObject.
                                        $properties = $table->getProperties(1);
                                        $media = JArrayHelper::toObject($properties, 'JObject');
                                        hwdMediaShareFactory::load('category');
                                        $media->categories = hwdMediaShareCategory::get($media);
                                        for ($i=0, $n=count($media->categories); $i < $n; $i++)
                                        {
                                                $catids[] = $media->categories[$i]->id; 
                                        }
                                }
                        }
                        $catids = array_diff($catids, array(-1));
                }    
                return $catids;        
	}
        
        /**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function setAuthors()
	{      
                $authors = $this->params->get('created_by');
                if (is_array($authors) && in_array(-1, $authors))
                {
                        // Get a media row instance.
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
                        $table = JTable::getInstance('Media', 'hwdMediaShareTable');
                        if ($table->load(JRequest::getInt('id')))
                        {                               
                                // Convert the JTable to a clean JObject.
                                $properties = $table->getProperties(1);
                                $media = JArrayHelper::toObject($properties, 'JObject');
                                $authors[] = $media->created_user_id; 
                        }
                        $authors = array_diff($authors, array(-1));
                }
                return $authors;     
	}        
}