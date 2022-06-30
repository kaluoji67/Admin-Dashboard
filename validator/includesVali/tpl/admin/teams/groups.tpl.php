<?php 


function my_project_group_name($group_id,$DB){

    $project_group = "Unknown";

    $query = "select * from user_pgroups where pgroup_id = '$group_id' limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        return $check[0]['pgroup_name'];
    }
    return $project_group;
}

?>

<style type="text/css">
    
    @keyframes appear{
        0%{transform: translateX(-100px);opacity: 0}
        100%{transform: translateX(0px);opacity: 1}
    }

    #group_creator{
        position: absolute;
        min-height: 100px;
        width: 300px;
        background-color: #eee;
        border: solid thin #ddd;
        animation: appear 1s ease;
       box-shadow: 0px 0px 10px #aaa;
        padding: 10px;
    }

    #group_editor{
        position: absolute;
        min-height: 100px;
        width: 300px;
        background-color: #eee;
        border: solid thin #ddd;
        animation: appear 1s ease;
       box-shadow: 0px 0px 10px #aaa;
        padding: 10px;
    }

    

    #group_viewer{
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
        height: 40px;
        box-shadow: 0px 0px 10px #aaa;
        padding: 6px;
        background-color: white;
        margin: 4px;
        font-size: 12px;
        width: auto;
    }

    .content-draggable{
        width: 99%;
        min-height: 89%;
        
    }


 
</style>
<p><b>Groups</b></p>
     <div onclick="show_add_group(event)" type="button" class="btn btn-default btn-sm pull-right" style="font-size:13px;width:120px;min-width:120px;margin-top: -30px;" ><span class="glyphicon glyphicon-plus" ></span> New Group</div>
    
    <!--new group UI-->
    <div id="group_creator" class="hide">
        <p><b>Add New Group</b></p>
        <form method="post">
            <input class="js-group-name form-control" type="text" name="group_name" placeholder="Group Name" autofocus><br>
 
            <select class="js-project-group form-control " name="project_group" >
                <option>-- Select Project Group --</option>
                <?php 

                    $sql = "select * from user_pgroups order by id desc";
                    $mypgroups = $DB->run($sql);

                    if(is_array($mypgroups)){
                        foreach ($mypgroups as $mypgroup) {
                            # code...
                            echo "<option value='$mypgroup[pgroup_id]'>$mypgroup[pgroup_name]</option>";
                        }
                    }

                ?>
            </select><br><br>

            <input onclick="add_group(this.parentNode.querySelector('.js-group-name').value,this.parentNode.querySelector('.js-project-group').value)" type="button" value="Create" class="button2 pull-right green">
            <input onclick="show_add_group(event)" type="button" value="Cancel" class="button2 pull-left orange">

         </form>
    </div>
    <!--end new group UI-->

    <!--edit group UI-->
    <div id="group_editor" class="hide">
        <p><b>Edit Group</b></p>
        <form method="post">
            <input class="js-group-name-edit form-control" type="text" name="group_name" placeholder="Group Name" autofocus><br><br>
            <input onclick="edit_group(this.parentNode.querySelector('.js-group-name-edit').value)" type="button" value="Create" class="button2 pull-right green">
            <input onclick="show_edit_group(event)" type="button" value="Cancel" class="button2 pull-left orange">

         </form>
    </div>
    <!--end edit group UI-->

    
<table class="table">

    <tr><th>Group ID</th><th>Group Name</th><th>Project Group</th><th>Members</th><th>Date Created</th><th>Action</th></tr>
    <?php 

        $sql = "select * from user_groups order by id desc";
        $groups = $DB->run($sql);

    ?>
    
    <?php if(is_array($groups)): ?>
        <?php foreach($groups as $group): ?>

            <?php
                //get all group members
                $sql = "select * from user_group_members where disabled = 0 && group_id = '$group[group_id]' ";
                $group_members = $DB->run($sql);
                if(is_array($group_members)){
                    $group_members = array_column($group_members, "user_id");
                }else{
                    $group_members = array();
                }

                $project_group =  "Unknown";
                $project_group = my_project_group_name($group['pgroup_id'],$DB);
            ?>

    <tr><td><?=$group['id']?></td><td><?=$group['group_name']?></td><td><?=$project_group?></td><td><?=count($group_members)?></td><td><?=date("jS M Y ",strtotime($group['date']))?></td>
        <td>
            <button onclick="delete_group('<?=$group['group_id']?>')" type="button" class="btn btn-default btn-sm" style="font-size:13px;" ><span class="glyphicon glyphicon-trash" ></span> Delete</button>
            <button onclick="show_edit_group('<?=$group['group_id']?>','<?=$group['group_name']?>',event)" type="button" class="btn btn-default btn-sm" style="font-size:13px;" ><span class="glyphicon glyphicon-pencil" ></span> Edit</button>
            <button onclick="show_group_members(event)" type="button" class="btn btn-default btn-sm" style="font-size:13px;" ><span class="glyphicon glyphicon-eye-open" ></span> View Members</button>
            <br>

            <!--view group members UI-->
            <div class="js-group-view hide" style="display: flex;position: absolute;width: 600px;left: 0px;tosp:0px;width: 100%;z-index: 10;margin-top: 10px;">
            <svg fill="orange" style="position:absolute;right:20px;top:45px;margin:4px;cursor:pointer" onclick="show_group_members(event)" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.151 17.943l-4.143-4.102-4.117 4.159-1.833-1.833 4.104-4.157-4.162-4.119 1.833-1.833 4.155 4.102 4.106-4.16 1.849 1.849-4.1 4.141 4.157 4.104-1.849 1.849z"/></svg>

                <div class="all_users hide" style="flex: 1;overflow-y: auto;heidght: 300px;background-color: #ddd;padding: 10px;">
                    <p><b>All Users</b>
                        
                    </p>
                    <div class="content-draggable drag-source" ondragstart="drag_started(event)" ondrop="drag_dropped(event)" ondragover="drag_over(event)">
                         
                    </div>
                </div>
                <div class="group_members" style="text-align: center;background-color: #ddd;padding: 10px;width: 99%;">
                        
                        <div style="max-width: 500px;width:100%;display: inline-block;">
                        <div style="display: flex;">
                            <input type="text" class="js-find-member form-control" onkeyup="find_member(event)" >
                            <input type="button" class="btn" onclick="add_member_by_email(event)" value="Add" group="grp_<?=$group['group_id']?>" user="usr_<?=$user->getId()?>" />
                        </div>
                        </div>
                        <br>

                    <p><b><?=$group['group_name']?> | Group Members</b></p>
                    <div class="content-drasggable drag-destination" ondragstart="drag_started(event)" ondrop="drag_dropped(event)" ondragover="drag_over(event)">
                          
                          <?php foreach(User::getByCondition("usr_sem_id > ?", array(0)) as $u): ?>
                            <?php if(in_array($u->getId(), $group_members)):?>

                            <div id="usr_<?=$u->getId();?>" group="grp_<?=$group['group_id']?>" user="usr_<?=$u->getId();?>" class="single-member" >
                                <span><?=$u->getEmail()?></span>
                                <span onclick="remove_group_member(event)" style="cursor:pointer;background-color:#ccc;padding: 1px;border-radius: 50%;border:solid thin #aaa;margin:4px;display: inline-block;width:20px;height: 20px;">X</span>
                            </div>
                        <?php endif; ?>
                        <?php endforeach; ?>

                    </div>

                </div>
            </div>
            <!--end view group members UI-->
        </td>
    </tr>
        <?php endforeach;?>
    <?php endif;?>
</table> 

<script type="text/javascript">
    
    var DRAG_SOURCE = "";
    var DRAG_DESTINATION = "";
    var EDIT_GROUP_ID = 0;
    var GROUP_MEMBERS_EDITED = false;
    var CURRENT_EVENT = false;
    var MEMBER_DESTINATION = false;

    function add_group(group_name,project_group)
    {
        if(group_name.trim() == ""){
            alert("please enter a valid group name");
            
            var group_creator = document.querySelector("#group_creator");
            group_creator.querySelector(".js-group-name").focus();
            return;
        }
        
        if(project_group.trim() == "" || isNaN(project_group)){
            alert("please select a valid project group");
            
            var group_creator = document.querySelector("#group_creator");
            group_creator.querySelector(".js-project-group").focus();
            return;
        }

        group_send_data({
            group_name: group_name.trim(),
            user_id:<?=$user->getId()?>,
            data_type:'add_group',
            pgroup_id:project_group
        });
    }

    function edit_group(group_name)
    {
        if(group_name.trim() == ""){
            alert("please enter a valid group name");
            
            var group_creator = document.querySelector("#group_creator");
            group_creator.querySelector(".js-group-name-edit").focus();
            return;
        }

        group_send_data({group_name: group_name.trim(),group_id:EDIT_GROUP_ID,data_type:'edit_group'});
    }

     
    function delete_group(id)
    {
        if(!confirm("Are you sure you want to delete this group? ALL MEMBERS WILL BE REMOVED!!"))
        {
            return;
        }

        group_send_data({
            user_id:'<?=$user->getId()?>',
            group_id:id,
            data_type:'delete_group'
        });
    }


    function show_add_group(e)
    {
        var group_creator = document.querySelector("#group_creator");
        group_creator.querySelector(".js-group-name").value = "";

        if(group_creator.classList.contains("hide")){
            group_creator.classList.remove("hide");
            group_creator.querySelector(".js-group-name").focus();
        }else{

            group_creator.classList.add("hide");
        }
        
    }

    function show_edit_group(id,group_name,e)
    {
        var group_editor = document.querySelector("#group_editor");
        group_editor.querySelector(".js-group-name-edit").value = group_name;

        if(group_editor.classList.contains("hide")){
            group_editor.classList.remove("hide");
            group_editor.querySelector(".js-group-name-edit").focus();
        }else{

            group_editor.classList.add("hide");
        }
        
        EDIT_GROUP_ID = id;
    }

    

    function group_send_data(data)
    {
        
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        form.append('data',JSON.stringify(data));

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                group_handle_result(ajax.responseText);
            }
        });
        
        ajax.open("POST",get_root() + "admin_ajax.php",true)
        ajax.send(form);
    }

    function group_handle_result(result)
    {
          console.log(result);
      if(result != ""){

            var obj = JSON.parse(result);
            if(obj.data_type == "add_group")
            {
                alert(obj.message);
                show_add_group(true);
                sessionStorage.setItem("tab-name","groups");
                window.location.href = window.location.href;
            }else 
            if(obj.data_type == "delete_group")
            {
                alert(obj.message);
                sessionStorage.setItem("tab-name","groups");
                window.location.href = window.location.href;

            }else
            if(obj.data_type == "find_user_by_email")
            {
                email_autocomplete(obj.data);
            }else
            if(obj.data_type == "remove_group_member")
            {
                
                MEMBER_DESTINATION.element.remove();

            }else
            if(obj.data_type == "add_group_member")
            {
                if(obj.data == ""){
                    alert(obj.message);
                }else{
                GROUP_MEMBERS_EDITED = true;
                MEMBER_DESTINATION.destination.innerHTML += `<div id="usr_${MEMBER_DESTINATION.user_id}" group="grp_${MEMBER_DESTINATION.group_id}" user="usr_${MEMBER_DESTINATION.user_id}" class="single-member" >
                                <span>${obj.data}</span>
                                <span onclick="remove_group_member(event)" style="cursor:pointer;background-color:#ccc;padding: 1px;border-radius: 50%;border:solid thin #aaa;margin:4px;display: inline-block;width:20px;height: 20px;">X</span>
                            </div>`;
                }
            }else
            if(obj.data_type == "edit_group")
            {
                alert(obj.message);
                show_edit_group(true);
                sessionStorage.setItem("tab-name","groups");
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

    function show_group_members(e)
    {

        var view = e.currentTarget.parentNode.querySelector(".js-group-view");
        
        //close all
        var panels = document.querySelectorAll(".js-group-view");
        for (var i = panels.length - 1; i >= 0; i--) {

            if(view !== panels[i]){
                panels[i].classList.add("hide");
            }
        }

        if(view){

            if(view.classList.contains("hide")){

                view.classList.remove("hide");
            }else{

                view.classList.add("hide");
            }
        }

        if(GROUP_MEMBERS_EDITED){

            GROUP_MEMBERS_EDITED = false;
            sessionStorage.setItem("tab-name","groups");
            window.location.href = window.location.href;
        }

        e.currentTarget.parentNode.querySelector(".js-find-member").focus();
    }

    function drag_started(e)
    {
        e.dataTransfer.setData('data',e.target.id);
        
        if(e.target.parentNode.classList.contains("drag-destination"))
        {
            DRAG_SOURCE = "destination";
        }else 
        if(e.target.parentNode.classList.contains("drag-source")){
            DRAG_SOURCE = "source";
        }
        
    }

    function drag_dropped(e)
    {
        e.preventDefault();
        
        if(e.target.classList.contains("drag-destination"))
        {
            DRAG_DESTINATION = "destination";
        }else 
        if(e.target.classList.contains("drag-source")){
            DRAG_DESTINATION = "source";
        }

        if(DRAG_SOURCE != ""  && DRAG_DESTINATION != "" && DRAG_SOURCE != DRAG_DESTINATION)
        {   
            var source = e.target.parentNode.parentNode.querySelector(".drag-source");
            var destination = e.target.parentNode.parentNode.querySelector(".drag-destination");
            
            if(DRAG_SOURCE == "source")
            {
                //add member
                var element = source.querySelector("#"+e.dataTransfer.getData('data'));
                var group_id = element.getAttribute("group");
                var user_id = element.getAttribute("user");
                destination.append(element);
                group_send_data({
                    email:element.innerHTML,
                    user_id: user_id,
                    group_id: group_id,
                    data_type: 'add_group_member'
                });

                GROUP_MEMBERS_EDITED = true;
            }else{
                //remove member;
                var element = destination.querySelector("#"+e.dataTransfer.getData('data'));
                var group_id = element.getAttribute("group");
                var user_id = element.getAttribute("user");
                source.append(element);
                group_send_data({
                    email:element.innerHTML,
                    user_id: user_id,
                    group_id: group_id,
                    data_type: 'remove_group_member'
                });

                GROUP_MEMBERS_EDITED = true;
            }
        }

        DRAG_SOURCE = "";
        DRAG_DESTINATION = "";

    }

    function remove_group_member(e)
    {
        MEMBER_DESTINATION = {};

        //remove member;
        var element = e.target.parentNode;
        var group_id = element.getAttribute("group");
        var user_id = element.getAttribute("user");
        MEMBER_DESTINATION.element = element;

        group_send_data({
            email:element.children[0].innerHTML.trim(),
            user_id: user_id,
            group_id: group_id,
            data_type: 'remove_group_member'
        });

        GROUP_MEMBERS_EDITED = true;
    }

    function add_member_by_email(e)
    {
        MEMBER_DESTINATION = {};

        var search_text = e.target.parentNode.querySelector(".js-find-member").value.trim();
        MEMBER_DESTINATION.destination = e.target.parentNode.parentNode.parentNode.querySelector(".drag-destination");
        var group_id = e.target.getAttribute("group");
        var user_id = e.target.getAttribute("user");
        
        MEMBER_DESTINATION.user_id = user_id;
        MEMBER_DESTINATION.group_id = group_id;

        group_send_data({
            email:search_text,
            user_id: user_id,
            group_id: group_id,
            data_type: 'add_group_member'
        });

        GROUP_MEMBERS_EDITED = true;
    }

    function find_member(e)
    {
        
        var search_text = e.target.value.trim();
        search_text = search_text.toLowerCase();

        CURRENT_EVENT = e;

        group_send_data({
            email:search_text.trim(),
            data_type: 'find_user_by_email'
        });

    }

    function email_autocomplete(selected)
    {
  
        if(CURRENT_EVENT.keyCode != 8 && CURRENT_EVENT.keyCode != 46){
            var pos = CURRENT_EVENT.target.selectionStart;
            CURRENT_EVENT.target.value = selected == "" ? CURRENT_EVENT.target.value : selected;
            CURRENT_EVENT.target.selectionStart = pos;
        }
    }

    function drag_over(e)
    {
        e.preventDefault();
 
    }

    

    
</script>