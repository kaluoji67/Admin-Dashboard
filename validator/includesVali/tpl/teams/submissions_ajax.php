<?php

if($_SERVER['REQUEST_METHOD'] == "POST")
{
//echo "<pre>";
//print_r($_POST);
//print_r($_FILES);
//echo "</pre>";
//die;
    require __DIR__ . "/database.php";
    require __DIR__ . "/image_class.php";
    $DB = Database::getInstance();
 
    if($_POST['data_type'] == "add_post"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //add a post
        $arr = array();
        $arr['user_id'] = addslashes($_POST['user_id']);
        $arr['post_id'] = Database::create_id();
        $arr['project_id'] = addslashes($_POST['project_id']);
        $arr['post'] = addslashes($_POST['post']);
        $arr['date'] = date("Y-m-d H:i:s");
        $arr['group_id'] = addslashes($_POST['group_id']);
        $arr['task_id'] = addslashes($_POST['task_id']);

        $arr['file'] = "";

        
        //move file if any
        $folder = "uploads/";
        if(!file_exists($folder)){
            mkdir($folder,0777,true);
        }
        
        foreach ($_FILES as $FILE) {
            # code...
            if($FILE['error'] == 0 && ($FILE['type'] == "image/jpeg" || $FILE['type'] == "image/png")){

                $ext = pathinfo($FILE['name'],PATHINFO_EXTENSION);

                $destination = $folder . Image::generate_filename(60) .".".strtolower($ext);
                move_uploaded_file($FILE['tmp_name'], $destination);
                /*Image::resize_image($destination,$destination,1500,1500);*/
                $arr['file'] = $destination;
            }
        }
 
        $sql = "insert into user_submissions(user_id,post_id,project_id,post,date,file,group_id,task_id) values (:user_id,:post_id,:project_id,:post,:date,:file,:group_id,:task_id)";
        $DB->run($sql,$arr);

        //record as submitted
        $arr2['user_id'] = $arr['user_id'];
        $arr2['project_id'] = $arr['project_id'];
        $arr2['group_id'] = $arr['group_id'];
        $arr2['task_id'] = $arr['task_id'];

        $sql = "insert into submitted_projects (user_id,project_id,group_id,task_id) values (:user_id,:project_id,:group_id,:task_id)";
        $DB->run($sql,$arr2);

        echo '{"message":"Submitted successfully","data_type":"add_post"}';
    }else
    if($_POST['data_type'] == "delete_post"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        $arr = array();
        $arr['user_id'] = addslashes($_POST['user_id']);
        $arr['project_id'] = addslashes($_POST['project_id']);
        $arr['post_id'] = addslashes($_POST['post_id']);

        $sql = "delete from user_submissions where post_id = :post_id && project_id = :project_id && user_id = :user_id limit 1";
        $DB->run($sql,$arr);

        echo '{"message":"Submitted uccessfully","data_type":"delete_post"}';
    }else
    if($_POST['data_type'] == "edit_post"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //add a post
        $arr = array();
        $user_id = addslashes($_POST['user_id']);
        $arr['post_id'] = addslashes($_POST['post_id']);
        $arr['project_id'] = addslashes($_POST['project_id']);
        $arr['post'] = addslashes($_POST['post']);
        $arr['group_id'] = addslashes($_POST['group_id']);

        //move file if any
        $folder = "uploads/";
        if(!file_exists($folder)){
            mkdir($folder,0777,true);
        }
        
        foreach ($_FILES as $FILE) {
            # code...
            if($FILE['error'] == 0 && ($FILE['type'] == "image/jpeg" || $FILE['type'] == "image/png")){

                $ext = pathinfo($FILE['name'],PATHINFO_EXTENSION);

                $destination = $folder . Image::generate_filename(60) .".".strtolower($ext);
                move_uploaded_file($FILE['tmp_name'], $destination);
                /*Image::resize_image($destination,$destination,1500,1500);*/
                $arr['file'] = $destination;
            }
        }

        if(!isset($arr['file'])){
           
            $sql = "update user_submissions set post = :post where post_id = :post_id && project_id = :project_id && group_id = :group_id limit 1";
        }else{
            $sql = "update user_submissions set post = :post, file = :file  where post_id = :post_id && project_id = :project_id && group_id = :group_id limit 1";
        }

        $DB->run($sql,$arr);

        echo '{"message":"Successfully edited","data_type":"edit_post"}';
    }

}

 