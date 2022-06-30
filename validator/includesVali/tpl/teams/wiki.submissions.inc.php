<?php 

require_once __DIR__ . "/database.php";
$DB = Database::getInstance();

submission_create_db($DB);

function submission_create_db($DB)
{

    //create another database
    $sql = "create database if not exists teams01";
    $DB->run($sql);

    //make sure tables exixt
    $sql = "use $DB->DB_NAME";
    $DB->run($sql);

    $sql = "CREATE TABLE IF NOT EXISTS user_submissions (
        id BIGINT PRIMARY KEY AUTO_INCREMENT,
        post_id BIGINT DEFAULT NULL,
        user_id BIGINT DEFAULT NULL,
        project_id BIGINT DEFAULT NULL,
        group_id BIGINT DEFAULT NULL,
        task_id BIGINT DEFAULT NULL,
        post TEXT DEFAULT NULL,
        file VARCHAR(500) DEFAULT NULL,
        date DATETIME,
        INDEX post_id (post_id),
        INDEX group_id (group_id),
        INDEX project_id (project_id),
        INDEX task_id (task_id),
        INDEX user_id (user_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $DB->run($sql);

    $sql = "CREATE TABLE IF NOT EXISTS submitted_projects (
        id BIGINT PRIMARY KEY AUTO_INCREMENT,
        user_id BIGINT DEFAULT NULL,
        project_id BIGINT DEFAULT NULL,
        group_id BIGINT DEFAULT NULL,
        task_id BIGINT DEFAULT NULL,
        INDEX user_id (user_id),
        INDEX project_id (project_id),
        INDEX group_id (group_id),
        INDEX task_id (task_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $DB->run($sql);
    

}

function submission_group_name($group_id,$DB){

    $group_name = "Unknown";

    $query = "select group_name from user_groups where group_id = '$group_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['group_name'];
    }
    return $group_name;
}

function submission_project_name($project_id,$DB){

    $project_name = "Unknown";

    $query = "select project_name from user_projects where project_id = '$project_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['project_name'];
    }
    return $project_name;
}

function submission_task_name($task_id,$DB){

    $task_name = "Unknown";

    $query = "select task_name from user_tasks where task_id = '$task_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['task_name'];
    }
    return $task_name;
}

function submission_task_task($task_id,$DB){

    $task_task = "Unknown";

    $query = "select task from user_tasks where task_id = '$task_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['task'];
    }
    return $task_task;
}

function submission_project_task($project_id,$DB){

    $project_task = "Unknown";

    $query = "select task from user_projects where project_id = '$project_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['task'];
    }
    return $project_task;
}

function my_project_group($group_id,$DB){

    $project_group = "Unknown";

    $query = "select pgroup_id from user_groups where group_id = '$group_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['pgroup_id'];
    }
    return $project_group;
}



    $sql = "use $DB->DB_NAME";
    $DB->run($sql);

   
    //confirm the user belongs to a group
    $user_id = $user->getId();
    $project_id = 0;
    $group_id = 0;
    
    $query = "select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        //get current project
 
        $group_id = $check[0]['group_id'];
        $project_group_id = my_project_group($group_id,$DB);

        $group_name = submission_group_name($check[0]['group_id'],$DB);
        //show only the current and past projects
        $sql = "select * from submitted_projects where group_id = '$group_id' ";
        $done = $DB->run($sql);

        if(is_array($done)){

            $list = array_column($done, "project_id");
            $list = "'" . implode("','", $list) . "'";

            //get current project
            $sql = "select * from user_projects where pgroup_id = '$project_group_id' && project_id not in ($list) order by id asc limit 1";
            $currentproject = $DB->run($sql);
            if(is_array($currentproject)){
                $currentproject = $currentproject[0];
                $project_id = $currentproject['project_id'];
            }
        }else{

            //no submitted projects. load only first project
            $sql = "select * from user_projects where pgroup_id = '$project_group_id' order by id asc limit 1";
            $currentproject = $DB->run($sql);
            if(is_array($currentproject)){
                $currentproject = $currentproject[0];
                $project_id = $currentproject['project_id'];
            }
        }

    }
     
        
?>

<?php if(isset($currentproject) && is_array($currentproject)): ?>
<!--
  <form method="post" enctype="multipart/form-data">
      
      <textarea placeholder="type or paste here2" class="js-post form-control" autofocus></textarea>
      <input type="hidden" value="<?=$currentproject['project_id']?>" name="project_id">
      <label class="btn btn-primary pull-right" style="margin: 2px;cursor: pointer;">
          Add Image
          <input class="js-file" type="file" onchange="submission_loadFile(event)" style="display: none;" >
      </label>
      <input type="button" onclick="submission_add_post(event,'<?=$currentproject['project_id']?>')" class="btn btn-primary pull-right" value="Submit" style="margin: 2px;">
      <br><br>
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
-->
<?php endif; ?>

<?php if(isset($submissions) && is_array($submissions)): ?>

    <?php foreach($submissions as $post): ?>
        <?php 
        
            $thisuser = User::getById($post['user_id']);
             
        ?>

        <div id="single_post" style="position: relative; display: flex;margin: 10px;padding: 10px;min-height: 50px;border-radius: 10px;border:solid thin #ccc;box-shadow: 0px 0px 10px #aaa;">
            <div style="flex: 1;background-color: #ddd;margin-right:10px;text-align: center;border-top-left-radius: 10px;border-bottom-left-radius: 10px;">
                <img src="../../includesVali/tpl/teams/folder-documents-icon.png" style="margin:10px;width: 70px;">
            </div>
            <div style="flex: 8;">
                <table>
                    <tr><td>Submitted by:&nbsp </td><td><b style="color: #f0007c;"> <?=$thisuser->getFullname()?></b></td></tr>
                    <tr><td>Date: </td><td> <span style="font-size: 11px;"><?=date("jS M y H:i:s",strtotime($post['date']))?></span> </td></tr>
                    <tr><td>Group: </td><td> <?=submission_group_name($post['group_id'],$DB)?></td></tr>
                    <tr><td>Project: </td><td> <?=submission_project_name($post['project_id'],$DB)?></td></tr>
                    <tr><td>Description:&nbsp </td><td> <?=submission_project_task($post['project_id'],$DB)?></td></tr>
                </table>
                <hr style="margin-bottom: 5px;margin-top: 5px;">
                <table >
                    <tr><td>Task: </td><td><b style="color: #f0007c;"> <?=submission_task_name($post['task_id'],$DB)?></b></td></tr>
                    <tr><td>Description:&nbsp </td><td> <?=submission_task_task($post['task_id'],$DB)?></td></tr>
                </table>

                
                <hr>
                 <?=nl2br(htmlspecialchars($post['post']))?><br>

                <?php if($post['file'] && file_exists("../../includesVali/tpl/teams/".$post['file'])): ?>
                    <div style="text-align: center;">
                        <img src="../../includesVali/tpl/teams/<?=$post['file']?>" style="width: 50%;" />
                    </div>
                <?php endif; ?>
                     <!--<svg fill="orange" style="position:absolute;right:2px;top:2px;margin:4px;cursor:pointer" onclick="delete_post('<?=$post['post_id']?>')" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>-->
                    <svg fill="#15b377" style="position:absolute;right:5px;top:2px;margin:4px;cursor:pointer"  onclick="this.parentNode.querySelector('.js-post-edit-holder').style.display = 'block'" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-5 17l1.006-4.036 3.106 3.105-4.112.931zm5.16-1.879l-3.202-3.202 5.841-5.919 3.201 3.2-5.84 5.921z"/></svg>
 
               <!--post editor-->
               <div class="js-post-edit-holder" style="position: absolute;box-shadow: 0px 0px 10px #888;padding: 10px;background-color: white;width:50%;top:40px;right:10px;z-index: 10;display: none;">
                   <textarea class="form-control js-post-edit"><?=$post['post']?></textarea><br>
                   <input type="button" class="btn btn-primary" value="Save"  onclick="submission_edit_post('<?=$post['post_id']?>','<?=$post['project_id']?>',event)" >
                   <input type="button" class="btn pull-right" value="Cancel" onclick="this.parentNode.style.display = 'none'">
                   <label class="btn btn-primary">Add Image<input type="file" class="js-file-edit"  style="display: none" onchange="this.parentNode.parentNode.querySelector('#file_info').innerHTML = this.files[0].name" name="file"></label><br>
                   <p id="file_info"></p>
               </div>
            </div>
        </div>

    <?php endforeach; ?>
<?php else: ?>
    <h4 style="text-align: center;">You have not made any submissions yet</h4>
<?php endif; ?>


<script type="text/javascript">
    
    function submission_add_post(e,project_id,task_id)
    {

        if(!confirm("Are you sure you want to submit??")){
            return;
        }
        var post = e.target.parentNode.querySelector(".js-post").value.trim();
        var file = e.target.parentNode.querySelector(".js-file").files[0];
        if(post == "" && typeof file == "undefined"){
            alert("Please add something to submit");
            e.target.parentNode.querySelector(".js-post").focus();
            return;

        }

        submission_send_data({
            data_type:'add_post',
            user_id:'<?=$user->getId()?>',
            project_id: project_id,
            task_id: task_id,
            group_id:'<?=$group_id?>',
            post:post,
            file:file
        });
    } 

    function submission_edit_post(post_id,project_id,e)
    {

        var post = e.target.parentNode.querySelector(".js-post-edit").value.trim();
        var file = e.target.parentNode.querySelector(".js-file-edit").files[0];

        if(post == "" && typeof file == "undefined"){
            alert("Please add something to post");
            e.target.parentNode.querySelector(".js-post-edit").focus();
            return;

        }
  
        submission_send_data({
            data_type:'edit_post',
            user_id:'<?=$user->getId()?>',
            project_id:project_id,
            group_id:'<?=$group_id?>',
            post:post,
            file:file,
            post_id:post_id
        });
    }

    

    function submission_remove_image(e)
    {
        document.querySelector(".js-file").value = null;
        document.getElementById('output').src = "";
        document.querySelector('.js-image-delete').classList.add("hide");
    }


    function submission_send_data(data)
    {
        
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        for(var key of Object.keys(data)){

            form.append(key,data[key]);
        }

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                
                submission_handle_result(ajax.responseText);
                
            }
        });
        
        ajax.open("POST",get_root() + "submissions_ajax.php",true)
        ajax.send(form);
    }
 
    function submission_handle_result(result)
    {
         // console.log(result);
      if(result != ""){

            var obj = JSON.parse(result);
            if(obj.data_type == "add_post")
            {
                sessionStorage.setItem("tab-name",'submission');
                window.location.href = window.location.href;
            }else 
            if(obj.data_type == "delete_post")
            {
                window.location.href = window.location.href;
            }else 
            if(obj.data_type == "edit_post")
            {
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

    function submission_delete_post(post_id)
    {
        if(!confirm("Are you sure you want to delete this post?")){
            return;
        }

        submission_send_data({
            data_type:'delete_post',
            user_id:'<?=$user->getId()?>',
            project_id:'<?=$project_id?>',
            post_id:post_id
        });
    }

</script>

