<?php
$rootDirectory = "./../../includesVali";
require_once __DIR__ .$rootDirectory. '/include/config.inc.php';
require_once __DIR__ .$rootDirectory. '/classes/db/PDODbConnection.class.php';
require_once __DIR__ .$rootDirectory. '/classes/db/PostgreSQLConnection.class.php';
require_once __DIR__ .$rootDirectory. '/classes/db/MySQLiConnection.class.php';
require_once __DIR__ . $rootDirectory;
require_once __DIR__ .$rootDirectory. '/classes/EvalModels/EvalQuery.class.php';
require_once __DIR__ .$rootDirectory. '/classes/EvalModels/Questionnaire.class.php';
require_once __DIR__ .$rootDirectory. '/classes/EvalModels/trackingLog.class.php';
require_once __DIR__ .$rootDirectory. '/classes/model/User.class.php';
require_once __DIR__ .$rootDirectory. '/classes/localizer/Localizer.class.php';
require_once __DIR__ .$rootDirectory. '/classes/localizer/FileLocalizer.class.php';

//security check whether the side is loaded by a logged in admin
session_start();
if(array_key_exists("user", $_SESSION)) {
    $user = unserialize($_SESSION["user"]);
}
if(!isset($user) || $user->getFlagAdmin() != 'Y') {
    echo 'access_denied';
    die();
}

if (isset ($_POST["type"]) AND $_POST["type"] != NULL)
{
    //Establish Database Connection
    $dbEval = db_connection(SQLVALI_DB_USERNAME,SQLVALI_DB_PASSWORD,SQLVALI_DB_DBNAMEEVAL);
    if ($_POST["type"]=="quest") {
        $l = new FileLocalizer();
        $languages = $l->getSupportedLanguages();
        if (!(isset ($_POST["lang"]) AND $_POST["lang"] != NULL AND in_array($_POST["lang"],$languages) AND
            isset ($_POST["id"]) AND $_POST["id"] != NULL))
            die();
        else
            $lang =$_POST["lang"];
        $QID = $_POST["id"];


        //Prepare file Export
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="RawDataQ' . $QID .$lang.'_Export.csv"');

        $fp = fopen('php://output', 'wb');
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));//Set file to UTF-8

        $questExport = Questionnaire::getByIDaLanguage($QID, $lang);
        //Export Header
        $columnOrder = array();
        $row = array();
        //Include user and timestamp
        $row[] = "User";
        $row[] = "trial";
        $row[] = "Timestamp";
        //Get all tasks and write their numbers as header
        $tasks = $questExport->getAllTasksWithItems();
        foreach ($tasks as $task) {
            foreach ($task["item"] as $item) {
                $row[] = $lang . ":" . $task["TaskNum"] . "." . $item["INum"];
                $columnOrder[] = $task["TaskNum"] * 100 + $item["INum"];
            }
        }
        fputcsv($fp, $row, "|");
        //Export Answers
        $where = "Q_id = ? AND Q_language = ?";
        $answers = $questExport->getGeneralConditionedJoined(array(Questionnaire::getAnswersTableName()), $questExport->getAnswersColums(), $where, array($QID, $lang), array("UserID","trial", "TaskNum", "INum"));
        $columnCounter = count($columnOrder) + 1;
        $row = array();
        foreach ($answers as $answer) {
            //One User is succesfully added - go on with the next one and reset the columns
            if ($columnCounter >= count($columnOrder)) {
                $columnCounter = 0;
                fputcsv($fp, $row, "|");
                $row = array();
                //Start with the user and timestamp once at the beginning
                $row[] = $answer["UserID"];
                $row[] = $answer["trial"];
                $row[] = $answer["ts"];
            }

            //Make sure that all columns are filled at the right position and skip non-present columns with NULL
            while ($columnCounter < count($columnOrder) and (($answer["TaskNum"] * 100 + $answer["INum"]) != $columnOrder[$columnCounter])){
                $columnCounter++;
                $row[] = "NULL";
                //Check whether it was the last element in the line that was NULL - if so the current answer is already from the next student
            }
            if ($columnCounter == count($columnOrder)) {
                $columnCounter = 0;
                fputcsv($fp, $row, "|");
                $row = array();
                //Start with the user and timestamp once at the beginning
                $row[] = $answer["UserID"];
                $row[] = $answer["trial"];
                $row[] = $answer["ts"];
            }

            //answer is now at the right position - fill it in
            if ($columnCounter < count($columnOrder) and (($answer["TaskNum"] * 100 + $answer["INum"]) == $columnOrder[$columnCounter])) {
                //Add result to the file and remove various unnecessary characters that may appear
                $val = rtrim(ltrim(preg_replace("/\r|\n/", "", str_replace('"', '', $answer["result"]))));
                if (strlen($val) > 0)
                    $row[] = $val;
                else
                    $row[] = "No answer";
                }

            $columnCounter++;

        }
        //Fill in the last entry
        fputcsv($fp, $row, "|");

        //Finalize export
        fclose($fp);
    }else if ($_POST["type"] == "task"){
        //Iterate the transmitted Post-variables to determine the Export Ids
        $taskIDs = array();
        $taskGroupCounter = 0;//the number of taskgroups displayed on the evaladmin tpl side
        $taskCounter = 0;//the number of tasks displayed on the evaladmin tpl side relative to the task group
        //Loop through all transmitted potentially checked tasks
        while(isset($_POST["tskId".$taskGroupCounter.'_'.$taskCounter]) OR isset($_POST["tskId".($taskGroupCounter+1).'_0'])){
            //Check whether we have the next task in the current taskgroup or the first task of the next taskgroup
            if (isset($_POST["tskId".$taskGroupCounter.'_'.$taskCounter])) {
                //Check whether the checkbox was checked for this task and if the ID is a valid numeric one
                if (isset($_POST["tskC" . $taskGroupCounter . '_' . $taskCounter]) AND
                    is_numeric($_POST["tskId" . $taskGroupCounter . '_' . $taskCounter])) {
                    //Add the ID to the output array
                    $taskIDs[] = $_POST["tskId" . $taskGroupCounter . '_' . $taskCounter];
                }
            }else{
                $taskGroupCounter +=1;
                $taskCounter = -1;//-1 because we add 1 to all in the next step
            }
            $taskCounter +=1;
        }

        //Prepare file Export
        $date = new DateTime();
        $date = date('m/d/Y', time());
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="TasksExport_'.$date.'.csv"');
        $fp = fopen('php://output', 'wb');
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));//Set file to UTF-8

        // creates a string containing ?,?,?
        $clause = implode(',', array_fill(0, count($taskIDs), '?'));
        $queries = EvalQuery::getGeneralConditionedJoined(array(EvalQuery::getTableName()),
            EvalQuery::getColumns(),"eq_taskid IN (".$clause.")",$taskIDs,array("eq_taskid,eq_UserID,eq_ts"));
        $row = ["ID","UserID","UserQuery","errorClasses","taskid","timestamp"];
        fputcsv($fp, $row, "|");

        foreach ($queries as $query){
            $queryConv = str_replace(array("\n","\r"),'',$query);
            fputcsv($fp, $queryConv, "|");
        }

        //Finalize export
        fclose($fp);
    } else if ($_POST["type"] == "tts"){
        $entries = trackingLog::getByCondition("tl_semester = ?",array($_POST["semester"]),array("tl_user","tl_timestamp"));

        //Prepare file Export
        $date = new DateTime();
        $date = date('m/d/Y', time());
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="TTSRawExport_'.$date.'.csv"');
        $fp = fopen('php://output', 'wb');
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));//Set file to UTF-8

        $row = array("tts_Id","EvalUserId","timestamp","action","action supplement");
        fputcsv($fp, $row, "|");

        foreach ($entries as $e){
            $row = array($e->getId(),$e->getUser(),$e->getTimestamp(),$e->getActioncode(),$e->getActionsupp() == NULL ? "-" : $e->getActionsupp());
            fputcsv($fp, $row, "|");
        }
        //Finalize export
        fclose($fp);
    }
    else if ($_POST["type"] == "ttsC"){
        $entries = trackingLog::getByCondition("tl_semester = ?",array($_POST["semester"]),array("tl_user","tl_timestamp"));

        //Prepare file Export
        $date = new DateTime();
        $date = date('m/d/Y', time());
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="TTSCalculatedExport_'.$date.'.csv"');
        $fp = fopen('php://output', 'wb');
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));//Set file to UTF-8

        $row = array("entry_Id","EvalUserId","action","timeTaken(seconds)","action supplement");
        fputcsv($fp, $row, "|");

        $lastUser = null;
        $lastAction = null;
        $lastTimeStamp = null;
        $lastActionSupp = null;
        $counter = 1;
        $lastTs = null;
        /*-- For logging purposes
         * foreach ($entries as $e){
            echo $e->getUser()." : ".$e->getActioncode()." - ".$e->getActionsupp();
            if ($lastTs !=null)
                echo " - ".(strtotime($e->getTimestamp()) - strtotime($lastTs))."s";
            $lastTs = $e->getTimestamp();
            echo "<br><br>";
        }
        echo "<br><br><br>";*/
        foreach ($entries as $e){
            if ($lastTimeStamp == null || $lastUser != $e->getUser()){
                if ($lastUser != $e->getUser() && $lastUser != null)
                {
                    //Display last action of the user
                    $row = array($counter, $e->getUser(), "", "-",$e->getActionsupp() == NULL ? "-" : $e->getActionsupp());
                    switch ($lastAction){
                        case 1:
                            $row[2] = "browsed and exited website";
                            break;
                        case 2:
                        case 3:
                        case 4:
                            $row[2] = "viewed task and exited website";
                            break;
                        case 5:
                        case 7:
                            $row[2] = "viewed questionnaire and exited website";
                            break;
                        case 6:
                            $row[2] = "finished questionnaire and exited website";
                            break;
                    }
                    $counter++;
                    $lastUser = $e->getUser();
                    fputcsv($fp, $row, "|");
                }
                //Fresh start to export
                $lastUser = $e->getUser();
                $lastAction = $e->getActioncode();
                $lastTimeStamp = $e->getTimestamp();
                $lastActionSupp = $e->getActionsupp() == NULL ? "-" : $e->getActionsupp();
            }
            else {
                //Calculate Time
                $time = (strtotime($e->getTimestamp()) - strtotime($lastTimeStamp));
                $row = array($counter, $e->getUser(), "", $time,$e->getActionsupp() == NULL ? "-" : $e->getActionsupp());
                if ($time > 1) {
                    if ($lastAction == $e->getActioncode() && $lastAction != 4)
                        $row[2] = "0;continued action";
                    else {
                        switch ($lastAction) {
                            case 0:
                                switch ($e->getActioncode()) {
                                    case 1://
                                        $row[2] = "2;started browsing tasks";
                                        break;
                                    case 2://
                                        $row[2] = "3;directly selected a new task";
                                        break;
                                    case 3://
                                        $row[2] = "4;directly revisited a task";
                                        break;
                                    case 5://
                                        $row[2] = "5;browsed and started a questionnaire";
                                        break;
                                    case 7://
                                        $row[2] = "18;browsed and revisited a selfcheck";
                                        break;
                                    case 8://
                                        $row[2] = "19;browsed and viewed account";
                                        break;
                                    default:
                                        $row[2] = "magically appeared - shouldn't be able to appear";
                                        break;
                                }
                                break;
                            case 1://viewTasks to
                                switch ($e->getActioncode()) {
                                    case 2://viewTasks to specific new viewTask
                                        $row[2] = "6;browsed and selected a new task";
                                        break;
                                    case 3://viewTasks to specific old viewTask
                                        $row[2] = "7;browsed and revisited a task";
                                        break;
                                    case 5://viewTasks to specific old viewTask
                                        $row[2] = "8;browsed and started a questionnaire";
                                        break;
                                    case 7://
                                        $row[2] = "18;browsed and revisited a selfcheck";
                                        break;
                                    default://viewTasks to abort and visiting another site
                                        $row[2] = "9;browsed and aborted";
                                        break;
                                }
                                break;
                            case 2://viewTask for first time to
                                switch ($e->getActioncode()) {
                                    case 1://returning to browsing
                                        $row[2] = "10;returned to browsing";
                                        break;
                                    case 4://submit a solution for the task
                                        $row[2] = "11;submitted first solution for a task";
                                        break;
                                    default://aborted task
                                        $row[2] = "12;task aborted without submission";
                                        break;
                                }
                                break;
                            case 3:
                                switch ($e->getActioncode()) {
                                    case 1://returning to browsing
                                        $row[2] = "10;returned to browsing";
                                        break;
                                    case 4://submit a new solution to a task
                                        $row[2] = "13;submitted solution for a already solved task";
                                        break;
                                    default://aborted task
                                        $row[2] = "14;task aborted without change";
                                        break;
                                }
                                break;
                            case 4:
                                switch ($e->getActioncode()) {
                                    case 1://returning to browsing
                                        $row[2] = "10;returned to browsing";
                                        break;
                                    case 4://submit a solution for the task
                                        $row[2] = "13;submitted another solution";
                                        break;
                                    default://aborted task
                                        $row[2] = "14;task aborted";
                                        break;
                                }
                                break;
                            case 5:
                                switch ($e->getActioncode()) {
                                    case 1://returning to browsing
                                        $row[2] = "10;returned to browsing";
                                        break;
                                    case 2://
                                        $row[2] = "3;directly selected a new task";
                                        break;
                                    case 3://
                                        $row[2] = "4;directly revisited a task";
                                        break;
                                    case 6://submit a solution for the task
                                        $row[2] = "15;finished questionnaire";
                                        break;
                                    default://aborted questionnaire
                                        $row[2] = "16;aborted questionnaire";
                                        break;
                                }
                                break;
                            case 6:
                                switch ($e->getActioncode()) {
                                    case 1://returning to browsing
                                        $row[2] = "10;returned to browsing";
                                        break;
                                    case 2://
                                        $row[2] = "3;directly selected a new task";
                                        break;
                                    case 3://
                                        $row[2] = "4;directly revisited a task";
                                        break;
                                    case 5:
                                        $row[2] = "17;started a follow up questionnaire";
                                        break;
                                    default://aborted questionnaire
                                        $row[2] = "16;aborted questionnaire";
                                        break;
                                }
                                break;
                            default:
                                $row[2] = "1;default browsing validator";
                                break;
                        }
                        $lastAction = $e->getActioncode();
                        $lastTimeStamp = $e->getTimestamp();
                        $lastActionSupp = $e->getActionsupp() == NULL ? "-" : $e->getActionsupp();
                        $counter++;
                    }
                    fputcsv($fp, $row, "|");
                }
            }
        }
        fclose($fp);
    }

}

?>