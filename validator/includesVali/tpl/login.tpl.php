<h1>Login</h1>
<?php if($loginSuccessful): ?>
    <div class="alert alert-success" role="alert">
        <strong><?php echo $l->getString('login_success'); ?></strong>
        <?php if ($user->getFlagAdmin() == "Y"): ?>
            <?php
            echo $l->getString("login_message_admin");
            $semDescr = Semester::getByCondition('sem_id = ?',array($_SESSION["sem_id"]));
            ?>
            <a href="index.php?action=admin/editPreferredSemester">
                <?php echo $semDescr[0]->getDescr();?>
            </a>
        <?php echo "<br>".$l->getString("login_message_admin_change");
        else:
            echo $l->getString('login_success_message');
        endif;?>
    </div>
    <?php
    $questionnaires = null;
    if (isset($user) and $user != null and isset($_SESSION["sem_id"]) and $_SESSION["sem_id"] != null AND $user->getFlagAdmin() != 'Y')
        $questionnaires = questionnaire::checkForquestionnaires($_SESSION["sem_id"],$user->getUserEvalID(),$l->getLanguage());
    if ($questionnaires != null):
        foreach ($questionnaires as $questionnaire):?>
        <div class="alert alert-warning">
            <?php if(count($questionnaire->isVoluntarily($_SESSION["sem_id"])) == 0)
                echo "<strong>".$l->getString("questionnaire_newquestionnaire_heading_vol")."</strong><br>".$l->getString("questionnaire_newquestionnaire_message_vol");
            else {
                echo "<strong>" . $l->getString("questionnaire_newquestionnaire_heading") . "</strong><br>" . $l->getString("questionnaire_newquestionnaire_message");
            }?>
            <a class="alert-link" href="index.php?action=eval/questionnaire&q=<?php echo $questionnaire->getPks()[0];?>"><span class="glyphicon glyphicon-arrow-right"></span> <?php echo $questionnaire->getTitle();?></a>
        </div>
    <?php endforeach;endif; ?>
<?php else: ?>
    <?php if($loginFailed): ?>
        <div class="alert alert-danger col-sm-8" role="alert">
            <strong><?php echo $l->getString('login_failed'); ?></strong>
            <?php echo $l->getString('login_failed_message'); ?>
        </div>
        <div class="clearfix"></div>
    <?php endif; ?>

    <form action="index.php?action=login" method="post" role="form" class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-2"  for="username"><?php echo $l->getString('login_username'); ?></label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="username" placeholder="<?php echo $l->getString('login_enter_username'); ?>" required />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2"  for="password"><?php echo $l->getString('login_password'); ?></label>
            <div class="col-sm-6">
                <input type="password" class="form-control" name="password" placeholder="<?php echo $l->getString('login_enter_password'); ?>" required />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-2">
                <button name="submit" type="submit" class="btn btn-default"><?php echo $l->getString('login_submit'); ?></button>
            </div>
        </div>
    </form>
<?php endif; ?>


