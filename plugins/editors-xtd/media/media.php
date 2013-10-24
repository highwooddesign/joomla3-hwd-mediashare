<?php
/**
 * @version    $Id: media.php 425 2012-06-28 07:48:57Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla plugin library
jimport('joomla.plugin.plugin');

/**
 * Editor Article buton
 *
 * @package		Joomla.Plugin
 * @subpackage	Editors-xtd.article
 * @since 1.5
 */
class plgButtonMedia extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	/**
	 * Display the button
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	function onDisplay($name)
	{
		$app = &JFactory::getApplication();

                /*
		 * Javascript to insert the link
		 * View element calls jSelectMedia when a media is clicked
		 * jSelectMedia creates the link tag, sends it to the editor,
		 * and closes the select frame.
		 */
		$js = "
		function jSelectMedia(id, width, align, display) {
			var tag = '{media load=media,id='+id+',width='+width+',align='+align+',display='+display+'}';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

                $css = "
                .button2-left .media {
                        background: transparent url('".JURI::root( true )."/plugins/editors-xtd/media/assets/j_button2_media.jpg') no-repeat 100% 0px;
                }";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
                ($app->isAdmin() ? $doc->addStyleDeclaration($css) : null);                

		JHtml::_('behavior.modal');

		/*
		 * Use the built-in element view to select the media.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_hwdmediashare&amp;view=media&amp;layout=editor&amp;tmpl=component&amp;function=jSelectMedia';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_EDITORS-XTD_MEDIA_BUTTON_MEDIA'));
		$button->set('name', 'media');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
