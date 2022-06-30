<style type="text/css">
    
    @keyframes appear{
        0%{transform: translateX(-100px);opacity: 0}
        100%{transform: translateX(0px);opacity: 1}
    }

    #task_creator{
        position: absolute;
        min-height: 100px;
        width: 300px;
        background-color: #eee;
        border: solid thin #ddd;
        animation: appear 1s ease;
       box-shadow: 0px 0px 10px #aaa;
        padding: 10px;
    }

    #task_editor{
        position: absolute;
        min-height: 100px;
        width: 300px;
        background-color: #eee;
        border: solid thin #ddd;
        animation: appear 1s ease;
       box-shadow: 0px 0px 10px #aaa;
        padding: 10px;
    }

    

    #task_viewer{
        position: absolute;
        min-height: 100px;
        width: 300px;
        background-color: #eee;
        border: solid thin #ddd;
        animation: appear 1s ease;
       box-shadow: 0px 0px 10px #aaa;
        padding: 10px;
    }

 
    .single-member{

        display: inline-block;
        height: 30px;
        box-shadow: 0px 0px 10px #aaa;
        padding: 6px;
        background-color: white;
        cursor: move;
        margin: 4px;
        font-size: 12px;
        width: 47%;
    }

    .content-draggable{
        width: 99%;
        min-height: 89%;
        border: solid thin #aaa;
    }


 
</style>
<p><b>Tasks</b></p>
     <div onclick="show_add_task(event)" type="button" class="btn btn-default btn-sm pull-right" style="font-size:13px;width:120px;min-width:120px;margin-top: -30px;" ><span class="glyphicon glyphicon-plus" ></span> New Task</div>
    
    <!--new task UI-->
    <div id="task_creator" class="hide">
        <p><b>Add New Task</b></p>
        <form method="post">
            <input class="js-task-name form-control" type="text" name="task_name" placeholder="Task Name" autofocus><br>
            <textarea class="js-task-task form-control" name="task_task" placeholder="Task Description" ></textarea><br>
            <select class="js-task-project form-control " name="task_project" placeholder="Project">
                <option>-- Select Project --</option>
                <?php 

                    $sql = "select * from user_projects order by id desc";
                    $projects = $DB->run($sql);

                    if(is_array($projects)){
                        foreach ($projects as $project) {
                            # code...
                            echo "<option value='$project[project_id]'>$project[project_name]</option>";
                        }
                    }

                ?>
            </select><br>
             
            <input onclick="add_task(event)" type="button" value="Create" class="button2 pull-right green">
            <input onclick="show_add_task(event)" type="button" value="Cancel" class="button2 pull-left orange">

         </form>
    </div>
    <!--end new task UI-->

    <!--edit task UI-->
    <div id="task_editor" class="hide">
        <p><b>Edit Task</b></p>
        <form method="post">
            <input class="js-task-name-edit form-control" type="text" name="task_name" placeholder="Task Name" autofocus><br><br>
            <textarea class="js-task-task-edit form-control" name="task_task" placeholder="Task Description" ></textarea><br>
            <select class="js-task-project-edit form-control hide" name="task_group" placeholder="Group" >
                <option></option>
                <?php 

                    $sql = "select * from user_groups order by id desc";
                    $groups = $DB->run($sql);

                    if(is_array($groups)){
                        foreach ($groups as $group) {
                            # code...
                            echo "<option value='$group[group_id]'>$group[project_name]</option>";
                        }
                    }

                ?>
            </select><br>

            <input onclick="edit_task(event)" type="button" value="Create" class="button2 pull-right green">
            <input onclick="show_edit_task(event)" type="button" value="Cancel" class="button2 pull-left orange">

         </form>
    </div>
    <!--end edit task UI-->

    
<table class="table">

    <tr><th>Task ID</th><th>Task Name</th><th>Task Description</th><th>For Project</th><th>Date Created</th><th>Action</th></tr>
    <?php 

        $sql = "select * from user_tasks order by id desc";
        $tasks = $DB->run($sql);

    ?>
    
    <?php if(is_array($tasks)): ?>
        <?php foreach($tasks as $task): 
 
            $edit_arr = array();
            $edit_arr['task_name'] = $task['task_name'];
            $edit_arr['task'] = $task['task'];
            $edit_arr['project_id'] = $task['project_id'];

            $edit_values = json_encode($edit_arr);
            $edit_values = str_replace('"', "'", $edit_values);

            $project['project_name'] = "Unknown";
            $sql = "select * from user_projects where project_id = '$task[project_id]' limit 1";
            $myproject = $DB->run($sql);
            if(is_array($myproject)){
                $project['project_name'] = $myproject[0]['project_name'];
            }

        ?>
            
    <tr><td><?=$task['id']?></td><td><?=$task['task_name']?></td><td><?=$task['task']?></td><td><?=$project['project_name']?></td><td><?=date("jS M Y ",strtotime($task['date']))?></td>
        <td>
            <button onclick="delete_task('<?=$task['task_id']?>')" type="button" class="btn btn-default btn-sm" style="font-size:13px;" ><span class="glyphicon glyphicon-trash" ></span> Delete</button>
            <button onclick="show_edit_task('<?=$task['task_id']?>',event)" edit="<?=$edit_values?>" type="button" class="btn btn-default btn-sm" style="font-size:13px;" ><span class="glyphicon glyphicon-pencil" ></span> Edit</button>
 
        </td>
    </tr>
        <?php endforeach;?>
    <?php endif;?>
</table> 

<script type="text/javascript">
    
    var TASK_EDIT_PROJECT_ID = 0;
    
    function add_task(e)
    {
        var task_creator = document.querySelector("#task_creator");
        var task_name = task_creator.querySelector('.js-task-name').value;
        var task = task_creator.querySelector('.js-task-task').value;
        var project_id = task_creator.querySelector('.js-task-project').value;

        if(task_name.trim() == ""){
            alert("please enter a valid task name");
            
            task_creator.querySelector(".js-task-name").focus();
            return;
        }

        if(task.trim() == ""){
            alert("please enter a valid task description");
            
            task_creator.querySelector(".js-task-task").focus();
            return;
        }

        if(project_id.trim() == "" || isNaN(project_id)){
            
            alert("please select a valid project");
            
            task_creator.querySelector(".js-task-project").focus();
            return;
        }

        task_send_data({
            task_name: task_name.trim(),
            user_id:<?=$user->getId()?>,
            task:task.trim(),
            project_id:project_id.trim(),
            data_type:'add_task'
        });
    }

    function edit_task(e)
    {
        var task_editor = document.querySelector("#task_editor");
        var task_name = task_editor.querySelector('.js-task-name-edit').value;
        var task = task_editor.querySelector('.js-task-task-edit').value;
        var group_id = task_editor.querySelector('.js-task-project-edit').value;

        if(task_name.trim() == ""){
            alert("please enter a valid task name");
            
            task_editor.querySelector(".js-task-name-edit").focus();
            return;
        }

        if(task.trim() == ""){
            alert("please enter a valid task description");
            
            task_editor.querySelector(".js-task-task-edit").focus();
            return;
        }

        if(group_id.trim() == ""){
            //alert("please enter a valid group assigned");
            
            //task_editor.querySelector(".js-task-project-edit").focus();
            //return;
        }

        task_send_data({
            task_name: task_name.trim(),
            user_id:<?=$user->getId()?>,
            task:task.trim(),
            group_id:group_id.trim(),
            task_id:TASK_EDIT_PROJECT_ID,
            data_type:'edit_task'
        });
    }

     
    function delete_task(id)
    {
        if(!confirm("Are you sure you want to delete this task??"))
        {
            return;
        }

        task_send_data({
            task_id:id,
            user_id:<?=$user->getId()?>,
            data_type:'delete_task'
        });
    }


    function show_add_task(e)
    {
        var task_creator = document.querySelector("#task_creator");
        task_creator.querySelector(".js-task-name").value = "";

        if(task_creator.classList.contains("hide")){
            task_creator.classList.remove("hide");
            task_creator.querySelector(".js-task-name").focus();
        }else{

            task_creator.classList.add("hide");
        }
        
    }

    function show_edit_task(id,e)
    {
        var task_editor = document.querySelector("#task_editor");

        if(e){

            var data = e.currentTarget.getAttribute("edit").replaceAll("'",'"');
            var obj = JSON.parse(data);

            if(obj){
                task_editor.querySelector(".js-task-name-edit").value = obj.task_name;
                task_editor.querySelector(".js-task-task-edit").value = obj.task;
                task_editor.querySelector(".js-task-project-edit").value = obj.group_id;   
            }
              
        }        

        if(task_editor.classList.contains("hide")){
            task_editor.classList.remove("hide");
            task_editor.querySelector(".js-task-name-edit").focus();
        }else{

            task_editor.classList.add("hide");
        }
        
        TASK_EDIT_PROJECT_ID = id;
    }

    function task_send_data(data)
    {
        
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        form.append('data',JSON.stringify(data));

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                task_handle_result(ajax.responseText);
            }
        });
        
        ajax.open("POST",get_root() + "admin_ajax.php",true)
        ajax.send(form);
    }

    function task_handle_result(result)
    {
          console.log(result);
      if(result != ""){

            var obj = JSON.parse(result);
            if(obj.data_type == "add_task")
            {
                alert(obj.message);
                show_add_task(true);
                sessionStorage.setItem("tab-name","tasks");
                window.location.href = window.location.href;
            }else 
            if(obj.data_type == "delete_task")
            {
                alert(obj.message);
                sessionStorage.setItem("tab-name","tasks");
                window.location.href = window.location.href;

            }else
            if(obj.data_type == "edit_task")
            {
                alert(obj.message);
                show_edit_task(true);
                sessionStorage.setItem("tab-name","tasks");
                window.location.href = window.location.href;
            }
        }
    }

    function get_root()
    {
        var a = window.location.href;
        var b = a.split("index.php");
        return b[0] + "../../includesVali/tpl/teams/";

    }

 
</script>