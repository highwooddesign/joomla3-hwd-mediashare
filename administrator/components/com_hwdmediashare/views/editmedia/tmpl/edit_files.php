<?php
/**
 * @version    SVN $Id: edit_files.php 277 2012-03-28 10:03:31Z dhorsfall $
 * @package    hwdMediaShare
 * @copyright  Copyright (C) 2011 Highwood Design Limited. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Dave Horsfall
 * @since      15-Apr-2011 10:13:15
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.html.html.number' );
?>
<thead>
        <tr>
                <th width="5">
                        <?php echo JText::_('COM_HWDMS_ID'); ?>
                </th>
                <th>
                        <?php echo JText::_('COM_HWDMS_FILE_TYPE'); ?>
                </th>
                <th>
                        <?php echo JText::_('COM_HWDMS_FILE_PATH'); ?>
                </th>
                <th>
                        <?php echo JText::_('COM_HWDMS_EXTENSION'); ?>
                </th>
                <th>
                        <?php echo JText::_('COM_HWDMS_FILE_SIZE'); ?>
                </th>
                <th>
                        <?php echo JText::_('COM_HWDMS_DOWNLOAD'); ?>
                </th>
        </tr>
</thead>
<tbody>
    <?php foreach($this->item->mediaitems as $i => $item):
            JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
            $table =& JTable::getInstance('Media', 'hwdMediaShareTable');
            $table->load( $item->element_id );

            $properties = $table->getProperties(1);
            $row = JArrayHelper::toObject($properties, 'JObject'); ?>
            <tr class="row<?php echo $i % 2; ?>">
                    <td>
                            <?php echo $item->id; ?>
                    </td>
                    <td>
                            <a href="<?php echo 'index.php?option=com_hwdmediashare&task=editmedia.view&id='.$this->item->id.'&file_type='.$item->file_type.'&tmpl=component'; ?>" class="modal" rel="{handler: 'iframe', size: {x: 500, y: 400}}" ><?php echo $this->getFileType($item); ?></a>
                    </td>
                    <td>
                            <?php echo $this->getPath($item); ?>
                    </td>
                    <td>
                            <?php echo $this->getExtension($item); ?>
                    </td>
                    <td>
                            <?php echo JHtml::_('number.bytes', $item->size); ?>
                    </td>
                    <td>
                            <a href="<?php echo hwdMediaShareDownloads::url($row,$item->file_type); ?>">Download</a>
                    </td>
            </tr>
    <?php endforeach; ?>
</tbody>