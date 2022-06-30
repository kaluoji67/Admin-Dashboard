<div id="taskGroupDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    <?php if((!empty($taskGroup))&&(!isset($copySem))): ?>
                        Edit Task Group <strong>#<?php echo $taskGroup->getId(); ?></strong>
                    <?php elseif(!empty($taskGroup)): ?>
                        Copy Task Group to Semester <strong>#<?php echo $taskGroup->getId(); ?></strong>
                    <?php else: ?>
                        Create Task Group
                    <?php endif; ?>
                </h4>
            </div>
            <form id="taskGroupForm" action="index.php?action=admin/viewTaskGroups" class="form-horizontal" role="form" method="post">
                <input type="hidden" name="id" value="<?php echo (empty($taskGroup) ? 0 : $taskGroup->getId()); ?>" />
                <input type="hidden" name="copySem" value="<?php echo (isset($copySem) ? "true" : "false"); ?>" />
                <div class="modal-body">
                
                    <?php if(isset($copySem)):?>
                        <span id="spanSemester">
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
                    <?php endif;?>
                    
                    <span id="spanTask" <?php echo ((isset($copySem)) ? 'style="display:none;"' : '' );?>>
                        <?php echo ((isset($copySem)) ? '<input style="display:none;" name="changeMarker" id="changeMarker" value="0"/>' : '' );?>
                        <?php $de = empty($taskGroup) ? "" : $taskGroup->getName('de'); ?>
                        <?php $en = empty($taskGroup) ? "" : $taskGroup->getName('en'); ?>
                        <?php $order = empty($taskGroup) ? "" : $taskGroup->getOrder(); ?>
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="name">Name (de)</label>
                            <div class="col-sm-9">
                                <input <?php echo ((isset($copySem)) ? 'onchange="markChange()"' : '' );?> autocomplete="off" name="name_de" type="text" class="form-control"
                                       placeholder="Enter name" value="<?php echo $de; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="name">Name (en)</label>
                            <div class="col-sm-9">
                                <input <?php echo ((isset($copySem)) ? 'onchange="markChange()"' : '' );?> autocomplete="off" name="name_en" type="text" class="form-control"
                                       placeholder="Enter name" value="<?php echo $en; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="group">Visible?</label>
                            <div class="col-sm-9">
                                <select name="visible" class="form-control">
                                    <option value="N" <?php if(!empty($taskGroup) && $taskGroup->getVisible() == 'N') echo "selected"; ?>>N</option>
                                    <option value="Y" <?php if(!empty($taskGroup) && @$taskGroup->getVisible() == 'Y') echo "selected"; ?>>Y</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="task_group">Position</label>
                            <div class="col-sm-9">
                                <input autocomplete="off" name="order" type="number" class="form-control" placeholder="Enter position" value="<?php echo $order; ?>" />
                            </div>
                        </div>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button <?php echo ((isset($copySem))? 'style="display:none;"':'');?> id="submit" name="submit" type="submit" class="btn btn-primary">Save Task Group</button>
                    <button <?php echo ((!isset($copySem))? 'style="display:none;"':'');?> id="submitSemester" type="button" class="btn btn-primary" onclick="semesterSelected();"> Copy to Semester</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function markChange()
    {
        ctl=document.getElementById("changeMarker");
        ctl.value="1";
    }
    
    function semesterSelected()
    {
        ctl = document.getElementById("spanSemester");
        ctl.style["display"]="none";
    
        ctl = document.getElementById("spanTask");
        ctl.style["display"]="inline";
        
        ctl = document.getElementById("semester");
        semID=ctl.options[ctl.selectedIndex].value;
        
        ctl = document.getElementById("submitSemester");
        ctl.style["display"]="none";
    
        ctl = document.getElementById("submit");
        ctl.style["display"]="inline";
    }
</script>