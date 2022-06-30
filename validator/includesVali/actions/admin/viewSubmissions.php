<?php
/**
 * Created by PhpStorm.
 * User: Sören
 * Date: 28.04.2015
 * Time: 00:00
 */

$show_tasks = false;
$tasks = null;
$users = null;
$submissions = array();

function user_by_id($id) {
    global $users;
    foreach($users as $u) {
        if($u->getId() == $id) {
            return $u;
        }
    }
    return null;
}

function task_by_id($id) {
    global $tasks;
    foreach($tasks as $t) {
        if($t->getId() == $id) {
            return $t;
        }
    }
    return null;
}

if(array_key_exists("submit", $_POST)) {
    $taskGroup = TaskGroup::getById($_POST["taskGroup"]);
    $group = ExerciseGroup::getById($_POST["group"]);

    if(!empty($group)) {
        $users = User::getByCondition('usr_egrp_id = ? and usr_sem_id = ?', array($group->getId(), $_SESSION["sem_id"]));
    } else {
        $users = User::getByCondition("usr_sem_id = ?", array($_SESSION["sem_id"]));
    }

    if(!empty($taskGroup)) {
        $tasks = Task::getByCondition('tsk_tskg_id = ? and tsk_sem_id = ?', array($taskGroup->getId(), $_SESSION["sem_id"]));
    } else {
        $tasks = Task::getAll();
    }

    $show_tasks = true;

    foreach($tasks as $task) {
        $submissions[$task->getId()] = array();
        foreach($users as $user_) {
            $userQuery = UserQuery::getByCondition('usq_usr_id = ? and usq_tsk_id = ?', array($user_->getId(), $task->getId()));
            if(!empty($userQuery)) {
                $userQuery = $userQuery[count($userQuery) - 1];
                $submissions[$task->getId()][$user_->getId()] = $userQuery;
            }
        }
    }

}