<?php 
    require_once __DIR__ . "/database.php";
    $DB = Database::getInstance();
    $sql = "use $DB->DB_NAME";
    $DB->run($sql);

//function
function chat_group_name($group_id,$DB){

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

<style type="text/css">

    #ToDo{
        height: 50px; background-color: #d3d3d3;color: gray;
    }
    #search_box{
        width: 400px; height: 29px; border-radius: 5px; border: none; padding: 4px;
        background-image: ;
    }
    #cover_Area{
        mdax-width: 900px; width: 100%; margin: auto; background-color: whitesmoke; 
        text-align: left;padding: 10px;
    }

    #post_area{
        background-color: ghostwhite; flex: 2.5; padding: 20px;
    }
    #white_board{
        border: solid thin #aaa; padding: 10px;
    }

</style>

<div style="font-family: 'Arial Unicode MS',serif; width:100%;" >
 <div style="background-color: #0c7bb3;color:white;padding: 5px;">CHAT:</div>
<!--Cover Area-->

<div id="cover_Area" style="box-shadow: 0px 0px 10px #666;">

    <!--Below Cover Area-->
  
        <?php

            $group_id = 0;
            $me = $user->getId();
            $sql = "select * from user_group_members where disabled = 0 && user_id = '$me' limit 1";
            $mygroup = $DB->run($sql);

            $group_name = "Unknown";
            $group_members = "Unknown";

            if(is_array($mygroup))
            {
                $group_id = $mygroup[0]['group_id'];
                $group_name = chat_group_name($group_id,$DB);

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
            <b>Members: </b><span><?=$group_members?></span><br>
            </div> 

            <!--chat post area-->
            <form method="post" enctype="multipart/form-data">
                
                <textarea placeholder="Chat with group members" class="js-post form-control" autofocus></textarea>
                <label class="btn btn-primary pull-right" style="margin: 2px;cursor: pointer;">
                    Add Image
                    <input class="js-image-file" type="file" onchange="chat_loadFile(event)" style="display: none;" >
                </label>
                <input type="button" onclick="chat_add_post(event)" class="btn btn-primary pull-right" value="Post" style="margin: 2px;">
                <br><br>
                <p style="position: relative;">
                    <img id="image_output" width="344px" />
                    <svg class="js-chat-image-delete hide" fill="orange" style="position:relative;right:2px;top:2px;margin:4px;cursor:pointer" onclick="chat_remove_image(event)" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>

                </p>

                <script>
                    var chat_loadFile = function(event){
                        var image = document.getElementById('image_output');
                        document.querySelector('.js-chat-image-delete').classList.remove("hide");
                        image.src = URL.createObjectURL(event.target.files[0]);
                    };
                </script>
            </form>
            <!--end chat post area-->
           
            <div class="js-posts-view">
                <div class="js-user-chats">
                <?php 

                    $limit = 10;
                    $page_number = isset($_GET['chat_page']) ? (int)$_GET['chat_page']: 1;
                    $offset = ($page_number - 1) * $limit;
                    
                    $posts = $DB->run("select * from user_chat where group_id = '$group_id' order by id desc limit $limit offset $offset");?>
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
                                        <svg fill="orange" style="position:absolute;right:2px;top:-12px;margin:4px;cursor:pointer" onclick="chat_delete_post('<?=$post['post_id']?>')" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>
                                        <svg fill="#15b377" style="position:absolute;right:35px;top:-12px;margin:4px;cursor:pointer"  onclick="this.parentNode.querySelector('.js-post-edit-holder').style.display = 'block'" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-5 17l1.006-4.036 3.106 3.105-4.112.931zm5.16-1.879l-3.202-3.202 5.841-5.919 3.201 3.2-5.84 5.921z"/></svg>
                                   <?php endif; ?>

                                   <!--post editor-->
                                   <div class="js-post-edit-holder" style="position: absolute;box-shadow: 0px 0px 10px #888;padding: 10px;background-color: white;width:50%;top:40px;right:10px;z-index: 10;display: none;">
                                       <textarea class="form-control js-post-edit"><?=$post['post']?></textarea><br>
                                       <input type="button" class="btn btn-primary" value="Save"  onclick="chat_edit_post('<?=$post['post_id']?>',event)" >
                                       <input type="button" class="btn pull-right" value="Cancel" onclick="this.parentNode.style.display = 'none'">
                                       <!--<label class="btn btn-primary">Add File<input type="file" class="js-file-edit"  style="display: none" onchange="this.parentNode.parentNode.querySelector('#file_info').innerHTML = this.files[0].name" name="file"></label><br>-->
                                       <p id="file_info"></p>
                                   </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                       
                    <?php endif; ?>
                </div>
                        <div>
                            <a href="index.php?action=<?=$_GET['action']?>&chat_page=<?=($page_number>1)?($page_number-1):1;?>">
                                <input type="button" class="btn pull-left" value="< Prev chats">
                            </a>
                            <a href="index.php?action=<?=$_GET['action']?>&chat_page=<?=($page_number+1)?>">
                                <input type="button" class="btn pull-right" value="Next chats >">
                            </a>
                            
                        </div>
            </div>


</div>


</div>


<script type="text/javascript">
    
    var TIMER = 0;
    window.requestAnimationFrame(chat_read);

    function chat_add_post(e)
    {

        var post = e.target.parentNode.querySelector(".js-post").value.trim();
        var file = e.target.parentNode.querySelector(".js-image-file").files[0];
       
        if(post == "" && typeof file == "undefined"){
            alert("Please add something to post");
            e.target.parentNode.querySelector(".js-post").focus();
            return;

        }

        chat_send_data({
            data_type:'add_post',
            user_id:'<?=$user->getId()?>',
            project_id:'<?=$project_id?>',
            group_id:'<?=$group_id?>',
            post:post,
            file:file
        });
    }

    function chat_edit_post(post_id,e)
    {

        var post = e.target.parentNode.querySelector(".js-post-edit").value.trim();
        //var file = e.target.parentNode.querySelector(".js-file-edit").files[0];
       var file = null;
        if(post == "" && typeof file == "undefined"){
            alert("Please add something to post");
            e.target.parentNode.querySelector(".js-post-edit").focus();
            return;

        }
  
        chat_send_data({
            data_type:'edit_post',
            user_id:'<?=$user->getId()?>',
            project_id:'<?=$project_id?>',
            group_id:'<?=$group_id?>',
            post:post,
            file:file,
            post_id:post_id
        });
    }

    

    function chat_remove_image(e)
    {
        document.querySelector(".js-image-file").value = null;
        document.getElementById('image_output').src = "";
        document.querySelector('.js-chat-image-delete').classList.add("hide");
    }


    function chat_send_data(data)
    {
        
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        for(var key of Object.keys(data)){

            form.append(key,data[key]);
        }

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                
                chat_handle_result(ajax.responseText);
                
            }
        });
        
        ajax.open("POST",get_root() + "chat_ajax.php",true)
        ajax.send(form);
    }

  

    function chat_handle_result(result)
    {
    
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
            }else 
            if(obj.data_type == "read")
            {
                update_chat(obj.data);
                //window.location.href = window.location.href;
            }
            
        }
    }

    function update_chat(data)
    {
        var container = document.querySelector(".js-user-chats");
        var str = "";
        for(var key in data)
        {
            str += 
            `
                <div id="single_post" style="position: relative; display: flex;margin: 10px;padding: 10px;min-height: 50px;border-radius: 10px;border:solid thin #ccc;box-shadow: 0px 0px 10px #aaa;">
                    <div style="flex: 1;background-color: #ddd;margin-right:10px;text-align: center;border-top-left-radius: 10px;border-bottom-left-radius: 10px;">
                        <img src="../../includesVali/tpl/teams/generic-user-purple.png" style="margin:10px;width: 70px;border-radius: 50%;">
                    </div>
                    <div style="flex: 8;">
                        <b style="color: #f0007c;">${data[key].full_name}</b> . <span style="font-size: 11px;">${data[key].date}</span> <br>
                        
                        ${data[key].post}<br>`;

                        if(data[key].file){

                            str +=
                            `<div style="text-align: center;">
                                <img src="../../includesVali/tpl/teams/${data[key].file}" style="width: 50%;" />
                            </div>`;
                        }

                        if(data[key].user_id == data[key].user_id){
                            
                            str += 
                            `<svg fill="orange" style="position:absolute;right:2px;top:-12px;margin:4px;cursor:pointer" onclick="chat_delete_post('${data[key].post_id}')" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>
                            <svg fill="#15b377" style="position:absolute;right:35px;top:-12px;margin:4px;cursor:pointer"  onclick="this.parentNode.querySelector('.js-post-edit-holder').style.display = 'block'" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-5 17l1.006-4.036 3.106 3.105-4.112.931zm5.16-1.879l-3.202-3.202 5.841-5.919 3.201 3.2-5.84 5.921z"/></svg>
                            `;
                       }

                       str +=
                       `<!--post editor-->
                       <div class="js-post-edit-holder" style="position: absolute;box-shadow: 0px 0px 10px #888;padding: 10px;background-color: white;width:50%;top:40px;right:10px;z-index: 10;display: none;">
                           <textarea class="form-control js-post-edit">${data[key].post}</textarea><br>
                           <input type="button" class="btn btn-primary" value="Save"  onclick="chat_edit_post('${data[key].post_id}',event)" >
                           <input type="button" class="btn pull-right" value="Cancel" onclick="this.parentNode.style.display = 'none'">
                           <!--<label class="btn btn-primary">Add File<input type="file" class="js-file-edit"  style="display: none" onchange="this.parentNode.parentNode.querySelector('#file_info').innerHTML = this.files[0].name" name="file"></label><br>-->
                           <p id="file_info"></p>
                       </div>
                    </div>
                </div>
            `;

        }

        container.innerHTML = str;

    }

    function get_root()
    {
        var a = window.location.href;
        var b = a.split("index.php");
        return b[0] + "../../includesVali/tpl/teams/";

    }

    function chat_delete_post(post_id)
    {
        if(!confirm("Are you sure you want to delete this post?")){
            return;
        }

        chat_send_data({
            data_type:'delete_post',
            user_id:'<?=$user->getId()?>',
            project_id:'<?=$project_id?>',
            post_id:post_id
        });
    }

    function chat_read()
    {
        TIMER++;

        if(TIMER >= 60 * 10)
        {
            TIMER = 0;
            check_messages();
        }
        window.requestAnimationFrame(chat_read);
    }

    function check_messages()
    {

        chat_send_data({
            data_type:'read',
            user_id:'<?=$user->getId()?>',
            group_id:'<?=$group_id?>'
        });
    }

</script>
<!--
todo

1. the project should appear here. the instructors add the project and it appeasr here.
   then a notification appear in the main page about the new information in the project page
   also i think a notification page is needed in the admin side. 

-->
