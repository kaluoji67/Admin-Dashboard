<?php

if($_SERVER['REQUEST_METHOD'] == "POST")
{
 
    require __DIR__ . "/database.php";
    require __DIR__ . "/image_class.php";
    $DB = Database::getInstance();
 
    //make sure tables exists
    create_db($DB);

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

        $sql = "insert into user_chat(user_id,post_id,project_id,post,date,file,group_id) values (:user_id,:post_id,:project_id,:post,:date,:file,:group_id)";
        $DB->run($sql,$arr);

        echo '{"message":"Post created successfully","data_type":"add_post"}';
    }else
    if($_POST['data_type'] == "delete_post"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        $arr = array();
        $arr['user_id'] = addslashes($_POST['user_id']);
        $arr['project_id'] = addslashes($_POST['project_id']);
        $arr['post_id'] = addslashes($_POST['post_id']);

        $sql = "delete from user_chat where post_id = :post_id && project_id = :project_id && user_id = :user_id limit 1";
        $DB->run($sql,$arr);

        echo '{"message":"Post deleted successfully","data_type":"delete_post"}';
    }else
    if($_POST['data_type'] == "read"){

        $limit = 10;
        $page_number = isset($_GET['chat_page']) ? (int)$_GET['chat_page']: 1;
        $offset = ($page_number - 1) * $limit;

       //$user_id = addslashes($_POST['user_id']);
        $arr['group_id'] = addslashes($_POST['group_id']);
        
        $sql = "select * from user_chat where group_id = :group_id order by id desc limit $limit offset $offset";
 
        $data = $DB->run($sql,$arr);

        //get user names
        if(is_array($data))
        {
            $sql = "use sqlvali_data";
            $DB->run($sql);

            foreach ($data as $key => $row) {
                // code...
                $arr = array();
                $arr['usr_id'] = $row['user_id'];
                $sql = "select usr_fullname from user where usr_id = :usr_id limit 1";
                $check = $DB->run($sql,$arr);

                if(is_array($check)){
                    $data[$key]['full_name'] = $check[0]['usr_fullname'];
                }
            }
        }
        $a['data'] = $data;
        $a['data_type'] = "read";

        echo json_encode($a);
       
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
           
            $sql = "update user_chat set post = :post where post_id = :post_id && project_id = :project_id && group_id = :group_id limit 1";
        }else{
            $sql = "update user_chat set post = :post, file = :file  where post_id = :post_id && project_id = :project_id && group_id = :group_id limit 1";
        }

        $DB->run($sql,$arr);

        echo '{"message":"Post edited successfully","data_type":"edit_post"}';
    }

}

    function create_db($DB){
     

        $sql = "CREATE DATABASE IF NOT EXISTS teams01";
        $DB->run($sql);

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        $sql = "CREATE TABLE IF NOT EXISTS user_chat (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            replies INT DEFAULT 0,
            parent BIGINT DEFAULT 0,
            post_id BIGINT DEFAULT 0,
            user_id BIGINT DEFAULT 0,
            project_id BIGINT DEFAULT 0,
            group_id BIGINT DEFAULT 0,
            post TEXT DEFAULT NULL,
            file VARCHAR(500) DEFAULT NULL,
            date DATETIME,
            INDEX post_id (post_id),
            INDEX group_id (group_id),
            INDEX replies (replies),
            INDEX parent (parent),
            INDEX project_id (project_id),
            INDEX user_id (user_id)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $DB->run($sql);
        
    }

