<?php

$task = intval($_GET["task"]);
$task = $task > 0 ? Task::getById($task) : null;

$tasksOftaskGroup = task::getByCondition("tsk_tskg_id = ? ",array($task->getTskgId()),array("tsk_order","tsk_id"));
//Search for predecessor and successor of the task
$indexTask = null;
$i = 0;
$stop = false;
while ($i < count($tasksOftaskGroup) && !$stop){
    if ($tasksOftaskGroup[$i]->getId() == $task->getId()){
        $indexTask = $i;
        $stop = true;
    }else
        $i++;
}
if ($indexTask !== null && $indexTask >= 0 && $indexTask < count($tasksOftaskGroup)){
    if ($indexTask == 0)
        $predTask = null;
    else
        $predTask = $tasksOftaskGroup[$indexTask-1];
    if ($indexTask == count($tasksOftaskGroup)-1)
        $succTask = null;
    else
        $succTask = $tasksOftaskGroup[$indexTask+1];
}

$task_executed = false;
$saved_sql = null;//last executed sqlQuery
$sql = null;//sql query that shall be executed

//Load only latest user query
$userQuery = UserQuery::getByCondition('usq_usr_id = ? and usq_tsk_id = ?', array($user->getId(), $task->getId()));
if (!empty($userQuery)) {
    $userQuery = $userQuery[count($userQuery) - 1];
    $sql = $userQuery->getSql();
    $saved_sql = $sql;
}
//Load latest sql if the task was send
if (array_key_exists("sql", $_POST)) {
    $sql = $_POST["sql"];
}

$uid = $user->getId();
$uname = "user_{$uid}";
$stmts = $task->getStatementsForLang($lang);

//TimeTrackingSystem - add a log that the user has loaded a task
if (!array_key_exists("sql", $_POST)) {
    if ($saved_sql == null)
        trackingLog::basicInsert(trackingLog::getTableName(), array_diff(trackingLog::getColumns(), trackingLog::getPKColumns()), array($_SESSION["sem_id"], EvalModule::getRegisteredUser($uid), null, 2, $task->getId()));
    else
        trackingLog::basicInsert(trackingLog::getTableName(), array_diff(trackingLog::getColumns(), trackingLog::getPKColumns()), array($_SESSION["sem_id"], EvalModule::getRegisteredUser($uid), null, 3, $task->getId()));
}

$actual_res = array();//Database result executed with user query or predefined statement
$desired_res = array();//Database results executed with the desired solution; The count is always the same as the count of statments
$titles = array();//array to store all titles of the statements from the database that have been iterated
$resTitles = array();//array to store whether for a given statement title a result was already fetched
$stmtsCheckNull = array();//array to store whether the statement shall be checked for null
$stmtsCheckDefault = array();//array to store whether the statement shall be checked for default
$stmtsCheckCase = array();//array to store whether the statement shall be checked for case
$resSet = array();//array to store the specific results gained from the statement queries
$usedTitles = array();//array to store the initialised active_titles
$defaultStmtTitle = 'Table';//Default value to define how a standard sql Query i.O. a SELECT statement should be checked
$error_syntax = "";//database syntaxerror occurring by querying the user query
$errortext = "";
$error_prefix = "er00r";

$db_slave = null;
$db_master = db_connection();
$errors_syntax_sp = array();//errors that occur when preparing the slave

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
$aLanguagesToCheck = $l->getSupportedLanguages();//changed to centralize the supported languages - old: array('de','en');

for ($k = 0; $k < count($aLanguagesToCheck); $k++) {
    foreach ($task->getPreparationsForLang($aLanguagesToCheck[$k]) as $tskp) {
        multiqueryWithParams($db_master, $tskp->getSql());
    }
}


//Query user and desired sql on slave and master
if (!empty($sql)) {

    // Create slave connection
    $db_slave = db_connection($uname, "sqlvalidator", $uname);

    //Execute the user query and check whether a syntax error occurs
    if (!multiqueryWithParams($db_slave, $sql)) {
        $error_syntax = $db_slave->getErrorText();         //syntaxerror saved as error
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
            //initialise result set
            $resTitles[$act_title] = false; //Set for keystore resTitles the act_title as false
            $resSet[$act_title] = NULL;
            $usedTitles[] = $act_title;
        }
        multiqueryWithParams($db_master, $stmt->getSqlDesired());
        $desired_res[] = $db_master->result();
        $stmtsCheckDefault[] = $stmt->getCheckdefault() == 1 ? true : false;
        $stmtsCheckNull[] = $stmt->getChecknull() == 1 ? true : false;
        $stmtsCheckCase[] = $stmt->getCheckcase() == 1 ? true : false;
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
            $validator->setCheckCase($stmtsCheckCase[$i]);

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

//if there is no syntax error or no content error display the results
if ($result_actual_acquired || !@$error_syntax) {
    foreach ($usedTitles as $title) {
        $actual[] = build_html_table($resSet[$title]->getActualResult());
        $desired[] = build_html_table($resSet[$title]->getDesiredResult());
        $results[] = $resSet[$title];
        $result_correct[] = $resTitles[$title];
    }
}

if (array_key_exists("sql", $_POST)) {
    $sql = $_POST["sql"];


    if ($saved_sql != $sql) {
        $userQuery = UserQuery::create();
        $userQuery->setTskId($task->getId());
        $userQuery->setUsrId($user->getId());
        $userQuery->setSql($sql);
        $userQuery->setSuccess($result_actual_acquired ? 'Y' : 'N');
        $userQuery->save();

        //Store user query in the eval database
        //Ignore admins, because they often only enter queries for tests and make mistakes intentionaly ;)
        if ($user->getFlagAdmin() != 'Y')
            EvalQuery::insertQuery($user->getUserEvalID(),$sql,$aggregatedErrorIndices,$task->getid());
    }
}

$task_executed = true;

//TimeTrackingSystem -  Log the submision of a query with the corresponding error class
if (array_key_exists("sql", $_POST))
    trackingLog::basicInsert(trackingLog::getTableName(),array_diff(trackingLog::getColumns(),trackingLog::getPKColumns()),array($_SESSION["sem_id"],EvalModule::getRegisteredUser($uid),null,4,$task->getid().";".implode(";",$aggregatedErrorIndices)));

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

/**
 * Transform hints to html table
 * @param $s
 * @return string|string[]|null
 */
function parseHints($s)
{
    function parseHint($s, &$head, &$body)
    {
        $s = preg_replace_callback(
            '|\{hint title:(.+?)\}(.+?)\{/hint\}|',
            function ($m2) use (&$head, &$body) {
                $title = $m2[1];
                $sql = $m2[2];
                $head[] = $title;
                global $db_master;
                $db_master->queryWithParams($sql);
                $body[] = build_html_table($db_master->result());
                return "abc";
            },
            $s
        );
        return $s;
    }

    ;

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
