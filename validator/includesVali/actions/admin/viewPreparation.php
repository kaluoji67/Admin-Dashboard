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
    $tskp = TaskPreparation::getById($id);
    //if task exists and task references to another task
    if((!is_null($task))&&(!is_null($task->getCopyofTskId())))
    {
        //copy all data from referenced task and get id of the corresponding new preparation to delete it
        $newId = $task->dereferenceTask($task, $tskp->getId());
        $tskp->setId($newId);
    }
    $tskp->delete();
    $deleted = true;
}

if(array_key_exists("submit", $_POST)) {
    $tskp = intval($_POST["id"]);
    $task = intval($_POST["task"]);
    
    $lang = $_POST["lang"];
    $lang = empty($lang) ? null : $lang;
    $sql = $_POST["sql"];
    $task = $task > 0 ? Task::getById($task) : null;
    
    $tskp = $tskp > 0 ? TaskPreparation::getById($tskp) : null;
    
    //if task exists and task references to another task
    if((!is_null($task))&&(!is_null($task->getCopyofTskId())))
    {
        //when a preparation is created
        if(empty($tskp))
        {
            $task->dereferenceTask($task);
        }
        //else copy all data from referenced task and get id of the corresponding new preparation to edit it
        else
        {
            $newId = $task->dereferenceTask($task, $tskp->getId());
            $tskp->setId($newId);
        }
    }
    
    if(empty($tskp)) {
        $tskp = TaskPreparation::create();
        $tskp->setTskId($task->getId());
    }
    
    $tskp->setTskId($task->getId());
    $tskp->setLang($lang);
    $tskp->setSql($sql);
    $tskp->save();

    $edited = true;
}