<?php

require __DIR__ . "/database.php";
$DB = Database::getInstance();
$sql = "use $DB->DB_NAME";
$DB->run($sql);

//include("classes/post.tpl.php");
//include("classes/user_db.tpl.php");


//for post section
if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $post = new Post();
    $usr_id = $_SESSION["user"] ;
    $result = $post->create_post($_POST);
    echo"<script>document.location=
    '/sqlvali-master/sqlvali-master/publicRoot/sqlvali/index.php?action=teams/teams';
    </script>";

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

    //confirm the user belongs to a group
    $user_id = $user->getId();
    $group_id = 0;
    $belongs = false;

    $query = "select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1";
    $check = $DB->run($query);

    if(is_array($check)){

        $belongs = true;
        $group_id = $check[0]['group_id'];
        $group_name = group_name($check[0]['group_id'],$DB);

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
        /* Create three unequal columns that floats next to each other */
        .column {
            float: left;
            padding: 10px;
            margin-bottom:4%; /* height of the 3 columns */
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        /* Style the footer */
        .footer {
            background-color: mintcream;
            padding: 10px;
            text-align: center;
        }

        #header_links
        {
           
            border: solid thin mintcream;
        }
        #header2 {
            margin-top: 29px;
            border: solid thin mintcream;
        }
        #header2 {
            margin-top: 29px;
            border: solid thin mintcream;
        }
        #notification_status_text_area_boarder{
            padding-top: 2px;
            padding-left: 2px;
            padding-right: 2px;
        }

        #links{
            padding-right: 10px;
            text-align: center;
             list-style-type: none; /*prevents the bullet points from showing*/

        }

        #links div{
            width:200px;
            height:120px;
            display: inline-block;
            vertical-align: top;
            margin-bottom: 10px;
        }

        #links img{
            width:80px;
        }
 
        #teams_links_boarder{
            border: solid thin mintcream; padding: 2px;height: 430px;
        }

        #middle_textarea_boarder{
            min-height: 340px;
            border: solid thin mintcream;
            margin-top: 23px;
            padding-top: 2px;
            padding-left: 2px;
            padding-right: 2px;
        }
        #first_comment{
            height: auto;
            width: 350px;
            display: block;
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0;
        }
        #left_textArea_1{
            height: 100px;
            width: 98%;
            padding:1%;
            border:none;
        }
        #notification_status{
            border: solid thin mintcream; padding: 2px; margin-top: 23px;
        }
        #menu_button {
            display: block;width: 100%; margin: 5px; padding:7px; background-color: mintcream;
        }
        #href{
            text-decoration: none;
        }

        .button{

            font-size: 14px;
            border:none;
            padding:10px;
            color: white;
            margin: 1px;
            text-align: center;
            cursor: pointer;
         }

        .button a{
            color: white;
        }

        .icon{
            float: right;
            width: 20px;
            margin: 2px;
        }

        .green:hover{
            background-color: #0c9f8a;
        }

        .green{
            background-color: #06c3a7;
        }

        .blue{
            background-color: #3273dc;
        }

        .blue:hover{
            background-color: #2b60b5;
        }
        
        .bluelight{
            background-color: #909597;
            transition: all .5s ease;
        }

        .bluelight:hover{
            background-color: #1b5d84;
        }

        .purple{
            background-color: #ac22dd;
        }

        .purple:hover{
            background-color: #9106c3;
        }


        /* Responsive layout - makes the three columns stack on top of each other instead of next to each other */
        @media (max-width: 600px) {
            .column.side, .column.middle {
                width: 100%;
            }
        }

        @keyframes appear{
            0%{opacity: 0;transform: translateX(-100px);}
            100%{opacity: 1;transform: translateX(0px);}
        }

    </style>
</head>
<body>

<div class="">
    <!--Links column area-->
    <div class="coldumn side" style=";margin-right:1%;margin-left:5.4%;background-color: #d6dce6;">
        <div id="teams_links_boarder" >
            <div id="header_links"><h2>Teams</h2>  
                    <?php if($belongs || ($user->getFlagAdmin() == "Y")):?>
                    <span onclick="show_chat(event)" style="z-index:2;cursor: pointer;float: right;position: absolute;right:10px;top:10px;margin-right: 30px;">
                        Chat <br><svg fill="#0c7bb3" width="24" height="24" viewBox="0 0 24 24"><path d="M0 1v16.981h4v5.019l7-5.019h13v-16.981h-24zm13 12h-8v-1h8v1zm6-3h-14v-1h14v1zm0-3h-14v-1h14v1z"/></svg>
                    </span> 
                    <?php endif;?>  
                 <div class="js-chat-holder" style="max-width:500px;animation: appear 0.5s ease; display:none;position: absolute;right:0px;top:10px;margin-right: 30px;">
                     <br><br><br>
                    <?php require "chat.tpl.php" ?>
                </div>
            </div>
            <div id="links">
            <?php if($belongs || ($user->getFlagAdmin() == "Y")):?>
                <br>
                <!--
               <a id="href" href="index.php?action=teams/projects">
                    <div class="button bluelight" >
                         <img src="../../includesVali/tpl/teams/projects.png">
                        <?php echo $l->getString('project'); ?>s
                        
                    </div></a>
                -->
 
               <a id="href" href="index.php?action=teams/groupWiki">
                    <div class="button bluelight" >
                        <img src="../../includesVali/tpl/teams/group.png">
                        <br>Projects / GroupWiki
                        
                    </div></a>
                

               <a id="href" href="index.php?action=teams/editor">
                    <div class="button bluelight" >
                        <img src="../../includesVali/tpl/teams/code.png">
                        <br><?php echo $l->getString('editor');?>
                        
                    </div></a>
                <!--
                <a id="href" href="index.php?action=teams/submissions">
                    <div class="button bluelight" >
                        <img src="../../includesVali/tpl/teams/paper-icon.png">
                        Submission
                        
                    </div></a> 
                -->
               
                
                <?php if($user->getFlagAdmin() == "Y"):?>
                <a id="href" href="index.php?action=admin/teams/statistics">
                    <div class="button bluelight" >
                        <img src="../../includesVali/tpl/teams/SEO-icon.png">
                        <br>Statistics
                        
                    </div></a>
                
                <?php endif; ?>

                <?php if($user->getFlagAdmin() == "Y"):?>
                <a id="href" href="index.php?action=admin/teams/teamsAdmin">
                    <div class="button bluelight" >
                        <img src="../../includesVali/tpl/teams/admin.png">
                        <br>Admin
                        
                    </div></a>
                
                <?php endif; ?>

                
                <i></i>
            <?php endif;?>
            <?php if(!$belongs):?>
                 <a id="href" href="index.php?action=teams/joingroup">
                    <div class="button bluelight" >
                        <img src="../../includesVali/tpl/teams/user-group-new-icon.png">
                        <br>Join Group
                        
                    </div></a>

            <?php endif;?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    var state = "off";
    if(sessionStorage.getItem("chat-state")){
        state = sessionStorage.getItem("chat-state");
        if(state == 'on'){
            var chat_holder = document.querySelector('.js-chat-holder');
            var chat_text = document.querySelector('.js-post');
            chat_holder.style.display = 'block';
            chat_text.focus();
        }
    }

    function show_chat(e){

        var chat_holder = e.currentTarget.parentNode.querySelector('.js-chat-holder');
        var chat_text = e.currentTarget.parentNode.querySelector('.js-post');
        
        if(chat_holder.style.display == 'block'){
            chat_holder.style.display = 'none';
            sessionStorage.setItem("chat-state","off");
        }else{
            chat_holder.style.display = 'block';
            chat_text.focus();
            sessionStorage.setItem("chat-state","on");
        }
    }
</script>
    <!--POST column area--><!--
    <div class="column middle" style="background-color: #f1f1f1;;margin-right:1%;margin-left:1%;">
        <div id="header2" style="display: flex; padding-left: 4px" >
            <!--Temporary Image upload--><!--
            <form action="">
                <p><input type="file"  accept="image/*" name="image" id="file"  onchange="loadFile(event)" style="display: none;"></p>
                <p><label for="file" style="cursor: pointer;color: #0000cc">Upload Image</label></p>
                <p><img id="output" width="344px" /></p>

                <script>
                    var loadFile = function(event) {
                        var image = document.getElementById('output');
                        image.src = URL.createObjectURL(event.target.files[0]);
                    };
                </script>
            </form>

        </div>

        <div id="middle_textarea_boarder">
            <form method="post">
                <?php
                //include("user_post.tpl.php");

                ?>
            </form>
            <form method="post">
                <label for="">
                    <textarea id="first_comment" name="post"></textarea>
                    <input type="submit" value="Post / Comment">
                </label>

            </form>

        </div>
    </div>

    <!--notification / status column area--><!--
    <div class="column side" style="background-color: #f1f1f1;">
        <div id="header2"><h4>Notification</h4> </div>
        <div id="notification_status">
            <div id="notification_status_text_area_boarder">
                <label>
                    <textarea id="left_textArea_1" ></textarea>
                </label>
            </div>
        </div>
        <div id="header2"><h4>Status</h4> </div>
        <div id="notification_status">
            <div id="notification_status_text_area_boarder">
                <label>
                    <textarea id="left_textArea_1" ></textarea>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>Group Chat pop up here</p>
</div>
-->
</body>
</html>

