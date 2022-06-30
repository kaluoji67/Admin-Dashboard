<?php require 'ajax/deleteRequest.tpl.php'; ?>

<h1><?php echo $l->getString('edit_task_groups'); ?></h1>

<script>
    function createTaskGroup_show() {
        $.ajax({
            url: 'ajax.php?action=task_group&subaction=show_form',
            success: function(r) {
                $("#ajax_container").html(r);
                $("#taskGroupDialog").modal('show');
            }
        });
    }

    function editTaskGroup_show(id) {
        $.ajax({
            url: 'ajax.php?action=task_group&subaction=show_form&id=' + id,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#taskGroupDialog").modal('show');
            }
        });
    }

    //get the id of the original taskgroup, set copy true and open ajax with the (sub-)action 
    function copyTaskGroupSem_show(id) {
        $.ajax({
            url: 'ajax.php?action=task_group&subaction=show_form&copySem=true&id=' + id,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#taskGroupDialog").modal('show');
            }
        });
    }
    
    //show copy-form, to copy multiple taskgroups at once
    function copyMoreTaskGroups_show() {
        $.ajax({
            url: 'ajax.php?action=task_group&subaction=show_copyform',
            success: function(r) {
                $("#ajax_container").html(r);
                $("#taskDialog").modal('show');
                loadMirror();
            }
        });
    }
</script>

<?php if(@$edited): ?>
    <div class="alert alert-success">
        <strong><?php echo $l->getString('task_group_edit_success_headline'); ?></strong>
        <?php echo $l->getString('task_group_edit_success'); ?>
    </div>
<?php endif; ?>

<table class="table table-hover">
    <thead>
    <tr>
        <th><?php echo $l->getString('task_group_id'); ?></th>
        <th>Position</th>
        <th><?php echo $l->getString('task_group_name'); ?> (de)</th>
        <th><?php echo $l->getString('task_group_name'); ?> (en)</th>
        <th><?php echo $l->getString('task_group_task_count'); ?></th>
        <th><?php echo $l->getString('task_group_visible'); ?></th>
        <th>
            <div class="btn-toolbar" role="toolbar" style="float: right;">
                <div class="btn-group" style="white-space: nowrap;">
                    <button type="button" onclick="copyMoreTaskGroups_show()" class="btn btn-default btn-sm" style="float: right;" aria-label="Left Align"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span><?php echo $l->getString('task_group_copy'); ?> </button>
                    <button type="button" onclick="createTaskGroup_show()" class="btn btn-default btn-sm" style="float: right;"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <?php echo $l->getString('task_group_action_create'); ?></button>
                </div>
            </div>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach(TaskGroup::getByCondition("tskg_sem_id = ?", array($_SESSION["sem_id"]), array('tskg_order')) as $tg): ?>
        <tr>
            <td><?php echo $tg->getId(); ?></td>
            <td><?php echo $tg->getOrder(); ?></td>
            <td><?php echo $tg->getName('de'); ?></td>
            <td><?php echo $tg->getName('en'); ?></td>
            <td>
                <?php
                    $tasks = Task::getByCondition('tsk_tskg_id = ?', array($tg->getId()));
                    echo count($tasks);
                ?>
            </td>
            <td><?php echo $tg->getVisible(); ?></td>
            <td>
                <div class="btn-toolbar" role="toolbar" style="float: right;">
                    <div class="btn-group" style="white-space: nowrap;">
                        <!-- 12.06.17: the delete-button open via function in deleteRequest a deletion-request -->
                        <button type="button" class="btn btn-default btn-sm" onclick="deleteTaskGroup_open('<?php echo $tg->getId(); ?>');" aria-label="Center Align">
                        	<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <?php echo $l->getString("account_delete");?>
                        </button>
                        <button type="button" class="btn btn-default btn-sm" onclick="editTaskGroup_show(<?php echo $tg->getId(); ?>);">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <?php echo $l->getString('task_group_action_edit'); ?>
                        </button>
                     	<!--  09.06.17 Ulli -->
                        <a href="index.php?action=admin/viewSubmissions&tgId=<?php echo $tg->getId(); ?>" class="btn btn-default btn-sm">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> <?php echo $l->getString('view_submissions', 'View Submissions|Einreichungen ans.'); ?>
                   		</a>

                        <button type="button" class="btn btn-default btn-sm" onclick="copyTaskGroupSem_show(<?php echo $tg->getId(); ?>);">
                            <span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span> <?php echo $l->getString('task_group_copy_semester'); ?>
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>