<h1><?php echo $l->getString('view_tasks'); ?></h1>

<?php
/**
 * Blocking the tasks for the users until they have completed the questionnaire
 */
$questionnaires = Null;
if ($user->getFlagAdmin() != 'Y')//Admins can see the tasks without completing the form
    $questionnaires = questionnaire::checkForquestionnaires($_SESSION["sem_id"],$user->getUserEvalID(),$l->getLanguage());
$continue = True;
$hiddenTasks = array();
if ($questionnaires != NULL AND count($questionnaires) != 0):
    foreach ($questionnaires as $questionnaire):?>
    <div class="alert alert-warning">
        <?php
        $hiddenTasks = $questionnaire->isVoluntarily($_SESSION["sem_id"]);
        if (count($hiddenTasks) == 0)
            echo "<strong>".$l->getString("questionnaire_newquestionnaire_heading_vol")."</strong><br>".$l->getString("questionnaire_newquestionnaire_message_vol");
        else {
            //$continue = false;
            echo "<strong>" . $l->getString("questionnaire_newquestionnaire_heading") . "</strong><br>" . $l->getString("questionnaire_newquestionnaire_message");
        }?>
        <a class="alert-link" href="index.php?action=eval/questionnaire&q=<?php echo $questionnaire->getPks()[0];?>"><span class="glyphicon glyphicon-arrow-right"></span> <?php echo $questionnaire->getTitle();?></a>
    </div>

<?php endforeach; endif;
if ($continue):?>

<div class="panel-group" id="accordion">
    <?php foreach($taskGroups as $taskGroup):
        if (!in_array($taskGroup->getId(),$hiddenTasks)):?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a id="tskGLink<?php echo $taskGroup->getId();?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $taskGroup->getId(); ?>">
                        <?php echo $taskGroup->getName($lang); ?>
                    </a>
                </h4>
            </div>
            <div id="collapse<?php echo $taskGroup->getId(); ?>" class="panel-collapse collapse ">
                <div class="panel-body">
                    <ul class="list-group">
                        <?php foreach(Task::getByCondition("tsk_tskg_id = ?", array($taskGroup->getId()), array('tsk_order')) as $task): ?>
                            <li class="list-group-item" id="taskLGI<?php echo $task->getId();?>">
                                <a href="index.php?action=viewTask&task=<?php echo $task->getId(); ?>">
                                    <strong><?php echo $task->getTitle($lang); ?></strong>
                                </a> </br>
                                <?php
                                echo parseDescriptionForTasks($task->getDescription($lang));?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif;endforeach; ?>
</div>




<?php endif; ?>
<script type="text/javascript">
    const urlParams = new URLSearchParams(window.location.search);
    var refTask = urlParams.get("taskId");
    var refTaskGroup  = urlParams.get("tskGId");
    if (refTask != null){
        if (refTaskGroup != null){
            var taskGroupAccordeonLink = document.getElementById("tskGLink"+refTaskGroup);
            taskGroupAccordeonLink.click();
        }
        var gi = document.getElementById("taskLGI"+refTask);
        gi.firstElementChild.focus();
    }
</script>
