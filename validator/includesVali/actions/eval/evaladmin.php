<?php

$tskgStatisticsCont = EvalModule::getTaskStatistics($_SESSION["sem_id"],$l->getLanguage());
$questsCont = EvalModule::getQuestionnaires();

$semester = Semester::getAll();

?>