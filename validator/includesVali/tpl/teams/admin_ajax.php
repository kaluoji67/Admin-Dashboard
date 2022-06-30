<?php

if($_SERVER['REQUEST_METHOD'] == "POST")
{

    require __DIR__ . "/database.php";
    $DB = Database::getInstance();
     
    $sql = "CREATE DATABASE IF NOT EXISTS teams01";
    $DB->run($sql);

    //make sure tables exists
    create_db($DB);

    $data = json_decode($_POST['data']);
 
    $data_type = trim($data->data_type);
    if($data_type == "add_group"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //add a group
        $arr['user_id'] = $data->user_id;
        $arr['group_id'] = Database::create_id();
        $arr['group_name'] = addslashes(ucwords($data->group_name));
        $arr['pgroup_id'] = addslashes(ucwords($data->pgroup_id));
        $arr['date'] = date("Y-m-d H:i:s");

        $sql = "insert into user_groups(user_id,group_id,group_name,pgroup_id,date) values (:user_id,:group_id,:group_name,:pgroup_id,:date)";
        $DB->run($sql,$arr);

        echo '{"message":"Group created successfully","data_type":"add_group"}';
    }else
    if($data_type == "add_pgroup"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //add a project group
        $arr['user_id'] = $data->user_id;
        $arr['pgroup_id'] = Database::create_id();
        $arr['pgroup_name'] = addslashes(ucwords($data->pgroup_name));
        $arr['date'] = date("Y-m-d H:i:s");

        $sql = "insert into user_pgroups(user_id,pgroup_id,pgroup_name,date) values (:user_id,:pgroup_id,:pgroup_name,:date)";
        $DB->run($sql,$arr);

        echo '{"message":"Project Group created successfully","data_type":"add_pgroup"}';
    }else
    if($data_type == "add_group_member" || $data_type == "remove_group_member"){
       
        $sql = "use sqlvali_data";
        $DB->run($sql);
        $email = $data->email;
        $found = false;
        $user_id = 0;

        if(trim($email) != ""){

            $sql = "select * from user where usr_email like '$email%' limit 1";
            $data2 = $DB->run($sql);
            if(is_array($data2)){
                
                $found = true;
                $user_id = $data2[0]['usr_id'];
            }else{
                $found = false;
            }
        }else{

            $found = false;
        }

        if($found){


            $sql = "use $DB->DB_NAME";
            $DB->run($sql);
            $email = trim($data->email);
     
            //check if user already belongs to another group
            $a = $DB->run("select * from user_group_members where user_id = '$user_id' && disabled = 0 limit 1");
            $already_added = false;

            if(is_array($a))
            {
                $already_added = true;
            }

            if(!$already_added || $data_type == "remove_group_member"){

                $group_id = trim(str_replace("grp_", "", $data->group_id));
                if(!is_numeric($group_id)){
                    echo '{"message":"Please select a valid group","data_type":"add_group_member","data":""}';
                    die;
                }

                $date = date("Y-m-d H:i:s");

                $u = $DB->run("select * from user_group_members where user_id = '$user_id' && group_id = '$group_id' limit 1");
                
                if(is_array($u))
                {
                    $u = $u[0];
                    $bool = $data_type == "add_group_member" ? 0 : 1;

                    $sql = "update user_group_members set disabled = $bool where id = '$u[id]' limit 1";
                    $DB->run($sql);

                    if($bool){
                        echo '{"message":"Group member removed successfully","data_type":"remove_group_member","data":"'.$email.'"}';
                    }else{
                        echo '{"message":"Group member added successfully","data_type":"add_group_member","data":"'.$email.'"}';
                    }
                }else
                {
                    $sql = "insert into user_group_members (user_id,group_id,date) values ('$user_id','$group_id','$date')";
                    $DB->run($sql);
                    echo '{"message":"Group member added successfully","data_type":"add_group_member","data":"'.$email.'"}';
                }

            }else{
                echo '{"message":"User already exists in another group","data_type":"add_group_member","data":""}';
            }

        }else{
            echo '{"message":"Could not find that email","data_type":"","data":""}';
        }

    }else
    if($data_type == "edit_group"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);
 
        $group_id = addslashes(trim($data->group_id));
        $group_name = addslashes(trim($data->group_name));
 
        $sql = "update user_groups set group_name = '$group_name' where group_id = '$group_id' limit 1";
        $DB->run($sql);
      
        echo '{"message":"Group edited successfully","data_type":"edit_group"}';
    }else
    if($data_type == "edit_pgroup"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);
 
        $pgroup_id = addslashes(trim($data->pgroup_id));
        $pgroup_name = addslashes(trim($data->pgroup_name));
 
        $sql = "update user_pgroups set pgroup_name = '$pgroup_name' where pgroup_id = '$pgroup_id' limit 1";
        $DB->run($sql);
      
        echo '{"message":"Project Group edited successfully","data_type":"edit_pgroup"}';
    }else
    if($data_type == "add_project"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //add a project
        $arr = array();
        $arr['user_id'] = $data->user_id;
        $arr['project_id'] = Database::create_id();
        $arr['project_name'] = addslashes(ucwords($data->project_name));
        $arr['task'] = addslashes($data->task);
        $arr['pgroup_id'] = addslashes($data->pgroup_id);
        $arr['date'] = date("Y-m-d H:i:s");

        $sql = "insert into user_projects(user_id,project_id,project_name,task,pgroup_id,date) values (:user_id,:project_id,:project_name,:task,:pgroup_id,:date)";
        $DB->run($sql,$arr);
        
        echo '{"message":"Project created successfully","data_type":"add_project"}';
    }else
    if($data_type == "add_task"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //add a task
        $arr = array();
        $arr['user_id'] = $data->user_id;
        $arr['task_id'] = Database::create_id();
        $arr['task_name'] = addslashes(ucwords($data->task_name));
        $arr['task'] = addslashes($data->task);
        $arr['project_id'] = addslashes($data->project_id);
        $arr['date'] = date("Y-m-d H:i:s");

        $sql = "insert into user_tasks(user_id,task_id,task_name,task,project_id,date) values (:user_id,:task_id,:task_name,:task,:project_id,:date)";
        $DB->run($sql,$arr);
        
        echo '{"message":"Task created successfully","data_type":"add_task"}';
    }else
    if($data_type == "edit_project"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //edit a project
        $arr = array();
        //$arr['user_id'] = $data->user_id;
        $arr['project_id'] = $data->project_id;
        $arr['project_name'] = addslashes(ucwords($data->project_name));
        $arr['task'] = addslashes($data->task);
        $arr['group_id'] = addslashes($data->group_id);

        $sql = "update user_projects set project_name = :project_name,task = :task,group_id = :group_id where project_id = :project_id limit 1";
        $DB->run($sql,$arr);
        
        echo '{"message":"Project edited successfully","data_type":"edit_project"}';
    }else
    if($data_type == "edit_task"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //edit a project
        $arr = array();
        //$arr['user_id'] = $data->user_id;
        $arr['task_id'] = $data->task_id;
        $arr['task_name'] = addslashes(ucwords($data->task_name));
        $arr['task'] = addslashes($data->task);
        //$arr['group_id'] = addslashes($data->group_id);

        $sql = "update user_tasks set task_name = :task_name,task = :task where task_id = :task_id limit 1";
        $DB->run($sql,$arr);
        
        echo '{"message":"Task edited successfully","data_type":"edit_task"}';
    }else
    if($data_type == "delete_project"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //delete a project
        $arr = array();
        $arr['user_id'] = $data->user_id;
        $arr['project_id'] = $data->project_id;
 
        $sql = "delete from user_projects where project_id = :project_id && user_id = :user_id limit 1";
        $DB->run($sql,$arr);
        
        echo '{"message":"Project deleted successfully","data_type":"delete_project"}';
    }else
    if($data_type == "delete_task"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //delete a project
        $arr = array();
        $arr['user_id'] = $data->user_id;
        $arr['task_id'] = $data->task_id;
 
        $sql = "delete from user_tasks where task_id = :task_id && user_id = :user_id limit 1";
        $DB->run($sql,$arr);
        
        echo '{"message":"Task deleted successfully","data_type":"delete_task"}';
    }else
    if($data_type == "find_user_by_email"){

        $sql = "use sqlvali_data";
        $DB->run($sql);
        $email = $data->email;

        if(trim($email) != ""){

            $sql = "select * from user where usr_email like '$email%' limit 1";
            $data = $DB->run($sql);
            if(is_array($data)){
                
                echo '{"data":"'.$data[0]['usr_email'].'","data_type":"find_user_by_email"}';
            }else{
                 echo '{"data":"","data_type":"find_user_by_email"}';
            }
        }else{

            echo '{"data":"","data_type":"find_user_by_email"}';
        }

    }else
    if($data_type == "delete_group"){

        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //delete a project
        $arr = array();
        //$arr['user_id'] = $data->user_id;
        $arr['group_id'] = $data->group_id;
 
        $sql = "delete from user_groups where group_id = :group_id limit 1";
        $DB->run($sql,$arr);

        $arr = array();
        $arr['group_id'] = $data->group_id;
        $sql = "delete from user_group_members where group_id = :group_id ";
        $DB->run($sql,$arr);

        echo '{"message":"Group deleted successfully","data_type":"delete_group"}';
    }




}

    function create_db($DB){
 
        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        $sql = "CREATE TABLE IF NOT EXISTS user_groups (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT DEFAULT NULL,
            group_id BIGINT DEFAULT NULL,
            pgroup_id BIGINT DEFAULT NULL,
            group_name VARCHAR(30) DEFAULT NULL,
            date DATETIME,
            INDEX group_id (group_id),
            INDEX pgroup_id (group_id),
            INDEX user_id (user_id),
            INDEX group_name (group_name)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $DB->run($sql);

        $sql = "CREATE TABLE IF NOT EXISTS user_pgroups (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT DEFAULT NULL,
            pgroup_id BIGINT DEFAULT NULL,
            pgroup_name VARCHAR(30) DEFAULT NULL,
            date DATETIME,
            INDEX pgroup_id (pgroup_id),
            INDEX user_id (user_id),
            INDEX pgroup_name (pgroup_name)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $DB->run($sql);

        
        $sql = "CREATE TABLE IF NOT EXISTS user_group_members (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT DEFAULT NULL,
            group_id BIGINT DEFAULT NULL,
            disabled TINYINT(1) DEFAULT 0,
            date DATETIME,
            INDEX group_id (group_id),
            INDEX disabled (disabled),
            INDEX user_id (user_id)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $DB->run($sql);

        $sql = "CREATE TABLE IF NOT EXISTS user_projects (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT DEFAULT 0,
            project_id BIGINT DEFAULT 0,
            pgroup_id BIGINT DEFAULT 0,
            project_name VARCHAR(60) DEFAULT NULL,
            task VARCHAR(2048) DEFAULT NULL,
            group_id BIGINT DEFAULT NULL,
            disabled TINYINT(1) DEFAULT 0,
            date DATETIME,
            INDEX project_id (project_id),
            INDEX pgroup_id (project_id),
            INDEX project_name (project_name),
            INDEX disabled (disabled),
            INDEX group_id (group_id),
            INDEX user_id (user_id)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $DB->run($sql);

        $sql = "CREATE TABLE IF NOT EXISTS user_tasks (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT DEFAULT 0,
            task_id BIGINT DEFAULT 0,
            task_name VARCHAR(60) DEFAULT NULL,
            task VARCHAR(2048) DEFAULT NULL,
            project_id BIGINT DEFAULT 0,
            disabled TINYINT(1) DEFAULT 0,
            date DATETIME,
            INDEX task_id (task_id),
            INDEX task_name (task_name),
            INDEX disabled (disabled),
            INDEX project_id (project_id),
            INDEX user_id (user_id)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $DB->run($sql);

        

        
    }

