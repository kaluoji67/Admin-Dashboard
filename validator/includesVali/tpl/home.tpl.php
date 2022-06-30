<h1><?php echo $l->getString('home_heading'); ?></h1>
<p>
    <?php echo $l->getString('home_intro_short'); ?>
</p>
<p>
    <?php echo $l->getString('home_intro_long'); ?>
</p>
<?php if (isset($user) && $user->getFlagAdmin() == 'Y'):
    echo $l->getString("login_message_admin_change");
    $semDescr = Semester::getByCondition('sem_id = ?',array($_SESSION["sem_id"]));
    ?>
    <a href="index.php?action=admin/editPreferredSemester">
        <?php echo $semDescr[0]->getDescr();?>
    </a>

<?php endif;
$questionnaires = null;
if (isset($user) and $user != null and isset($_SESSION["sem_id"]) and $_SESSION["sem_id"] != null AND $user->getFlagAdmin() != 'Y')
    $questionnaires = questionnaire::checkForquestionnaires($_SESSION["sem_id"],$user->getUserEvalID(),$l->getLanguage());
if ($questionnaires != NULL AND count($questionnaires) != 0):
    foreach ($questionnaires as $questionnaire):?>
    <div class="alert alert-warning">
        <?php if (count($questionnaire->isVoluntarily($_SESSION["sem_id"])) == 0)
            echo "<strong>".$l->getString("questionnaire_newquestionnaire_heading_vol")."</strong><br>".$l->getString("questionnaire_newquestionnaire_message_vol");
        else {
            $continue = false;
            echo "<strong>" . $l->getString("questionnaire_newquestionnaire_heading") . "</strong><br>" . $l->getString("questionnaire_newquestionnaire_message");
        }?>
        <a class="alert-link" href="index.php?action=eval/questionnaire&q=<?php echo $questionnaire->getPks()[0];?>"><span class="glyphicon glyphicon-arrow-right"></span> <?php echo $questionnaire->getTitle();?></a>
    </div>
<?php endforeach;endif; ?>


