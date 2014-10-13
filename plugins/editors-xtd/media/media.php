<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.editors-xtd.media
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class plgButtonMedia extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @access  protected
	 * @var     boolean
	 */
	protected $autoloadLanguage = true;
        
	/**
	 * Display the button.
	 *
	 * @access  protected
	 * @param   string     $name    The name of the button to display.
	 * @param   string     $asset   The name of the asset being edited.
	 * @param   integer    $author  The id of the author owning the asset being edited.
	 * @return  object     JObject.
	 */
	public function onDisplay($name, $asset, $author)
	{
		/*
		 * Javascript to insert the link
		 * View element calls jSelectMedia when an article is clicked
		 * jSelectMedia creates the plugin tag, sends it to the editor,
		 * and closes the select frame.
		 */
		$js = "
		function jSelectMedia(id, title, layout) {
			var tag = '<div>{media id=' + id + ',layout=' + layout + '}</div><br />';
			jInsertEditorText(tag, '" . $name . "');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
                
                $link = 'index.php?option=com_hwdmediashare&amp;view=media&amp;layout=modal&amp;tmpl=component&amp;function=jSelectMedia';
                JHtml::_('behavior.modal');
                $button = new JObject;
                $button->modal = true;
                $button->class = 'btn';
                $button->link = $link;
                $button->text = JText::_('PLG_EDITORS-XTD_MEDIA_BUTTON_MEDIA');
                $button->name = 'video';
                $button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

                return $button;
	}
}
