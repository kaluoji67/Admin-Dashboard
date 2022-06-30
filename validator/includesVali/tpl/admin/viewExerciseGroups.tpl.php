<?php require 'ajax/deleteRequest.tpl.php'; ?>
<?php require 'ajax/editExerciseGroup.tpl.php'; ?>

<h1><?php echo $l->getString('edit_exercise_groups', 'Edit Exercise Groups|Übungsgruppen bearbeiten'); ?></h1>

<script>
    function createExerciseGroup_show() {
        $.ajax({
            url: 'ajax.php?action=exercise_group&subaction=show_form',
            success: function(r) {
                $("#ajax_container").html(r);
                $("#exerciseGroupDialog").modal('show');
            }
        });
    }

    function editExerciseGroup_show(id) {
        $.ajax({
            url: 'ajax.php?action=exercise_group&subaction=show_form&id=' + id,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#exerciseGroupDialog").modal('show');
            }
        });
    }
    function viewSubmissionGroup_show(id) {
        $.ajax({
            url: 'ajax.php?action=submission_group&subaction=show_form&id=' + id,
            success: function(r) {
                $("#ajax_container").html(r);
                $("#exerciseGroupDialog").modal('show');
            }
        });
    }
</script>

<?php if(@$edited): ?>
    <div class="alert alert-success">
        <strong><?php echo $l->getString('exercise_group_edit_success_headline', 'Success!|Erfolgreich!'); ?></strong>
        <?php echo $l->getString('exercise_group_edit_success', 'Everything\'s done.|Alles gut'); ?>
    </div>
<?php endif; ?>

<table class="table table-hover">
    <thead>
    <tr>
        <th><?php echo $l->getString('exercise_group_id', 'ID|ID'); ?></th>
        <th><?php echo $l->getString('exercise_group_name', 'Exercise Group Name|Name der Übungsgruppe'); ?> (de)</th>
        <th><?php echo $l->getString('exercise_group_name'); ?> (en)</th>
        <th><?php echo $l->getString('exercise_group_instructor', 'Instructor|Übungsleiter'); ?></th>
        <th>
            <button type="button" onclick="createExerciseGroup_show()" class="btn btn-default btn-sm" style="float: right;"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <?php echo $l->getString('create', 'Create|Hinzufügen'); ?></button>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach(ExerciseGroup::getByCondition("egrp_sem_id = ?", array($_SESSION["sem_id"])) as $eg): ?>
        <tr>
            <td><?php echo $eg->getId(); ?></td>
            <td><?php echo $eg->getName('de'); ?></td>
            <td><?php echo $eg->getName('en'); ?></td>
            <td><?php echo $eg->getInstructor(); ?></td>
            <td>
                <div class="btn-toolbar" role="toolbar" style="float: right;">
                    <div class="btn-group" style="white-space: nowrap;">
                       	<!-- 12.06.17: the delete-button open via function in deleteRequest a deletion-request -->
                        <button type="button" class="btn btn-default btn-sm" onclick="deleteExerciseGroup_open('<?php echo $eg->getId(); ?>');" aria-label="Center Align">
                        	<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <?php echo $l->getString("account_delete");?>
                        </button>
                        <button type="button" class="btn btn-default btn-sm" onclick="editExerciseGroup_show(<?php echo $eg->getId(); ?>);">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <?php echo $l->getString('edit', 'Edit|Bearbeiten'); ?>
                        </button>
                        <!--  09.06.17 Ulli -->
                        <a href="index.php?action=admin/viewSubmissions&egId=<?php echo $eg->getId(); ?>" class="btn btn-default btn-sm">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> <?php echo $l->getString('view_submissions', 'View Submissions|Einreichungen ans.'); ?>
                   		</a>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>