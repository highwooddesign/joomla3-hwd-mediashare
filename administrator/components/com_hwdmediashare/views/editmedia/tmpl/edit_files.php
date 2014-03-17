<?php
/**
 * @package     Joomla.administrator
 * @subpackage  Component.hwdmediashare
 *
 * @copyright   Copyright (C) 2013 Highwood Design Limited. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

jimport( 'joomla.html.html.number' );
?>
<table class="table table-striped" id="filesList">
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
    <?php foreach($this->item->mediaitems as $i => $item): ?>
        <tr class="row<?php echo $i % 2; ?>">
                <td>
                        <?php echo $item->id; ?>
                </td>
                <td>
                        <?php echo $this->getFileType($item); ?>
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
                        <a class="btn" href="<?php echo hwdMediaShareDownloads::url($this->item, $item->file_type); ?>"><?php echo JText::_('COM_HWDMS_DOWNLOAD'); ?></a>
                </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>