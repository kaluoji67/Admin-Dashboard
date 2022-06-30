<?php 
    require __DIR__ . "/database.php";
    $DB = Database::getInstance();
    $sql = "use teams01";
    $DB->run($sql);

//function
function group_name($group_id,$DB){

    $group_name = "Unknown";

    $query = "select group_name from user_groups where group_id = '$group_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['group_name'];
    }
    return $group_name;
}

    $project_id = (isset($_GET['prj'])) ? addslashes($_GET['prj']): 0;

    //confirm the user belongs to a group
    $user_id = $user->getId();

    $query = "select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        $sql = "select * from user_projects where project_id = '$project_id' limit 1";
        $myproject = $DB->run($sql);

        $project_name = "";
        if(is_array($myproject)){
            $myproject = $myproject[0];
            $project_name = $myproject['project_name'];
        }
    }
?>

<html lang="">
<head>
    <title> Project Listing </title>
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
        max-width: 900px; width: 100%; margin: auto; background-color: whitesmoke; min-height: 500px;
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
    
    <?php if(isset($myproject) && is_array($myproject)): ?>

<div id="ToDo">


    <div style="margin: auto; width: 800px; font-size: 30px;">
        Project: <?=$project_name?> &nbsp &nbsp
    </div>
</div>

<!--Cover Area-->
<div id="cover_Area">

    <!--Below Cover Area-->
  
        <?php if(is_array($myproject)): 

            $group_id = 0;
            $me = $user->getId();
            $sql = "select * from user_group_members where disabled = 0 && user_id = '$me' limit 1";
            $mygroup = $DB->run($sql);

            $group_name = "Unknown";
            $group_members = "Unknown";

            if(is_array($mygroup))
            {
                $group_id = $mygroup[0]['group_id'];
                $group_name = group_name($group_id,$DB);

                $sql = "select * from user_group_members where disabled = 0 && group_id = '$group_id' ";
                $mymembers = $DB->run($sql);

                if(is_array($mymembers)){

                    $group_members = "";
                    foreach ($mymembers as $key => $row) {
                        # code...
                        $thisuser = User::getById($row['user_id']);
                        $group_members .= $thisuser->getFullname() . " . ";
                    }
                }

            }
        
        ?>

            <div style="background-color: #ddd;padding: 10px;border-radius: 5px;margin-bottom: 5px;">
            <b>Your Group:</b> <?=$group_name?><br>
            <b>Members: </b><a><?=$group_members?></a><br>
            </div> 

            <div style="background-color: #ddd;padding: 10px;border-radius: 5px;">

            <b>Project Task:</b><br>
            <?=$myproject['task']?>
            </div><br>
            COMMENTS:<br>
            <div class="js-posts-view">
                <?php 

                    $posts = $DB->run("select * from user_posts where project_id = '$myproject[project_id]' && group_id = '$group_id' order by id desc limit 100");?>
                    <?php if(is_array($posts)): ?>
                        <?php foreach($posts as $post): ?>
                            <?php 
                                $thisuser = User::getById($post['user_id']);
                            ?>
                            <div id="single_post" style="position: relative; display: flex;margin: 10px;padding: 10px;min-height: 50px;border-radius: 10px;border:solid thin #ccc;box-shadow: 0px 0px 10px #aaa;">
                                <div style="flex: 1;background-color: #ddd;margin-right:10px;text-align: center;border-top-left-radius: 10px;border-bottom-left-radius: 10px;">
                                    <img src="../../includesVali/tpl/teams/generic-user-purple.png" style="margin:10px;width: 70px;border-radius: 50%;">
                                </div>
                                <div style="flex: 8;">
                                    <b style="color: #f0007c;"><?=$thisuser->getFullname()?></b> . <span style="font-size: 11px;"><?=date("jS M y H:i:s",strtotime($post['date']))?></span> <br>
                                    
                                    <?=nl2br(htmlspecialchars($post['post']))?><br>

                                    <?php if($post['file'] && file_exists("../../includesVali/tpl/teams/".$post['file'])): ?>
                                        <div style="text-align: center;">
                                            <img src="../../includesVali/tpl/teams/<?=$post['file']?>" style="width: 50%;" />
                                        </div>
                                    <?php endif; ?>
                                    <?php if($post['user_id'] == $user->getId()): ?>
                                        <svg fill="orange" style="position:absolute;right:2px;top:2px;margin:4px;cursor:pointer" onclick="delete_post('<?=$post['post_id']?>')" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>
                                        <svg fill="#15b377" style="position:absolute;right:35px;top:2px;margin:4px;cursor:pointer"  onclick="this.parentNode.querySelector('.js-post-edit-holder').style.display = 'block'" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-5 17l1.006-4.036 3.106 3.105-4.112.931zm5.16-1.879l-3.202-3.202 5.841-5.919 3.201 3.2-5.84 5.921z"/></svg>
                                   <?php endif; ?>

                                   <!--post editor-->
                                   <div class="js-post-edit-holder" style="position: absolute;box-shadow: 0px 0px 10px #888;padding: 10px;background-color: white;width:50%;top:40px;right:10px;z-index: 10;display: none;">
                                       <textarea class="form-control js-post-edit"><?=$post['post']?></textarea><br>
                                       <input type="button" class="btn btn-primary" value="Save"  onclick="edit_post('<?=$post['post_id']?>',event)" >
                                       <input type="button" class="btn pull-right" value="Cancel" onclick="this.parentNode.style.display = 'none'">
                                       <!--<label class="btn btn-primary">Add File<input type="file" class="js-file-edit"  style="display: none" onchange="this.parentNode.parentNode.querySelector('#file_info').innerHTML = this.files[0].name" name="file"></label><br>-->
                                       <p id="file_info"></p>
                                   </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php endif; ?>
                 
            </div>
            <form method="post" enctype="multipart/form-data">
                
                <textarea placeholder="Leave a comment" class="js-post form-control" autofocus></textarea>
                <label class="btn btn-primary pull-right" style="margin: 2px;cursor: pointer;">
                    Add Image
                    <input class="js-file" type="file" onchange="loadFile(event)" style="display: none;" >
                </label>
                <input type="button" onclick="add_post(event)" class="btn btn-primary pull-right" value="Post" style="margin: 2px;">
                <br><br>
                <p style="position: relative;">
                    <img id="output" width="344px" />
                    <svg class="js-image-delete hide" fill="orange" style="position:relative;right:2px;top:2px;margin:4px;cursor:pointer" onclick="remove_image(event)" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>

                </p>

                <script>
                    var loadFile = function(event){
                        var image = document.getElementById('output');
                        document.querySelector('.js-image-delete').classList.remove("hide");
                        image.src = URL.createObjectURL(event.target.files[0]);
                    };
                </script>
            </form>
        <?php endif; ?>
 

</div>
<?php else: ?>

    <div style="padding: 1em;text-align: center;font-size: 18px;">Sorry, that project was not found!</div>
<?php endif; ?>

</body>
</html>

<script type="text/javascript">
    
    function add_post(e)
    {

        var post = document.querySelector(".js-post").value.trim();
        var file = document.querySelector(".js-file").files[0];
       
        if(post == "" && typeof file == "undefined"){
            alert("Please add something to post");
            document.querySelector(".js-post").focus();
            return;

        }

        send_data({
            data_type:'add_post',
            user_id:'<?=$user->getId()?>',
            project_id:'<?=$project_id?>',
            group_id:'<?=$group_id?>',
            post:post,
            file:file
        });
    }

    function edit_post(post_id,e)
    {

        var post = e.target.parentNode.querySelector(".js-post-edit").value.trim();
        //var file = e.target.parentNode.querySelector(".js-file-edit").files[0];
       var file = null;
        if(post == "" && typeof file == "undefined"){
            alert("Please add something to post");
            e.target.parentNode.querySelector(".js-post-edit").focus();
            return;

        }
  
        send_data({
            data_type:'edit_post',
            user_id:'<?=$user->getId()?>',
            project_id:'<?=$project_id?>',
            group_id:'<?=$group_id?>',
            post:post,
            file:file,
            post_id:post_id
        });
    }

    

    function remove_image(e)
    {
        document.querySelector(".js-file").value = null;
        document.getElementById('output').src = "";
        document.querySelector('.js-image-delete').classList.add("hide");
    }


    function send_data(data)
    {
        show_loader();
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        for(var key of Object.keys(data)){

            form.append(key,data[key]);
        }

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                
                handle_result(ajax.responseText);
                
            }
        });
        
        ajax.open("POST",get_root() + "project_ajax.php",true)
        ajax.send(form);
    }

    function show_loader()
    {

    }

    function handle_result(result)
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

    function delete_post(post_id)
    {
        if(!confirm("Are you sure you want to delete this post?")){
            return;
        }

        send_data({
            data_type:'delete_post',
            user_id:'<?=$user->getId()?>',
            project_id:'<?=$project_id?>',
            post_id:post_id
        });
    }

</script>
<!--
todo

1. the project should appear here. the instructors add the project and it appeasr here.
   then a notification appear in the main page about the new information in the project page
   also i think a notification page is needed in the admin side. 

-->
