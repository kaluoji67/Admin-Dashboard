<style type="text/css">
    
    @keyframes appear{
        0%{transform: translateX(-100px);opacity: 0}
        100%{transform: translateX(0px);opacity: 1}
    }

    #project_creator{
        position: absolute;
        min-height: 100px;
        width: 300px;
        background-color: #eee;
        border: solid thin #ddd;
        animation: appear 1s ease;
       box-shadow: 0px 0px 10px #aaa;
        padding: 10px;
    }

    #project_editor{
        position: absolute;
        min-height: 100px;
        width: 300px;
        background-color: #eee;
        border: solid thin #ddd;
        animation: appear 1s ease;
       box-shadow: 0px 0px 10px #aaa;
        padding: 10px;
    }

    

    #project_viewer{
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
<p><b>Projects</b></p>
     <div onclick="show_add_project(event)" type="button" class="btn btn-default btn-sm pull-right" style="font-size:13px;width:120px;min-width:120px;margin-top: -30px;" ><span class="glyphicon glyphicon-plus" ></span> New Project</div>
    
    <!--new project UI-->
    <div id="project_creator" class="hide">
        <p><b>Add New Project</b></p>
        <form method="post">
            <input class="js-project-name form-control" type="text" name="project_name" placeholder="Project Name" autofocus><br>
            <textarea class="js-project-task form-control" name="project_task" placeholder="Project Description" ></textarea><br>
            <select class="js-project-group form-control" name="project_group" placeholder="Group">
                <option>-- Select Project Group --</option>
                <?php 

                    $sql = "select * from user_pgroups order by id desc";
                    $groups = $DB->run($sql);

                    if(is_array($groups)){
                        foreach ($groups as $group) {
                            # code...
                            echo "<option value='$group[pgroup_id]'>$group[pgroup_name]</option>";
                        }
                    }

                ?>
            </select><br>
             
            <input onclick="add_project(event)" type="button" value="Create" class="button2 pull-right green">
            <input onclick="show_add_project(event)" type="button" value="Cancel" class="button2 pull-left orange">

         </form>
    </div>
    <!--end new project UI-->

    <!--edit project UI-->
    <div id="project_editor" class="hide">
        <p><b>Edit Project</b></p>
        <form method="post">
            <input class="js-project-name-edit form-control" type="text" name="project_name" placeholder="Project Name" autofocus><br><br>
            <textarea class="js-project-task-edit form-control" name="project_task" placeholder="Project Description" ></textarea><br>
            <select class="js-project-group-edit form-control hide" name="project_group" placeholder="Group" >
                <option></option>
                <?php 

                    $sql = "select * from user_groups order by id desc";
                    $groups = $DB->run($sql);

                    if(is_array($groups)){
                        foreach ($groups as $group) {
                            # code...
                            echo "<option value='$group[group_id]'>$group[group_name]</option>";
                        }
                    }

                ?>
            </select><br>

            <input onclick="edit_project(event)" type="button" value="Create" class="button2 pull-right green">
            <input onclick="show_edit_project(event)" type="button" value="Cancel" class="button2 pull-left orange">

         </form>
    </div>
    <!--end edit project UI-->

    
<table class="table">

    <tr><th>Project ID</th><th>Project Name</th><th>Project Description</th><th>Project Group</th><th>Number of tasks</th><th>Date Created</th><th>Action</th></tr>
    <?php 

        $sql = "select * from user_projects order by id desc";
        $projects = $DB->run($sql);

    ?>
    
    <?php if(is_array($projects)): ?>
        <?php foreach($projects as $project): 
 
            $edit_arr = array();
            $edit_arr['project_name'] = $project['project_name'];
            $edit_arr['task'] = $project['task'];
            //$edit_arr['pgroup_id'] = $project['pgroup_id'];

            $edit_values = json_encode($edit_arr);
            $edit_values = str_replace('"', "'", $edit_values);

            $tasks_count = 0;
            $sql = "select * from user_tasks where project_id = '$project[project_id]' ";
            $mytasks = $DB->run($sql);
            if(is_array($mytasks)){
                $tasks_count = count($mytasks);
            }

            $project_group = "Unknown";
            $sql = "select * from user_pgroups where pgroup_id = '$project[pgroup_id]' limit 1";
            $pgroup = $DB->run($sql);
            if(is_array($pgroup)){
                $project_group = $pgroup[0]['pgroup_name'];
            }

            
        ?>
            
    <tr><td><?=$project['id']?></td><td><?=$project['project_name']?></td><td><?=$project['task']?></td><td><?=$project_group?></td><td><?=$tasks_count?></td><td><?=date("jS M Y ",strtotime($project['date']))?></td>
        <td>
            <button onclick="delete_project('<?=$project['project_id']?>')" type="button" class="btn btn-default btn-sm" style="font-size:13px;" ><span class="glyphicon glyphicon-trash" ></span> Delete</button>
            <button onclick="show_edit_project('<?=$project['project_id']?>',event)" edit="<?=$edit_values?>" type="button" class="btn btn-default btn-sm" style="font-size:13px;" ><span class="glyphicon glyphicon-pencil" ></span> Edit</button>
 
        </td>
    </tr>
        <?php endforeach;?>
    <?php endif;?>
</table> 

<script type="text/javascript">
    
    var EDIT_PROJECT_ID = 0;
    
    function add_project(e)
    {
        var project_creator = document.querySelector("#project_creator");
        var project_name = project_creator.querySelector('.js-project-name').value;
        var task = project_creator.querySelector('.js-project-task').value;
        var pgroup_id = project_creator.querySelector('.js-project-group').value;

        if(project_name.trim() == ""){
            alert("please enter a valid project name");
            
            project_creator.querySelector(".js-project-name").focus();
            return;
        }

        if(task.trim() == ""){
            alert("please enter a valid task description");
            
            project_creator.querySelector(".js-project-task").focus();
            return;
        }

        if(pgroup_id.trim() == ""){
            alert("please select a valid project group");
            
            project_creator.querySelector(".js-project-group").focus();
            return;
        }

        project_send_data({
            project_name: project_name.trim(),
            user_id:<?=$user->getId()?>,
            task:task.trim(),
            pgroup_id:pgroup_id.trim(),
            data_type:'add_project'
        });
    }

    function edit_project(e)
    {
        var project_editor = document.querySelector("#project_editor");
        var project_name = project_editor.querySelector('.js-project-name-edit').value;
        var task = project_editor.querySelector('.js-project-task-edit').value;
        var group_id = project_editor.querySelector('.js-project-group-edit').value;

        if(project_name.trim() == ""){
            alert("please enter a valid project name");
            
            project_editor.querySelector(".js-project-name-edit").focus();
            return;
        }

        if(task.trim() == ""){
            alert("please enter a valid task description");
            
            project_editor.querySelector(".js-project-task-edit").focus();
            return;
        }

        if(group_id.trim() == ""){
            //alert("please enter a valid group assigned");
            
            //project_editor.querySelector(".js-project-group-edit").focus();
            //return;
        }

        project_send_data({
            project_name: project_name.trim(),
            user_id:<?=$user->getId()?>,
            task:task.trim(),
            group_id:group_id.trim(),
            project_id:EDIT_PROJECT_ID,
            data_type:'edit_project'
        });
    }

     
    function delete_project(id)
    {
        if(!confirm("Are you sure you want to delete this project??"))
        {
            return;
        }

        project_send_data({
            project_id:id,
            user_id:<?=$user->getId()?>,
            data_type:'delete_project'
        });
    }


    function show_add_project(e)
    {
        var project_creator = document.querySelector("#project_creator");
        project_creator.querySelector(".js-project-name").value = "";

        if(project_creator.classList.contains("hide")){
            project_creator.classList.remove("hide");
            project_creator.querySelector(".js-project-name").focus();
        }else{

            project_creator.classList.add("hide");
        }
        
    }

    function show_edit_project(id,e)
    {
        var project_editor = document.querySelector("#project_editor");

        if(e){

            var data = e.currentTarget.getAttribute("edit").replaceAll("'",'"');
            var obj = JSON.parse(data);

            if(obj){
                project_editor.querySelector(".js-project-name-edit").value = obj.project_name;
                project_editor.querySelector(".js-project-task-edit").value = obj.task;
                project_editor.querySelector(".js-project-group-edit").value = obj.group_id;   
            }
              
        }        

        if(project_editor.classList.contains("hide")){
            project_editor.classList.remove("hide");
            project_editor.querySelector(".js-project-name-edit").focus();
        }else{

            project_editor.classList.add("hide");
        }
        
        EDIT_PROJECT_ID = id;
    }

    function project_send_data(data)
    {
        
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        form.append('data',JSON.stringify(data));

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                project_handle_result(ajax.responseText);
            }
        });
        
        ajax.open("POST",get_root() + "admin_ajax.php",true)
        ajax.send(form);
    }

    function project_handle_result(result)
    {
          console.log(result);
      if(result != ""){

            var obj = JSON.parse(result);
            if(obj.data_type == "add_project")
            {
                alert(obj.message);
                show_add_project(true);
                sessionStorage.setItem("tab-name","project");
                window.location.href = window.location.href;
            }else 
            if(obj.data_type == "delete_project")
            {
                alert(obj.message);
                sessionStorage.setItem("tab-name","project");
                window.location.href = window.location.href;

            }else
            if(obj.data_type == "edit_project")
            {
                alert(obj.message);
                show_edit_project(true);
                sessionStorage.setItem("tab-name","project");
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