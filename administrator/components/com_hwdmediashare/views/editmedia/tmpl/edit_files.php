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

jimport('joomla.html.html.number');
?>
<table class="table table-striped" id="filesList">
        <thead>
                <tr>
                        <th width="10%" class="nowrap">
                                <?php echo JText::_('COM_HWDMS_FILE_TYPE'); ?>
                        </th>
                        <th>
                                <?php echo JText::_('COM_HWDMS_FILE_PATH'); ?>
                        </th>   
                        <th width="1%"></th>                        
                        <th width="10%" class="nowrap hidden-phone">
                                <?php echo JText::_('COM_HWDMS_EXTENSION'); ?>
                        </th>
                        <th width="10%" class="nowrap hidden-phone">
                                <?php echo JText::_('COM_HWDMS_FILE_SIZE'); ?>
                        </th>
                        <th width="1%" class="nowrap hidden-phone">
                                <?php echo JText::_('COM_HWDMS_ID'); ?>
                        </th>
                </tr>
        </thead>
        <tbody>
        <?php foreach($this->item->mediafiles as $i => $item): ?>
                <tr class="row<?php echo $i % 2; ?>">
                        <td>
                                <?php echo $this->getFileType($item); ?>
                        </td>
                        <td>
                                <?php echo $this->getPath($item); ?>
                        </td>
                        <td>
                                <a class="btn pull-right" href="<?php echo hwdMediaShareDownloads::url($this->item, $item->file_type, 1); ?>" target="_blank"><?php echo JText::_('COM_HWDMS_DOWNLOAD'); ?></a>
                        </td>                        
                        <td class="hidden-phone">
                                <?php echo $this->getExtension($item); ?>
                        </td>
                        <td class="hidden-phone">
                                <?php echo JHtml::_('number.bytes', $item->size); ?>
                        </td>
                        <td class="hidden-phone">
                                <?php echo $item->id; ?>
                        </td>               
                </tr>
        <?php endforeach; ?>
        </tbody>
</table>