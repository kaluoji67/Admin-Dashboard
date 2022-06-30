<div id="taskDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    <!-- show title of the form -->
                    Copy Tasks
                </h4>
            </div>
            <form id="taskForm" action="index.php?action=admin/viewTasks" class="form-horizontal" role="form" method="post">
                <div class="modal-body">
                    <input style="display:none;" name ="selectedTasks" id="selectedTasks" value=""/>
                    <input style="display:none;" name ="copySem" id="copySem" value="true"/>
                    <input style="display:none;" name ="changeMarker" id="changeMarker" value="0"/>
                    <input style="display:none;" name ="id" id="id" value="copyTasks"/>
                    <span id="spanSemester" style="display:none;">
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="semester">Semester</label>
                            <div class="col-sm-9">
                                <select id="semester" name="semester" class="form-control">
                                    <?php foreach(Semester::getAll() as $sem): ?>
                                        <option value="<?php echo $sem->getId(); ?>"
                                            <?php if(!empty($task) && $task->getSemId() == $sem->getId()) { echo "selected"; } ?>
                                            >
                                            <?php echo $sem->getId() . ': ' . $sem->getDescr(); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </span>
                    <span id="spanTasks">
                        <table class="table  table-hover">
                            <thead>
                                <tr>
                                    <th>Task-ID</th>
                                    <th>Task Group (de)</th>
                                    <th>Position</th>
                                    <th>Title (de)</th>
                                    <th>Title (en)</th>
                                    <th>Copy</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach(Task::getByCondition("tsk_sem_id = ?", array($_SESSION["sem_id"]), array('tsk_tskg_id', 'tsk_order')) as $t): ?>
                                <?php $tg = $t->getTskgId() ? TaskGroup::getById($t->getTskgId()) : null; ?>
                                <tr>
                                    <td><?php echo $t->getId(); ?></td>
                                    <td><?php echo empty($tg) ? "(none)" : @$tg->getName('de'); ?></td>
                                    <td><?php echo $t->getOrder(); ?></td>
                                    <td><?php echo $t->getTitle('de'); ?></td>
                                    <td><?php echo $t->getTitle('en');?></td>
                                    <td><input type="CheckBox" name="cbCopyTrue" id="<?php echo $t->getId();?>"/></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button style="display:none;" id="submit" name="submitCopyTasks" type="submit" class="btn btn-primary">Save Tasks</button>
                    <button id="submitSelection" type="button" class="btn btn-primary" onclick="tasksSelected();" aria-label="Center Align"> Confirm Selection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function tasksSelected()
    {
        ctl = document.getElementById("spanSemester");
        ctl.style["display"]="inline";
    
        ctl = document.getElementById("spanTasks");
        ctl.style["display"]="none";
    
    
        ctl = document.getElementById("submitSelection");
        ctl.style["display"]="none";
    
        ctl = document.getElementById("submit");
        ctl.style["display"]="inline";

        ctlCBs = document.getElementsByName("cbCopyTrue");
        ctl = document.getElementById("selectedTasks");
        for(i=0; i<ctlCBs.length; i++)
        {
            if(ctlCBs[i].checked)
            {
                ctl.value+=ctlCBs[i].id+";";
            }
        }
    }
</script>
