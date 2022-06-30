<?php require 'ajax/editUser.tpl.php'; ?>
<?php require 'ajax/createUser.tpl.php'; ?>
<?php require 'ajax/deleteRequest.tpl.php'; ?>

<h1><?php echo $l->getString('edit_users'); ?></h1>

<?php if(@$success): ?>
    <div class="alert alert-success">
        <strong><?php echo $l->getString("account_success") ?></strong>
        <?php echo $l->getString("account_success_message") ?>
    </div>
<?php elseif(!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong><?php echo $l->getString("account_error") ?> </strong>
        <?php echo $l->getString("account_error_message") ?>
        <ul>
            <?php foreach($errors as $e): ?>
                <li><?php echo $e; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<table class="table  table-hover">
    <thead>
        <tr>
            <th>User-ID</th>
            <th><?php echo $l->getString('login_username');?></th>
            <th><?php echo $l->getString('register_fullname');?></th>
            <th><?php echo $l->getString('register_mail'); ?></th>
            <th><?php echo $l->getString('register_group'); ?></th>
            <th>Admin?</th>
            <th>
                <button type="button" onclick="createUser_show()" class="btn btn-default btn-sm" style="float: right;" aria-label="Left Align"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span><?php echo $l->getString("create_user"); ?></button>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach(User::getByCondition("usr_sem_id = ?", array($_SESSION["sem_id"])) as $u): ?>
            <?php $g = ExerciseGroup::getById($u->getEgrpId()); ?>
            <tr>
                <td><?php echo $u->getId(); ?></td>
                <td><?php echo $u->getName(); ?></td>
                <td><?php echo $u->getFullname(); ?></td>
                <td><?php echo $u->getEmail(); ?></td>
                <td>
                    <?php
                    if(is_object($g)) {
                        echo @$g->getName($lang);
                    }
                    ?>
                </td>
                <td><?php echo $u->getFlagAdmin(); ?></td>
                <td>
                    <div class="btn-toolbar" role="toolbar" style="float: right;">
                        <div class="btn-group" style="white-space: nowrap;">
                        	<!-- 16.05.17: the delete-button open via function in deleteRequest a deletion-request -->
                          	<button type="button" class="btn btn-default btn-sm" onclick="deleteUser_open('<?php echo $u->getName(); ?>');" aria-label="Center Align">
                          		<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <?php echo $l->getString("account_delete");?>
                          	</button>
                          	<button type="button" class="btn btn-default btn-sm" onclick="editUser_open('<?php echo $u->getName(); ?>');" aria-label="Center Align">
                          		<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <?php echo $l->getString("edit");?>
                          	</button>
                          	<a href="index.php?action=admin/viewSubmissionsUser&userId=<?php echo $u->getId(); ?>" class="btn btn-default btn-sm">
                            	<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> <?php echo $l->getString('view_submissions', 'View Submissions|Einreichungen ans.'); ?>
                   			</a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
