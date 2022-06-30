<?php
    require __DIR__ . "/../../teams/database.php";
    $DB = Database::getInstance();
    

    //collect data
    $mode = isset($_GET['mode']) ? $_GET['mode']:'all';    

     header('Content-Disposition: inline');
     header('Content-Disposition: attachment');
     header('Content-Disposition: attachment; filename="'.$mode.'_data.csv"');

     $tables = array();

     //get chats
    if($mode == 'all' || $mode == 'chats'){

        $tables[] = 'user_chat';
    }
    if($mode == 'all' || $mode == 'submissions'){

         $tables[] = 'user_submissions';
    }
    if($mode == 'all' || $mode == 'queries'){

         $tables[] = 'queries';
    }
    if($mode == 'all' || $mode == 'errors'){

         $tables[] = 'queries';
    }

    $tables = array_unique($tables);
    
    //get chats
    foreach($tables as $table){
        
        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        $read = true;
        $page = 1;

         while($read){
            
             $limit = 1000;
             $offset = ($page - 1) * $limit;

             $query = "select * from $table limit $limit offset $offset";
             if($mode == 'queries'){
                $query = "select * from $table where failed = 0 limit $limit offset $offset";
             }
             if($mode == 'errors'){
                $query = "select * from $table where failed = 1 limit $limit offset $offset";
             }
             if($mode == 'all'){
                $query = "select * from $table limit $limit offset $offset";
             }
             
             $data = $DB->run($query);

             if(is_array($data)){

                $page++;

                $columns = array_keys($data[0]);
                array_unshift($columns, 'username');
                array_unshift($columns, 'email');

                echo str_replace("user_", "", $table) . " table \r\n";
                echo implode("|", $columns);
                echo "\r\n";

                $sql = "use sqlvali_data";
                $DB->run($sql);

                 foreach ($data as $row) {
                     // code...

                    //get user email and full name
8                    array_unshift($row, $user[0]['usr_fullname']);
                    array_unshift($row, $user[0]['usr_email']);

                    echo implode("|", $row);
                    echo "\r\n";
                 }

             }else{
                $read = false;
             }
         }

        echo "\r\n";

    }


?>