<?php require 'ajax/deleteRequest.tpl.php';?>

<h1><?php echo $l->getString('edit_solution'); ?> (Task <strong>#<?php echo $task->getId(); ?></strong>)</h1>

<script>
    function editStatement_show(task, id) {
        $.ajax({
            url: 'ajax.php?action=statement&subaction=show_form&task=' + task + '&id=' + id,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#statementDialog").modal('show');
            }
        });
    }

    function createStatement_show(task) {
        $.ajax({
            url: 'ajax.php?action=statement&subaction=show_form&task=' + task,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#statementDialog").modal('show');
            }
        });
    }
</script>

<?php if(@$edited): ?>
    <div class="alert alert-success">
        <strong>Success!</strong>
        The statements have been updated or created successfully.
    </div>
<?php endif; ?>

<table class="table table-hover">
    <thead>
    <tr>
        <th>Statement ID</th>
        <th>Language</th>
        <th>Title</th>
        <th>SQL (desired)</th>
        <th>SQL (actual)</th>
        <th>Check Null</th>
        <th>Check Default</th>
        <th>Check Case</th>
        <th>
            <button type="button" onclick="createStatement_show(<?php echo $task->getId(); ?>);" class="btn btn-default btn-sm" style="float: right;" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create Statement</button>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($task->getStatements() as $stmt): ?>
        <tr>
            <td><?php echo $stmt->getId(); ?></td>
            <td><?php echo !is_null($stmt->getLang()) ? $stmt->getLang() : "(all)"; ?></td>
            <td><?php echo $stmt->getTitle(); ?></td>
            <td><?php echo $stmt->getSqlDesired(); ?></td>
            <td><?php echo $stmt->getSqlActual(); ?></td>
            <td><?php echo $stmt->getchecknull()==1 ? "Yes" : "No"; ?></td>
            <td><?php echo $stmt->getcheckdefault()==1 ? "Yes" : "No"; ?></td>
            <td><?php echo $stmt->getcheckcase()==1 ? "Yes" : "No"; ?></td>
            <td>
                <div class="btn-toolbar" role="toolbar" style="float: right;">
                    <div class="btn-group" style="white-space: nowrap;">
                        <!-- 16.06.17: the delete-button open via function in deleteRequest a deletion-request -->
                        <button type="button" class="btn btn-default btn-sm" onclick="deleteStatement_open('<?php echo $stmt->getId(); ?>');" aria-label="Center Align">
                        	<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                        </button>
                        <button type="button" class="btn btn-default btn-sm" onclick="editStatement_show(<?php echo $task->getId(); ?>, <?php echo $stmt->getId(); ?>);" aria-label="Center Align">
                        	<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>