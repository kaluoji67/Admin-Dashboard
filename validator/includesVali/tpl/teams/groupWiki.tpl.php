<?php

require __DIR__ . "/database.php";
$DB = Database::getInstance();


//functions
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


    //select db
    $sql = "use $DB->DB_NAME";
    $DB->run($sql);

    //read all saved queries
    $user_id = $user->getId();

    //check which group i belong to
    //check if user belongs to a group
    $sql = "select * from user_group_members where user_id = '$user_id' && disabled = 0  limit 1";
    $mygroup = $DB->run($sql);

    if(is_array($mygroup))
    {
        $mygroupid = $mygroup[0]['group_id'];
        $group_id = $mygroup[0]['group_id'];

        //get queries
        $sql = "select * from editor where group_id = '$mygroupid' ";
        $myqueries = $DB->run($sql);

        //get images
        //$posts = $DB->run("select * from user_posts where project_id = '$myproject[project_id]' && group_id = '$group_id' order by id desc limit 100"); 


        //get submissions
        $submissions = $DB->run("select * from user_submissions where group_id = '$group_id' order by id desc limit 100");
        

    }

   

?>

<html lang="">
<head>
    <title> Teams | Profile | Group Wiki </title>
</head>

<style type="text/css">

    #Group_Wiki{
        height: 50px; background-color: #d3d3d3;color: gray;
    }
    #search_box{
        width: 400px; height: 29px; border-radius: 5px; border: none; padding: 4px;
        background-image: ;
    }
    #cover_Area{
        width: 800px; margin: auto; background-color: whitesmoke; min-height: 500px;
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
<div id="">
    <div style="background-color:#ccc;margin: auto; width: 100%; font-size: 14px;display: flex;">
        <div style="flex: 1;padding:10px;background-color: #444;color: white;">Group Wiki</div>  <div style="padding:10px;text-align:right;flex:10;"><?php include("header.inc.php");?></div> 
    </div>
</div>

<!--Cover Area-->
<div id="cover_Area" style="display: flex;">

    <!--Below Cover Area-->
    <div id="Noti_Work" style="dispdlay: flex; padding: 8px;flex: 1.3;">
     <!--saved queries top area-->

        <ul class="nav nav-tabs" onclick="select_tab(event)" style="cursor: pointer;">
      
            <li class="nav-item">
            <a class="nav-link" page="projects"  style="background-color: #eee;">Projects</a>
            </li>

            <li class="nav-item">
            <a class="nav-link" page="queries"  style="background-color: #eee;">Queries</a>
            </li>
            
            <li class="nav-item">
            <a class="nav-link" page="submission" >Task Submissions</a>
            </li>
       
        </ul>
        <div class="tab-pages">

            <div class="submission tab-page hide" >
               
                <?php require "wiki.submissions.inc.php" ?>
            </div>
                    
            <div class="projects tab-page">
                <?php require "wiki.projects.inc.php" ?>
            </div>
            
            <div class="queries tab-page">
                <?php require "wiki.queries.inc.php" ?>
            </div>
      
          
            <br style="clear: both;">
        </div>

            
    <!--end saved queries top area-->

    </div>
    <div style="flex: 1;">
        <?php require "chat.tpl.php" ?>
    </div>


</div>
</body>
</html>

<script type="text/javascript">
    
    var tab = "projects";
    if(sessionStorage.getItem("tab-name")){
        tab = sessionStorage.getItem("tab-name");
    }
    
    display_tab(tab);

    function select_tab(e)
    {
        var page = e.target.getAttribute("page");
        display_tab(page);
    }

    function display_tab(page)
    {

        var links = document.querySelector(".nav-tabs").querySelectorAll("A");
        var tabPages = document.querySelector(".tab-pages").querySelectorAll(".tab-page");
        var currentTab = null;
        var currentLink = null;

        //hide all tab pages
        for (var i = tabPages.length - 1; i >= 0; i--) {
            tabPages[i].classList.add("hide");
            if(tabPages[i].classList.contains(page)){
                currentTab = tabPages[i];
            }
        }

        //clear links background color
        for (var i = links.length - 1; i >= 0; i--) {
            links[i].style.backgroundColor = "";
            if(links[i].getAttribute("page") == page){
                currentLink = links[i];
            }
        }
        //show the one clicked
        if(currentTab){
            currentTab.classList.remove("hide");
            currentLink.style.backgroundColor = "#eee";
            sessionStorage.setItem("tab-name",page);
        }else{
            
            currentTab = tabPages[0];
            currentLink = links[0];
            currentTab.classList.remove("hide");
            currentLink.style.backgroundColor = "#eee";
            
        }
    }


</script>


<!--
todo

1. to create a wiki like system so the students can develop knowledge

-->