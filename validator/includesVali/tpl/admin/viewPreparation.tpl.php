<?php require 'ajax/deleteRequest.tpl.php'; ?>

<h1><?php echo $l->getString('edit_preparation'); ?> (Task <strong>#<?php echo $task->getId(); ?></strong>)</h1>

<script>
    function editPreparation_show(task, id) {
        $.ajax({
            url: 'ajax.php?action=preparation&subaction=show_form&task=' + task + '&id=' + id,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#preparationDialog").modal('show');
            }
        });
    }

    function createPreparation_show(task) {
        $.ajax({
            url: 'ajax.php?action=preparation&subaction=show_form&task=' + task,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#preparationDialog").modal('show');
            }
        });
    }
</script>

<?php if(@$edited): ?>
    <div class="alert alert-success">
        <strong><?php echo $l->getString('alert_success_title', 'Success!|Erfolgreich!'); ?></strong>
        <?php echo $l->getString('alert_success_update', 'The object has been updated successfully.|Das Objekt wurde erfolgreich geändert.'); ?>
    </div>
<?php endif; ?>

<table class="table table-hover">
    <thead>
    <tr>
        <th><?php echo $l->getString('preparation_id', 'ID|ID'); ?></th>
        <th><?php echo $l->getString('preparation_language', 'Language|Sprache'); ?></th>
        <th><?php echo $l->getString('preparation_sql', 'SQL|SQL'); ?></th>
        <th>
            <button type="button" onclick="createPreparation_show(<?php echo $task->getId(); ?>);" class="btn btn-default btn-sm" style="float: right;"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <?php echo $l->getString('create', 'Create|Erstellen'); ?></button>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($task->getPreparations() as $tskp): ?>
        <tr>
            <td><?php echo $tskp->getId(); ?></td>
            <td><?php echo is_null($tskp->getLang()) ? "(all)" : $tskp->getLang() ; ?></td>
            <td><?php echo $tskp->getSql(); ?></td>
            <td>
                <div class="btn-toolbar" role="toolbar" style="float: right;">
                    <div class="btn-group" style="white-space: nowrap;">
                    	<!-- 16.06.17: the delete-button open via function in deleteRequest a deletion-request -->
                        <button type="button" class="btn btn-default btn-sm" onclick="deletePreparation_open('<?php echo $tskp->getId(); ?>');" aria-label="Center Align">
                        	<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                        </button>
                        <button type="button" class="btn btn-default btn-sm" onclick="editPreparation_show(<?php echo $task->getId(); ?>, <?php echo $tskp->getId(); ?>);" aria-label="Center Align">
                        	<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>