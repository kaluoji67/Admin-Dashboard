<h1>Logout</h1>
<?php if($logoutSuccessful): ?>
    <div class="alert alert-success col-sm-8" role="alert">
        <strong><?php echo $l->getString('logout_success'); ?></strong>
        <?php echo $l->getString('logout_success_message'); ?>
    </div>
<?php else: ?>
    <div class="alert alert-danger col-sm-8" role="alert">
        <strong>Logout failed!</strong>
        Seems like you were not logged in?
    </div>
<?php endif; ?>