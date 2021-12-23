<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

//$published = $this->state->get('filter.published');
?>

<div id="collapseModal" role="dialog" tabindex="-1" class="joomla-modal modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			<h3 class="modal-title"><?php echo Text::_('COM_PHOCAGALLERY_BATCH_OPTIONS_CATEGORIES');?></h3>
				<button type="button" class="btn-close novalidate" data-bs-dismiss="modal" aria-label="<?php Text::_('COM_PHOCAGALLERY_CLOSE'); ?>">
		</button>
	</div>
<div class="modal-body">

<div class="p-3">
	<div class="row">
        <div class="form-group col-md-6">
			<div class="controls"><?php echo LayoutHelper::render('joomla.html.batch.access', []);?></div>
		</div>
	</div>

    <div class="row">
        <div class="form-group col-md-6">
            <div class="controls">
                <label id="batch-accessuserid-lbl" for="batch-accessuserid" class="modalTooltip" title="<strong><?php echo Text::_('COM_PHOCAGALLERY_SET_ACCESS_RIGHTS_LEVEL'); ?></strong><br /><?php echo Text::_('COM_PHOCAGALLERY_NOT_MAKING_SELECTION_WILL_KEEP_ORIGINAL_ACCESS_RIGHTS_LEVELS'); ?>"><?php echo Text::_('COM_PHOCAGALLERY_SET_ACCESS_RIGHTS_LEVEL'); ?></label>

                <?php
                $userList = PhocaGalleryAccess::usersList( 'batch[accessuserid][]', 'batch-accessuserid', -3, 1, NULL, 'name', 0 );
                echo $userList
                ?>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="form-group col-md-6">
			<div class="controls"><?php echo LayoutHelper::render('joomla.html.batch.language', []); ?></div>
		</div>
	</div>
    <div class="row">
        <div class="form-group col-md-6">
			<div class="controls"><?php echo PhocaGalleryBatch::item('', 1); ?></div>
		</div>
	</div>
</div>
</div>

<div class="modal-footer">
		<button class="btn" type="button" onclick="document.getElementById('batch-category-id').value='';document.getElementById('batch-access').value='';document.getElementById('batch-language-id').value=''" data-bs-dismiss="modal">
			<?php echo Text::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('phocagalleryc.batch');" id="batch-submit-button-id" data-submit-task="phocagalleryc.batch">
			<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>


		</div>
	</div>
</div>


