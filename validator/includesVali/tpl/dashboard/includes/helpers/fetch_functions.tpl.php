<?php
require_once (__DIR__."/../db/connection.tpl.php");

function getErrorClasses()
{

}

function getNumberOfStudents($semId)
{
    global $pdo;
    $statement = $pdo->prepare('select usr_id,usr_sem_id from user where usr_flag_admin= :flag and usr_sem_id=:sem_id');
    $statement->bindValue(":flag","N");
    $statement->bindValue(":sem_id",$semId);
    if(!$statement->execute())
    {
        echo "students not retrieved";
    }
    return  $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getSemesters()
{
    global $pdo;
    $statement = $pdo->prepare('select sem_id,sem_descr from semester order by sem_id ');
    if(!$statement->execute())
    {
        echo "semester not retrieved";
    }
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function getDemographic($semId)
{
    global $pdo;
    $statement = $pdo->prepare('select qanswers.result from qanswers join user on 
                    qanswers.UserID=user.usr_id where user.usr_sem_id = :sem_id and (Q_ID=:q_id1 or Q_ID=:q_id2) and TaskNum=:task_num');
    $statement->bindValue(":sem_id",$semId);
    $statement->bindValue(":q_id1",2);
    $statement->bindValue(":q_id2",7);
    $statement->bindValue(":task_num",1);
    if(!$statement->execute())
    {
        echo "sex not retrieved";
    }

    $demographic =  $statement->fetchAll(PDO::FETCH_ASSOC);
    return processDemographic($demographic);
}

function processDemographic($demographic)
{
    $processedDemo = Array();
    foreach($demographic as $key=>$demo)
    {
        $result= trim($demo["result"]);
        if(array_key_exists($result,$processedDemo)){
            $processedDemo[$result]++;
        }else{
            $processedDemo[$result]=1;
        }
    }

    if(array_key_exists("Weiblich",$processedDemo))
    {
        if(array_key_exists("Female",$processedDemo))
            $processedDemo["Female"] += $processedDemo["Weiblich"];
        else
            $processedDemo["Female"] = $processedDemo["Weiblich"];

        unset($processedDemo["Weiblich"]);
    }

    if(array_key_exists("M??nnlich",$processedDemo))
    {
        if(array_key_exists("Male",$processedDemo))
            $processedDemo["Male"] += $processedDemo["M??nnlich"];
        else
            $processedDemo["Male"] = $processedDemo["M??nnlich"];

        unset($processedDemo["M??nnlich"]);
    }
    if(array_key_exists("Divers",$processedDemo))
    {
        $processedDemo["Other"] += $processedDemo["Divers"];
        unset($processedDemo["Divers"]);
    }

    return $processedDemo;
}

function getProgrammes($semId)
{
    global $pdo;
    $statement = $pdo->prepare('select qanswers.result from qanswers join user on 
                    qanswers.UserID=user.usr_id where user.usr_sem_id = :sem_id and (Q_ID=:q_id1 or Q_ID=:q_id2) and TaskNum=:task_num');
    $statement->bindValue(":sem_id",$semId);
    $statement->bindValue(":q_id1",2);
    $statement->bindValue(":q_id2",7);
    $statement->bindValue(":task_num",4);
    if(!$statement->execute())
    {
        echo "programmes not retrieved";
    }

    $programmes = $statement->fetchAll(PDO::FETCH_ASSOC);

    return processProgrammes($programmes);
}

function processProgrammes($programmes)
{
    $processedProgrammes = Array();
    foreach($programmes as $key=>$programme)
    {
        $result= trim($programme["result"]);
        if(array_key_exists($result,$processedProgrammes)){
            $processedProgrammes[$result]++;
        }else{
            $processedProgrammes[$result]=1;
        }
    }
    unset($programmes);

    return $processedProgrammes;
}
function getStudySemesters($semId)
{
    global $pdo;
    $statement = $pdo->prepare('select qanswers.result from qanswers join user on 
                    qanswers.UserID=user.usr_id where user.usr_sem_id = :sem_id and (Q_ID=:q_id1 or Q_ID=:q_id2) and TaskNum=:task_num');
    $statement->bindValue(":sem_id",$semId);
    $statement->bindValue(":q_id1",2);
    $statement->bindValue(":q_id2",7);
    $statement->bindValue(":task_num",3);
    if(!$statement->execute())
    {
        echo "programmes not retrieved";
    }

    $studySemesters = $statement->fetchAll(PDO::FETCH_ASSOC);

    return processStudySem($studySemesters);
}

function processStudySem($studySemesters)
{
    $processedSem = Array();
    foreach($studySemesters as $key=>$studySemester)
    {
        if($key <1 || $key >10 )
            continue;
        $result= trim($studySemester["result"]);
        if(array_key_exists($result,$processedSem)){
            $processedSem[$result]++;
        }else{
            $processedSem[$result]=1;
        }
    }
    unset($studySemesters);

    //return as assoc array of study semester and freq
    return $processedSem;
}

function getTaskList($semId,$lang)
{
    global $pdo;
    //Get List of task and task Group in selected semester
    $statement = $pdo->prepare('select tsk_id,tskg_id,tsk_copyof_tsk_id from task join task_group on tsk_tskg_id=tskg_id where tsk_sem_id = :sem_id');
    $statement->bindValue(":sem_id",$semId);
    $statement->execute();
    $taskNTaskGroup = $statement->fetchAll(PDO::FETCH_ASSOC);

    //Get details of the titles of the taskGroups
    $statement = $pdo->prepare('select tskg_id,tskgl_name from task_group join task_group_localization on tskgl_tskg_id=tskg_id where tskg_sem_id = :sem_id and tskgl_lang=:lang');
    $statement->bindValue(":sem_id",$semId);
    $statement->bindValue(":lang",$lang);
    $statement->execute();
    $taskGroups = $statement->fetchAll(PDO::FETCH_ASSOC);

    //Get List of task and title
    $statement = $pdo->prepare('select tskl_tsk_id,tsk_id,tskl_title from task join task_localization on tsk_id=tskl_tsk_id where tskl_lang=:lang');
    $statement->bindValue(":lang",$lang);
    $statement->execute();
    $taskTitles = $statement->fetchAll(PDO::FETCH_ASSOC);
    $taskTitles = processTaskTitles($taskTitles);

    //return usable assoc array
    $result=array();
    foreach($taskGroups as $key=>$taskGroup)
    {
        $tg= trim($taskGroup["tskg_id"]);

        if(!array_key_exists($tg,$result)){
            $result[$tg]= array();
        }else{
            continue;
        }
        $tgDetails = array();
        $tgDetails["group_title"] = $taskGroup["tskgl_name"];
        $tgDetails["tasks"]= array();
        //assign the tasks to the task groups
        foreach($taskNTaskGroup as $nkey=>$taskNGroup)
        {
            $task_tg = trim($taskNGroup["tskg_id"]);
            if($task_tg==$tg)
            {
                $taskId = $taskNGroup["tsk_id"];
                $taskCopyId = $taskNGroup["tsk_copyof_tsk_id"];
                if(array_key_exists($taskId,$taskTitles)){
                    $taskTitle = $taskTitles[$taskId];
                }
                else{
                    $taskTitle = $taskTitles[$taskCopyId];
                }
                $tgDetails["tasks"][$taskId]= $taskTitle;

            }

        }
        $result[$tg]= $tgDetails;
    }
    //returns assoc array with task group as key and tasks as assoc arrays within
    return $result;
}

function processTaskTitles($taskTitles)
{
    $result = array();
    foreach ($taskTitles as $key => $taskTitle) {
        $result[$taskTitle["tskl_tsk_id"]] = $taskTitle["tskl_title"];
    }
    return $result;
}
function getErrorList($taskId)
{
    global $pdo;
    //Get all errors of the task
    $statement = $pdo->prepare('select eq_errors from evalquery where eq_taskid = :taskid');
    $statement->bindValue(":taskid",$taskId);
    $statement->execute();
    $errorMash = $statement->fetchAll(PDO::FETCH_ASSOC);

    //split the error as they are usually concatenated
    $splitErrors = array();
    foreach ($errorMash as $key=>$errors)
    {
        $errorArr = explode(';', $errors["eq_errors"]);
        foreach ($errorArr as $nkey=> $error)
        {
            $splitErrors[] = $error;
        }
    }

    //get new array of errors and frequency
    $result = array();
    foreach ($splitErrors as $key=> $error)
    {
        if($error==0)
            continue;

        if(array_key_exists($error,$result))
            $result[$error] += 1;
        else
            $result[$error] = 1;
    }
    unset($errorMash);
    unset($splitErrors);
    //return error and respective frequency as assoc
    return $result;
}

function getErrorListProficiency($taskId,$proficiency)
{
    global $pdo;
    //Get errors of the task
    $statement = $pdo->prepare('select eq_errors from evalquery join qanswers on eq_UserID=UserID 
                        where eq_taskid = :taskid and (Q_ID=7 or Q_ID=2) and TaskNum=8 and result=:profiency');
    $statement->bindValue(":taskid",$taskId);
    $statement->bindValue("profiency",$proficiency);
    $statement->execute();
    $errorMash = $statement->fetchAll(PDO::FETCH_ASSOC);

    //split the error as they are usually concatenated
    $splitErrors = array();
    foreach ($errorMash as $key=>$errors)
    {
        $errorArr = explode(';', $errors["eq_errors"]);
        foreach ($errorArr as $nkey=> $error)
        {
            $splitErrors[] = $error;
        }
    }

    //get new array of errors and frequency
    $result = array();
    foreach ($splitErrors as $key=> $error)
    {
        if($error==0)
            continue;

        if(array_key_exists($error,$result))
            $result[$error] += 1;
        else
            $result[$error] = 1;
    }
    unset($errorMash);
    unset($splitErrors);
    //return error and respective frequency as assoc
    return $result;
}

function getRetrialTaskList($semOneGroupId, $semId,$lang)
{
    global $pdo;
    //Get user and task table that correspond to the group
    $statement = $pdo->prepare('select eq_UserID,eq_taskid,tsk_copyof_tsk_id from evalquery join task on tsk_id=eq_taskid where tsk_tskg_id=:groupId and tsk_sem_id=:semId');
    $statement->bindValue(":groupId",$semOneGroupId);
    $statement->bindValue(":semId",$semId);
    $statement->execute();
    //list has taskid and userid numbering to the number of tries of the task
    $taskList = $statement->fetchAll(PDO::FETCH_ASSOC);

    //get titles
    //Get List of task and title
    $statement = $pdo->prepare('select tskl_tsk_id,tskl_title from task_localization where tskl_lang=:lang');
    $statement->bindValue(":lang",$lang);
    $statement->execute();
    $taskTitles = $statement->fetchAll(PDO::FETCH_ASSOC);
    $taskTitles = processTaskTitles($taskTitles);

    //get a list with task containing title and  number of retries
    $res = processRetrialTaskList($taskList,$taskTitles);
    return $res;
}

function processRetrialTaskList($list,$title)
{
    $result = array();
    $userRecord = array();
    foreach ($list as $key=>$retry){
        $taskId = $retry['eq_taskid'];
        $userId = $retry['eq_UserID'];
        if(array_key_exists($taskId,$title))
            $taskTitle = $title[$taskId];
        else
            $taskTitle = $title[$retry['tsk_copyof_tsk_id']];

        if(!key_exists($taskId,$result)){
            $result[$taskId] = array();
            $result[$taskId][$taskTitle]=0;
            $userRecord[$taskId] = array();
        }

        if(!in_array($userId,$userRecord[$taskId])){
            $userRecord[$taskId][] =$userId;
        }else{
            $result[$taskId][$taskTitle] +=1;
        }
    }

    //get a list with task containing title and  number of retries
    return $result;
}


//Get ACTIVITY

function getErrorActivity($semId)
{
    global $pdo;
    $statement = $pdo->prepare('select eq_errors,eq_ts from evalquery join task on tsk_id=eq_taskid where tsk_sem_id=:semId order by eq_ts');
    $statement->bindValue(":semId",$semId);
    $statement->execute();
    $errorActivity = $statement->fetchAll(PDO::FETCH_ASSOC);

    //split the error as they are usually concatenated
    $splitErrorActivity = array();
    $weeks = array();
    $activities = array();

    foreach ($errorActivity as $key=>$errors) {
        $week = date('Y W', strtotime($errors['eq_ts']));

        if (!in_array($week, $weeks))
            $weeks[] = $week;
    }
    $numberOfWeeks =count($weeks);

    foreach ($errorActivity as $key=>$errors)
    {

        $week = date('Y W', strtotime($errors['eq_ts']));
        //get the index of the week
        $weekIndex = array_search($week, $weeks);

        $errorArr = explode(';', $errors["eq_errors"]);
        foreach ($errorArr as $nkey=> $error)
        {
            if(!array_key_exists($error,$activities)){
                $activities[$error] = array_fill(0, $numberOfWeeks, 0);
            }
            // create count for new record at that error
            $activities[$error][$weekIndex]+= 1/count($errorArr);
        }
    }

    //rename the weeks
    foreach ($weeks as $key=> $wk)
    {
        $weeks[$key] = "week " .($key+1);

    }
    $splitErrorActivity["labels"] = $weeks;
    $splitErrorActivity["errors"] = $activities;

    unset($errorActivity);
    //return error and respective frequency as assoc
    return $splitErrorActivity;
}

function getQuest($semId,$lang){
    global $pdo;
    // get QID titles
    $type1=2;
    $type2=3;
    $statement = $pdo->prepare('select Q.Q_id,Q.Q_title,T.TaskNum,T.Tdescription,I.INum, I.Idescription from questionnaire Q 
                                        left join qtask T on Q.Q_id= T.Q_id 
                                        join taskitem I on T.Q_id= I.Q_ID and I.TaskNum = T.TaskNum
                                        where Q.Q_language = :lang and T.Q_language = :lang and I.Q_language = :lang
                                          and (T.type= :type2 or T.type=:type2)
                                          and Q.Q_id in(select distinct Q_ID from questsemest where sem_id=:semId)');
    $statement->bindValue(":lang",$lang);
    $statement->bindValue(":semId",$semId);
    //$statement->bindValue(":type1",$type1);
    $statement->bindValue(":type2",$type2);
    $statement->execute();
    $qTitles = $statement->fetchAll(PDO::FETCH_ASSOC);

    //Create assoc to house the questionnaire hierachichally
    $titles = array();
    foreach($qTitles as $key => $record){
        $q_id = $record['Q_id'];
        $taskNum = $record['TaskNum'];
        $item = $record['INum'];
        if(!isset($titles[$q_id])){
            $titles[$q_id]= array();
            $titles[$q_id]['title'] = $record['Q_title'];
            $titles[$q_id]['taskNum'] = array();
        }

        if(!isset($titles[$q_id]['taskNum'][$taskNum])){
            $titles[$q_id]['taskNum'][$taskNum]= array();
            $titles[$q_id]['taskNum'][$taskNum]['title'] = $record['Tdescription'];
            $titles[$q_id]['taskNum'][$taskNum]['items'] = array();
        }

        if($record['Idescription'] == null || $record['Idescription']=="")
            $titles[$q_id]['taskNum'][$taskNum]['items'][$item] = $record['Tdescription'];
        else
            $titles[$q_id]['taskNum'][$taskNum]['items'][$item] = $record['Idescription'];
    }
    return $titles;
}

function getQuestAnswers($semId,$lang,$q_id,$taskNum){
    global $pdo;
    $statement = $pdo->prepare('select INum,result from qanswers join user on UserID= usr_id where usr_sem_id=:semId and Q_ID=:qId and TaskNum=:taskNum order by result');
    $statement->bindValue(":semId",$semId);
    $statement->bindValue(":qId",$q_id);
    $statement->bindValue(":taskNum",$taskNum);
    $statement->execute();
    $answers = $statement->fetchAll(PDO::FETCH_ASSOC);

    //get from the task the label/extremes of the answer eg. disagree, strongly agree, etc
    $statement = $pdo->prepare('select extrema from qtask where Q_id=:qId and Q_language= :lang and TaskNum=:taskNum	');
    $statement->bindValue(":qId",$q_id);
    $statement->bindValue(":taskNum",$taskNum);
    $statement->bindValue(":lang",$lang);
    $statement->execute();
    $taskExtremes = $statement->fetchAll(PDO::FETCH_ASSOC);

    $extremes= explode(";",$taskExtremes[0]["extrema"]);

    //Get titles of the taskItem
    $statement = $pdo->prepare('select T.TaskNum,T.Tdescription,I.INum,I.Idescription from taskitem I join qtask T
                                        on I.Q_ID=T.Q_id and I.TaskNum= T.TaskNum 
                                        where I.Q_ID=:qId and T.Q_language= :lang and I.Q_language= :lang and T.TaskNum=:taskNum	order by I.INum');
    $statement->bindValue(":qId",$q_id);
    $statement->bindValue(":taskNum",$taskNum);
    $statement->bindValue(":lang",$lang);
    $statement->execute();
    $titles =$statement->fetchAll(PDO::FETCH_ASSOC);

    $itemTitles=array();
    foreach($titles as $key => $title) {
        if($title["Idescription"]==null)
            $itemTitles[$title["INum"]] = $title["TaskNum"].".".$title["INum"]." ". $title["Tdescription"];
        else
            $itemTitles[$title["INum"]] = $title["TaskNum"].".".$title["INum"]." ". $title["Idescription"];
    }

    $myAnswers = array();
    foreach($answers as $key => $answer){
        if(!array_key_exists($answer["result"],$myAnswers)){
            $myAnswers[$answer["result"]] =array_fill(0, count($titles), 0);
        }
        $index =0;
        foreach($itemTitles as $key => $title) {
            if($key !=$answer["INum"]){
                $index++;
            }else{
                break;
            }

        }
        $myAnswers[$answer["result"]][$index]++;
    }

    if(count($myAnswers)<5){
        for($i=0;$i<5;$i++){
            if(!array_key_exists($i,$myAnswers)){
                $myAnswers[$i]=array_fill(0, count($titles), 0);
            }
        }
    }
    /*
     result={
            "answers" :{
                        0 : [1, 4 , etc], // choice : [frequency for individual questions]
                        1 : [1, 4 , etc],
                        },
            "extremes" :["minor","major"],
            "titles" :{
                        INum : "title",
                        INum : "title"
                        },
            }
     */
    $result=array();
    $result["answers"]= $myAnswers;
    $result["extremes"]= $extremes;
    $result["titles"]= $itemTitles;

    return $result;
}

function getSkillError(){

}