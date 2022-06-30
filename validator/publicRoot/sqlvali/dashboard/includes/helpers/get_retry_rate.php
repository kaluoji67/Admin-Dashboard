<?php
$rootDirectory = "./../../../../../includesVali";
require_once __DIR__ . $rootDirectory . '/tpl/dashboard/includes/db/connection.tpl.php';
require_once __DIR__ . $rootDirectory . '/tpl/dashboard/includes/helpers/fetch_functions.tpl.php';


$retrySemester= $_POST['semester'];
$retryLang =$_POST['lang'];

$semTaskList = getTaskList($retrySemester,$retryLang);

$semGroupId="";
if(isset($_POST['groupId']))
    $semGroupId=$_POST['groupId'];
else
    $semGroupId = key($semTaskList);

//$semRetrialList = getRetrialTaskList($semGroupId,$_POST['semester'], $_POST['lang']);

$semRetrialList = getRetrialTaskList($semGroupId,$retrySemester, $retryLang);

//return array with elements : groupList and retrialList of First group or selected group
$result = array();

$result["retrialList"] = $semRetrialList;
$result["groupList"] = $semTaskList;

echo json_encode($result);
