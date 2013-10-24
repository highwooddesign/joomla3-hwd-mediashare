<?php
/**
 * @version    SVN $Id: media.php 1672 2013-08-22 15:39:02Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2012 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      27-Feb-2012 16:29:22
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Avoid problems with RokComment plugin #55621
JLoader::register('ContentModelCategories', JPATH_ROOT.'/components/com_content/models/categories.php');

class plgContentMedia extends JPlugin
{
	/**
	 * An item.
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected $item = null;

        /**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int	The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		$app = JFactory::getApplication();

                // Simple admin check to determine whether plugin should process further
		if ($app->isAdmin()) return true;

                // Simple performance check to determine whether plugin should process further
		if (strpos($article->text, 'media') === false) {
			return true;
		}

                JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_hwdmediashare/tables');
                JLoader::register('hwdMediaShareFactory', JPATH_ROOT . '/components/com_hwdmediashare/libraries/factory.php');
                $hwdms = hwdMediaShareFactory::getInstance();
                $this->config = $hwdms->getConfig();
                $this->config->merge( $this->params );

		// Expression to search for (positions)
		$regex	= '/{media\s+(.*?)}/i';

		// Find all instances of plugin and put in $matches for loadposition
		// $matches[0] is full pattern match, $matches[1] is the position
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

                // No matches, skip this
		if ($matches)
                {
			foreach ($matches as $match)
                        {
                                $matcheslist =  explode(',', $match[1]);

                                $options = array();
                                $data = '';

                                foreach ($matcheslist as $list)
                                {
                                        $data.= "$list\n";
                                }


                                // Load default configuration
                                jimport( 'joomla.html.parameter' );
                                $this->config->merge( new JRegistry( $data ) );

                                // We take the more human readable width option and set the main mediaitem_size parameter
                                $this->config->set('mediaitem_size', $this->config->get('width', 200));
                                // We take the more human readable height option and set the main mediaitem_height parameter
                                $this->config->get('height') ? $this->config->set('mediaitem_height', $this->config->get('height')) : null;
                                // Don't show the more link
                                $this->config->set('show_more_link', 'hide');

                                switch ($this->config->get('load')) {
                                    case 'forum':
                                        $this->config->set('mediaitem_size', '430' );
                                        $this->config->set('display', 'inline');
                                        $this->config->set('align', 'left');
                                        $this->config->set('media_autoplay', '0');
                                        break;
                                }

				$align = (($this->config->get('align', 'left') == 'center') ? 'margin: 0 auto;' : 'float:'.$this->config->get('align', 'left').';');

                                $output = '<div class="media-content" style="max-width:'.$this->config->get('width', 200).'px;width:100%;'.$align.'">';
                                switch ($this->config->get('load', 'media')) {
                                    case 'media':
                                    case 'forum':
                                        $output.= $this->_loadMedia();
                                        break;
                                    case 'album':
                                        $output.= $this->_loadAlbum();
                                        break;
                                }
                                $output.= '</div>';

                                // We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\'), $article->text, 1);
			}
		}
	}

	protected function _loadMedia()
	{
                if (!$this->config->def('id'))
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }
                if (!$this->config->def('load'))
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }

                JLoader::register('hwdMediaShareHelperNavigation', JPATH_ROOT . '/components/com_hwdmediashare/helpers/navigation.php');
                JLoader::register('hwdMediaShareModelMediaItem', JPATH_ROOT . '/components/com_hwdmediashare/models/mediaitem.php');

                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());

                $model = hwdMediaShareModelMediaItem::getInstance('MediaItem', 'hwdMediaShareModel');

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

                if (!$item = $model->getItem($this->config->get('id')))
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }

                hwdMediaShareFactory::load('media');
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('utilities');

                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT . '/components/com_hwdmediashare/helpers/route.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');

                $params	= $this->config;

                $helper	= new JObject;
                $helper->set('utilities', hwdMediaShareUtilities::getInstance());
                $helper->set('return', base64_encode(JFactory::getURI()->toString()));
                $helper->set('columns', 1);

                $items = array($item);

                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare');

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');
                if ($this->config->get('load_joomla_css') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');
                if ($this->config->get('list_thumbnail_aspect') != 0) $doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/aspect.css');
                if ($this->config->get('list_thumbnail_aspect') != 0) $doc->addScript(JURI::base( true ).'/media/com_hwdmediashare/assets/javascript/aspect.js');

                ob_start();
                if ($this->config->get('display') == 'inline')
                {
                        if (file_exists(JPATH_SITE . '/modules/mod_media_item/helper.php'))
                        {
                                jimport( 'joomla.application.module.helper' );
                                require JModuleHelper::getLayoutPath('mod_media_item', $params->get('layout', 'default'));
                        }
                }
                else
                {
                        if (file_exists(JPATH_SITE . '/modules/mod_media_media/helper.php'))
                        {
                                // Check if we should set the modal parameter
                                if ($this->config->get('display') == 'modal') $params->set('modal', 1);

                                // Load module layout
                                JLoader::register('modMediaMediaHelper', JPATH_SITE . '/modules/mod_media_media/helper.php');
                                $dummy = new JObject;
                                $dummy->id = 0;
                                $helper	= new modMediaMediaHelper($dummy, $params);
                                require JModuleHelper::getLayoutPath('mod_media_media', $params->get('layout', 'default'));
                        }

                }
                $retval = ob_get_contents();
                ob_end_clean();

		return $retval;
	}

	protected function _loadAlbum()
	{
                if (!$this->config->def('id'))
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }
                if (!$this->config->def('load'))
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }

                JLoader::register('hwdMediaShareHelperNavigation', JPATH_ROOT . '/components/com_hwdmediashare/helpers/navigation.php');
                JLoader::register('hwdMediaShareModelAlbum', JPATH_ROOT . '/components/com_hwdmediashare/models/album.php');

                // Load the HWDMediaShare language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare', JPATH_SITE.'/components/com_hwdmediashare', $lang->getTag());

                $model = hwdMediaShareModelAlbum::getInstance('Album', 'hwdMediaShareModel');

                if (!$item = $model->getAlbum($this->config->get('id')))
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }

                if (!$item->id)
                {
                        return '<!-- Failed media plugin: ' . $this->config . '-->';
                }
                
                hwdMediaShareFactory::load('downloads');
                hwdMediaShareFactory::load('files');
                hwdMediaShareFactory::load('utilities');

                JLoader::register('hwdMediaShareHelperRoute', JPATH_ROOT . '/components/com_hwdmediashare/helpers/route.php');
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                JLoader::register('JHtmlHwdIcon', JPATH_ROOT.'/components/com_hwdmediashare/helpers/icon.php');

                $params	= $this->config;

                $helper	= new JObject;
                $helper->set('utilities', hwdMediaShareUtilities::getInstance());
                $helper->set('return', base64_encode(JFactory::getURI()->toString()));
                $helper->set('columns', 1);

                $items = array($item);

                $lang =& JFactory::getLanguage();
                $lang->load('com_hwdmediashare');

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/lite.css');

                ob_start();
                if (file_exists(JPATH_SITE . '/modules/mod_media_albums/helper.php'))
                {
                        // Check if we should set the modal parameter
                        if ($this->config->get('display') == 'slideshow') $params->set('slideshow', 1);

                        // Load module layout
                        JLoader::register('modMediaAlbumsHelper', JPATH_SITE . '/modules/mod_media_albums/helper.php');
                        $dummy = new JObject;
                        $dummy->id = 0;
                        $helper	= new modMediaAlbumsHelper($dummy, $params);
                        require JModuleHelper::getLayoutPath('mod_media_albums', $params->get('layout', 'default'));
                }
                $retval = ob_get_contents();
                ob_end_clean();

		return $retval;
	}
}
