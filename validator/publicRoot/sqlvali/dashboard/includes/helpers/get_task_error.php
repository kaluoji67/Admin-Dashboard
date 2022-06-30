<?php
//require_once (__DIR__."/../db/connection.tpl.php");
$rootDirectory = "./../../../../../includesVali";
require_once __DIR__ . $rootDirectory . '/tpl/dashboard/includes/db/connection.tpl.php';
require_once __DIR__ . $rootDirectory . '/tpl/dashboard/includes/helpers/fetch_functions.tpl.php';



$semTaskList = getTaskList($_POST['semester'],$_POST['lang']);
if(isset($_POST['task']))
    $semTask=$_POST['task'];
else
    $semTask = key(reset($semTaskList)["tasks"]);

if(isset($_POST['proficiency']) ){
    if($_POST['proficiency'] != "-1")
        $semErrorList = getErrorListProficiency($semTask,$_POST['proficiency']);
    else
        $semErrorList = getErrorList($semTask);
}
else
    $semErrorList = getErrorList($semTask);


//return array with elements : taskList and errorList of First Task
$result = array();
$result["taskList"] = $semTaskList;
$result["errorList"] = $semErrorList;

echo json_encode($result);
