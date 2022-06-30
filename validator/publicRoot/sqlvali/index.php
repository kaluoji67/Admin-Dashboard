<?php
error_reporting(E_ALL);
ini_set("display_errors", true);
mb_internal_encoding('UTF-8');//Set internal encoding to utf-8 so that there are no problems with strtolower
$rootDirectory = "/../../includesVali";
require_once __DIR__ . $rootDirectory. '/include/config.inc.php';
require_once __DIR__ . $rootDirectory. '/classes/db/PDODbConnection.class.php';
require_once __DIR__ . $rootDirectory. '/classes/db/PostgreSQLConnection.class.php';
require_once __DIR__ . $rootDirectory. '/classes/db/MySQLiConnection.class.php';
require_once __DIR__ . $rootDirectory. '/classes/Validator.class.php';
require_once __DIR__ . $rootDirectory. '/classes/localizer/Localizer.class.php';
require_once __DIR__ . $rootDirectory. '/classes/localizer/FileLocalizer.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/ModelObject.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/ExerciseGroup.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/ExerciseGroupLocalization.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/User.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/UserQuery.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/Task.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/TaskPreparation.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/TaskPreparationTemplate.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/Semester.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/Statement.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/StatementTemplate.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/TaskLocalization.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/TaskGroup.class.php';
require_once __DIR__ . $rootDirectory. '/classes/model/TaskGroupLocalization.class.php';
require_once __DIR__ . $rootDirectory. '/classes/EvalModels/BaseClassEval.class.php';
require_once __DIR__ . $rootDirectory. '/classes/EvalModels/Questionnaire.class.php';
require_once __DIR__ . $rootDirectory. '/classes/EvalModels/EvalModule.class.php';
require_once __DIR__ . $rootDirectory. '/classes/EvalModels/EvalQuery.class.php';
require_once __DIR__ . $rootDirectory. '/classes/EvalModels/trackingLog.class.php';

function require_user(&$page) {
    global $user;
    if(!isset($user)) {
        $page = 'access_denied';
        //require_once 'tpl/dash_index.tpl.php';
        //die();
    }
}

function require_admin(&$page) {
    require_user($page);
    global $user;
    if(!isset($user) || $user->getFlagAdmin() != 'Y') {
        $page = 'access_denied';
        //require_once 'tpl/dash_index.tpl.php';
        //die();
    }
}

session_start();
if(array_key_exists("user", $_SESSION)) {
    $user = unserialize($_SESSION["user"]);
}

$l = new FileLocalizer();
$l->setLanguage('en');
if(array_key_exists("lang", $_SESSION)) {
    $l->setLanguage($_SESSION["lang"]);
    $lang = $_SESSION["lang"];
} else {
	//26.05.17: get the prefered language of the user
	if(isset($user))
	{
	    $l->setLanguage($user -> getLang());
	    $lang = $user -> getLang();
	}
	else 
	{
		$l -> setLanguage('en');
		$lang = 'en';
	}
}

$db = db_connection();
$dbEval = db_connection(SQLVALI_DB_USERNAME,SQLVALI_DB_PASSWORD,SQLVALI_DB_DBNAMEEVAL);


if(isset($_GET["semSelection"]))
{
    $_SESSION["sem_id"]= $_GET["semSelection"];
}

$action = null;
if(array_key_exists("action", $_GET) && isset($_GET["action"]) ) {
    $action = $_GET["action"];
	//26.05.17: pefered language of the user is saved
    if ($action == "set_lang") {
        $lang = $_GET["lang"];
        //Check whether the provided language is supported
        if (!in_array($lang,$l->getSupportedLanguages())){
            echo "<span class=error> Wrong language provided. Fall back to english.</span>";
            $lang = "en";
        }
        $_SESSION["lang"] = $lang;
        $l->setLanguage($lang);
        if(isset($user))
        {
	        $user->setLang($lang);
	        $user->save();
        }

        $ref = $_SERVER["HTTP_REFERER"];
        $loc=explode("index.php?",$ref);
        if(count($loc)==1)
        {
        	header("Location: " . $ref);
        }
       	else 
       	{
       		$newloc="index.php";
       		
       		$restrictedLeftParts=array("delete", "semSelection");
       		$restrictedRightParts=array("register", "login", "logout");
       		
       		$firstItem=true;
       		
       		$locParts=explode("&",$loc[1]);
       		
       		foreach($locParts as $part)
       		{
       			$splittedPart=explode("=",$part);
       			
       			$keep=false;
       			
       			if((!in_array($splittedPart[0],$restrictedLeftParts))&&(!in_array($splittedPart[1],$restrictedRightParts)))
       			{
       				$keep=true;
       			}
       			
       			if($keep)
       			{
       				if($firstItem)
       				{
       					$firstItem=false;
       					$newloc .= "?".$part;
       				}
       				else
       				{
       					$newloc .= "&".$part;
       				}
       			}
       		}
       		header("Location: " . $newloc);
       	}
        exit;
    }

    $page = $action;
    switch ($action) {
        case "login":
        case "forgotPassword":
        case "register":
        case "about" :
            break;
        case "account":
        case "viewTask":
        case "teams/teams":
       /* case "teams/timeLine":
        case "teams/members":*/
        case "teams/project":
        case "teams/projects":
        case "teams/submissions":
        case "teams/joingroup":
        case "admin/teams/statistics":
        case "teams/groupWiki":
        case "teams/editor":
        case "dashboard/dash_index":
        case "introduction/tutorial_introduction":
        case "introduction/tutorial_exercise_page":
        case "viewTasks":
        case "logout":
        case "eval/questionnaire":
        case "eval/selfcheck":
            require_user($page);
            break;
        case "admin/viewUsers":
        case "admin/viewTaskGroups":
        case "admin/teams/teamsAdmin":
        case "admin/viewExerciseGroups":
        case "admin/viewTasks":
        case "admin/viewStatements":
        case "admin/viewSubmissions":
        case "admin/viewPreparation":
        case "admin/editSemester":
        case "admin/viewSubmissionsUser":
        case "eval/evaladmin":
        case "eval/questionnaireedit":
        case "admin/editPreferredSemester":
            require_admin($page);
            break;
        default:
            $page = "access_denied";
            break;
    }

    if(isset($page) && file_exists(  __DIR__ . $rootDirectory."/actions/$page.php")) {//Execute related actions
        require_once __DIR__ . $rootDirectory."/actions/$page.php";
    }
}
//TimeTrackingSystem - Log action
trackingLog::addLog(isset($user) ? $user : null,$action,$_GET,$_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : null);
$page = isset($page) ? $page : "home"; // If there is no page givven by GetParameter -> Fall back to home
$page = file_exists(__DIR__ . $rootDirectory."/tpl/$page.tpl.php") ? $page : "access_denied";

require __DIR__ . $rootDirectory."/tpl/index.tpl.php"; //Load the template of index now

?>


