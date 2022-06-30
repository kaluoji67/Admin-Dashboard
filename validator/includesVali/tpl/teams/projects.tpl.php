<?php 
    require __DIR__ . "/database.php";
    $DB = Database::getInstance();
    $sql = "use $DB->DB_NAME";
    $DB->run($sql);
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
        text-align: center;
    }

    #posdt_area{
        background-color: ghostwhite; flex: 2.5; padding: 20px;
    }
    #white_board{
        border: solid thin #aaa; padding: 10px;
    }

    #single_project{

        border: solid thin #ccc;
        box-shadow: 0px 0px 10px #ccc;
        margin:10px;
        padding: 10px;
        min-height: 250px;
        width: 250px;
        display: inline-block;
        vertical-align: top;
        border-radius: 5px;
    }

</style>
<br>
<body style="font-family: tahoma">
<div id="ToDo">
    <div style="margin: auto; width: 800px; font-size: 30px;">
        Project Listing &nbsp &nbsp
    </div>
</div>

<!--Cover Area-->
<div id="cover_Area">

    <!--Below Cover Area-->
    <div id="Noti_Work" style="padding: 10px;">

        <!--show all projects from all groups i belong to-->
        <?php 
 
            //confirm the user belongs to a group
            $user_id = $user->getId();

            $query = "select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1";
            $check = $DB->run($query);

            $myprojects = false;
            if(is_array($check)){

                $group_id = $check[0]['group_id'];
                //show only the current and past projects
                $sql = "select * from submitted_projects where group_id = '$group_id' ";
                $done = $DB->run($sql);

                if(is_array($done)){

                    $list = array_column($done, "project_id");
                    $list = "'" . implode("','", $list) . "'";

                    //query all submitted projects
                    $sql = "select * from user_projects where project_id in ($list) order by id desc";
                    $myprojects = $DB->run($sql);

                    //add current project
                    $sql = "select * from user_projects where project_id not in ($list) order by id asc limit 1";
                    $current = $DB->run($sql);
                    if(is_array($current)){

                        array_unshift($myprojects, $current[0]);
                        
                    }

                }else{

                    //no submitted projects. load only first project
                    $sql = "select * from user_projects order by id asc limit 1";
                    $myprojects = $DB->run($sql);
                }

            }
         
        ?>

        <?php if(is_array($myprojects)): ?>
            <?php foreach($myprojects as $project): ?>
                <a  href="index.php?action=teams/project&prj=<?=$project['project_id']?>" >
                    <div id="single_project" >
                        <b>Project</b><br><br>
                        <table class="table">
                            <tr><th>ID:</th><td style="text-align: left;"><?=$project['id']?></td></tr>
                            <tr><th>Title:</th><td style="text-align: left;"><?=$project['project_name']?></td></tr>
                            <tr><th>Task:</th><td style="text-align: left;"><?=$project['task']?></td></tr>
                        </table>
                    </div>
                </a>

            <?php endforeach;?>
        <?php endif;?>
    </div>


</div>
</body>
</html>

<!--
todo

1. the project should appear here. the instructors add the project and it appeasr here.
   then a notification appear in the main page about the new information in the project page
   also i think a notification page is needed in the admin side. 

-->
