<div class="row">
    <div class="col">
        <a href="index.php?action=viewTasks&taskId=<?php echo $task->getId();?>&tskGId=<?php echo $task->getTskgId();?>">
            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span><?php echo $l->getString("pageNav_backToTaskView");?></button>
        </a>
    </div>
    <div class="col"></div>
    <div class="col text-right pr-1">
        <a href="index.php?action=viewTask&task=<?php echo isset($predTask) ? $predTask->getId() :$task->getId();?>" >
            <button type="button" class="btn btn-default" <?php if(!isset($predTask) || $predTask == null) echo "disabled" ?>>
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> <?php echo $l->getString("pageNav_predecessorTask");?>
            </button>
        </a>
    </div>
    <div class="col text-left pl-0">
        <a href="index.php?action=viewTask&task=<?php echo isset($succTask) ? $succTask->getId() :$task->getId();?>">
            <button type="button" class="btn btn-default" <?php if(!isset($succTask) || $succTask == null) echo "disabled" ?>>
                <?php echo $l->getString("pageNav_successorTask");?> <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>
            </button>
        </a>
    </div>
</div>

<h1><?php echo $l->getString("task_task"); ?> <strong><?php echo $task->getTitle($lang); ?></strong></h1>

<h2><?php echo $l->getString("task_description_label"); ?></h2>
<p>
    <?php echo parseDescription($task->getDescription($lang)); ?>
</p>
<br />

<h2><?php echo $l->getString("task_taskresult"); ?></h2>
<?php if(@$error_syntax): ?>
    <div class="alert alert-danger">
        <strong><?php echo $l->getString('task_result_error_headline'); ?></strong>
        <?php echo $l->getString('task_result_error'); ?> <br />
        <?php echo $error_syntax; ?>
    </div>
<?php elseif(!empty($actual)): ?>
    <?php if($result_actual_acquired): ?>
        <div class="alert alert-success">
            <strong><?php echo $l->getString('task_result_success_headline'); ?></strong>
            <?php echo $l->getString('task_result_success'); ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <strong><?php echo $errortext ?></strong>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div>
    <form role="form" method="post" action="index.php?action=viewTask&task=<?php echo $task->getId(); ?>">
        <textarea name="sql"  class="form-control sql-input" ><?php if(@$sql) { echo $sql; } ?></textarea>
        <button name="submit" type="submit" class="btn btn-primary" style="float: right;">
            <?php echo $l->getString('pageNav_sendSolution'); ?>
        </button>
    </form>
</div>

<?php if($task_executed): ?>
    <div class="clearfix"></div>
    <br />
    <br />

    <?php if(!empty($actual)): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a>
                        <?php echo $l->getString('solution_desired'); ?>
                    </a>
                </h4>
            </div>
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr style="background: #f4f4f4;">
                        <?php for($i = 0; $i < count($usedTitles); $i++): ?>
                            <th><center><?php echo $usedTitles[$i]; ?></center></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tr>
                    <?php for($i = 0; $i < count($usedTitles); $i++): ?>
                        <td>
                            <?php echo $desired[$i]; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            </table>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a>
                        <?php echo $l->getString('solution_actual'); ?>
                    </a>
                </h4>
            </div>
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr style="background: #f4f4f4;">
                        <?php for($i = 0; $i < count($usedTitles); $i++): ?>
                            <th>
                                <center>
                                    <?php if($result_correct[$i]): ?>
                                        <span class="glyphicon glyphicon-ok"></span>
                                    <?php else: ?>
                                        <span class="glyphicon glyphicon-remove"></span>
                                    <?php endif; ?>
                                    <?php echo $titles[$i]; ?>

                                </center>
                            </th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tr>
                    <?php for($i = 0; $i < count($usedTitles); $i++): ?>
                        <td>
                            <?php echo $actual[$i]; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>
