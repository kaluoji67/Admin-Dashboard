<div id="taskDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    <!-- show title of the form -->
                    Copy TaskGroups
                </h4>
            </div>
            <form id="taskForm" action="index.php?action=admin/viewTaskGroups" class="form-horizontal" role="form" method="post">
                <div class="modal-body">
                    <input style="display:none;" name ="selectedTaskGroups" id="selectedTaskGroups" value=""/>
                    <input style="display:none;" name ="copySem" id="copySem" value="true"/>
                    <input style="display:none;" name ="changeMarker" id="changeMarker" value="0"/>
                    <input style="display:none;" name ="id" id="id" value="copyTaskGroups"/>
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
                    <span id="spanTaskGroups">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Task Group ID</th>
                                <th>Position</th>
                                <th>Name (de)</th>
                                <th>Name (en)</th>
                                <th>Task Count</th>
                                <th>Visible</th>
                                <th>Copy</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach(TaskGroup::getByCondition("tskg_sem_id = ?", array($_SESSION["sem_id"]), array('tskg_order')) as $tg): ?>
                                <tr>
                                    <td><?php echo $tg->getId(); ?></td>
                                    <td><?php echo $tg->getOrder(); ?></td>
                                    <td><?php echo $tg->getName('de'); ?></td>
                                    <td><?php echo $tg->getName('en'); ?></td>
                                    <td>
                                        <?php
                                            $tasks = Task::getByCondition('tsk_tskg_id = ?', array($tg->getId()));
                                            echo count($tasks);
                                        ?>
                                    </td>
                                    <td><?php echo $tg->getVisible(); ?></td>
                                    <td><input type="CheckBox" name="cbCopyTrue" id="<?php echo $tg->getId();?>"/></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button style="display:none;" id="submit" name="submitCopyTaskGroups" type="submit" class="btn btn-primary">Save TaskGroups</button>
                    <button id="submitSelection" type="button" class="btn btn-primary" onclick="taskgroupsSelected();" aria-label="Center Align"> Confirm Selection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function taskgroupsSelected()
    {
        ctl = document.getElementById("spanSemester");
        ctl.style["display"]="inline";
    
        ctl = document.getElementById("spanTaskGroups");
        ctl.style["display"]="none";
    
    
        ctl = document.getElementById("submitSelection");
        ctl.style["display"]="none";
    
        ctl = document.getElementById("submit");
        ctl.style["display"]="inline";

        ctlCBs = document.getElementsByName("cbCopyTrue");
        ctl = document.getElementById("selectedTaskGroups");
        for(i=0; i<ctlCBs.length; i++)
        {
            if(ctlCBs[i].checked)
            {
                ctl.value+=ctlCBs[i].id+";";
            }
        }
    }
</script>
