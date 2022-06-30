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
<?php

    $student = isset($_GET['user']) ? $_GET['user'] : null;

?>
    <?php if($student):?>

        <?php include('statistics.user.inc.php')?>
    <?php else:?>

        <?php if($user->getFlagAdmin() == "Y"):?>

            <?php

                //get all users from selected group
                //$sql = "select * from user_group_members where group_id = '$mygroupid' && disabled = 0 ";
                //$members = $DB->run($sql);
     
            ?>
           <center><h3>Please select what to export</h3></center>
           <style>
               .mylinks img{
                width: 100px;
                margin-bottom: -20px;
               }

               .mylinks a{
                 display: inline-block;
               }

               .mylinks a div{
                    width: 180px;
                    margin: 4px;
                    border: solid thin #ccc;
                    border-radius: 5px;
                    background-color: #eee;
                    padding: 4px;
               }

               
           </style>
           <div class="mylinks text-center">
                <a href="#" onclick="window.open('../../includesVali/tpl/admin/teams/export.tpl.php?mode=queries')">
                    <div class="text-center" style="">
                        <img src="../../includesVali/tpl/admin/teams/sql.png">
                        <h3>Queries</h3>
                    </div>
                </a>

                <a href="#" onclick="window.open('../../includesVali/tpl/admin/teams/export.tpl.php?mode=errors')">
                    <div class="text-center" style="">
                        <img src="../../includesVali/tpl/admin/teams/bug.png">
                        <h3>Query Errors</h3>
                    </div>
                </a>

                <a href="#" onclick="window.open('../../includesVali/tpl/admin/teams/export.tpl.php?mode=chats')">
                    <div class="text-center" style="">
                        <img src="../../includesVali/tpl/admin/teams/chat.png">
                        <h3>Chats</h3>
                    </div>
                </a>

                <a href="#" onclick="window.open('../../includesVali/tpl/admin/teams/export.tpl.php?mode=submissions')">
                    <div class="text-center" style="">
                        <img src="../../includesVali/tpl/admin/teams/submit.png">
                        <h3>Submissions</h3>
                    </div>
                </a>

                <a href="#" onclick="window.open('../../includesVali/tpl/admin/teams/export.tpl.php')">
                    <div class="text-center" style="">
                        <img src="../../includesVali/tpl/admin/teams/google-cloud-sql-1.svg">
                        <h3>All</h3>
                    </div>
                </a>

                

            </div>
        <?php else: ?>
            Access Denied!!
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>

<script type="text/javascript">
    
 

</script>

