<div id="exerciseGroupDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    <?php if(!empty($exerciseGroup)): ?>
                        <?php echo $l->getString('edit_exercise_groups');?><strong>#<?php echo $exerciseGroup->getId(); ?></strong>
                    <?php else: ?>
                        <?php echo $l->getString('create_exercise_groups');?>
                    <?php endif; ?>
                </h4>
            </div>
            <form id="exerciseGroupForm" action="index.php?action=admin/viewExerciseGroups" class="form-horizontal" role="form" method="post">
                <input type="hidden" name="id" value="<?php echo (empty($exerciseGroup) ? 0 : $exerciseGroup->getId()); ?>" />
                <div class="modal-body">
                    <?php $de = empty($exerciseGroup) ? "" : $exerciseGroup->getName('de'); ?>
                    <?php $en = empty($exerciseGroup) ? "" : $exerciseGroup->getName('en'); ?>
                    <?php $instructor = empty($exerciseGroup) ? "" : $exerciseGroup->getInstructor(); ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="name">Name (de)</label>
                        <div class="col-sm-9">
                            <input autocomplete="off" name="name_de" type="text" class="form-control"
                                   placeholder="Enter name" value="<?php echo $de; ?>" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="name">Name (en)</label>
                        <div class="col-sm-9">
                            <input autocomplete="off" name="name_en" type="text" class="form-control"
                                   placeholder="Enter name" value="<?php echo $en; ?>" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 text-left" for="group"><?php echo $l->getString("exercise_group_instructor");?></label>
                        <div class="col-sm-9">
                            <input autocomplete="off" name="instructor" type="text" class="form-control"
                                   placeholder="Enter instructor" value="<?php echo $instructor; ?>" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $l->getString("account_edit_close");?></button>
                    <button name="submit" type="submit" class="btn btn-primary"><?php echo $l->getString("account_edit_save");?></button>
                </div>
            </form>
        </div>
    </div>
</div>
