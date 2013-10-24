<?php
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.categories');

class modMediaCategoriesHelper extends JObject
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
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
    
                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());
                $lang->load('com_hwdmediashare', JPATH_SITE, $lang->getTag(), true, false);

                $params = $config;
                $this->set('utilities', hwdMediaShareUtilities::getInstance());
                $this->set('params', $config);
		$this->set('url', JURI::root().'modules/mod_media_categories/');
		$this->set('container', 'modmediacategories_'.$module->id);
                $this->set('return', base64_encode(JFactory::getURI()->toString()));
                $this->set('elementType', 6);
	}

	public function addHead()
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
                if ($this->params->get('load_joomla_css') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
	}

	public function getItems()
	{
		// Get an instance with a dummary option array to load a complete cateogry node tree
                $categories = JCategories::getInstance('hwdMediaShare', array('module' => true));
		$category = $categories->get($this->params->get('parent', 'root'));

		if ($category != null)
		{
			$items = $category->getChildren();
			if($this->params->get('count', 0) > 0 && count($items) > $this->params->get('count', 0))
			{
				$items = array_slice($items, 0, $this->params->get('count', 0));
			}
			return $items;
		}                          
	}
}