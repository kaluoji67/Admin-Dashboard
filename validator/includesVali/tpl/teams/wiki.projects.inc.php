<!--show all projects from all groups i belong to-->
        <?php 
 
            //confirm the user belongs to a group
            $user_id = $user->getId();

            $query = "select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1";
            $check = $DB->run($query);

            $myprojects = false;
            if(is_array($check)){

                $group_id = $check[0]['group_id'];
                $project_group_id = my_project_group($group_id,$DB);

                //show only the current and past projects
                $sql = "select * from submitted_projects where group_id = '$group_id' ";
                $done = $DB->run($sql);

                if(is_array($done)){

                    $list = array_column($done, "project_id");
                    $list = "'" . implode("','", $list) . "'";

                    //query all submitted projects
                    $sql = "select * from user_projects where pgroup_id = '$project_group_id' && project_id in ($list) order by id desc";
                    $myprojects = $DB->run($sql);

                    //add current project only if all its tasks where submitted
                    $last_submitted_project = end($myprojects);
                    $last_submitted_project = $last_submitted_project['project_id'];

                    $sql = "select task_id from user_tasks where project_id = '$last_submitted_project' order by id desc limit 1";
                    $last_task = $DB->run($sql);
                    if(is_array($last_task)){

                        $last_task = $last_task[0];
                        $sql = "select * from submitted_projects where task_id = '$last_task[task_id]' limit 1 ";
                        $check = $DB->run($sql);
                        if(is_array($check)){

                            $sql = "select * from user_projects where pgroup_id = '$project_group_id' && project_id not in ($list) order by id asc limit 1";
                            $current = $DB->run($sql);
                            if(is_array($current)){

                                array_unshift($myprojects, $current[0]);
                                
                            }
                        }
                        
                    }

                    
                }else{

                    //no submitted projects. load only first project
                    $sql = "select * from user_projects where pgroup_id = '$project_group_id' order by id asc limit 1";
                    $myprojects = $DB->run($sql);
                }

            }
         
        ?>

        <?php if(is_array($myprojects)): $num = 0;?>
            <?php foreach($myprojects as $project): $num++;?>
                
                    <?php 

                        //get tasks for this project
                        $sql = "select * from user_tasks where project_id = '$project[project_id]' order by id asc";
                        $tasks = $DB->run($sql);
                       $num2 = is_array($tasks) ? count($tasks): 0;
                       
                    ?>

                <style type="text/css">
                    .panel:hover{
                        border:solid thin #aaa;
                    }
                </style>
            <div class="panel panel-default" style="margin-bottom: 5px;box-shadow: 0px 0px 10px #aaa;">
                <div class="panel-heading" style="">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$num?>" aria-expanded="true">
                        <tables class="table" style="font-size: 14px;margin-bottom: 0px;">
                             <tr><th><?=$project['project_name']?></th> . <span style="float: right;"><?=$num2?> Tasks </span><br>
                            <td style="text-align: left;"><?=$project['task']?></td></tr>
                         </tables>
                         </a>
                    </h4>
                </div>
                <div id="collapse<?=$num?>" class="panel-collapse collapse <?=$num==1?'in':''?>" aria-expanded="true" style="<?=$num>1?'height: 0px;':''?> ">
                
                    &nbsp Project Tasks: 
                    <?php if(!isset($_GET['show_all_tasks'])):?>
                        <a href="index.php?action=teams/groupWiki&show_all_tasks=true" style="float: right;margin-right: 10px;">Show all tasks</a> 
                    <?php else:?>
                        <a href="index.php?action=teams/groupWiki" style="float: right;margin-right: 10px;">Show current task only</a> 
                    <?php endif;?>
                    <br>
                    <div class="js-single-task panel-body">
                        <?php if(isset($tasks) && is_array($tasks)): $num2++;$loop_count = 0; ?>

                            <?php foreach($tasks as $task): $num2--;$loop_count++ ?>
 
                               <?php
                                    $color = "orange";
                                    $break_loop = true;
                                    //if this task is not in the submission, break the loop
                                    $sql = "select task_id from user_submissions where task_id = '$task[task_id]' limit 1";
                                    $check = $DB->run($sql);
                                    if(is_array($check)){
                                        $break_loop = false;
                                        $color = "green";
                                        if(!isset($_GET['show_all_tasks'])){
                                            continue;
                                        }
                                    }
                               ?>

                                <div style="position: relative;" class="list-group-item">
                                    <div style="position: absolute;left: -10px;top: -10px;background-color: <?=$color?>;color: white;height:20px;width: 20px;text-align: center;"><?=$loop_count?></div>
                                    <b>Task name:</b> <?=$task['task_name']?><br><b>Description:</b> <?=$task['task']?>
                                </div>
                                    
                                    <?php if($break_loop):?>
                                    <!--task submission-->
                                      <form method="post" enctype="multipart/form-data">
                                           
                                            <textarea placeholder="Submit to this task here" class="js-post form-control" autofocus></textarea>
                                            <input type="hidden" value="<?=$currentproject['project_id']?>" name="project_id">
                                            <label class="btn btn-primary pull-right" style="margin: 2px;cursor: pointer;">
                                              Add Image
                                              <input class="js-file" type="file" onchange="submission_loadFile(event)" style="display: none;" >
                                            </label>
                                            <input type="button" onclick="submission_add_post(event,'<?=$project['project_id']?>','<?=$task['task_id']?>')" class="btn pull-right" value="Submit to task <?=$loop_count?>" style="margin: 2px;background-color: orange;color: white;" >
                                           <br style="clear: both;">
                                            <p style="position: relative;">
                                              <img id="output" width="344px" />
                                              <svg class="js-image-delete hide" fill="orange" style="position:relative;right:2px;top:2px;margin:4px;cursor:pointer" onclick="submission_remove_image(event)" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>

                                            </p>

                                              <script>
                                                  var submission_loadFile = function(event){
                                                      var image = document.getElementById('output');
                                                      document.querySelector('.js-image-delete').classList.remove("hide");
                                                      image.src = URL.createObjectURL(event.target.files[0]);
                                                  };
                                              </script>
                                       </form>
                                    <?php endif; ?>

                                   <?php
                                      
                                        if($break_loop){
                                            break;
                                        }
                                   ?>
                            <?php endforeach; ?>
                        
                        <?php endif; ?>
                    </div>

                </div>

            </div>
            
            <?php endforeach;?>
        <?php else: ?>
            <h4 style="text-align: center;">You have no projects to work on yet</h4>
        <?php endif;?>