<?php

$QID = isset($_GET["q"]) ? intval($_GET["q"]) : 0;
$lang = $l->getLanguage();
$questionnaire = $QID > 0 ? questionnaire::getByPK(array($QID,$lang)) : null;

if ($questionnaire == null){
    //Try to get it in another language
    $questionnaire = questionnaire::getByPK(array($QID,array_diff($l->getSupportedLanguages(),array($lang))[0]));
    if ($questionnaire != null) {
        $otherLanguage = true;
        $lang = $questionnaire->getPks()[1];
    }
}

//Check whether the User already completed this questionnaire
$tookPart = False;
if ($questionnaire != null)
	$tookPart = $questionnaire->checkUserParticipation($user->getUserEvalID());

if ($tookPart){
    //If the user already took this one, but there is a proceeding selfcheck - load this
    if ($questionnaire->getProceededby() != NULL)
        header('location:index.php?action=eval/selfcheck&q='.$questionnaire->getProceededby());
}

if ($questionnaire != null && strpos($questionnaire->getType(), 'Selfcheck') !== false)
    header('location:index.php?action=eval/selfcheck&q='.$questionnaire->getPKs()[0]);

//Load all tasks and items to display them
if ($questionnaire != null && !$tookPart)
    $tasks = $questionnaire->getAllTasksWithItems();

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    /**
     * Site is loaded the second time with results showing
     * -- > Save the results in the database
     */
    $errors = array();

    //Save result data in database
    $UserID = $user->getUserEvalID();
    $counterTask=1;
    $counterItem=1;
    //Start the Transaction
    if ($questionnaire->getvalidityCheck() == 1)
    {
        global $dbEval;
        $dbEval->startTransaction();
    }
    //Go through all tasks and items from the questionnaire and save en entry if there is one
    $tasks = $questionnaire->getAllTasksWithItems();
    foreach ($tasks as $task)
    {
        $taskNum = $task["TaskNum"];
        foreach($task["item"] as $item)
        {
            $itemNum = $item["INum"];
            if (isset($_POST["T".$taskNum."I".$itemNum]))
            {
                $resultVal = $_POST["T".$taskNum."I".$itemNum];

                //Check on multiple choices whether the answer is correct or wrong
                if ($task["type"] == 4)
                {
                    $correctChoices = array_map('trim', explode(";",$item["correctChoices"]));
                    if (in_array(trim($resultVal),$correctChoices))
                        $resultVal .= ";(Correct)";
                    else
                        $resultVal .= ";(False)";
                }
                else if ($questionnaire->getvalidityCheck()== 1)//Should there be a validity Check
                {
                    if ($task["type"] == 1)//Check whether free text is in the correct type format
                    {
                        $type = $item["inputType"];
                        if (substr($type,0,3) == "int"){
                            if (!ctype_digit($resultVal))
                                $errors[] = $l->getString("questionnaire_task")." ".$counterTask.".".$counterItem.": ".$l->getString("questionnaire_error_intNeeded");
                        }
                        if ($item["inputLength"] != NULL AND $item["inputLength"] < strlen($resultVal)){
                            $diff = strlen($resultVal) - intval($item["inputLength"]);
                            $errors[] = $l->getString("questionnaire_task")." ".$counterTask.".".$counterItem.": ".$l->getString("questionnaire_error_inputTooLong")." ".$diff;
                        }
                    }
                }
                $vals = array($UserID, $QID,$lang, $taskNum, $itemNum, $resultVal);
                $res = $questionnaire::basicInsert(Questionnaire::getAnswersTableName(), array_slice($questionnaire::getAnswersColums(),0,6), $vals);
            }
        }
    }

    if ($questionnaire->getvalidityCheck()== 1)
    {
        if (count($errors)==0)
            $dbEval->endTransaction(True);
        else
            $dbEval->endTransaction(False);
    }

    //Delegate if there is a need for
    if ($questionnaire->getProceededby() != NULL){
        if (strpos($questionnaire->getType(), 'Selfcheck') !== false)
            header('location:index.php?action=eval/selfcheck&q='.$questionnaire->getProceededby());
        else
            header('location:index.php?action=eval/questionnaire&q='.$questionnaire->getProceededby());
    }


}

//Error handling
if ($QID == null || $questionnaire == null)
    $page="access_denied";

?>