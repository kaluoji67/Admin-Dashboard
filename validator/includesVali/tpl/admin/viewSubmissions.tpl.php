<h1><?php echo $l->getString('view_submissions'); ?></h1>

<form action="index.php?action=admin/viewSubmissions" class="form-horizontal" role="form" method="post">
    <div class="form-group">
        <label class="control-label col-sm-2 text-left" for="group">Group</label>
        <div class="col-sm-5">
        	<?php if(isset($_GET["egId"])){  //when exercisegroup is set:
        			$submissions =array();
                	$group = ExerciseGroup::getById($_GET["egId"]); // group is the Id of the exercise group
                	foreach(Task::getAll() as $task) { // all tasks are checked, if the user submitted a solution
                		$submissions[$task->getId()] = array();
                		foreach(User::getAll() as $user_) { // for all users
                			if(($_GET["egId"])==$user_->getEgrpId()){ // when the group id is the same as the users group
                			$userQuery = UserQuery::getByCondition('usq_usr_id = ? and usq_tsk_id = ?', array($user_->getId(), $task->getId()));
                			if(!empty($userQuery)) {
                				$userQuery = $userQuery[count($userQuery) - 1];
                				$submissions[$task->getId()][$user_->getId()] = $userQuery;
                			}
                			}
                		}
                	
                	}
                // all submissions of users from a special exercisegroup	
                } ?>
            <select name="group" class="form-control">
                <option value="">(all)</option>
                <?php foreach(ExerciseGroup::getByCondition("egrp_sem_id = ?", array($_SESSION["sem_id"])) as $g): ?>
                    <option value="<?php echo $g->getId(); ?>"
                        <?php if(!empty($group) && $group->getId() == $g->getId()): ?>
                            selected="selected"
                        <?php endif; ?>><?php echo $g->getName($lang); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2 text-left" for="group">Task Group</label>
        <div class="col-sm-5">
        	<?php if(isset($_GET["tgId"])){ //Ulli
        			$submissions =array();
                	$taskGroup = TaskGroup::getById($_GET["tgId"]); // get the id for a special task
                	foreach(Task::getAll() as $task) { // for all tasks
                		$submissions[$task->getId()] = array();
                		foreach(User::getAll() as $user_) { // fo all users
                			if(($_GET["tgId"])==$task->getTskgId()){ // when the task id is the same as the id of the special task
                				$userQuery = UserQuery::getByCondition('usq_usr_id = ? and usq_tsk_id = ?', array($user_->getId(), $task->getId()));
                				if(!empty($userQuery)) {
                					$userQuery = $userQuery[count($userQuery) - 1];
                					$submissions[$task->getId()][$user_->getId()] = $userQuery;
                				}
                			}
                		}
                	// all submissions for one special task	
                	}
                	
            } ?>
            <select name="taskGroup" class="form-control">
                <option value="">(all)</option>
                <?php foreach(TaskGroup::getByCondition("tskg_sem_id = ?", array($_SESSION["sem_id"])) as $g): ?>
                    <option value="<?php echo $g->getId(); ?>"
                            <?php if(!empty($taskGroup) && $taskGroup->getId() == $g->getId()): ?>
                                selected="selected"
                            <?php endif; ?>
                    ><?php echo $g->getName($lang); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-2">
        <button type="submit" class="btn btn-default btn-primary" name="submit">Show</button>
        </div>
    </div>
</form>

<?php if(($show_tasks)||(isset($_GET["egId"]))||(isset($_GET["tgId"]))): ?>
    <?php foreach($submissions as $task_id => $user_ids): ?>
        <?php $task =Task::getById($task_id); ?>
        <?php if(!empty($user_ids)): ?>
            <h2>Task <strong>#<?php echo $task->getId(); ?></strong> (<?php echo $task->getTitle($lang); ?>)</h2>
            <?php foreach($user_ids as $user_id => $userQuery): ?>
                <?php $user_ = User::getById($user_id); ?>
                <h4><?php echo $user_->getFullname(); ?> (<?php echo $user_->getName(); ?>):
                    <?php if($userQuery->getSuccess() == 'Y'): ?>
                        <span class="glyphicon glyphicon-ok"></span>
                    <?php else: ?>
                        <span class="glyphicon glyphicon-remove"></span>
                    <?php endif; ?>
                </h4>
                <pre><?php echo $userQuery->getSql(); ?></pre>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>