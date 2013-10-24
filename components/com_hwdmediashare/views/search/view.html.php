<?php
/**
 * @version    SVN $Id: view.html.php 1032 2013-02-01 10:47:55Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      25-Nov-2011 16:54:01
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the hwdMediaShare Component
 */
class hwdMediaShareViewSearch extends JViewLegacy {
	public function display($tpl = null)
	{
		require_once JPATH_ROOT.'/administrator/components/com_search/helpers/search.php';

		// Initialise some variables
		$app        = JFactory::getApplication();
		$pathway    = $app->getPathway();
		$uri        = JFactory::getURI();
                
                hwdMediaShareFactory::load('downloads');

                // Load search language file
                $lang =& JFactory::getLanguage();
                $lang->load('com_search');
                
		$error      = null;
		$rows       = null;
		$results    = null;
		$total      = 0;

		// Get some data from the model
		$area       = $this->get('area');
		$state      = $this->get('state');
		$searchword = $state->get('keyword');
                $params     = &$state->params;
                $form       = $this->get('Form');
                
                jimport( 'joomla.utilities.arrayhelper' );
                
                // Bind the form data
                $form->bind(JArrayHelper::toObject(JRequest::get( 'get' )));
                
		// Limit searchword
		$lang = JFactory::getLanguage();
		$upper_limit = $lang->getUpperLimitSearchWord();
		$lower_limit = $lang->getLowerLimitSearchWord();
		//if (SearchHelper::limitSearchWord($searchword)) 
                //{
		//	$error = JText::sprintf('COM_SEARCH_ERROR_SEARCH_MESSAGE', $lower_limit, $upper_limit);
		//}

                // Instead of returning an error when the search term is too large, we just truncate it
                // This enables us to perform "related" searches from the media item page
                JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
                $new_limit = $upper_limit*2;
                $searchword = JHtmlString::truncate($searchword, $new_limit);
                
		// Sanatise searchword
		if (SearchHelper::santiseSearchWord($searchword, $state->get('match')))
                {
			$error = JText::_('COM_SEARCH_ERROR_IGNOREKEYWORD');
		}

		// built select lists
		$orders = array();
		$orders[] = JHtml::_('select.option',  'newest', JText::_('COM_SEARCH_NEWEST_FIRST'));
		$orders[] = JHtml::_('select.option',  'oldest', JText::_('COM_SEARCH_OLDEST_FIRST'));
		$orders[] = JHtml::_('select.option',  'popular', JText::_('COM_SEARCH_MOST_POPULAR'));
		$orders[] = JHtml::_('select.option',  'alpha', JText::_('COM_SEARCH_ALPHABETICAL'));
		// $orders[] = JHtml::_('select.option',  'category', JText::_('JCATEGORY'));

		$lists = array();
		$lists['ordering'] = JHtml::_('select.genericlist', $orders, 'ordering', 'class="inputbox"', 'value', 'text', $state->get('ordering'));

		$searchphrases		= array();
		$searchphrases[]	= JHtml::_('select.option',  'all', JText::_('COM_SEARCH_ALL_WORDS'));
		$searchphrases[]	= JHtml::_('select.option',  'any', JText::_('COM_SEARCH_ANY_WORDS'));
		$searchphrases[]	= JHtml::_('select.option',  'exact', JText::_('COM_SEARCH_EXACT_PHRASE'));
		$lists['searchphrase' ] = JHtml::_('select.radiolist',  $searchphrases, 'searchphrase', '', 'value', 'text', $state->get('match'));

		// Put the filtered results back into the model
		$state->set('keyword', $searchword);
		if ($error == null) 
                {
			$results	= $this->get('data');
			$total		= $this->get('total');
			$pagination	= $this->get('pagination');

			require_once JPATH_SITE . '/components/com_content/helpers/route.php';

			for ($i=0, $count = count($results); $i < $count; $i++)
			{
				$row = &$results[$i]->text;

				if ($state->get('match') == 'exact') {
					$searchwords = array($searchword);
					$needle = $searchword;
				}
				else {
					$searchworda = preg_replace('#\xE3\x80\x80#s', ' ', $searchword);
					$searchwords = preg_split("/\s+/u", $searchworda);
 					$needle = $searchwords[0];
				}

				$row = SearchHelper::prepareSearchContent($row, $needle);
				$searchwords = array_unique($searchwords);
				$searchRegex = '#(';
				$x = 0;

				foreach ($searchwords as $k => $hlword)
				{
					$searchRegex .= ($x == 0 ? '' : '|');
					$searchRegex .= preg_quote($hlword, '#');
					$x++;
				}
				$searchRegex .= ')#iu';

				$row = preg_replace($searchRegex, '<span class="highlight">\0</span>', $row);

				$result = &$results[$i];
				if ($result->created) {
					$created = JHtml::_('date',$result->created, JText::_('DATE_FORMAT_LC3'));
				}
				else {
					$created = '';
				}

				$result->text		= JHtml::_('content.prepare', $result->text);
				$result->created	= $created;
				$result->count		= $i + 1;
			}
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assignRef('pagination',  $pagination);
		$this->assignRef('results',	$results);
		$this->assignRef('lists',	$lists);
		$this->assignRef('params',	$params);
                $this->assignRef('state',	$state);
                $this->assignRef('form',	$form);
                
		$this->assign('ordering',	$state->get('ordering'));
		$this->assign('searchword',	$searchword);
		$this->assign('origkeyword',	$state->get('origkeyword'));
		$this->assign('searchphrase',	$state->get('match'));
		$this->assign('searcharea',	$area);

		$this->assign('total',		$total);
		$this->assign('error',		$error);
		$this->assign('action',		$uri);

		$this->_prepareDocument();
                
		parent::display($tpl);
	}
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
                $menus	= $app->getMenu();
		$title	= null;

                $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/hwd.css');
                if ($this->state->params->get('load_joomla_css') != 0) $this->document->addStyleSheet(JURI::base( true ).'/media/com_hwdmediashare/assets/css/joomla.css');

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
                else
                {
			$this->params->def('page_heading', JText::_('COM_HWDMS_SEARCH'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title))
                {
			$title = JText::_('COM_HWDMS_SEARCH');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
                {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
                {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
                $this->document->setTitle($title);

		if ($this->params->get('meta_desc'))
		{
			$this->document->setDescription($this->params->get('meta_desc'));
		}

		if ($this->params->get('meta_keys'))
		{
			$this->document->setMetadata('keywords', $this->params->get('meta_keys'));
		}

		if ($this->params->get('meta_rights'))
		{
			$this->document->setMetadata('copyright', $this->params->get('meta_rights'));
		}
         	
                if ($this->params->get('meta_author'))
		{
			//$this->document->setMetadata('author', $this->params->get('meta_author'));
		}       
	}
}
