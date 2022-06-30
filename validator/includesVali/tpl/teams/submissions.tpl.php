<?php

require_once __DIR__ . "/database.php";
$DB = Database::getInstance();

create_db($DB);

function create_db($DB)
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
        post TEXT DEFAULT NULL,
        file VARCHAR(500) DEFAULT NULL,
        date DATETIME,
        INDEX post_id (post_id),
        INDEX group_id (group_id),
        INDEX project_id (project_id),
        INDEX user_id (user_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $DB->run($sql);

    $sql = "CREATE TABLE IF NOT EXISTS submitted_projects (
        id BIGINT PRIMARY KEY AUTO_INCREMENT,
        user_id BIGINT DEFAULT NULL,
        project_id BIGINT DEFAULT NULL,
        group_id BIGINT DEFAULT NULL,
        INDEX user_id (user_id),
        INDEX project_id (project_id),
        INDEX group_id (group_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $DB->run($sql);
    

}

function group_name($group_id,$DB){

    $group_name = "Unknown";

    $query = "select group_name from user_groups where group_id = '$group_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['group_name'];
    }
    return $group_name;
}

function project_name($project_id,$DB){

    $project_name = "Unknown";

    $query = "select project_name from user_projects where project_id = '$project_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['project_name'];
    }
    return $project_name;
}

function project_task($project_id,$DB){

    $project_task = "Unknown";

    $query = "select task from user_projects where project_id = '$project_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['task'];
    }
    return $project_task;
}



    $sql = "use $DB->DB_NAME";
    $DB->run($sql);

   
    //confirm the user belongs to a group
    $user_id = $user->getId();
    $project_id = 0;

    $query = "select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1";
    $check = $DB->run($query);
    $group_id = 0;
    if(is_array($check)){

        //get current project
 
        $group_id = $check[0]['group_id'];
        $group_name = group_name($check[0]['group_id'],$DB);
        //show only the current and past projects
        $sql = "select * from submitted_projects where group_id = '$group_id' ";
        $done = $DB->run($sql);

        if(is_array($done)){

            $list = array_column($done, "project_id");
            $list = "'" . implode("','", $list) . "'";

            //get current project
            $sql = "select * from user_projects where project_id not in ($list) order by id asc limit 1";
            $currentproject = $DB->run($sql);
            if(is_array($currentproject)){
                $currentproject = $currentproject[0];
                $project_id = $currentproject['project_id'];
            }
        }else{

            //no submitted projects. load only first project
            $sql = "select * from user_projects order by id asc limit 1";
            $currentproject = $DB->run($sql);
            if(is_array($currentproject)){
                $currentproject = $currentproject[0];
                $project_id = $currentproject['project_id'];
            }
        }

    }
         
?>

<html lang="">
<head>
    <title> Submissions </title>
</head>

<style type="text/css">

    #ToDo{
        height: 50px; background-color: #d3d3d3;color: gray;
    }
    #search_box{
        width: 400px; height: 29px; border-radius: 5px; border: none; padding: 4px;
        background-image: ;
    }
    #cover_Area{
        msax-width: 900px; width: 100%; margin: auto; background-color: whitesmoke; min-height: 500px;
        text-align: left;padding: 10px;
    }

    #post_area{
        background-color: ghostwhite; flex: 2.5; padding: 20px;
    }
    #white_board{
        border: solid thin #aaa; padding: 10px;
    }

</style>
<br>
<body style="font-family: 'Arial Unicode MS',serif">
    
    <?php //if(isset($currentproject) && is_array($currentproject)): ?>

<div id="ToDo">


    <div style="margin: auto; width: 800px; font-size: 30px;">
        Submissions:
    </div>
</div>

<!--Cover Area-->
<div id="cover_Area" style="display: flex;">

    <div style="flex: 1">
    <!--Below Cover Area-->
  
        <?php 

            $group_id = 0;
            $me = $user->getId();
            $sql = "select * from user_group_members where disabled = 0 && user_id = '$me' limit 1";
            $mygroup = $DB->run($sql);
            
            if(is_array($mygroup))
            {
                $group_id = $mygroup[0]['group_id'];
                $submissions = $DB->run("select * from user_submissions where group_id = '$group_id' order by id desc limit 100");
            }

            if($user->getFlagAdmin() == "Y"){

                //show all submissions for admins
                $submissions = $DB->run("select * from user_submissions order by id desc limit 100");

            }
        
        ?>

            <?php if(is_array($currentproject)): ?>
                Submit to Project: <b><?=$currentproject['project_name']?></b><br>
                For group: <b><?=$group_name?></b><br><br>
            <?php else: ?>
                NO current project was found:<br><br>
            <?php endif; ?>

            <div class="js-posts-view">

                <?php require "wiki.submissions.inc.php" ?>
                 
            </div>
            <?php if(is_array($currentproject)): ?>

            <form method="post" enctype="multipart/form-data">
                
                <textarea placeholder="type or paste here" class="js-post form-control" autofocus></textarea>
                <input type="hidden" value="<?=$currentproject['project_id']?>" name="project_id">
                <label class="btn btn-primary pull-right" style="margin: 2px;cursor: pointer;">
                    Add Image
                    <input class="js-file" type="file" onchange="submission_loadFile(event)" style="display: none;" >
                </label>
                <input type="button" onclick="submission_add_post(event)" class="btn btn-primary pull-right" value="Submit" style="margin: 2px;">
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
            <?php endif; ?>
        <?php //endif; ?>
    </div>
 
    <div style="flex: 1;">
        <?php require "chat.tpl.php" ?>
    </div>
</div>
 
 

</body>
</html>

<script type="text/javascript">
    
    function submission_add_post(e)
    {

        var post = document.querySelector(".js-post").value.trim();
        var file = document.querySelector(".js-file").files[0];
        if(post == "" && typeof file == "undefined"){
            alert("Please add something to post");
            document.querySelector(".js-post").focus();
            return;

        }

        submission_send_data({
            data_type:'add_post',
            user_id:'<?=$user->getId()?>',
            project_id:'<?=$project_id?>',
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
          console.log(result);
      if(result != ""){

            var obj = JSON.parse(result);
            if(obj.data_type == "add_post")
            {
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

