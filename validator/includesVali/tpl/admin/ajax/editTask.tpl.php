<div id="taskDialog" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                	<!-- show title of the form and taskID,
                		 if a task exists and copy isn�t set => show Title: Edit Tasks
                		 if a task only exists (and copy is set) => show Title: Copy Task
                		 otherwise show Title: Create Task-->
                    <?php if((!empty($task))&&(!isset($copy))&&(!isset($copySem))): ?>
                        Edit Task <strong>#<?php echo $task->getId(); ?></strong>
                    <?php elseif((!empty($task))&&(isset($copySem))): ?>
                        Copy Task to Semester <strong>#<?php echo $task->getId(); ?></strong>
                    <?php elseif(!empty($task)): ?>
                        Copy Task <strong>#<?php echo $task->getId(); ?></strong>
                    <?php else: ?>
                        Create Task
                    <?php endif; ?>
                </h4>
            </div>
            <form id="taskForm" action="index.php?action=admin/viewTasks" class="form-horizontal" role="form" method="post">
                <input type="hidden" name="id" value="<?php echo (empty($task) ? 0 : $task->getId()); ?>" />
                <input type="hidden" name="copy" value="<?php echo (isset($copy) ? "true" : "false"); ?>" />
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
                        <?php $title_de = empty($task) ? "" : $task->getTitle('de'); ?>
                        <?php $title_en = empty($task) ? "" : $task->getTitle('en'); ?>
                        <?php $descr_de = empty($task) ? "" : $task->getDescription('de'); ?>
                        <?php $descr_en = empty($task) ? "" : $task->getDescription('en'); ?>
                        <?php $order = empty($task) ? "" : $task->getOrder(); ?>
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="title_de">Title (de)</label>
                            <div class="col-sm-9">
                                <input <?php echo ((isset($copySem)) ? 'onchange="markChange()"' : '' );?> autocomplete="off" name="title_de" type="text" class="form-control"
                                       placeholder="Enter title" value="<?php echo $title_de; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="title_en">Title (en)</label>
                            <div class="col-sm-9">
                                <input <?php echo ((isset($copySem)) ? 'onchange="markChange()"' : '' );?> autocomplete="off" name="title_en" type="text" class="form-control"
                                       placeholder="Enter title" value="<?php echo $title_en; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="description_de">Description (de)</label>
                            <div class="col-sm-9" <?php echo ((isset($copySem)) ? 'onkeyup="markChange()"' : '' );?>>
                                <textarea name="description_de" class="form-control summernote" placeholder="Enter description"><?php echo $descr_de; ?></textarea>
                                <!-- <input name="description_de" type="text" class="form-control"
                                       placeholder="Enter description" value="<?php echo $descr_de; ?>" required /> -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="description_en">Description (en)</label>
                            <div class="col-sm-9" <?php echo ((isset($copySem)) ? 'oninput="markChange()"' : '' );?>>
                                <textarea name="description_en" class="form-control summernote" placeholder="Enter description"><?php echo $descr_en; ?></textarea>
                                <!--<input name="description_en" type="text" class="form-control"
                                       placeholder="Enter description" value="<?php echo $descr_en; ?>" required />-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 text-left" for="task_group">Task Group</label>
                            <div class="col-sm-9">
                                <select id="task_group" name="task_group" class="form-control">
                                    <option value="">(none)</option>
                                    <?php 
                                    if(isset($copySem))
                                    {
                                        $taskGroups=TaskGroup::getAll();
                                    }
                                    else
                                    {
                                        $taskGroups=TaskGroup::getByCondition("tskg_sem_id=?",array($_SESSION["sem_id"]));
                                    }
                                    
                                    foreach($taskGroups as $tg): ?>
                                        <option value="<?php echo $tg->getId(); ?>"
                                            <?php if(!empty($task) && $task->getTskgId() == $tg->getId()) { echo "selected"; } ?>
                                            >
                                            <?php echo ((isset($copySem))?$tg->getSemId().":":"").$tg->getId() . ': ' . $tg->getName('de'); ?>
                                        </option>
                                    <?php endforeach; ?>
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
                    <button <?php echo ((isset($copySem))? 'style="display:none;"':'');?> id="submit" name="submit" type="submit" class="btn btn-primary">Save Tasks</button>
                    <button <?php echo ((!isset($copySem))? 'style="display:none;"':'');?> id="submitSemester" type="button" class="btn btn-primary" onclick="semesterSelected();" aria-label="Center Align"> Copy to Semester</button>
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
        
        ctl = document.getElementById("task_group");
        for(i=ctl.options.length-1; i>=0; i--)
        {
            if(ctl.options[i].text!="(none)")
            {
                currOpt=ctl.options[i].text.split(":");
                if(currOpt[0]!=semID)
                {
                    ctl.options.remove(i);
                }
                else
                {
                    ctl.options[i].text=currOpt[1]+":"+currOpt[2];
                }
            }
        }
    
    
        ctl = document.getElementById("submitSemester");
        ctl.style["display"]="none";
    
        ctl = document.getElementById("submit");
        ctl.style["display"]="inline";
    }

    function sendFile(file, summernote) {
        data = new FormData();
        data.append("file", file);
        $.ajax({
            data: data,
            type: "POST",
            url: "ajax.php?action=attachment&subaction=upload",
            dataType: "json",
            cache: false,
            contentType: false,
            processData: false,
            success: function(json) {
                console.log(json);
                var url = 'ajax.php?action=attachment&subaction=download&id=' + json.id;
                //console.log(summernote);
                summernote.summernote("insertImage", url);
            }
        });
    }
    $('.summernote').each(function() {
        $(this).summernote({
            onImageUpload: function(files) {
                sendFile(files[0], $(this));
            },
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['fullscreen', 'codeview']],
                ['sqlvali', ['hint']]
            ]
        });
    });
</script>
