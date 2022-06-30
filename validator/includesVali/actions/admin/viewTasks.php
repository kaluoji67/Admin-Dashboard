<?php
/**
 * Created by PhpStorm.
 * User: Sören
 * Date: 26.04.2015
 * Time: 13:33
 */

$edited = false;
$deleted = false;

if(array_key_exists("delete", $_GET)) {
    $id = $_GET["delete"];
    $stmt = Task::getById($id);
    $stmt->delete();
    $deleted = true;
}

if(array_key_exists("submitCopyTasks", $_POST)) {
    $selectedTasks = $_POST["selectedTasks"];
    $semID = $_POST["semester"];
    
    $taskIDs=explode(";",$selectedTasks);
    for($i=0; $i<count($taskIDs)-1;$i++)
    {
        $t = Task::getById($taskIDs[$i]);
        $t->setSemId($semID);
        $t->setOrder(0);
        $t->setTskgId(null);
        $t->save();
    }
}

if(array_key_exists("submit", $_POST)) {
    $tskg = intval($_POST["task_group"]);
    $tskg = $tskg > 0 ? $tskg : null;
    $id = intval($_POST["id"]);
    $pos = intval($_POST["order"]);
    $task = $id > 0 ? Task::getById($id) : null;
    $copy = $_POST["copy"];
    $copySem = $_POST["copySem"];

    if(empty($task)) {
        $task = Task::create();
        $task->setSemId($_SESSION["sem_id"]);
    }

    if($task->getOrder() != $pos || $task->getTskgId() != intval($_POST["task_group"])) {
        $task->setOrder($pos);
        $tasks = Task::getByCondition('tsk_order >= ? and tsk_tskg_id = ?', array($pos, $tskg), array('tsk_order'));
        foreach($tasks as $taskTmp) {
            $taskTmp->setOrder(++$pos);
            $taskTmp->save(true);
        }
    }
    $task->setTskgId($tskg);
    $task->save();

    if((($copySem=='false')&&($copy=='false'))||($task->getCopyofTskId()==null))
    {
        foreach(array('en', 'de') as $lang) {
            $loc = TaskLocalization::getByCondition('tskl_tsk_id = ? and tskl_lang = ?', array($task->getId(), $lang));
            if (count($loc) < 1) {
                $loc = TaskLocalization::create();
                $loc->setTskId($task->getId());
                $loc->setLang($lang);
            } else {
                $loc = $loc[0];
            }
            $loc->setTitle($_POST["title_$lang"]);
            $loc->setDescription($_POST["description_$lang"]);
            $loc->save();
        }
    }

    $edited = true;
}