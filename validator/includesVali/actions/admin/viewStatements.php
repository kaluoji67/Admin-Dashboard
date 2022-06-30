<?php
/**
 * Created by PhpStorm.
 * User: Sören
 * Date: 26.04.2015
 * Time: 13:33
 */

$task = @$_GET["task"];
$task = Task::getById(intval($task));

$edited = false;
$deleted = false;

if(array_key_exists("delete", $_GET)) {
    $id = $_GET["delete"];
    $task = intval($_GET["task"]);
    $task = $task > 0 ? Task::getById($task) : null;
    $stmt = Statement::getById($id);
    //if task exists and task references to another task
    if((!is_null($task))&&(!is_null($task->getCopyofTskId())))
    {
        //copy all data from referenced task and get id of the corresponding new statement to delete it
        $newId = $task->dereferenceTask($task, null, $stmt->getId());
        $stmt->setId($newId);
    }
    $stmt->delete();
    $deleted = true;
}

if(array_key_exists("submit", $_POST)) {
    $title = $_POST["title"];
    $stmt = intval($_POST["id"]);
    $task = intval($_POST["task"]);
    $sql_desired = $_POST["sql_desired"];
    $sql_actual = $_POST["sql_actual"];
    $checkNull = (isset($_POST["checkNull"]) && $_POST["checkNull"] == "on") ? 1 : 0;
    $checkDefault = (isset($_POST["checkDefault"]) && $_POST["checkDefault"] == "on") ? 1 : 0;
    $checkCase = (isset($_POST["checkCase"]) && $_POST["checkCase"] == "on") ? 1 : 0;
    $lang = $_POST["lang"];
    $lang = empty($lang) ? null : $lang;
    $task = $task > 0 ? Task::getById($task) : null;
    $stmt = $stmt > 0 ? Statement::getById($stmt) : null;

    //if task exists and task references to another task
    if((!is_null($task))&&(!is_null($task->getCopyofTskId())))
    {
        //when a statement is created
        if(empty($stmt))
        {
            $task->dereferenceTask($task);
        }
        //else copy all data from referenced task and get id of the corresponding new statement to edit it
        else
        {
            $newId = $task->dereferenceTask($task, null, $stmt->getId());
            $stmt->setId($newId);
            $stmt->setTskId($task->getId());
        }
    }

    if(empty($stmt)) {
        $stmt = Statement::create();
        $stmt->setTskId($task->getId());
    }
    $stmt->setLang($lang);
    $stmt->setTitle($title);
    $stmt->setSqlDesired($sql_desired);
    $stmt->setSqlActual($sql_actual);
    $stmt->setCheckdefault($checkDefault);
    $stmt->setChecknull($checkNull);
    $stmt->setCheckcase($checkCase);
    $stmt->save();

    $edited = true;
}