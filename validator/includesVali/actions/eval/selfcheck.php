<?php

$QID = isset($_GET["q"]) ? intval($_GET["q"]) : 0;
$nextTrial = isset($_GET["t"]) ? intval($_GET["t"]) : 0;
$lang = $l->getLanguage();
$questionnaire = $QID > 0 ? questionnaire::getByPK(array($QID,$lang)) : null;

if ($questionnaire == null){
    //Try to get it in another language
    $questionnaire = questionnaire::getByPK(array($QID,array_diff($l->getSupportedLanguages(),array($lang))[0]));
    if ($questionnaire != null)
        $otherLanguage = true;
}

//Check first whether the user has to complete another questionnaire first - admins are excluded
if ($questionnaire != null) {
    $pre = $questionnaire->CheckForPredecessor();
    if ($pre != NULL and !$pre->checkUserParticipation($user->getUserEvalID()) AND $user->getFlagAdmin() != 'Y')
        header('location:index.php?action=eval/questionnaire&q=' . $pre->getPKs()[0]);

}

//Check whether the User already completed this questionnaire
$tookPart = False;
$somethingInserted = False;//Try to prevent an empty result from being inserted
if ($questionnaire != null)
	$tookPart = $questionnaire->checkUserParticipation($user->getUserEvalID());

//Load all tasks and items to display them - check whether the user already took part or takes another try
if ($questionnaire != null && (!$tookPart || $nextTrial != 0))
    $tasks = $questionnaire->getSelfcheckTasksWithItems();

//TTS - Time Tracking System - log the start of a new selfcheck or the revisit of a already completed selfcheck
if ($questionnaire != null && (!$tookPart || $nextTrial != 0))
    trackingLog::basicInsert(trackingLog::getTableName(), array_diff(trackingLog::getColumns(), trackingLog::getPKColumns()), array($_SESSION["sem_id"], $user->getUserEvalID(), null, 5, $QID));
else if($questionnaire != null && $tookPart)
    trackingLog::basicInsert(trackingLog::getTableName(), array_diff(trackingLog::getColumns(), trackingLog::getPKColumns()), array($_SESSION["sem_id"], $user->getUserEvalID(), null, 7, $QID));


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["QType"]) && $_POST["QType"] == "sc")
{
    /**
     * Site is loaded the second time with results showing
     * -- > Save the results in the database
     */
    $errors = array();

    //Save result data in database
    $UserID = $user->getUserEvalID();
    //Get the current trial
    $trial = $questionnaire->latestTrial($UserID)+1;
    $counterTask=1;
    $counterItem=1;
    global $dbEval;
    $dbEval->startTransaction();
    //Go through all tasks and items from the questionnaire and save en entry if there is one
    $allTasks = $questionnaire->getAllTasksWithItems();
    foreach ($allTasks as $task)
    {
        $taskNum = $task["TaskNum"];
        //Switch on task type ; 1-3 - normal; 4-MCTask; 5 - sqltask with validator
        if ($task["type"] == 5){
            $itemsCount = $task["scalesize"];//get how many awnsers are expected
            $resultValAggregated = "";
            for($i = 0; $i < $itemsCount;$i++) {
                //SQLTask -- parse and store result
                $resultVal = "(Not submitted)";
                if (isset($_POST["T" . $taskNum . "I" . $i . "_itemID"])
                    && isset($_POST["T" . $taskNum . "I" . $i])
                    && strlen(trim($_POST["T" . $taskNum . "I" . $i])) > 0) {
                    $transSql = $_POST["T" . $taskNum . "I" . $i];
                    $transID = $_POST["T" . $taskNum . "I" . $i . "_itemID"];
                    $res = checkSQL($transSql, $transID, $l->getLanguage(), $db, $user, $l);
                    $thisTask = Task::getById($transID);
                    $resultVal = $thisTask->getTitle($lang) . "|" . str_replace('|', '', parseDescription($thisTask->getDescription($lang))) . "|";
                    $resultVal .= str_replace('|','',$transSql).'|';
                    if (strlen($res) == 0) {
                        $resultVal .= "(Correct)|";
                    } else {
                        $resultVal .= "(False)|" . $res;
                    }
                    $somethingInserted = true;//we got a post variable delivered which means that the selfcheck is not empty
                }
                $resultValAggregated .= $resultVal."||";//Separate tasks with ||
            }
            $vals = array($UserID, $QID, $lang, $taskNum, "1", $resultValAggregated,$trial);//Save everything in the result of the first item
            $res = $questionnaire::basicInsert(Questionnaire::getAnswersTableName(), array_merge(array_slice($questionnaire::getAnswersColums(), 0, 6),["trial"]), $vals);
        }
        else if ($task["type"] == 4){
            foreach($task["item"] as $item) {
                //MC task
                $itemNum = $item["INum"];
                //Iterate maximum 10 possible choices - needs to be done, because on a checkbox not every value is transmitted
                $resultVal = "";
                //Check for hidden input first because not necessary every task is answered
                if (isset($_POST["T" . $taskNum . "I" . $itemNum . "_numChoices"])) {
                    $maxChoices = ($_POST["T" . $taskNum . "I" . $itemNum . "_numChoices"] < 10) ? $_POST["T" . $taskNum . "I" . $itemNum . "_numChoices"] : 10;
                    $correctChoices = array_map('trim', explode(";", $item["correctChoices"]));
                    $correctChoices = str_replace("'", '', str_replace('"', '', $correctChoices));
                    for ($inputCounter = 0; $inputCounter < $maxChoices; $inputCounter++) {
                        if (isset($_POST["T" . $taskNum . "I" . $itemNum . "_" . $inputCounter])) {
                            $resultCheck = $_POST["T" . $taskNum . "I" . $itemNum . "_" . $inputCounter];
                            $resultVal .= $resultCheck;
                            //Check on multiple choices whether the answer is correct or wrong
                            if (in_array(trim($resultCheck), $correctChoices))
                                $resultVal .= ";(Correct);";
                            else
                                $resultVal .= ";(False);";
                            $somethingInserted = true;
                        }
                    }

                    $vals = array($UserID, $QID, $lang, $taskNum, $itemNum, $resultVal,$trial);
                    $res = $questionnaire::basicInsert(Questionnaire::getAnswersTableName(), array_merge(array_slice($questionnaire::getAnswersColums(), 0, 6),["trial"]), $vals);
                }
            }
        }
    }

    if ($somethingInserted) {
        $dbEval->endTransaction(True);
        //TTS-Time Tracking System Log the completion of a selfcheck
        trackingLog::basicInsert(trackingLog::getTableName(), array_diff(trackingLog::getColumns(), trackingLog::getPKColumns()), array($_SESSION["sem_id"], $UserID, null, 6, $QID));
    }
    else
        $dbEval->endTransaction(False);

}

//Error handling
if ($QID == null || $questionnaire == null)
    $page="access_denied";

//Functions from view task
/**
 * Transform hints to html table
 * @param $s
 * @return string|string[]|null
 */
function parseHint($s, &$head, &$body)
{
    $s = preg_replace_callback(
        '|\{hint title:(.+?)\}(.+?)\{/hint\}|',
        function ($m2) use (&$head, &$body) {
            $title = $m2[1];
            $sql = $m2[2];
            $head[] = $title;
            $db_master = db_connection(SQLVALI_DB_USERNAME,SQLVALI_DB_PASSWORD,'sqlvali_master');
            $db_master->queryWithParams($sql);
            $body[] = build_html_table($db_master->result());
            return "abc";
        },
        $s
    );
    return $s;
}
function parseHints($s)
{
    return preg_replace_callback(
        '|\{hints\}(.+?)\{/hints\}|s',
        function ($m) {
            $head = array();
            $body = array();
            parseHint($m[1], $head, $body);

            $output = "<div class=\"panel panel-default\">";
            $output .= "  <div class=\"panel-heading\">";
            $output .= "    <h4 class=\"panel-title\">";
            $output .= "      <a>Hints</a>";
            $output .= "    </h4>";
            $output .= "  </div>";
            $output .= "  <table class=\"table table-bordered table-condensed\">";
            $output .= "    <thead>";
            $output .= "      <tr style=\"background: #f4f4f4;\">";
            foreach ($head as $h) {
                $output .= "        <th><center>$h</center></th>";
            }
            $output .= "      </tr>";
            $output .= "    </thead>";
            $output .= "    <tr>";
            foreach ($body as $b) {
                $output .= "      <td>$b</td>";
            }
            $output .= "    </tr>";
            $output .= "  </table>";
            $output .= "</div>";
            return $output;
        },
        $s
    );
}

function parseDescription($s)
{
    $s = parseHints($s);
    return $s;
}

/**
 *Just a copy from viewTask.php for now
 */
function checkSQL($sql,$taskID,$lang,$db,$user,$l){
    $errortext = null;

    $uid = $user->getId();
    $uname = "user_{$uid}";
    $task = $taskID > 0 ? Task::getById($taskID) : null;

    $stmts = $task->getStatementsForLang($lang);

    $actual_res = array();//Database result executed with user query or predefined statement
    $desired_res = array();//Database results executed with the desired solution; The count is always the same as the count of statments
    $titles = array();//array to store all titles of the statements from the database that have been iterated
    $resTitles = array();//array to store whether for a given statement title a result was already fetched
    $stmtsCheckNull = array();//array to store whether the statement shall be checked for null
    $stmtsCheckDefault = array();//array to store whether the statement shall be checked for default
    $resSet = array();//array to store the specific results gained from the statement queries
    $usedTitles = array();//array to store the initialised active_titles
    $defaultStmtTitle = 'Table';//Default value to define how a standard sql Query i.O. a SELECT statement should be checked
    $error_syntax = "";//database syntaxerror occurring by querying the user query
    $errortext = "";
    $error_prefix = "er00r";

    $db_slave = null;
    $db_master = db_connection();
    $errors_syntax_sp = array();//errors that occure when preparing the slave

//Prepare slave - master db
    if ($db->getType() == PostgreSQLConnection::TYPE) {
        $db_master->queryWithParams("drop schema if exists ${uname} cascade") or $errors_syntax_sp[] = $db_master->getLastError();
        $db_master->queryWithParams("drop role if exists ${uname}") or $errors_syntax_sp[] = $db_master->getLastError();
        $db_master->queryWithParams("create user ${uname} with password 'sqlvalidator'") or die($db_master->getLastError());
        $db_master->queryWithParams("create schema authorization ${uname}") or die($db_master->getLastError());
        $db_master->queryWithParams("set search_path to {$uname}") or die($db_master->getLastError());
        $db_master->queryWithParams("alter default privileges in schema {$uname} grant all on tables to {$uname}");
    } else if ($db->getType() == 'mysql') {
        $db_master->queryWithParams("drop database if exists {$uname}") or $errors_syntax_sp[] = $db_master->getLastError();
        @$db_master->queryWithParams("drop user {$uname}");
        $db_master->queryWithParams("create user {$uname} identified by 'sqlvalidator'") or $errors_syntax_sp[] = $db_master->getLastError();
        $db_master->queryWithParams("create database {$uname}") or $errors_syntax_sp[] = $db_master->getLastError();
        $db_master->queryWithParams("grant all privileges on {$uname}.* to {$uname}@'%'") or $errors_syntax_sp[] = $db_master->getLastError();
        $db_master->queryWithParams("grant all privileges on {$uname}.* to " . SQLVALI_DB_USERNAME . "@'%'") or $errors_syntax_sp[] = $db_master->getLastError();
        $db_master->queryWithParams("flush privileges") or $errors_syntax_sp[] = $db_master->getLastError();
        $db_master->queryWithParams("use {$uname}") or $errors_syntax_sp[] = $db_master->getLastError();
    }

// Execute task preparation sql
        foreach ($task->getPreparationsForLang($lang) as $tskp) {
            multiqueryWithParams($db_master, $tskp->getSql());
        }


//Query user and desired sql on slave and master
    if (!empty($sql)) {
        // Create slave connection
        $db_slave = db_connection($uname, "sqlvalidator", $uname);

        //Execute the user query and check whether a syntax error occurs
        if (!multiqueryWithParams($db_slave, $sql)) {
            $error_syntax = $errortext = $db_slave->getErrorText();         //syntaxerror saved as error
        }

        $act_title = Null; //active title used as key between actual and desired result arrays
        $db_slave_res = Null;
        //Iterate over all statements that shall be checked - statements are parts of the solution that shall be checked. The type is defined by the title
        foreach ($stmts as $stmt) {

            if (strlen($stmt->getSqlActual()) == 0) {
                //If there is no comparison result predefined take the actual user result
                if (!$db_slave_res) {
                    $db_slave_res = $db_slave->result();
                }
                $actual_res[] = $db_slave_res;
            } else {
                //if there is a comparison predefined execute this query.
                // This is often the case when we want to gather the table structure when the user did a CREATE
                multiqueryWithParams($db_master, $stmt->getSqlActual());
                $actual_res[] = $db_master->result();
            }
            if ((strlen($stmt->getTitle())) == 0) {
                //Set default value as statement title, so it is later possible to correctly validate the statement
                $stmt->setTitle($defaultStmtTitle);
            }

            $act_title = $stmt->getTitle();

            $titles[] = $act_title;//collect statement title for later comparison

            if (!array_key_exists($act_title, $resTitles)) {
                //initalise result set
                $resTitles[$act_title] = false; //Set for keystore resTitles the act_title as false
                $resSet[$act_title] = NULL;
                $usedTitles[] = $act_title;
            }
            multiqueryWithParams($db_master, $stmt->getSqlDesired());
            $desired_res[] = $db_master->result();
            $stmtsCheckDefault[] = $stmt->getCheckdefault() == 1 ? true : false;
            $stmtsCheckNull[] = $stmt->getChecknull() == 1 ? true : false;
        }

    }

//Evaluate the results of the queries by comparing actual and desired result
    $actual = array();//actual result set transformed as html table
    $desired = array();//desired result set transformed as html table
    $results = array();
    $result_actual_acquired = true;
//Check whether we have an actual result to compare to
    if (!empty($actual_res)) {
        //loop through every desired result from the statements
        for ($i = 0; $i < count($desired_res); $i++) {
            //check whether a validation was already performed for the statement
            if (!$resTitles[$titles[$i]]) {
                $validator = new Validator();
                $validator->setActualResult($actual_res[$i]);
                $validator->setDesiredResult($desired_res[$i]);
                $validator->setType($titles[$i]);
                $validator->setCheckNull($stmtsCheckNull[$i]);
                $validator->setCheckDefault($stmtsCheckDefault[$i]);

                $a = $actual_res[$i]->getResultSet();
                $d = $desired_res[$i]->getResultSet();

                if (($titles[$i]) == $defaultStmtTitle) {
                    //if the solution is given in an sql statement, check if it contains "order by"
                    if (strpos($stmt->getSqlDesired(), 'order by') !== false) {
                        $validator->setCompareRowOrder(true);
                    } else {
                        $validator->setCompareRowOrder(false);
                    }
                }


                $r = $validator->validate();

                /*resTitles contains a boolean for every title, which shows if a correct Solution exists.
                 * In Case of  a correct Solution resSet contains the validator.
                 * Otherwise it will just show the first Solution for this title.
                 * (Possible Titles are schema, foreignkey, constraints ......)
                 */
                if ($resSet[$titles[$i]] == NULL || ($resTitles[$titles[$i]] == false && $r)) {
                    $resTitles[$titles[$i]] = $r;
                    $resSet[$titles[$i]] = $validator;

                }
            }

        }
    }
    else {
        $result_actual_acquired = false;
    }

//Go through all types/titles of statements we want to check - if we couldn't gather the table on a CT only display this
    $noTableError = false;
    $aggregatedErrorIndices = array();
    foreach ($usedTitles as $title) {

        $result_actual_acquired = $result_actual_acquired && $resTitles[$title];

        //wenn keine Syntaxfehler vorliegen, werden die inhaltlichen Fehler betrachtet
        if (($error_syntax == NULL || $error_syntax == "") && !$noTableError) {
            //Convert errors from the statement validator to an error message for the user
            if (!empty($resSet[$title]->getErrors())) {
                $curr_errors = $resSet[$title]->getErrors();
                $aggregatedErrorIndices = array_merge($aggregatedErrorIndices,$resSet[$title]->getErrorIndices());
                foreach ($curr_errors as $curr_error) {
                    if (!empty($curr_error)) {
                        //Hard check/ Reset for table name error - Nothing else should be displayed than
                        if ($curr_error == "er00r_head_table_title"){
                            $errortext = $l->getString($curr_error);
                            $noTableError = true;
                        }else {
                            //Check whether we have mutliple errors
                            if (strlen($errortext) != 0)
                                $errortext .= "<br>";
                            if (is_array($curr_error)) {
                                foreach ($curr_error as $e) {
                                    if (array_key_exists($e, $l->getStrings())) {
                                        $errortext = $errortext . $l->getString($e);
                                    } else {
                                        $errortext = $errortext . $e;
                                    }
                                }
                            } else {

                                $errortext = $errortext . $l->getString($curr_error);
                            }
                        }
                    }
                }
            }
        }
    }
    return $errortext;
}

function build_html_table($resultSet)
{
    $fields = $resultSet->getColumns();
    $result = $resultSet->getResultSet();

    $output = "";
    $output .= "<table class=\"table table-hover table-bordered table-condensed\">";
    $output .= "<tbody>";

    if ($fields) {
        $output .= "<tr style=\"background: #fcfcfc;\">";
        for ($i = 0; $i < count($fields); $i++) {
            $output .= "<td><b>{$fields[$i]}</b></td>";
        }
        $output .= "<tr>";
    }


    if (count($result) > 0) {
        for ($i = 0; $i < count($result); $i++) {
            $output .= "<tr>";
            for ($j = 0; $j < count($result[$i]); $j++) {
                $output .= "<td>" . $result[$i][$j] . "</td>";
            }
            $output .= "</tr>";
        }
    } else {
        $output .= "<tr><td align=\"center\" colspan=\"" . count($fields) . "\"><i>(empty resultset)</i></td>";
    }
    $output .= "</tbody>";
    $output .= "</table>";
    return $output;
}

/** Executes multiple queries combined in a string as single statements
 * @param $db database the query should be executed on
 * @param $query the query composed of multiple statements
 * @return bool whether execution was successfull or not
 */
function multiqueryWithParams(&$db, $query)
{
    foreach (explode(';', $query) as $stmt) {
        if (strlen(trim($stmt)) > 0) {
            if (!$db->query($stmt)) {
                return false;
            }
        }
    }
    return true;
}

?>