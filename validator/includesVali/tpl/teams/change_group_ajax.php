<?php
 
    require __DIR__ . "/database.php";
    $DB = Database::getInstance();
    
    //set database
    $sql = "use $DB->DB_NAME";
    $DB->run($sql);

     if(isset($_POST['group_id'])){

        $user_id = addslashes($_POST['user_id']);
        $group_id = addslashes($_POST['group_id']);

        //first disable all groups i belong to
        $sql = "update user_group_members set disabled = 1 where user_id = '$user_id' ";
        $DB->run($sql);

        //check if a record already exists for this group
        $sql = "select * from user_group_members where user_id = '$user_id' && group_id = '$group_id' limit 1 ";
        $check = $DB->run($sql);

        if(is_array($check))
        {
            //enable the row
            $id = $check[0]['id'];
            $sql = "update user_group_members set disabled = 0 where id = '$id' ";
            $DB->run($sql);

        }else
        {
            $arr['date'] = date("Y-m-d H:i:s");
            $arr['user_id'] = $user_id;
            $arr['group_id'] = $group_id;

            $sql = "insert into user_group_members (user_id,group_id,date) values (:user_id,:group_id,:date)";
            $DB->run($sql,$arr);
        }
   
    }

