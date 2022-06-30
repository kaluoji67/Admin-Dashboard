<style type="text/css">
    
    @keyframes appear{
        0%{transform: translateX(-100px);opacity: 0}
        100%{transform: translateX(0px);opacity: 1}
    }

    #pgroup_creator{
        position: absolute;
        min-height: 100px;
        width: 300px;
        background-color: #eee;
        border: solid thin #ddd;
        animation: appear 1s ease;
       box-shadow: 0px 0px 10px #aaa;
        padding: 10px;
    }

    #pgroup_editor{
        position: absolute;
        min-height: 100px;
        width: 300px;
        background-color: #eee;
        border: solid thin #ddd;
        animation: appear 1s ease;
       box-shadow: 0px 0px 10px #aaa;
        padding: 10px;
    }

    

    #pgroup_viewer{
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
     <div onclick="show_add_pgroup(event)" type="button" class="btn btn-default btn-sm pull-right" style="font-size:13px;width:120px;min-width:160px;margin-top: -30px;" ><span class="glyphicon glyphicon-plus" ></span> New Project Group</div>
    
    <!--new pgroup UI-->
    <div id="pgroup_creator" class="hide">
        <p><b>Add New Project Group</b></p>
        <form method="post">
            <input class="js-pgroup-name form-control" type="text" name="pgroup_name" placeholder="Project Group Name" autofocus><br><br>
            <input onclick="add_pgroup(this.parentNode.querySelector('.js-pgroup-name').value)" type="button" value="Create" class="button2 pull-right green">
            <input onclick="show_add_pgroup(event)" type="button" value="Cancel" class="button2 pull-left orange">

         </form>
    </div>
    <!--end new pgroup UI-->

    <!--edit pgroup UI-->
    <div id="pgroup_editor" class="hide">
        <p><b>Edit Project Group</b></p>
        <form method="post">
            <input class="js-pgroup-name-edit form-control" type="text" name="pgroup_name" placeholder="Project Group Name" autofocus><br><br>
            <input onclick="edit_pgroup(this.parentNode.querySelector('.js-pgroup-name-edit').value)" type="button" value="Create" class="button2 pull-right green">
            <input onclick="show_edit_pgroup(event)" type="button" value="Cancel" class="button2 pull-left orange">

         </form>
    </div>
    <!--end edit pgroup UI-->

    
<table class="table">

    <tr><th>Project Group ID</th><th>Project Group Name</th><th>Groups</th><th>Date Created</th><th>Action</th></tr>
    <?php 

        $sql = "select * from user_pgroups order by id desc";
        $pgroups = $DB->run($sql);

    ?>
    
    <?php if(is_array($pgroups)): ?>
        <?php foreach($pgroups as $pgroup): ?>

            <?php
                //get all groups that belong to this project group
                $sql = "select * from user_groups where pgroup_id = '$pgroup[pgroup_id]' ";
                $pgroup_members = $DB->run($sql);
                if(is_array($pgroup_members)){
                    $pgroup_members = count($pgroup_members);
                }else{
                    $pgroup_members = 0;
                }
            ?>

    <tr><td><?=$pgroup['id']?></td><td><?=$pgroup['pgroup_name']?></td><td><?=$pgroup_members?></td><td><?=date("jS M Y ",strtotime($pgroup['date']))?></td>
        <td>
            <button onclick="delete_pgroup('<?=$pgroup['pgroup_id']?>')" type="button" class="btn btn-default btn-sm" style="font-size:13px;" ><span class="glyphicon glyphicon-trash" ></span> Delete</button>
            <button onclick="show_edit_pgroup('<?=$pgroup['pgroup_id']?>','<?=$pgroup['pgroup_name']?>',event)" type="button" class="btn btn-default btn-sm" style="font-size:13px;" ><span class="glyphicon glyphicon-pencil" ></span> Edit</button>
         
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

    function add_pgroup(pgroup_name)
    {
        if(pgroup_name.trim() == ""){
            alert("please enter a valid pgroup name");
            
            var pgroup_creator = document.querySelector("#pgroup_creator");
            pgroup_creator.querySelector(".js-pgroup-name").focus();
            return;
        }

        pgroup_send_data({pgroup_name: pgroup_name.trim(),user_id:<?=$user->getId()?>,data_type:'add_pgroup'});
    }

    function edit_pgroup(pgroup_name)
    {
        if(pgroup_name.trim() == ""){
            alert("please enter a valid pgroup name");
            
            var pgroup_creator = document.querySelector("#pgroup_creator");
            pgroup_creator.querySelector(".js-pgroup-name-edit").focus();
            return;
        }

        pgroup_send_data({pgroup_name: pgroup_name.trim(),pgroup_id:EDIT_GROUP_ID,data_type:'edit_pgroup'});
    }

     
    function delete_pgroup(id)
    {
        if(!confirm("Are you sure you want to delete this project group?"))
        {
            return;
        }

        pgroup_send_data({
            user_id:'<?=$user->getId()?>',
            pgroup_id:id,
            data_type:'delete_pgroup'
        });
    }


    function show_add_pgroup(e)
    {
        var pgroup_creator = document.querySelector("#pgroup_creator");
        pgroup_creator.querySelector(".js-pgroup-name").value = "";

        if(pgroup_creator.classList.contains("hide")){
            pgroup_creator.classList.remove("hide");
            pgroup_creator.querySelector(".js-pgroup-name").focus();
        }else{

            pgroup_creator.classList.add("hide");
        }
        
    }

    function show_edit_pgroup(id,pgroup_name,e)
    {
        var pgroup_editor = document.querySelector("#pgroup_editor");
        pgroup_editor.querySelector(".js-pgroup-name-edit").value = pgroup_name;

        if(pgroup_editor.classList.contains("hide")){
            pgroup_editor.classList.remove("hide");
            pgroup_editor.querySelector(".js-pgroup-name-edit").focus();
        }else{

            pgroup_editor.classList.add("hide");
        }
        
        EDIT_GROUP_ID = id;
    }

    

    function pgroup_send_data(data)
    {
        
        var ajax = new XMLHttpRequest();
        var form = new FormData();

        form.append('data',JSON.stringify(data));

        ajax.addEventListener('readystatechange', function(){

            if(ajax.status == 200 && ajax.readyState == 4)
            {
                pgroup_handle_result(ajax.responseText);
            }
        });
        
        ajax.open("POST",get_root() + "admin_ajax.php",true)
        ajax.send(form);
    }

    function pgroup_handle_result(result)
    {
          console.log(result);
      if(result != ""){

            var obj = JSON.parse(result);
            if(obj.data_type == "add_pgroup")
            {
                alert(obj.message);
                show_add_pgroup(true);
                sessionStorage.setItem("tab-name","project-groups");
                window.location.href = window.location.href;
            }else 
            if(obj.data_type == "delete_pgroup")
            {
                alert(obj.message);
                sessionStorage.setItem("tab-name","project-groups");
                window.location.href = window.location.href;

            }else
            if(obj.data_type == "find_user_by_email")
            {
                email_autocomplete(obj.data);
            }else
            if(obj.data_type == "remove_pgroup_member")
            {
                
                MEMBER_DESTINATION.element.remove();

            }else
            if(obj.data_type == "add_pgroup_member")
            {
                if(obj.data == ""){
                    alert(obj.message);
                }else{
                GROUP_MEMBERS_EDITED = true;
                MEMBER_DESTINATION.destination.innerHTML += `<div id="usr_${MEMBER_DESTINATION.user_id}" pgroup="grp_${MEMBER_DESTINATION.pgroup_id}" user="usr_${MEMBER_DESTINATION.user_id}" class="single-member" >
                                <span>${obj.data}</span>
                                <span onclick="remove_pgroup_member(event)" style="cursor:pointer;background-color:#ccc;padding: 1px;border-radius: 50%;border:solid thin #aaa;margin:4px;display: inline-block;width:20px;height: 20px;">X</span>
                            </div>`;
                }
            }else
            if(obj.data_type == "edit_pgroup")
            {
                alert(obj.message);
                show_edit_pgroup(true);
                sessionStorage.setItem("tab-name","project-groups");
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

    function show_pgroup_members(e)
    {

        var view = e.currentTarget.parentNode.querySelector(".js-pgroup-view");
        
        //close all
        var panels = document.querySelectorAll(".js-pgroup-view");
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
            sessionStorage.setItem("tab-name","project-groups");
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
                var pgroup_id = element.getAttribute("pgroup");
                var user_id = element.getAttribute("user");
                destination.append(element);
                pgroup_send_data({
                    email:element.innerHTML,
                    user_id: user_id,
                    pgroup_id: pgroup_id,
                    data_type: 'add_pgroup_member'
                });

                GROUP_MEMBERS_EDITED = true;
            }else{
                //remove member;
                var element = destination.querySelector("#"+e.dataTransfer.getData('data'));
                var pgroup_id = element.getAttribute("pgroup");
                var user_id = element.getAttribute("user");
                source.append(element);
                pgroup_send_data({
                    email:element.innerHTML,
                    user_id: user_id,
                    pgroup_id: pgroup_id,
                    data_type: 'remove_pgroup_member'
                });

                GROUP_MEMBERS_EDITED = true;
            }
        }

        DRAG_SOURCE = "";
        DRAG_DESTINATION = "";

    }

    function remove_pgroup_member(e)
    {
        MEMBER_DESTINATION = {};

        //remove member;
        var element = e.target.parentNode;
        var pgroup_id = element.getAttribute("pgroup");
        var user_id = element.getAttribute("user");
        MEMBER_DESTINATION.element = element;

        pgroup_send_data({
            email:element.children[0].innerHTML.trim(),
            user_id: user_id,
            pgroup_id: pgroup_id,
            data_type: 'remove_pgroup_member'
        });

        GROUP_MEMBERS_EDITED = true;
    }

    function add_member_by_email(e)
    {
        MEMBER_DESTINATION = {};

        var search_text = e.target.parentNode.querySelector(".js-find-member").value.trim();
        MEMBER_DESTINATION.destination = e.target.parentNode.parentNode.parentNode.querySelector(".drag-destination");
        var pgroup_id = e.target.getAttribute("pgroup");
        var user_id = e.target.getAttribute("user");
        
        MEMBER_DESTINATION.user_id = user_id;
        MEMBER_DESTINATION.pgroup_id = pgroup_id;

        pgroup_send_data({
            email:search_text,
            user_id: user_id,
            pgroup_id: pgroup_id,
            data_type: 'add_pgroup_member'
        });

        GROUP_MEMBERS_EDITED = true;
    }

    function find_member(e)
    {
        
        var search_text = e.target.value.trim();
        search_text = search_text.toLowerCase();

        CURRENT_EVENT = e;

        pgroup_send_data({
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