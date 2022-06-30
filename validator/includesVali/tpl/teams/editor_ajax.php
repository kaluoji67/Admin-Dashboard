<?php

function strip_word_html($text, $allowed_tags = '<b><i><sup><sub><em><strong><u><br>')
{
    mb_regex_encoding('UTF-8');
    //replace MS special characters first
    $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
    $replace = array('\'', '\'', '"', '"', '-');
    $text = preg_replace($search, $replace, $text);
    //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
    //in some MS headers, some html entities are encoded and some aren't
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    //try to strip out any C style comments first, since these, embedded in html comments, seem to
    //prevent strip_tags from removing html comments (MS Word introduced combination)
    if(mb_stripos($text, '/*') !== FALSE){
        $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
    }
    //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
    //'<1' becomes '< 1'(note: somewhat application specific)
    $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
    $text = strip_tags($text, $allowed_tags);
    //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
    $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
    //strip out inline css and simplify style tags
    $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
    $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
    $text = preg_replace($search, $replace, $text);
    //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
    //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
    //some MS Style Definitions - this last bit gets rid of any leftover comments */
    $num_matches = preg_match_all("/\<!--/u", $text, $matches);
    if($num_matches){
          $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
    }
    return $text;
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{

    require __DIR__ . "/database.php";
    $DB = Database::getInstance();

    //create a database
    $sql = "create database if not exists sqlvali_query";
    $DB->run($sql);

    $sql = "use sqlvali_query";
    $DB->run($sql);

    $results = array();

    if(isset($_POST['query'])){

        $query = $_POST['query'];
        $user_id = $_POST['user_id'];
        $group_id = $_POST['group_id'];
        
        //clean the query
        $query = strip_word_html($query,"");
 
        $query = preg_replace("/<div[ a-z=\"-:;,0-9]*>/", "", $query);
        $query = preg_replace("/<span[ a-z=\"-:;,0-9]*>/", "", $query);
        $query = preg_replace("/<\/span[ a-z=\"-:;,0-9]*>/", "", $query);
        $query = trim($query);
        $query = trim($query,';');

        //split the queries

        //$query = trim(preg_replace('/[; ]{2,}+/', '', $query));
        $query = str_replace("&nbsp", " ", $query);
       // $query = trim(preg_replace('/[; ]{2,}+/', '', $query));
         
        $queries = explode(';', $query);
        $queries = array_values($queries);
        
        $executed_queries = array();

        foreach ($queries as $query) {
            # code...
            if(trim($query) != ""){
                $data = $DB->run($query);
                $results[] = $data;
                $executed_queries[] = $query;
            }
        }

        //$qnum = count($results);
        /*
        if($qnum == 1){
            echo "1 query was run:<hr>";
        }else{
            echo $qnum . " queries were run:<hr>";
        }
        */
        $num = 0;
        foreach ($results as $data) {
            # code...

            //save the query and its results
            $vars['failed'] = 0;
            $vars['result'] = $data;
            if(is_array($data)){

                $vars['result'] = "";
            }elseif(!$data || strstr($data, "SQLSTATE[HY000]")){
            }else{

                $vars['failed'] = 1;
            }

            $vars['date'] = date("Y-m-d H:i:s");
            $vars['user_id'] = $user_id;
            $vars['group_id'] = $group_id;
            $vars['query'] = $executed_queries[$num];

            $sql = "use $DB->DB_NAME";
            $DB->run($sql);

            $sq = "insert into queries (user_id,group_id,query,failed,result,date) values 
            (:user_id,:group_id,:query,:failed,:result,:date)";
            
            $DB->run($sq,$vars);

            $num++;
            $color = $num % 2 ? "#403e3e" : "none";

            echo "<div style='padding:10px;background-color:$color'>
                <span style='color:#06c3a7'>Query " . $num . " results:</span><br> ";
            //<span style='color:#e6c3a7'>Query: " . $queries[$num - 1] . " </span><br>";



            if(is_array($data)){

                $columns = array_keys($data[0]);

                echo "<table>";

                echo "<tr>";
                foreach ($columns as $column) {
                    # code...
                    echo "<th>$column</th>";
                }
                echo "</tr>";

                foreach ($data as $row) {
                    # code...
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }

                echo "</table>";
            }else{

                if(!$data || strstr($data, "SQLSTATE[HY000]")){

                    echo "Query was run successfully. Empty result set returned";

                }else{
                    echo "<span style='color:#ffd700;'>" .(str_replace("sqlvali_query", "", $data)) . "</span>";
                }
            }

            echo "</div>";
        }

        //delete the database
        $sql = "drop database sqlvali_query";
        $DB->run($sql);
    }else
    if(isset($_POST['queries'])){

        $queries = $_POST['queries'];
        $user_id = addslashes($_POST['user_id']);
        $group_id = addslashes($_POST['group_id']);
        $date = date("Y-m-d H:i:s");

        //save user saved queries
        $sql = "use $DB->DB_NAME";
        $DB->run($sql);

        //delete all existing records
        $sql = "delete from editor where user_id = '$user_id' ";
        $DB->run($sql);

        //queries to array
        $queries = json_decode($queries);

        if(is_array($queries)){

            foreach ($queries as $key => $query) {
                # code...
                $query = addslashes($query);
                $sql = "insert into editor (user_id,group_id,query,date) values ('$user_id','$group_id','$query','$date')";
                $DB->run($sql);
            }
        }
        //if(is_array($data)){

           // $sql = "update editor set queries = '$queries' where user_id = '$user_id' limit 1";
            //$DB->run($sql);
        //}else{
            //$sql = "insert into editor (user_id,queries,date) values ('$user_id','$queries','$date')";
            //$DB->run($sql);
       // }
        
    }

}
