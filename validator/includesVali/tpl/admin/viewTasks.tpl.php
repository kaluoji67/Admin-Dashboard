<?php require 'ajax/deleteRequest.tpl.php';?>

<h1><?php echo $l->getString('edit_tasks'); ?></h1>

<script>
    function createTask_show() {
        $.ajax({
            url: 'ajax.php?action=task&subaction=show_form',
            success: function(r) {
                $("#ajax_container").html(r);
                loadMirror();
                $("#taskDialog").modal('show');
            }
        });
    }

    function editTask_open(id) {
        $.ajax({
            url: 'ajax.php?action=task&subaction=show_form&id=' + id,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#taskDialog").modal('show');
                loadMirror();
            }
        });
    }

    //get the id of the original task, set copy true and open ajax with the (sub-)action 
    function copyTask_open(id) {
        $.ajax({
            url: 'ajax.php?action=task&subaction=show_form&copy=true&id=' + id,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#taskDialog").modal('show');
                loadMirror();
            }
        });
    }

    //get the id of the original task, set copySem true and open ajax with the (sub-)action 
    function copyTaskSem_open(id) {
        $.ajax({
            url: 'ajax.php?action=task&subaction=show_form&copySem=true&id=' + id,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#taskDialog").modal('show');
                loadMirror();
            }
        });
    }
    
    //show copy-form, to copy multiple tasks at once
    function copyMoreTasks_show() {
        $.ajax({
            url: 'ajax.php?action=task&subaction=show_copyform',
            success: function(r) {
                $("#ajax_container").html(r);
                $("#taskDialog").modal('show');
                loadMirror();
            }
        });
    }
</script>


    <table class="table  table-hover">
        <thead>
        <tr>
            <th>Task-ID</th>
            <th>Task Group (de)</th>
            <th>Position</th>
            <th>Title (de)</th>
            <th>Title (en)</th>
            <th>
                <div class="btn-toolbar" role="toolbar" style="float: right;">
                    <div class="btn-group" style="white-space: nowrap;">
                        <button type="button" onclick="copyMoreTasks_show()" class="btn btn-default btn-sm" style="float: right;" aria-label="Left Align"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span> Copy Tasks</button>
                        <button type="button" onclick="createTask_show()" class="btn btn-default btn-sm" style="float: right;" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create Task</button>
                    </div>
                </div>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach(Task::getByCondition("tsk_sem_id = ?", array($_SESSION["sem_id"]), array('tsk_tskg_id', 'tsk_order')) as $t): ?>
            <?php $tg = $t->getTskgId() ? TaskGroup::getById($t->getTskgId()) : null; ?>
            <tr>
                <td><?php echo $t->getId(); ?></td>
                <td><?php echo empty($tg) ? "(none)" : @$tg->getName('de'); ?></td>
                <td><?php echo $t->getOrder(); ?></td>
                <td><?php echo $t->getTitle('de'); ?></td>
                <td><?php echo $t->getTitle('en'); ?></td>
                <td>
                    <div class="btn-toolbar" role="toolbar" style="float: right;">
                        <div class="btn-group" style="white-space: nowrap;">
                           	<!-- 12.06.17: the delete-button open via function in deleteRequest a deletion-request -->        		
                      		<button type="button" class="btn btn-default btn-sm" onclick="deleteTask_open('<?php echo $t->getId(); ?>');" aria-label="Center Align">
                      			<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                      		</button>
                      		<button type="button" class="btn btn-default btn-sm" onclick="editTask_open('<?php echo $t->getId(); ?>');" aria-label="Center Align">
                      			<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                      		</button>
                            <a href="index.php?action=admin/viewPreparation&task=<?php echo $t->getId(); ?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Preparation</a>
                            <a href="index.php?action=admin/viewStatements&task=<?php echo $t->getId(); ?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Solution</a>
                            <!-- insert Copy-Button - open the function copyTask_open -->
                            <button type="button" class="btn btn-default btn-sm" onclick="copyTask_open('<?php echo $t->getId(); ?>');" aria-label="Center Align"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span> Copy</button>
                            <button type="button" class="btn btn-default btn-sm" onclick="copyTaskSem_open('<?php echo $t->getId(); ?>');" aria-label="Center Align"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span> Copy to Semester</button>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
