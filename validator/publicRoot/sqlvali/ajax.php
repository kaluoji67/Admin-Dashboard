<?php
$rootDirectory = "./../../includesVali";
require_once __DIR__ . $rootDirectory . '/include/config.inc.php';
require_once __DIR__ . $rootDirectory . '/classes/db/PDODbConnection.class.php';
require_once __DIR__ . $rootDirectory . '/classes/db/PostgreSQLConnection.class.php';
require_once __DIR__ . $rootDirectory . '/classes/db/MySQLiConnection.class.php';
require_once __DIR__ . $rootDirectory . '/classes/Validator.class.php';
require_once __DIR__ . $rootDirectory . '/classes/localizer/Localizer.class.php';
require_once __DIR__ . $rootDirectory . '/classes/localizer/FileLocalizer.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/ModelObject.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/ExerciseGroup.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/ExerciseGroupLocalization.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/User.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/UserQuery.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/Task.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/TaskPreparation.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/TaskPreparationTemplate.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/Semester.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/Statement.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/StatementTemplate.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/TaskLocalization.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/TaskGroup.class.php';
require_once __DIR__ . $rootDirectory . '/classes/model/TaskGroupLocalization.class.php';

session_start();
$db = db_connection();
$user = unserialize($_SESSION["user"]);
// At the moment, all ajax functionalities may only be used by admins.
if(empty($user) || $user->getFlagAdmin() != 'Y') {
    //exit('You are not allowed to do this.');
}

$action = @$_GET["action"];
$subaction = @$_GET["subaction"];

$id = intval(@$_GET["id"]);

if($action == "attachment") {
    if($subaction == "upload") {
        if ($_FILES['file']['name']) {
            if (!$_FILES['file']['error']) {
                $name = $_FILES['file']['name'];
                $location = $_FILES["file"]["tmp_name"];

                $fp = fopen($location, 'r');
                $data = fread($fp, filesize($location));
                fclose($fp);

                $data = file_get_contents($location);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $type = finfo_file($finfo, $location);
                finfo_close($finfo);

                $att = Attachment::create();
                $att->setContent($data);
                $att->setContentType($type);

                if ($att->save()) {
                    echo json_encode(array('id' => $att->getId()));
                }
            }
        }
    } else if($subaction == "download") {
        $id = $_GET["id"];
        $att = Attachment::getById($id);

        if($att) {
            header("Content-Type: " . $att->getContentType());
            echo $att->getContent();
        }
    }
}
if($action == "statement_tpl") {
    if($subaction == "get") {
        $stmtt = $id > 0 ? StatementTemplate::getById($id) : null;
        echo json_encode(array(
            "id" => $stmtt->getId(),
            "title" => $stmtt->getTitle(),
            "actual" => $stmtt->getSqlActual(),
            "desired" => $stmtt->getSqlDesired()
        ));
    }
}

if($action == "preparation_tpl") {
    if($subaction == "get") {
        $tskpt = $id > 0 ? TaskPreparationTemplate::getById($id) : null;
        echo json_encode(array(
            "id" => $tskpt->getId(),
            "title" => $tskpt->getTitle(),
            "sql" => $tskpt->getSql()
        ));
    }
}

if($action == "exercise_group") {
    if ($subaction == "show_form") {
        $exerciseGroup = $id > 0 ? ExerciseGroup::getById($id) : null;
        require $rootDirectory.'/tpl/admin/ajax/editExerciseGroup.tpl.php';
        exit;
    }
}
if($action == "submission_group") {
	if ($subaction == "show_form") {
		$exerciseGroup = $id > 0 ? ExerciseGroup::getById($id) : null;
		require $rootDirectory.'/tpl/admin/ajax/viewSubmissionGroup.php';
		exit;
	}
}

if($action == "task_group") {
    if ($subaction == "show_form") {
        if(isset($_GET["copySem"]))
        {
            $copySem = $_GET["copySem"];
        }
        $taskGroup = $id > 0 ? TaskGroup::getById($id) : null;
        require $rootDirectory.'/tpl/admin/ajax/editTaskGroup.tpl.php';
        exit;
    }
    else if ($subaction == "show_copyform") {
        require $rootDirectory.'/tpl/admin/ajax/copyMoreTaskGroups.tpl.php';
        exit;
    }
}

if($action == "preparation") {
    if($subaction == "show_form") {
        $task = intval($_GET["task"]);
        $task = $task > 0 ? Task::getById($task) : null;
        $tskp = $id > 0 ? TaskPreparation::getById($id) : null;
        require $rootDirectory.'/tpl/admin/ajax/editPreparation.tpl.php';
        exit;
    }
}

if($action == "statement") {
    if($subaction == "show_form") {
        $task = intval($_GET["task"]);
        $task = $task > 0 ? Task::getById($task) : null;
        $statement = $id > 0 ? Statement::getById($id) : null;
        require $rootDirectory.'/tpl/admin/ajax/editStatement.tpl.php';
        exit;
    }
}
if($action == "task") {
    if ($subaction == "show_form") {
        $task = $id > 0 ? Task::getById($id) : null;
        //get the value of copy
        if(isset($_GET["copy"]))
        {
            $copy = $_GET["copy"];
        }
        if(isset($_GET["copySem"]))
        {
            $copySem = $_GET["copySem"];
        }
        require $rootDirectory.'/tpl/admin/ajax/editTask.tpl.php';
        exit;
    }
    else if ($subaction == "show_copyform") {
        require $rootDirectory.'/tpl/admin/ajax/copyMoreTasks.tpl.php';
        exit;
    }
}

if($action == "get_user_data") {
    $username = $_GET["username"];
    $user = User::getByName($username);
    $email = $user->getEmail();
    $split_email = explode("@",$email);
    if($user) {
        echo json_encode(array(
        	//for the deletion request, the user id is necessary
	    	"id" => $user->getId(),
            "username" => $user->getName(),
            "fullname" => $user->getFullname(),
            "email" => $user->getEmail(),
            "excgroup" => $user->getEgrpId(),
            "admin" => $user->getFlagAdmin(),
        	"email1" => $split_email[0],
        	"email2" => $split_email[1]
        ));
    }
}

//12.06.17
//get the data of the exercise group; id and name are necessary for the deletion request
if($action == "get_exgroup_data") {
	$eg_id = $_GET["eg_id"];
	$eg = ExerciseGroup::getById($eg_id);
	if($eg) {
		echo json_encode(array(
				"id" => $eg->getId(),
				"name" => $eg->getName('de'),
		)); 
	}
}

//get the data of the task group; id and name are necessary for the deletion request
if($action == "get_tskgroup_data") {
	$tg_id = $_GET["tg_id"];
	$tg = TaskGroup::getById($tg_id);
	if($tg) {
		echo json_encode(array(
				"id" => $tg->getId(),
				"name" => $tg->getName('de')
		));
	}
}

//get the data of the task; id is necessary for the deletion request
if($action == "get_tsk_data") { //Ulli
	$tsk_id = $_GET["tsk_id"];
	$tsk = Task::getById($tsk_id);
	if($tsk) {
		echo json_encode(array(
				"id" => $tsk->getId()
				//"name" => $tsk->getName('de')
		));
	}
}

//get the data of the preparation; id is necessary for the deletion request
if($action == "get_preparation_data") {
	$p_id = $_GET["p_id"];
	$p = TaskPreparation::getById($p_id);
	if($p) {
		echo json_encode(array(
				"id" => $p->getId()
		));
	}
}

//get the data of the statement; id is necessary for the deletion request
if($action == "get_statement_data") {
	$s_id = $_GET["s_id"];
	$s = Statement::getById($s_id);
	if($s) {
		echo json_encode(array(
				"id" => $s->getId()
		));
	}
}