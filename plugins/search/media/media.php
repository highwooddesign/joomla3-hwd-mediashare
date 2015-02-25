<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.search.media
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgSearchMedia extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @access  protected
	 * @var     boolean
	 */
	protected $autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @access  public
	 * @param   object  $subject   The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 * @return  void
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);

                // Load HWD assets.
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT.'/components/com_hwdmediashare/libraries/factory.php');
                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT.'/components/com_hwdmediashare/helpers/route.php');

                // Load HWD config.
                $hwdms = hwdMediaShareFactory::getInstance();
                $this->config = $hwdms->getConfig();
                
                // Merge with plugin parameters.
                $this->config->merge($this->params);

                // Load the HWD language file.
                $lang = JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);               
	}

	/**
	 * Determine areas searchable by this plugin.
	 *
	 * @access  public
         * @return  array  An array of search areas.
	 */
	public function onContentSearchAreas()
        {
		static $areas = array();

		if($this->config->get('search_media', 1))                                               $areas['media'] = 'PLG_SEARCH_MEDIA_MEDIA';
		if($this->config->get('search_albums', 1) && $this->config->get('enable_albums'))       $areas['albums'] = 'PLG_SEARCH_MEDIA_ALBUMS';
		if($this->config->get('search_groups', 1) && $this->config->get('enable_groups'))       $areas['groups'] = 'PLG_SEARCH_MEDIA_GROUPS';
		if($this->config->get('search_playlists', 1) && $this->config->get('enable_playlists')) $areas['playlists'] = 'PLG_SEARCH_MEDIA_PLAYLISTS';
		if($this->config->get('search_channels', 1) && $this->config->get('enable_channels'))   $areas['channels'] = 'PLG_SEARCH_MEDIA_CHANNELS';

		return $areas;
	}

	/**
	 * Search HWDMediaShare content.
	 * The SQL must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav.
	 *
         * @access  public
	 * @param   string  $text      Target search string.
	 * @param   string  $phrase    Matching option (possible values: exact|any|all).  Default is "any".
	 * @param   string  $ordering  Ordering option (possible values: newest|oldest|popular|alpha|category).  Default is "newest".
	 * @param   mixed   $areas     An array if the search it to be restricted to areas or null to search all areas.
	 * @return  array   Search results.
	 */
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$app = JFactory::getApplication();
                $use_caching = $this->config->def('use_caching', 1);

                if ($use_caching)
                {
                        // Get a reference to the global cache object.
                        $cache = JFactory::getCache('com_hwdmediashare');
                        $cache->setCaching(1);

                        return $cache->call(array($this, 'onContentSearchCache'), $text, $phrase, $ordering, $areas);
                }
                else
                {
                        return plgSearchMedia::onContentSearchCache($text, $phrase, $ordering, $areas);
                }
        }

	/**
	 * Search HWDMediaShare content.
	 * The SQL must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav.
	 *
         * @access  public
	 * @param   string  $text      Target search string.
	 * @param   string  $phrase    Matching option (possible values: exact|any|all).  Default is "any".
	 * @param   string  $ordering  Ordering option (possible values: newest|oldest|popular|alpha|category).  Default is "newest".
	 * @param   mixed   $areas     An array if the search it to be restricted to areas or null to search all areas.
	 * @param   mixed   $dummy     A dummy to prevent caching for different custom field searches.
	 * @return  array   Search results.
	 */
	function onContentSearchCache($text, $phrase='', $ordering='', $areas=null)
	{
		$db	= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();

                $mediaAreas = $this->onContentSearchAreas();

                // If we are not searching any media areas, we can return nothing. 
                if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($mediaAreas))) {
				return array();
			}
		}

                $search_title		= $this->config->def('search_title',		1);
                $search_description	= $this->config->def('search_description',	1);
                $search_metadata	= $this->config->def('search_metadata',		0);
                $search_alias		= $this->config->def('search_alias',		0);
                $search_method		= $this->config->def('search_method',		1);
                $relation_method        = $this->config->def('relation_method',		0);

                // Remove white space from input.
		$text = trim($text);

		// If the input was empty then return the array.
                if ($text == '') {
			return array();
		}

		$results = array();
                
                // Loop all search areas.
                foreach($mediaAreas as $searchKey => $searchValue)
                {
                        // Continue loop if we are not searching this area.
                        if (is_array($areas)) {
                                if (!in_array($searchKey, $areas)) {
                                        continue;
                                }
                        }

                        // Load the model.
                        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_hwdmediashare/models');
                        $model = JModelLegacy::getInstance($searchKey, 'hwdMediaShareModel', array('ignore_request' => true));               

                        // Populate state (and set the context).
                        $model->context = 'plg_search_media';
                        $model->populateState();
                        
                        // Set the start and limit states.
                        $model->setState('list.start', 0);
                        $model->setState('list.limit', (int) $this->params->def('search_limit', 50));

                        // Set the ordering states.
                        switch ($ordering)
                        {
                                case 'oldest':
                                        $model->setState('list.ordering', 'a.created');
                                        $model->setState('list.direction', 'ASC');
                                break;
                                case 'popular':
                                        $model->setState('list.ordering', 'a.hits');
                                        $model->setState('list.direction', 'DESC');
                                break;
                                case 'alpha':
                                        $model->setState('list.ordering', 'a.title');
                                        $model->setState('list.direction', 'ASC');
                                break;
                                case 'newest':
                                default:
                                        $model->setState('list.ordering', 'a.created');
                                        $model->setState('list.direction', 'DESC');
                        }

                        // Define search method.
                        switch ($phrase)
                        {
                                // When searching for exact word or phrase.
                                case 'exact':
                                        $model->setState('filter.search.method', 'like');
                                break;
                                // When searching for all words or any words.
                                case 'all':
                                case 'any':
                                default:
                                        $model->setState('filter.search.method', 'match');
                                break;
                        }
                        
                        // Define search term.
                        $model->setState('filter.search', $text);

                        // Perform search.
                        if ($items = $model->getItems())
                        {
                                foreach($items as $key => $item)
                                {    
                                        $row = new stdClass;
                                        $row->title = $item->title;
                                        $row->section = JText::_($searchValue);
                                        $row->created = $item->created;
                                        $row->text = $item->description;
                                        $row->browsernav = 2;

                                        switch ($searchKey)
                                        {
                                                case 'media':
                                                        $row->href = hwdMediaShareHelperRoute::getMediaItemRoute($item->id);
                                                        break;

                                                case 'albums':
                                                        $row->href = hwdMediaShareHelperRoute::getAlbumRoute($item->id);
                                                        break;

                                                case 'groups':
                                                        $row->href = hwdMediaShareHelperRoute::getGroupRoute($item->id);
                                                        break;

                                                case 'playlists':
                                                        $row->href = hwdMediaShareHelperRoute::getPlaylistRoute($item->id);
                                                        break;

                                                case 'channels':
                                                        $row->href = hwdMediaShareHelperRoute::getUserRoute($item->id);
                                                        break;
                                        }
                                        
                                        $results = array_merge($results, array($row));                                                
                                }
                        }
                }

		return $results;
	}
}
