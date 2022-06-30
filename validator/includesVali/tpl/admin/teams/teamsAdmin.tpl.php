<?php 
    require __DIR__ . "/../../teams/database.php";
    $DB = Database::getInstance();
    $sql = "use $DB->DB_NAME";
    $DB->run($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>CSS Template</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            height: 490px; /* height of the 3 columns */
            margin-bottom:4%; /* height of the 3 columns */
        }
        /* Left and right column */
        .column.side {
            min-width: 19%;
        }

        /* Middle column */
        .column.middle {
            width: 25%;
            padding-right: 5px;
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

        /* Responsive layout - makes the three columns stack on top of each other instead of next to each other */
        @media (max-width: 600px) {
            .column.side, .column.middle {
                width: 100%;
            }
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
        #notification_status_text_area_boarder{
            padding-top: 2px;
            padding-left: 2px;
            padding-right: 2px;

        }
        #header2 {
            margin-top: 29px;
            border: solid thin mintcream;
        }
        #teams_links_boarder{
            border: solid thin mintcream; padding: 2px;height: 430px;
        }

        .hide{
            display: none;
        }
 
        .tab-page{
            padding: 10px;
            background-color: #f1f1f1;
            height: auto;
            position: relative;
        }

        .button2{

            font-size: 14px;
            border:none;
            padding:10px;
            color: white;
            margin: 1px;
            text-align: center;
            cursor: pointer;
         }

        .button2 a{
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
            background-color: #258bc8;
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
 
        .orange{
            background-color: #e3af33;
        }

        .orange:hover{
            background-color: #c38b06;
        }
 
 
    </style>
</head>
<body>

<div id="">
    <div style="background-color:#ccc;margin: auto; width: 100%; font-size: 14px;display: flex;">
        <div style="flex: 1;padding:10px;background-color: #444;color: white;">Teams Admin</div>  <div style="padding:10px;text-align:right;flex:10;"><?php include(__DIR__."/../../teams/header.inc.php");?></div> 
    </div>
</div>
<br>
    <ul class="nav nav-tabs" onclick="select_tab(event)" style="cursor: pointer;">
             
        <li class="nav-item">
            <a class="nav-link" page="project-groups"  style="background-color: #eee;">Project Groups</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" page="groups" >Groups</a>
        </li>   
        <li class="nav-item">
            <a class="nav-link" page="project"  style="background-color: #eee;">Project Center</a>
        </li>
           
        <li class="nav-item">
            <a class="nav-link" page="tasks" >Tasks</a>
        </li>
    
      
    </ul>

    <div class="tab-pages">
        
        <div class="project tab-page">
            <?php require "projects.tpl.php" ?>
        </div>
        
        <div class="project-groups tab-page">
            <?php require "project-groups.tpl.php" ?>
        </div>

        <div class="tasks tab-page">
            <?php require "tasks.tpl.php" ?>
        </div>

        <div class="groups tab-page hide" >
            <?php require "groups.tpl.php" ?>
        </div>
        
      
        <br style="clear: both;">
    </div>
    <p><br></p>
 
<div class="footer">
    <p>Footer</p>
</div>

</body>
</html>

<script type="text/javascript">
    
    var tab = "project";
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

