<?php
if (isset($_GET["q"])){
    if (substr($_GET["q"],0,3) == "new"){
        $newQuest = true;
        $QID = $oldQID =intval(substr($_GET["q"],3));
    }
    else
        $QID = $oldQID =intval($_GET["q"]);
}
else
    $QID = $oldQID = -1;
$lang = $oldLang = isset($_GET["l"]) ? $_GET["l"] : "en";
global $db;
global $dbEval;

//Check whether we are delegated by a copy or a createNew
if (isset($newQuest)){
    if (!$dbEval->query("SELECT COUNT( DISTINCT ".Questionnaire::getPKColumns()[0].") FROM ".Questionnaire::getTableName()))
        echo $dbEval->getErrorText();
    else
        $countOfEntries = (int) $dbEval->fetch()[0];
    if ($QID == 0){
        //Completely blank new insert
        if (!Questionnaire::basicInsert(Questionnaire::getTableName(),Questionnaire::getPKColumns(),array($countOfEntries+1,$lang)))
            echo $dbEval->getErrorText();
    }
    else{
        //Copy with a new insert
        $SQL = "INSERT INTO ".Questionnaire::getTableName()." ".
            " (SELECT ".($countOfEntries+1).", '".$lang."', ".join(",",array_diff(Questionnaire::getColumns(),Questionnaire::getPKColumns()))." FROM ".Questionnaire::getTableName().
            " WHERE Q_ID = ? AND Q_language = ? LIMIT 1)";
        if (!$dbEval->queryWithParams($SQL,array($QID,$lang)))
            echo $dbEval->getErrorText();
        //TaskGroups
        $SQL = "INSERT INTO ".Questionnaire::getTaskTableName()." ".
            " (SELECT ".($countOfEntries+1).", '".$lang."', ".join(",",array_diff(Questionnaire::getTaskColumns(),Questionnaire::getPKColumns()))." FROM ".Questionnaire::getTaskTableName().
            " WHERE Q_ID = ? AND Q_language = ?)";
        if (!$dbEval->queryWithParams($SQL,array($QID,$lang)))
            echo $dbEval->getErrorText();
        //TaskItems
        $SQL = "INSERT INTO ".Questionnaire::getTaskItemTableName()." ".
            " (SELECT ".($countOfEntries+1).", '".$lang."', ".join(",",array_diff(Questionnaire::getTaskItemColumns(),Questionnaire::getPKColumns()))." FROM ".Questionnaire::getTaskItemTableName().
            " WHERE Q_ID = ? AND Q_language = ?)";
        if (!$dbEval->queryWithParams($SQL,array($QID,$lang)))
            echo $dbEval->getErrorText();
    }
    $QID = $countOfEntries+1;
}

//Insert/Update Changes in the data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //var_dump($_POST);
    $errors = array();
    $warnings = array();

    //Start the transaction -- at the end it will be checked for errors and everything marked as unchanged if needed
    $dbEval->startTransaction();

    //Check whether we should delete the questionnaire
    if (isset($_POST["deleteQuest"]) && $_POST["deleteQuest"] == 1){
        //Delete Questionnaire
        if (!Questionnaire::DeleteEntries(array($QID,$lang)))
            $errors[] = "QuestEdit_DeleteQuest";
        header("Location:index.php?action=eval/evaladmin");

    }else {

        //Update the primary key parts
        $newQID = isset($_POST["NewQID"]) ? $_POST["NewQID"] : $lang;
        $newLang = isset($_POST["language"]) ? $_POST["language"] : $QID;
        if ($newLang != $lang || $newQID != $QID) {
            //Check whether edit is possible
            if (Questionnaire::getByIDaLanguage($newQID, $newLang) != FALSE)
                $errors[] = "QuestEdit_DuplicatePrimary";
            else {
                if (Questionnaire::UpdateEntries(Questionnaire::getPKColumns(), array($QID, $lang), array($newQID, $newLang))) {
                    $lang = $newLang;
                    $QID = $newQID;
                } else
                    $errors[] = "QuestEdit_ErrorQuestionnaireUpdatePKS";
            }
        }
        //Update Header parts: title, type, description
        if (isset($_POST["title"]))
            if (!Questionnaire::UpdateEntries(array("Q_title"), array($QID, $lang), array($_POST["title"])))
                $errors[] = "QuestEdit_ErrorQuestionnaireUpdateTitle";
        if (isset($_POST["type"]) and in_array($_POST["type"], Questionnaire::getAvailableTypes()))
            if (!Questionnaire::UpdateEntries(array("Q_type"), array($QID, $lang), array($_POST["type"])))
                $errors[] = "QuestEdit_ErrorQuestionnaireUpdateType";
        if (isset($_POST["description"]))
            if (!Questionnaire::UpdateEntries(array("Q_description"), array($QID, $lang), array($_POST["description"])))
                $errors[] = "QuestEdit_ErrorQuestionnaireUpdateDescription";
        if (isset($_POST["activeStatus"]))
            if (!Questionnaire::UpdateEntries(array("Q_active"), array($QID, $lang), array($_POST["activeStatus"] == 1 ? 1 : 0)))
                $errors[] = "QuestEdit_ErrorQuestionnaireUpdateActiveStatus";
        //Update Header parts: proceededBy
        if (isset($_POST["proceededBy"])) {
            $proceededID = intval($_POST["proceededBy"]);
            $shouldUpdate = true;
            if ($_POST["proceededBy"] != -1) {
                //Check whether the proceed By Questionnaire/Selfcheck exists and whether it does in the same language
                if (Questionnaire::getByIDaLanguage($proceededID, $lang) == FALSE) {
                    //It doesn't exist in the same language -- check for other languages
                    $foundOne = false;
                    for ($i = 0; $i < count($l->getSupportedLanguages()) && !$foundOne; $i++) {
                        if (Questionnaire::getByIDaLanguage($proceededID, $l->getSupportedLanguages()[$i]) != FALSE)
                            $foundOne = true;
                    }
                    if ($foundOne)
                        $warnings[] = "QuestEdit_WarningProceededByInOtherLanguage";
                    else {
                        $errors[] = "QuestEdit_ErrorProceedByNotFound";
                        $shouldUpdate = false;
                    }
                }
            } else
                $proceededID = Null;
            if ($shouldUpdate)
                if (!Questionnaire::UpdateEntries(array("Q_proceededby"), array($QID, $lang), array($proceededID)))
                    $errors[] = "QuestEdit_ErrorQuestionnaireUpdateProceededBy";
        }
        //Update Header:PairedSemester
        if (isset($_POST["pairedSems"])){
            $pairedSemsTrans =  json_decode($_POST["pairedSems"]);
            Questionnaire::DeleteEntries(array($QID),"sqlvali_eval.questsemest",array(Questionnaire::getPKColumns()[0]));
            //Add all paired semester back again
            foreach ($pairedSemsTrans as $pS){
                foreach ($pS->tskg_Ids as $tsgi)
                    Questionnaire::basicInsert("sqlvali_eval.questsemest",Questionnaire::getQuestSemestColums(),array($QID,$lang,$pS->Sem_Id,$tsgi));
            }
        }

        //Update Body - Tasks and task items
        ///At first delegate every taskID to a higher number to make changes available without overriding the IDS
        ///  --> in order "copy" them to a different value scope to later move them to their original positions
        $multiplier = 10000; //Defines the movement of the value scope
        $lastTaskPk = Questionnaire::getTaskPKColumns()[count(Questionnaire::getTaskPKColumns()) - 1];
        MoveValueScope(Questionnaire::getTaskTableName(), $lastTaskPk, $multiplier, array($QID, $lang));
        $lastTaskItemPK = Questionnaire::getTaskItemPKColumns()[count(Questionnaire::getTaskItemPKColumns()) - 1];
        MoveValueScope(Questionnaire::getTaskItemTableName(), $lastTaskItemPK, $multiplier, array($QID, $lang), Questionnaire::getPKColumns());

        $orderCounter = 1;//marks the new order
        while (isset($_POST["taskOrder" . $orderCounter])) {
            $referrerTaskID = (int)$_POST["taskOrder" . $orderCounter];//marks the id under which we can find all other POST variables
            $oldTaskID = isset($_POST["taskID" . $referrerTaskID]) ? $_POST["taskID" . $referrerTaskID] : -1;//Stores solely whether the task has to be inserted (-1) or updated
            if ($oldTaskID == -1) {
                //Purely insert task with blank values - Rest is updated in further process
                Questionnaire::basicInsert(Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns(), array($QID, $lang, $orderCounter));
            } else {
                //Update task ID at first - Update should be propagated through the entire FK relations
                //Get Element back to normal value scope
                if ($orderCounter != $referrerTaskID)
                    Questionnaire::UpdateEntries(array($lastTaskPk), array($QID, $lang, ($referrerTaskID * $multiplier)), array($orderCounter), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());
                else
                    Questionnaire::UpdateEntries(array($lastTaskPk), array($QID, $lang, ($referrerTaskID * $multiplier)), array($orderCounter), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());
            }
            //Change the title of the task as this is equal in all tasks
            if (isset($_POST["groupName" . $referrerTaskID]))
                Questionnaire::UpdateEntries(array("Tdescription"), array($QID, $lang, $orderCounter), array($_POST["groupName" . $referrerTaskID]), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());
            //check now whether the type was changed
            if (isset($_POST["originalType" . $referrerTaskID]) && isset($_POST["newType" . $referrerTaskID])) {
                if ($_POST["originalType" . $referrerTaskID] != $_POST["newType" . $referrerTaskID]) {
                    //type was changed -- blank out all task items as they have different content - delete all already given answers
                    //answers are deleted because they cannot possibly reflect the correct answers to the changed task
                    Questionnaire::BlankEntries(array($QID, $lang, $orderCounter), Questionnaire::getTaskItemTableName(), Questionnaire::getTaskPKColumns(), array_diff(Questionnaire::getTaskItemColumns(), Questionnaire::getTaskItemPKColumns()));
                    Questionnaire::DeleteEntries(array($QID, $lang, $orderCounter), Questionnaire::getAnswersTableName(), Questionnaire::getAnswersPKColumns());

                }
                //Update Task specific header
                //TODO: add Choice variation in single and multiple choices in Choice type
                if ($_POST["newType" . $referrerTaskID] == 3) {
                    //Likerscale - Update size and extrema
                    if (isset($_POST["scaleScope" . $referrerTaskID])) {
                        Questionnaire::UpdateEntries(array("scalesize"), array($QID, $lang, $orderCounter), array($_POST["scaleScope" . $referrerTaskID]), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());

                        $extrema = array();
                        if (isset($_POST["extremaLeft" . $referrerTaskID]))
                            $extrema[] = $_POST["extremaLeft" . $referrerTaskID];
                        for ($i = 1; $i <= intval($_POST["scaleScope" . $referrerTaskID]) - 2; $i++) {
                            if (isset($_POST["extrema" . $referrerTaskID . ";" . $i]) && strlen(trim($_POST["extrema" . $referrerTaskID . ";" . $i])) > 0)
                                $extrema[] = $_POST["extrema" . $referrerTaskID . ";" . $i];
                        }
                        if (isset($_POST["extremaRight" . $referrerTaskID]))
                            $extrema[] = $_POST["extremaRight" . $referrerTaskID];
                        $extrema = join(";", $extrema);
                        Questionnaire::UpdateEntries(array("extrema"), array($QID, $lang, $orderCounter), array($extrema), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());
                    }

                } else if ($_POST["newType" . $referrerTaskID] == 4) {
                    //Multiple Choice - Update Type and tasks drawn for Selfcheck
                    if (isset($_POST["testType" . $referrerTaskID]))
                        Questionnaire::UpdateEntries(array("extrema"), array($QID, $lang, $orderCounter), array($_POST["testType" . $referrerTaskID]), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());
                    if (isset($_POST["tasksDrawn" . $referrerTaskID]))
                        Questionnaire::UpdateEntries(array("scalesize"), array($QID, $lang, $orderCounter), array($_POST["tasksDrawn" . $referrerTaskID]), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());
                } else if ($_POST["newType" . $referrerTaskID] == 5) {
                    //SQL Task - Update Pool and tasks drawn for Selfcheck
                    if (isset($_POST["sqlTaskPool" . $referrerTaskID]))
                        Questionnaire::UpdateEntries(array("extrema"), array($QID, $lang, $orderCounter), array($_POST["sqlTaskPool" . $referrerTaskID]), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());
                    if (isset($_POST["tasksDrawn" . $referrerTaskID]))
                        Questionnaire::UpdateEntries(array("scalesize"), array($QID, $lang, $orderCounter), array($_POST["tasksDrawn" . $referrerTaskID]), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());
                }
                //Update the new type
                Questionnaire::UpdateEntries(array("type"), array($QID, $lang, $orderCounter), array($_POST["newType" . $referrerTaskID]), Questionnaire::getTaskTableName(), Questionnaire::getTaskPKColumns());

            } else
                $errors[] = "QuestEdit_ErrorTypeNotCorrectlyDelivered";

            // Update now taskitems
            $itemCounter = 1;
            while (isset($_POST["taskItemOrder" . $orderCounter . "_" . $itemCounter])) {
                $frontItemIDFull = $_POST["taskItemOrder" . $orderCounter . "_" . $itemCounter];//marks the id under which we can find all other POST variables
                $frontItemID = explode('.', $frontItemIDFull)[1];
                $frontItemIDFull = explode('.', $frontItemIDFull)[0] . "_" . $frontItemID;
                $oldItemID = isset($_POST["taskItemOldID" . $referrerTaskID . "_" . $itemCounter]) && explode('.', $_POST["taskItemOldID" . $referrerTaskID . "_" . $itemCounter]) >= 2 ? (int)explode('.', $_POST["taskItemOldID" . $referrerTaskID . "_" . $itemCounter])[1] : -1;//Stores whether the task has to be inserted (-1) or updated
                if ($oldItemID == -1) {
                    //Purely insert task with blank values - Rest is updated in further process
                    Questionnaire::basicInsert(Questionnaire::getTaskItemTableName(), Questionnaire::getTaskItemPKColumns(), array($QID, $lang, $orderCounter, $itemCounter));
                } else {
                    //var_dump($oldItemID * $multiplier);
                    //echo "<br>OldItemId" . $oldItemID . "<br>itemCounter" . $itemCounter;
                    //Update task ID at first - Update should be propagated through the entire FK relations
                    if ($oldItemID != $itemCounter)
                        Questionnaire::UpdateEntries(array($lastTaskItemPK), array($QID, $lang, $orderCounter, ($oldItemID * $multiplier)), array($itemCounter), Questionnaire::getTaskItemTableName(), Questionnaire::getTaskItemPKColumns());
                    else
                        Questionnaire::UpdateEntries(array($lastTaskItemPK), array($QID, $lang, $orderCounter, ($oldItemID * $multiplier)), array($itemCounter), Questionnaire::getTaskItemTableName(), Questionnaire::getTaskItemPKColumns());
                }
                //General Task Header Information
                if (isset($_POST["itemTitle" . $frontItemIDFull]))
                    Questionnaire::UpdateEntries(array("Idescription"), array($QID, $lang, $orderCounter, $itemCounter), array($_POST["itemTitle" . $frontItemIDFull]), Questionnaire::getTaskItemTableName(), Questionnaire::getTaskItemPKColumns());
                //Task specific
                if ($_POST["newType" . $referrerTaskID] == 1) {//Freetext
                    $maxInput = isset($_POST["maxInputLength" . $frontItemIDFull]) && strlen($_POST["maxInputLength" . $frontItemIDFull]) > 0 ? $_POST["maxInputLength" . $frontItemIDFull] : null;
                    $inputType = isset($_POST["inputType" . $frontItemIDFull]) ? $_POST["inputType" . $frontItemIDFull] : null;
                    Questionnaire::UpdateEntries(array("inputType", "inputLength"), array($QID, $lang, $orderCounter, $itemCounter), array($inputType, $maxInput), Questionnaire::getTaskItemTableName(), Questionnaire::getTaskItemPKColumns());
                } else if ($_POST["newType" . $referrerTaskID] == 2) {//Choices
                    $itemsList = array();
                    $counter = 1;
                    while (isset($_POST["listItem" . $frontItemIDFull . "_" . $counter])) {
                        $itemsList[] = $_POST["listItem" . $frontItemIDFull . "_" . $counter];
                        $counter++;
                    }
                    $itemsList = join(';', $itemsList);
                    Questionnaire::UpdateEntries(array("possibleChoices"), array($QID, $lang, $orderCounter, $itemCounter), array($itemsList), Questionnaire::getTaskItemTableName(), Questionnaire::getTaskItemPKColumns());
                } else if ($_POST["newType" . $referrerTaskID] == 4) {// Multiple Choices
                    $itemsList = array();
                    $correctItemsList = array();
                    $counter = 1;
                    while (isset($_POST["listItem" . $frontItemIDFull . "_" . $counter])) {
                        $itemsList[] = $_POST["listItem" . $frontItemIDFull . "_" . $counter];
                        if (isset($_POST["listItemCB" . $frontItemIDFull . "_" . $counter]))
                            $correctItemsList[] = $_POST["listItem" . $frontItemIDFull . "_" . $counter];
                        $counter++;
                    }
                    $itemsList = join(';', $itemsList);
                    $correctItemsList = join(';', $correctItemsList);
                    Questionnaire::UpdateEntries(array("possibleChoices", "correctChoices"), array($QID, $lang, $orderCounter, $itemCounter), array($itemsList, $correctItemsList), Questionnaire::getTaskItemTableName(), Questionnaire::getTaskItemPKColumns());
                }

                $itemCounter++;
            }

            //add counter up to got to next front task
            $orderCounter++;
        }

        //Cleanup: Delete all remaining tasks, task items and answers (answers get propagated through taskItems)
        //Possible through simply deleting all items that have not been moved back to normal space
        Questionnaire::DeleteEntries(array($QID, $lang), Questionnaire::getTaskTableName(), Questionnaire::getPKColumns(), $lastTaskPk . " >= " . $multiplier);
        Questionnaire::DeleteEntries(array($QID,$lang),Questionnaire::getTaskItemTableName(),Questionnaire::getPKColumns(),$lastTaskPk." > 0 AND ".$lastTaskItemPK." >= ".$multiplier);
    }
    //$errors[]="TestError";
    //End the transaction spanning all changes of the questionnaire; Commit the changes if there are no errors - rollback else
    if (count($errors) > 0) {
        $dbEval->endTransaction(false);
        $QID = $oldQID;//Reset changes that may have occurred by changing ID/Lang
        $lang = $oldLang;
    }
    else
        $dbEval->endTransaction(True);

}
//Gather data to display
$questionnaire = $QID > 0 ? questionnaire::getByPK(array($QID,$lang)) : null;

$allSemester = array();//All semester with their ID and Name
$pairedSem = array();//Every semester that is associated with the questionnaire with ID and Name
$pairedSemIds = array();//Every semester that is associated with the questionnaire with ID and Name

//Gather all available Semesters
$db->query("SELECT sem_id,sem_descr FROM semester");
foreach ($db->fetchAll() as $line){
    $allSemester[] = [$line[0],$line[1]];
}
//Gather all available Questionnaires
$questsCont = EvalModule::getQuestionnaires();

//Gather all paired Semesters
$availableSem = Questionnaire::getGeneralConditionedJoined(array("questsemest"),array("Q_ID","Q_language","sem_id","tskg_id"),"Q_ID = ? AND Q_language = ?",array($QID,$lang));
$lastSemID = -2;
foreach ($availableSem as $avSem){
    $index = array_search($avSem["sem_id"],array_column($allSemester,0));
    if ($index !== false){
        if ($avSem["sem_id"] != $lastSemID){
            //New Semester -- add whole semester to array
            $lastSemID = $avSem["sem_id"];
            $pairedSem[] = ["Sem_Descr" => $allSemester[$index][1],"Sem_Id" => $allSemester[$index][0],"tskg_Ids" => array($avSem["tskg_id"])];
            //$pairedSem[] = ["Q_ID" =>$avSem["Q_ID"],"Sem_Descr" => $allSemester[$index][1],"Sem_Id" => $allSemester[$index][0],"tskg_Ids" => array($avSem["tskg_id"])];
            $pairedSemIds[] = $avSem["sem_id"];
        }else {
            //Same Semester -- only add the additionally task group id
            $pairedSem[count($pairedSem)-1]["tskg_Ids"][] = $avSem["tskg_id"];
        }

    }
}

//Gather the tasks and tasksgroup
$questGroups = array();
$questGroups = $questionnaire->getAllTasksWithItems();

//Gather the taskgroups of every semester
$tgSemPaired = array();
foreach ($allSemester as $sem){
    //var_dump(TaskGroup::getByCondition("tskg_sem_id = ".$sem[0]));echo "<br><br>";
    $tgSemPaired[] = ["sem_id" => $sem[0],"tgs" => array()];
    foreach (TaskGroup::getByCondition("tskg_sem_id = ".$sem[0]) as $tg){
        $tgSemPaired[count($tgSemPaired)-1]["tgs"][] = ["tg_id"=>$tg->getId(),"tg_name"=>$tg->getName($lang)];
        //echo $sem[0]." ".$tg->getId()." : ".$tg->getName($lang)."<br>";
    }
}

//Little helper function to move the value scope for editing and deleting

function MoveValueScope($table,$pkToSet,$multiplier,$refPks,$pkCols = null){
    global $dbEval;
    $pkUpdate = array();
    $pkCols = $pkCols == null ? Questionnaire::getPKColumns(): $pkCols;
    foreach ($pkCols as $pkC)
        $pkUpdate[] = $pkC." = ?";
    $pkUpdate = join(' AND ',$pkUpdate);
    $SQL = "UPDATE ".$table." Set ".$pkToSet." = ".$pkToSet."*".$multiplier." WHERE ".$pkUpdate;
    //echo $SQL;
    if (!$dbEval->queryWithParams($SQL,$refPks)) {
        $errors[] = "QuestEdit_ErrorMovementScope";
        echo $dbEval->getErrorText();
    }
}
?>