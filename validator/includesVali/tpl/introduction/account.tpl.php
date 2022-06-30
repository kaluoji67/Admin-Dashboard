<?php require 'editAccount.tpl.php'; ?>
<?php  require 'deleteAccount.tpl.php'; ?>

<!-- created on 15.05.2017 -->

<h1><?php echo $l->getString('account'); ?></h1>

<?php if(@$success): ?>
    <div class="alert alert-success">
        <strong><?php echo $l->getString('account_success'); ?></strong>
        <?php echo $l->getString('account_success_message'); ?>
    </div>
<?php elseif(!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong><?php echo $l->getString('account_error'); ?></strong>
        <?php echo $l->getString('account_error_message'); ?>
        <ul>
            <?php foreach($errors as $e): ?>
                <li><?php echo $e; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<table id="account_information_table" class="table  table-hover">
    <thead>
        <tr>
        	<!-- the account datas are username, the full name, email and the exercise group -->
            <th><?php echo $l->getString('login_username');//Name is already defined in login and the same ?></th>
            <th><?php echo $l->getString('register_fullname');//Fullname, Mail and Group are translated in register ?></th>
            <th><?php echo $l->getString('register_mail'); ?></th>
            <th><?php echo $l->getString('register_group'); ?></th>
            </tr>
    </thead>
    <tbody>
            <!-- show current user informations -->
            <?php $g = ExerciseGroup::getById($user->getEgrpId()); ?>
            <tr>
                <td><?php echo $user->getName(); ?></td>
                <td><?php echo $user->getFullname(); ?></td>
                <td><?php echo $user->getEmail(); ?></td>
                <td>
                    <?php if(is_object($g)): ?>
                        <?php echo @$g->getName($lang); ?>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="btn-toolbar" role="toolbar" style="float: right;">
                        <div class="btn-group" style="white-space: nowrap;">
                        	<!-- the Delete-Button open a form to confirm the deletion or not -->
                            <button type="button" class="btn btn-default btn-sm" onclick="deleteAccount_open('<?php echo $user->getName(); ?>');" aria-label="Center Align"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <?php echo $l->getString('account_delete','Delete | Löschen'); ?></button>
                            <!-- the Edit-Button open a form where the account datas can be change -->
                            <button type="button" class="btn btn-default btn-sm" onclick="editAccount_open('<?php echo $user->getName(); ?>');" aria-label="Center Align"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <?php echo $l->getString('account_edit','Edit | Bearbeiten'); ?></button>
                        </div>
                    </div>
                </td>
            </tr>
    </tbody>
</table>


<!-- created on 22.05.2017 -->

<h1><?php echo $l->getString('acount_statistics_heading'); ?></h1>

<table id="account_statistics_table" class="table  table-hover">
    <thead>
        <tr>
            <th><?php echo $l->getString('account_tasks'); ?></th>
            <th><?php echo $l->getString('account_submissions'); ?></th>
            <th><?php echo $l->getString('account_submissions_correct'); ?></th>
            <th><?php echo $l->getString('account_submissions_wrong'); ?></th>
            <th><?php echo $l->getString('account_submissions_percent'); ?></th>
       </tr>
    </thead>
    <tbody>
    <!-- 08.06.17
    get for the user the number of taks, submissions, correct submissions and wrong submissions -->
            <tr>
                <td> 
                    <?php 
                        $task_count=0;
                        //only the tasks of the semester in which the user is registrated should be count
                        foreach(TaskGroup::getByCondition('tskg_sem_id=?',array($user->getSemId()),array('tskg_order')) as $tg)
                        {
                            if($tg->getVisible()=='Y'){
                                $tasks = Task::getByCondition('tsk_tskg_id = ?', array($tg->getId()));
                                $task_count=$task_count+count($tasks);
                            }
                        }
                        echo $task_count;
                    ?>
                </td>
                <td>
                    <?php
                        //count every different task id as submission, count the last submission with the success "Y" as correct submission
                        $submission_count=0;
                        $last_tsk_id=0;
                        $correct_count=0;
                        $last_task_success="N";
                        $alluserquery=UserQuery::getByCondition("usq_usr_id=?",array($user->getId()),array('usq_tsk_id', 'usq_id'));
                        foreach($alluserquery as $userquery)
                        {
								if($userquery->getTskId()==$last_tsk_id){
									if($last_task_success=="N" && $userquery->getSuccess()=="Y"){
										$correct_count=$correct_count+1;
									}
									if($last_task_success=="Y" && $userquery->getSuccess()=="N"){
										$correct_count=$correct_count-1;
									}
								}
								else{
									
									if($userquery->getSuccess()=="Y"){
	                                    $correct_count=$correct_count+1;
									}
									$submission_count=$submission_count+1;
								}
                                $last_tsk_id=$userquery->getTskId();
                                $last_task_success=$userquery->getSuccess();
                        }
                        echo $submission_count;
                    ?>
                </td>
                <td>
                    <?php echo $correct_count;?>
                </td>
                <td>
                    <?php echo $submission_count-$correct_count; ?>
                </td> 
                <td>
                    <?php echo (($submission_count!=0)? round(($correct_count/$task_count)*100,2) : 0);?>
                </td>
            </tr>
    
    </tbody>
</table>

<!-- 09.06.17 -->
<!-- show the submissions from the user who is logged in
	 the submissions are classified by the task groups -->
<h1><?php echo $l->getString('account_submissions_heading'); ?></h1>

<div class="panel-group" id="accordion">
    <?php 
    $alltaskgroups=TaskGroup::getByCondition("tskg_sem_id = ? and tskg_visible='Y'", array($user->getSemId()), array('tskg_order'));
    foreach($alltaskgroups as $taskGroup): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $taskGroup->getId(); ?>">
                        <?php echo $taskGroup->getName($lang); ?>
                    </a>
                </h4>
            </div>
            <div id="collapse<?php echo $taskGroup->getId(); ?>" class="panel-collapse collapse ">
                <div class="panel-body">
                    <ul class="list-group">
                        <?php foreach($alluserquery as $userquery): ?>
                            <?php 
                                $task_id=$userquery->getTskId();
                                $alltasks = Task::getByCondition("tsk_id=? and tsk_tskg_id=?",array($task_id, $taskGroup->getId()));
                                foreach($alltasks as $task):
                            ?>
                                    <li class="list-group-item">
                                        <a href="index.php?action=viewTask&task=<?php echo $task_id; ?>">
                                            <h4>Task <strong>#<?php echo $task_id; ?></strong> (<?php  echo $task->getTitle($lang); ?>)
                                        </a>
                                                <span class="glyphicon glyphicon-<?php echo (($userquery->getSuccess() == 'Y')? "ok":"remove");?>"></span>
                                            </h4>
                                        <pre><?php echo $userquery->getSql(); ?></pre>
                                    </li>
                            
                            <?php endforeach;?>
                        <?php  endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<!-- show the submissions from the user who is logged in
	 the submissions are classified by the task groups -->
<h1><?php echo $l->getString('account_selfchecks_heading'); ?></h1>
<table class="table">
    <thead>
        <tr>
            <th><?php echo $l->getString('account_selfchecks_theading_SCTitle'); ?></th>
            <th><?php echo $l->getString('account_selfchecks_theading_SCparticipated'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php $scs = Questionnaire::getavailableSelfchecks($user->getSemId());
    foreach($scs as $sc):
        $userPart = $sc->checkUserParticipation($user->getUserEvalID());
    if ($userPart || $sc->getActive()==1):?>
    <tr>
        <td>
            <a href="index.php?action=eval/selfcheck&q=<?php echo $sc->getPks()[0]; ?>"><?php echo $sc->getTitle(); ?></a>
        </td>
        <td><?php if($sc->checkUserParticipation($user->getUserEvalID()))
            echo $l->getString('account_selfchecks_Participated');
        else echo $l->getString('account_selfchecks_NotParticipated'); ?></td>
    </tr>
    <?php endif;endforeach;?>
    </tbody>
</table>

